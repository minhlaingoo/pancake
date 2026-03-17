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
    public $availableMicrovalves = [];
    public $controllerCommands = [
        'tec' => [
            'setpoint' => 'float',
            'enable' => 'int',
        ],
        'stirrer' => [
            'speed' => 'int',
            'stop' => 'none',
        ],
        'microvalve' => [
            'open' => 'microvalve_select',
            'close' => 'microvalve_select',
        ],
        'pump_0' => [
            'init' => 'none',
            'aspirate' => 'float',
            'dispense' => 'float',
            'home' => 'none',
            'stop' => 'none',
        ],
        'pump_1' => [
            'init' => 'none',
            'aspirate' => 'float',
            'dispense' => 'float',
            'home' => 'none',
            'stop' => 'none',
        ],
        'rotary_valve_1' => [
            'init' => 'none',
            'position' => 'int',
            'home' => 'none',
            'stop' => 'none',
        ],
        'rotary_valve_2' => [
            'init' => 'none',
            'position' => 'int',
            'home' => 'none',
            'stop' => 'none',
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

    public function actionChanged($index, $action)
    {
        logger("actionChanged method called: index={$index}, action={$action}");
        $this->updateTestCommandType($index, $action);
        $this->dispatch('$refresh');
    }

    public function updatedMicrovalveCount()
    {
        $this->updateAvailableMicrovalves();
    }

    public function updatedMicrovalveStart()
    {
        $this->updateAvailableMicrovalves();
    }

    protected function updateAvailableMicrovalves()
    {
        $count = max(1, min(16, (int) $this->microvalveCount)); // Ensure valid range
        $start = max(0, min(15, (int) $this->microvalveStart)); // Ensure valid range
        
        $this->availableMicrovalves = range($start, $start + $count - 1);
        
        // Make sure we don't exceed 15 (max microvalve index)
        $this->availableMicrovalves = array_filter($this->availableMicrovalves, fn($v) => $v <= 15);
    }

    public function saveConfiguration()
    {
        // Update device configuration
        $this->device->setConfig('microvalves.count', (int) $this->microvalveCount);
        $this->device->setConfig('microvalves.start', (int) $this->microvalveStart);
        $this->device->setConfig('microvalves.description', "Microvalves {$this->microvalveStart}-" . ($this->microvalveStart + $this->microvalveCount - 1) . " are present");
        
        $this->device->setConfig('pumps.count', (int) $this->pumpCount);
        $this->device->setConfig('rotary_valves.count', (int) $this->rotaryValveCount);
        
        // Update available microvalves
        $this->updateAvailableMicrovalves();
        
        session()->flash('message', 'Device configuration saved successfully.');
    }

    protected function rules()
    {
        $rules = [];
        
        foreach ($this->testCommands as $index => $command) {
            $type = $command['type'] ?? 'string';
            
            switch ($type) {
                case 'int':
                    $rules["testCommands.{$index}.value"] = 'nullable|integer|min:0';
                    break;
                case 'float':
                    $rules["testCommands.{$index}.value"] = 'nullable|numeric|min:0';
                    break;
                case 'microvalve_select':
                    $availableList = implode(',', $this->availableMicrovalves);
                    $rules["testCommands.{$index}.value"] = "nullable|integer|in:{$availableList}";
                    break;
                case 'valve_select':
                    $rules["testCommands.{$index}.value"] = 'nullable|integer|between:1,16';
                    break;
                case 'none':
                    $rules["testCommands.{$index}.value"] = 'nullable'; // No validation needed
                    break;
                default:
                    $rules["testCommands.{$index}.value"] = 'nullable|string|max:255';
            }
        }
        
        return $rules;
    }

    protected function messages()
    {
        $availableMicrovalves = implode(', ', $this->availableMicrovalves);
        
        return [
            'testCommands.*.value.integer' => 'Value must be a whole number.',
            'testCommands.*.value.numeric' => 'Value must be a number.',
            'testCommands.*.value.min' => 'Value must be greater than or equal to 0.',
            'testCommands.*.value.between' => 'Value must be between :min and :max.',
            'testCommands.*.value.max' => 'Value cannot exceed 255 characters.',
            'testCommands.*.value.in' => "Microvalve must be one of the available ones: {$availableMicrovalves}.",
        ];
    }

    public function updated($property)
    {
        // Validate in real-time when testCommand values change
        if (str_starts_with($property, 'testCommands.') && str_ends_with($property, '.value')) {
            $this->validateOnly($property);
        }
    }

    public function removeTestCommand($index)
    {
        unset($this->testCommands[$index]);
        $this->testCommands = array_values($this->testCommands);
    }

    public function updatedTestCommandsAction($value, $index)
    {
        logger("updatedTestCommandsAction called: index={$index}, value={$value}");
        $this->updateTestCommandType($index, $value);
    }

    public function updatedTestCommandsController($value, $index)
    {
        logger("updatedTestCommandsController called: index={$index}, value={$value}");
        $this->testCommands[$index]['action'] = '';
        $this->testCommands[$index]['type'] = 'string';
        $this->testCommands[$index]['value'] = '';
    }

    public function updateTestCommandType($index, $action)
    {
        $controller = $this->testCommands[$index]['controller'] ?? '';
        logger("updateTestCommandType: Controller={$controller}, Action={$action}");
        
        if (isset($this->controllerCommands[$controller][$action])) {
            $newType = $this->controllerCommands[$controller][$action];
            $this->testCommands[$index]['type'] = $newType;
            $this->testCommands[$index]['value'] = '';
            
            logger("✅ Type updated: NewType={$newType}");
            logger("Updated command: " . json_encode($this->testCommands[$index]));
        } else {
            logger("❌ Action '{$action}' not found for controller '{$controller}'");
            logger("Available actions: " . json_encode($this->controllerCommands[$controller] ?? []));
        }
    }

    public function updatedTestCommands($value, $key)
    {
        // Log all updates
        logger("updatedTestCommands called: key={$key}, value={$value}");
        
        // Handle different key formats:
        // Format 1: "testCommands.0.action" (expected)
        // Format 2: "0.action" (what we're getting)
        $parts = explode('.', $key);
        
        if (count($parts) === 2) {
            // Format: "0.action" - add the missing prefix
            $index = $parts[0];
            $property = $parts[1];
            logger("Detected short format: index={$index}, property={$property}");
        } elseif (count($parts) === 3) {
            // Format: "testCommands.0.action" - standard format
            $index = $parts[1];
            $property = $parts[2];
            logger("Detected standard format: index={$index}, property={$property}");
        } else {
            logger("Invalid key format: {$key}");
            return;
        }

        if ($property === 'controller') {
            $this->testCommands[$index]['action'] = '';
            $this->testCommands[$index]['type'] = 'string';
            $this->testCommands[$index]['value'] = '';
            logger("Controller changed, reset action/type/value");
        }

        if ($property === 'action') {
            $controller = $this->testCommands[$index]['controller'];
            $action = $value;
            logger("Action change: Controller={$controller}, Action={$action}");
            
            if (isset($this->controllerCommands[$controller][$action])) {
                $newType = $this->controllerCommands[$controller][$action];
                $this->testCommands[$index]['type'] = $newType;
                // Clear the value when action changes to avoid conflicts
                $this->testCommands[$index]['value'] = '';
                
                logger("✅ Action updated: NewType={$newType}");
                logger("Current testCommand after update: " . json_encode($this->testCommands[$index]));
                
                // Force re-render
                $this->dispatch('$refresh');
            } else {
                logger("❌ Action not found in controllerCommands");
                logger("Available actions for {$controller}: " . json_encode($this->controllerCommands[$controller] ?? 'N/A'));
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
            // Allow empty value for none types (init, home, stop) and string types
            if (!in_array($command['type'], ['string', 'none'])) {
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

        try {
            $mqttService->deviceCommand($this->device->model, $controller, $action, $payload);
            session()->flash('message', 'Command sent: ' . strtoupper($controller) . '/' . $action);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send command: ' . $e->getMessage());
        }
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
        
        // Get available microvalves from device configuration
        $this->availableMicrovalves = $this->device->getAvailableMicrovalves();

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
                'action' => 'stop',
                'value' => '',
                'type' => 'none'
            ],
            [
                'controller' => 'microvalve',
                'action' => 'open',
                'value' => '0',
                'type' => 'microvalve_select'
            ],
            [
                'controller' => 'pump_0',
                'action' => 'init',
                'value' => '',
                'type' => 'none'
            ],
            [
                'controller' => 'pump_1',
                'action' => 'home',
                'value' => '',
                'type' => 'none'
            ],
            [
                'controller' => 'rotary_valve_1',
                'action' => 'init',
                'value' => '',
                'type' => 'none'
            ],
            [
                'controller' => 'rotary_valve_2',
                'action' => 'home',
                'value' => '',
                'type' => 'none'
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
