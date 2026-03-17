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
use Illuminate\Support\Facades\Log;

class RunPresetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deviceId;
    protected $presetId;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param int $deviceId
     * @param int $presetId
     */
    public function __construct($deviceId, $presetId)
    {
        $this->deviceId = $deviceId;
        $this->presetId = $presetId;
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

        Log::info("RunPresetJob: Starting preset '{$preset->name}' on device '{$device->model}'");

        foreach ($preset->commands as $index => $command) {
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
                        Log::error("RunPresetJob: Command #{$index} ({$controller}/{$action}) failed after {$maxAttempts} attempts. Continuing with next command.");
                    }
                }
            }

            // Respect delay between commands
            if ($delay > 0) {
                sleep((int) $delay);
            }
        }

        Log::info("RunPresetJob: Preset '{$preset->name}' completed on device '{$device->model}'");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("RunPresetJob failed for device {$this->deviceId}, preset {$this->presetId}: " . $exception->getMessage());
    }
}
