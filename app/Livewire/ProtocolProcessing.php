<?php

namespace App\Livewire;

use App\Models\Protocol;
use App\Models\ProtocolProcess;
use App\Services\MqttService;
use Exception;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\Computed;

class ProtocolProcessing extends Component
{
    public $protocolId;
    public $protocolProcessUid;
    public string $deviceId = 'adc-001'; // Default device ID for protocol control

    public $data = [];
    public $phases = [];
    public $onGoingTime = 0;
    public $currentPhase = 1;
    public bool $protocolProcessing = false;
    public $startTime;
    public $totalTime;

    // Phase management form models
    public $label;
    public $duration = 1;
    public $loop = 1;

    public function mount($protocol, $uid)
    {
        // Handle if $protocol is passed as an object (Livewire implicit hydration)
        $this->protocolId = is_object($protocol) ? $protocol->id : (int) $protocol;
        $this->protocolProcessUid = $uid;

        $process = $this->protocolProcess; // Access computed property

        $this->phases = $this->protocol->phases ?? [];

        $mqttMessages = $process->getData();

        if ($mqttMessages->isNotEmpty()) {
            $this->startTime = $process->created_at;
            $this->data = $this->mapMqttMessages($mqttMessages);

            // Sync running state: if we have data but not ended, it's running
            if (!$process->ended_at) {
                $this->protocolProcessing = true;
            }
        }
    }

    #[Computed]
    public function protocolProcess()
    {
        return ProtocolProcess::query()
            ->firstOrCreate(
                ['uid' => $this->protocolProcessUid],
                [
                    'uid' => $this->protocolProcessUid,
                    'protocol_id' => $this->protocolId
                ]
            );
    }

    #[Computed]
    public function protocol()
    {
        return Protocol::findOrFail($this->protocolId);
    }

    public function toggleProtocolProcessing()
    {
        try {
            // First launch — set start time
            if (!$this->startTime) {
                $this->startTime = now();
                $this->totalTime = array_sum(array_map(
                    fn($p) => (int) ($p['duration'] ?? 0) * (int) ($p['loop'] ?? 1),
                    $this->phases
                ));
            }

            if (!$this->protocolProcessing) {
                // Flatten loops into explicit steps so device runs each step once
                $flattenedPhases = [];
                foreach ($this->phases as $phase) {
                    $loopCount = $phase['loop'] ?? 1;
                    for ($i = 1; $i <= $loopCount; $i++) {
                        $flattenedPhases[] = [
                            'id' => ($phase['id'] ?? '') . '_loop_' . $i,
                            'label' => $phase['label'],
                            'duration' => $phase['duration'],
                            'loop_index' => $i,
                            'total_loops' => $loopCount,
                        ];
                    }
                }

                // Starting
                $payload = [
                    'type' => 'protocol_start',
                    'protocol_process_id' => $this->protocolProcessUid,
                    'protocol_id' => $this->protocol->sample_id,
                    'protocol_data' => $this->protocol->value,
                    'phases' => $flattenedPhases,
                    'total_steps' => count($flattenedPhases),
                ];
                $command = 'start';
            } else {
                // Pausing
                $payload = [
                    'type' => 'protocol_stop',
                    'protocol_process_id' => $this->protocolProcessUid,
                ];
                $command = 'stop';
            }

            $mqttService = app(MqttService::class);
            $mqttService->deviceCommand($this->deviceId, 'protocol', $command, json_encode($payload));

            $this->protocolProcessing = !$this->protocolProcessing;
            $this->dispatch('protocolDataUpdated', $this->data);
        } catch (Exception $e) {
            logger()->error('Protocol toggle failed: ' . $e->getMessage());
            session()->flash('error', 'Failed to toggle protocol: ' . $e->getMessage());
        }
    }

    public function addPhase()
    {
        $this->phases[] = [
            'id' => Str::random(),
            'label' => $this->label,
            'duration' => $this->duration,
            'loop' => $this->loop,
        ];

        $this->label = '';
        $this->duration = 1;
        $this->loop = 1;
    }

    public function removePhase($id)
    {
        $this->phases = array_values(
            array_filter($this->phases, fn($phase) => $phase['id'] != $id)
        );
    }

    public function refreshProtocolProcessData()
    {
        $process = $this->protocolProcess;

        if (!$this->startTime || $process->ended_at) {
            return;
        }

        $mqttMessages = $process->getData();

        // Check for protocol_end
        if ($mqttMessages->last()?->message['type'] === 'protocol_end') {
            $process->ended_at = $mqttMessages->last()->message['timestamp'];
            $process->save();
            $this->protocolProcessing = false;
            return;
        }

        if ($mqttMessages->isEmpty()) {
            return;
        }

        $this->data = $this->mapMqttMessages($mqttMessages);
        $this->dispatch('protocolDataUpdated', $this->data);
    }

    public function render()
    {
        return view('livewire.protocol-processing', [
            'protocol' => $this->protocol,
            'protocolProcess' => $this->protocolProcess,
        ]);
    }

    /**
     * Map raw MQTT messages into a normalized array for the frontend chart.
     */
    private function mapMqttMessages($mqttMessages): array
    {
        return $mqttMessages->map(function ($msg) {
            $data = $msg->message;

            if ($this->startTime) {
                // Determine ongoing time safe from null start time (though checked in caller)
                $this->onGoingTime = $data['timestamp'] - ($this->startTime->timestamp ?? $data['timestamp']);
            }

            return [
                'type' => $data['type'],
                'label' => $data['label'] ?? null,
                'loop_index' => $data['loop_index'] ?? null,
                'temperature' => $data['temperature'] ?? null,
                'volume' => $data['volume'] ?? null,
                'timestamp' => $data['timestamp'],
            ];
        })->toArray();
    }
}
