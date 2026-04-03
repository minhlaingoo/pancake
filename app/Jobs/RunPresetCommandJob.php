<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\ScheduledPresetCommand;
use App\Services\MqttService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RunPresetCommandJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 60;

    public function __construct(
        protected int $scheduledCommandId,
    ) {}

    public function handle(MqttService $mqttService): void
    {
        $scheduled = ScheduledPresetCommand::find($this->scheduledCommandId);

        if (!$scheduled || $scheduled->status !== 'dispatched') {
            return;
        }

        // Check emergency stop
        if (Cache::get("device:{$scheduled->device_id}:emergency_stop")) {
            $scheduled->update(['status' => 'cancelled']);
            Log::warning("RunPresetCommandJob: Emergency stop active for device {$scheduled->device_id}. Cancelled command #{$scheduled->command_index}.");
            return;
        }

        $device = Device::find($scheduled->device_id);
        if (!$device) {
            $scheduled->update(['status' => 'failed']);
            return;
        }

        $command = $scheduled->command_data;
        $controller = $command['controller'] ?? '';
        $action = $command['action'] ?? '';
        $value = $command['value'] ?? '';
        $type = $command['type'] ?? 'string';
        $retryCount = $command['retry_count'] ?? 0;

        if (empty($controller) || empty($action)) {
            $scheduled->update(['status' => 'completed']);
            Log::warning("RunPresetCommandJob: Skipping command #{$scheduled->command_index} - missing controller or action.");
            return;
        }

        // Format payload based on type
        $payload = (string) $value;
        if ($type === 'bool' || is_bool($value)) {
            $payload = $value ? '1' : '0';
        }

        // Per-command retry logic
        $attempts = 0;
        $maxAttempts = max(1, $retryCount + 1);
        $sent = false;

        while ($attempts < $maxAttempts && !$sent) {
            try {
                $mqttService->deviceCommand($device->model, $controller, $action, $payload);
                $sent = true;
            } catch (\Exception $e) {
                $attempts++;
                Log::warning("RunPresetCommandJob [{$scheduled->batch_id}]: Command #{$scheduled->command_index} ({$controller}/{$action}) attempt {$attempts} failed: " . $e->getMessage());

                if ($attempts < $maxAttempts) {
                    sleep(1);
                }
            }
        }

        if ($sent) {
            $scheduled->update(['status' => 'completed']);
            Log::info("RunPresetCommandJob [{$scheduled->batch_id}]: Command #{$scheduled->command_index} ({$controller}/{$action}) sent on device '{$device->model}'.");
        } else {
            $scheduled->update(['status' => 'failed']);
            Log::error("RunPresetCommandJob [{$scheduled->batch_id}]: Command #{$scheduled->command_index} failed after {$maxAttempts} attempts.");

            // Stop remaining commands in this batch
            ScheduledPresetCommand::where('batch_id', $scheduled->batch_id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("RunPresetCommandJob failed for scheduled command {$this->scheduledCommandId}: " . $exception->getMessage());

        $scheduled = ScheduledPresetCommand::find($this->scheduledCommandId);
        if ($scheduled) {
            $scheduled->update(['status' => 'failed']);
        }
    }
}
