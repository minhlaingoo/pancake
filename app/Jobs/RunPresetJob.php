<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Preset;
use App\Services\MqttService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RunPresetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deviceId;
    protected $presetId;
    protected $startFromIndex;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;  // Don't retry whole job — handle retries per command

    /**
     * Create a new job instance.
     *
     * @param int $deviceId
     * @param int $presetId
     */
    public function __construct($deviceId, $presetId, $startFromIndex = 0)
    {
        $this->deviceId = $deviceId;
        $this->presetId = $presetId;
        $this->startFromIndex = $startFromIndex;
    }

    /**
     * Execute the job.
     *
     * @param MqttService $mqttService
     */
    public function handle(MqttService $mqttService): void
    {
        $device = Device::find($this->deviceId);
        $preset = Preset::find($this->presetId);

        if (!$device || !$preset) {
            Log::warning("RunPresetJob: Device ({$this->deviceId}) or Preset ({$this->presetId}) not found. Skipping.");
            return;
        }

        if (empty($device->model)) {
            Log::error("RunPresetJob: Device {$device->id} has no model name. Cannot send commands.");
            return;
        }

        Log::info("RunPresetJob: Starting preset '{$preset->name}' on device '{$device->model}' from step {$this->startFromIndex}");

        foreach ($preset->commands as $index => $command) {
            // Check for emergency stop flag before each command
            if (Cache::get("device:{$this->deviceId}:emergency_stop")) {
                Log::warning("RunPresetJob: Emergency stop triggered for device {$this->deviceId}. Aborting preset '{$preset->name}' at command #{$index}.");
                Cache::forget("device:{$this->deviceId}:emergency_stop");
                return;
            }

            $controller = $command['controller'] ?? '';
            $action = $command['action'] ?? '';
            $value = $command['value'] ?? '';
            $type = $command['type'] ?? 'string';
            $delay = $command['delay'] ?? 0;
            $retryCount = $command['retry_count'] ?? 0;
            $timeout = $command['timeout'] ?? 30;

            if (empty($controller) || empty($action)) {
                Log::warning("RunPresetJob: Skipping command #{$index} - missing controller or action.");
                continue;
            }

            // Format payload based on type
            $payload = (string) $value;
            if ($type === 'bool' || is_bool($value)) {
                $payload = $value ? '1' : '0';
            }

            // Publish with per-command retry logic
            $attempts = 0;
            $maxAttempts = max(1, $retryCount + 1);
            $sent = false;

            while ($attempts < $maxAttempts && !$sent) {
                try {
                    $mqttService->deviceCommand($device->model, $controller, $action, $payload);
                    $sent = true;
                } catch (\Exception $e) {
                    $attempts++;
                    Log::warning("RunPresetJob: Command #{$index} ({$controller}/{$action}) attempt {$attempts} failed: " . $e->getMessage());

                    if ($attempts < $maxAttempts) {
                        sleep(1);
                    } else {
                        Log::error("RunPresetJob: Command #{$index} ({$controller}/{$action}) failed after {$maxAttempts} attempts. Stopping preset execution.");
                        return; // Stop — don't continue with broken state
                    }
                }
            }

            // Respect delay between commands
            if ($delay > 0) {
                sleep((int) $delay);
            }
        }

        Log::info("RunPresetJob: Preset '{$preset->name}' completed on device '{$device->model}' ({$totalCommands} commands)");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("RunPresetJob failed for device {$this->deviceId}, preset {$this->presetId}: " . $exception->getMessage());
    }
}
