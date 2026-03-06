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

class RunPresetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deviceId;
    protected $presetId;

    /**
     * Create a new job instance.
     */
    public function __construct($deviceId, $presetId)
    {
        $this->deviceId = $deviceId;
        $this->presetId = $presetId;
    }

    /**
     * Execute the job.
     */
    public function handle(MqttService $mqttService): void
    {
        $device = Device::find($this->deviceId);
        $preset = Preset::find($this->presetId);

        if (!$device || !$preset) {
            return;
        }

        foreach ($preset->commands as $command) {
            $controller = $command['controller'] ?? '';
            $action = $command['action'] ?? '';
            $value = $command['value'] ?? '';
            $type = $command['type'] ?? 'string';
            $delay = $command['delay'] ?? 0;

            if (empty($controller) || empty($action)) {
                continue;
            }

            // Format payload based on type
            $payload = (string) $value;
            if ($type === 'bool' || is_bool($value)) {
                $payload = $value ? '1' : '0';
            }

            // Publish message (MqttService now broadcasts TX messages automatically)
            $mqttService->deviceCommand($device->model, $controller, $action, $payload);

            // Respect delay between commands
            if ($delay > 0) {
                sleep((int) $delay);
            }
        }
    }
}
