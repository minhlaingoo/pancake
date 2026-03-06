<?php

namespace App\Livewire\Devices;

use App\Models\Device;
use Livewire\Component as LivewireComponent;
use Illuminate\Support\Str;

class Detail extends LivewireComponent
{
    public $device;
    public $testCommands = [];
    public $activeTab = 'info';
    public $logs = [];
    public $telemetryData = [];
    public $selectedPresetId;
    public $controllerCommands = [
        'tec' => [
            'setpoint' => 'float',
            'enable' => 'int',
        ],
        'stirrer' => [
            'speed' => 'int',
            'stop' => 'string',
        ],
        'microvalve' => [
            'open' => 'valve_select',
            'close' => 'valve_select',
        ],
        'pump' => [
            'init' => 'string',
            'aspirate' => 'float',
            'dispense' => 'float',
            'home' => 'string',
        ],
        'rotary_valve' => [
            'init' => 'string',
            'position' => 'int',
            'home' => 'string',
        ],
    ];

    public function getListeners()
    {
        return [
            "echo:mqtt.1,MqttMessageReceived" => 'onMqttMessage',
            "echo:device.{$this->device->id},TelemetryUpdated" => 'onTelemetryUpdated',
        ];
    }

    public function onMqttMessage($event)
    {
        // Filter by device model
        $topic = $event['topic'] ?? '';
        if (!Str::contains($topic, "/{$this->device->model}/")) {
            return;
        }

        array_unshift($this->logs, [
            'time' => now()->format('H:i:s'),
            'topic' => $event['topic'],
            'message' => $event['message'],
            'type' => Str::contains($topic, '/command/') ? 'outgoing' : 'incoming'
        ]);

        // Keep only last 50 logs
        if (count($this->logs) > 50) {
            array_pop($this->logs);
        }
    }

    public function onTelemetryUpdated($event)
    {
        $id = $event['deviceComponentId'];
        if (isset($this->telemetryData[$id])) {
            $this->telemetryData[$id]['value'] = $event['value'];
            $this->telemetryData[$id]['status'] = $event['status'];
            $this->telemetryData[$id]['last_seen'] = now()->timestamp; // For sorting
            $this->telemetryData[$id]['updated_at'] = now()->format('H:i:s');
        }
    }

    public function clearLogs()
    {
        $this->logs = [];
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function addTestCommand()
    {
        $this->testCommands[] = [
            'controller' => '',
            'action' => '',
            'value' => '',
            'type' => 'string'
        ];
    }

    public function removeTestCommand($index)
    {
        unset($this->testCommands[$index]);
        $this->testCommands = array_values($this->testCommands);
    }

    public function updatedTestCommands($value, $key)
    {
        // testCommands.0.controller
        $parts = explode('.', $key);
        if (count($parts) < 3)
            return;

        $index = $parts[1];
        $property = $parts[2];

        if ($property === 'controller') {
            $this->testCommands[$index]['action'] = '';
            $this->testCommands[$index]['type'] = 'string';
            $this->testCommands[$index]['value'] = '';
        }

        if ($property === 'action') {
            $controller = $this->testCommands[$index]['controller'];
            $action = $value;
            if (isset($this->controllerCommands[$controller][$action])) {
                $this->testCommands[$index]['type'] = $this->controllerCommands[$controller][$action];
            }
        }
    }

    public function sendSingleTestCommand($index, \App\Services\MqttService $mqttService)
    {
        if (!isset($this->testCommands[$index])) {
            session()->flash('error', 'Command not found.');
            return;
        }

        $command = $this->testCommands[$index];

        if (empty($command['controller']) || empty($command['action'])) {
            session()->flash('error', 'Please select a controller and action.');
            return;
        }

        if ($command['value'] === '' || $command['value'] === null) {
            // Allow empty value for string types (init, home, stop)
            if (!in_array($command['type'], ['string'])) {
                session()->flash('error', 'Please enter a value.');
                return;
            }
        }

        $controller = $command['controller'];
        $action = $command['action'];
        $value = $command['value'];
        $type = $command['type'];

        // Format payload based on type
        $payload = (string) $value;
        if ($type === 'bool' || is_bool($value)) {
            $payload = $value ? '1' : '0';
        }

        $mqttService->deviceCommand($this->device->model, $controller, $action, $payload);

        session()->flash('message', 'Command sent: ' . strtoupper($controller) . '/' . $action);
    }

    public function sendPreset()
    {
        if (!$this->selectedPresetId) {
            session()->flash('error', 'Please select a preset.');
            return;
        }

        // Dispatch background job for sequential execution with delays
        \App\Jobs\RunPresetJob::dispatch($this->device->id, $this->selectedPresetId);

        session()->flash('message', "Preset execution started in background.");
        $this->selectedPresetId = null;
    }

    public function mount($id)
    {
        $this->device = Device::query()->with('deviceComponents')->findOrFail($id);

        // Initialize telemetry data
        foreach ($this->device->deviceComponents as $component) {
            $this->telemetryData[$component->id] = [
                'name' => $component->name,
                'type' => $component->type,
                'unit' => $component->unit,
                'value' => $component->last_value ?? '--',
                'status' => $component->status ?? 'offline',
                'last_seen' => $component->updated_at->timestamp,
                'updated_at' => $component->updated_at->format('H:i:s'),
            ];
        }

        // Add default test commands
        $this->testCommands = [
            [
                'controller' => 'tec',
                'action' => 'setpoint',
                'value' => '',
                'type' => 'float'
            ],
            [
                'controller' => 'stirrer',
                'action' => 'speed',
                'value' => '',
                'type' => 'int'
            ],
            [
                'controller' => 'microvalve',
                'action' => 'open',
                'value' => '',
                'type' => 'valve_select'
            ],
            [
                'controller' => 'pump',
                'action' => 'init',
                'value' => '',
                'type' => 'string'
            ],
            [
                'controller' => 'rotary_valve',
                'action' => 'init',
                'value' => '',
                'type' => 'string'
            ],
        ];
    }

    public function render()
    {
        $sortedTelemetry = collect($this->telemetryData)
            ->sortByDesc('last_seen')
            ->toArray();

        return view(
            'livewire.devices.detail',
            [
                'presets' => \App\Models\Preset::all(),
                'sortedTelemetry' => $sortedTelemetry
            ]
        )->layout('components.layouts.device-detail.index');
    }
}
