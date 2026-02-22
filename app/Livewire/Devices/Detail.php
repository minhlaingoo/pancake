<?php

namespace App\Livewire\Devices;

use App\Models\Device;
use App\Models\DeviceComponent;
use Livewire\Component as LivewireComponent;

class Detail extends LivewireComponent
{
    public $device;
    public $testCommands = [];
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
            'set' => 'string',
            'open' => 'int',
            'close' => 'int',
        ],
        'pump' => [
            'init' => 'string',
            'aspirate' => 'float',
            'dispense' => 'float',
            'home' => 'string',
        ],
    ];

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

    public function sendTestCommands(\App\Services\MqttService $mqttService)
    {
        if (empty($this->testCommands)) {
            session()->flash('error', 'No commands to send.');
            return;
        }

        foreach ($this->testCommands as $command) {
            $controller = $command['controller'];
            $action = $command['action'];
            $value = $command['value'];
            $type = $command['type'];

            if (!$controller || !$action)
                continue;

            // Format payload based on type
            $payload = (string) $value;
            if ($type === 'bool' || is_bool($value)) {
                $payload = $value ? '1' : '0';
            }

            $mqttService->deviceCommand($this->device->model, $controller, $action, $payload);
        }

        session()->flash('message', 'Test commands sent successfully.');
    }

    public function mount($id)
    {
        $this->device = Device::query()->findOrFail($id);
    }

    public function render()
    {
        return view(
            'livewire.devices.detail',
            [
                'deviceComponents' => $this->device->deviceComponents
            ]
        )->layout('components.layouts.device-detail.index');
    }
}
