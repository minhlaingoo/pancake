<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\Preset;
use App\Models\ScheduledPresetCommand;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RunPresetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deviceId;
    protected $presetId;
    protected $startFromIndex;

    public int $tries = 1;
    public int $timeout = 30;

    public function __construct($deviceId, $presetId, $startFromIndex = 0)
    {
        $this->deviceId = $deviceId;
        $this->presetId = $presetId;
        $this->startFromIndex = $startFromIndex;
    }

    /**
     * Pre-calculate execution timestamps for all commands and insert them
     * into the scheduled_preset_commands table.
     *
     * The scheduler picks up rows where execute_at <= now and dispatches
     * them to the queue for actual MQTT execution.
     */
    public function handle(): void
    {
        $device = Device::find($this->deviceId);
        $preset = Preset::find($this->presetId);

        if (!$device || !$preset) {
            Log::warning("RunPresetJob: Device ({$this->deviceId}) or Preset ({$this->presetId}) not found.");
            return;
        }

        if (empty($device->model)) {
            Log::error("RunPresetJob: Device {$device->id} has no model name.");
            return;
        }

        $commands = $preset->commands;
        if (empty($commands)) {
            Log::warning("RunPresetJob: Preset '{$preset->name}' has no commands.");
            return;
        }

        $batchId = Str::uuid()->toString();
        $startTime = now();
        $cumulativeDelay = 0;
        $rows = [];

        foreach ($commands as $index => $command) {
            if ($index < $this->startFromIndex) {
                continue;
            }

            // Accumulate delay from previous command
            if ($index > $this->startFromIndex) {
                $prevDelay = $commands[$index - 1]['delay'] ?? 0;
                $cumulativeDelay += (int) $prevDelay;
            }

            $rows[] = [
                'batch_id' => $batchId,
                'device_id' => $this->deviceId,
                'preset_id' => $this->presetId,
                'command_index' => $index,
                'command_data' => json_encode($command),
                'execute_at' => $startTime->copy()->addSeconds($cumulativeDelay),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert all scheduled commands
        ScheduledPresetCommand::insert($rows);

        Log::info("RunPresetJob [{$batchId}]: Scheduled " . count($rows) . " commands for preset '{$preset->name}' on device '{$device->model}'. Timeline: {$cumulativeDelay}s.");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("RunPresetJob failed for device {$this->deviceId}, preset {$this->presetId}: " . $exception->getMessage());
    }
}
