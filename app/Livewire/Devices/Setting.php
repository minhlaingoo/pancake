<?php

namespace App\Livewire\Devices;

use App\Models\Device;
use App\Services\MqttService;
use App\Services\DeviceService;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Setting extends Component
{
    public int $deviceId;

    // Form fields
    public $name;
    public $model;
    public $ip;
    public $port;
    public bool $is_active;
    
    // Microvalve configuration
    public $availableMicrovalves = [];
    public $microvalveCount = 6;
    public $microvalveStart = 0;

    public $ntp_server;
    public $timezone;
    public $ntp_interval;

    // Manual command fields
    public $manual_component;
    public $manual_action;
    public $manual_payload;

    public function rules()
    {
        return [
            'name' => 'required',
            'model' => 'required',
            'ip' => 'required|ipv4',
            'port' => 'required|integer|min:1|max:65535',
            'is_active' => 'required',
            'ntp_server' => 'nullable|string',
            'timezone' => 'nullable|string',
            'ntp_interval' => 'nullable|integer|min:60',
            'manual_component' => 'nullable|string',
            'manual_action' => 'nullable|string',
            'manual_payload' => 'nullable|string',
        ];
    }

    public function mount($id)
    {
        $this->deviceId = $id;
        $device = $this->device; // Access computed property

        $this->name = $device->name;
        $this->model = $device->model;
        $this->ip = $device->ip;
        $this->port = $device->port;
        $this->is_active = (bool) $device->is_active;
        $this->ntp_server = $device->ntp_server;
        $this->timezone = $device->timezone ?? 'Asia/Ho_Chi_Minh';
        $this->ntp_interval = $device->ntp_interval ?? 3600;
        
        // Initialize microvalve settings with clear defaults (0-5)
        $this->microvalveCount = $device->getConfig('microvalves.count', 6);  // Default: 6 microvalves
        $this->microvalveStart = $device->getConfig('microvalves.start', 0);  // Default: start from 0
        $this->updateAvailableMicrovalves();
        
        // Ensure device has default configuration if empty
        if (empty($device->configuration)) {
            $device->configuration = Device::getDefaultConfiguration();
            $device->save();
        }
    }

    #[Computed]
    public function device()
    {
        return Device::findOrFail($this->deviceId);
    }

    public function update()
    {
        $data = $this->validate();
        $this->device->update($data);
        session()->flash('message', "Setting updated successfully!");
        return to_route('devices.setting', ['id' => $this->deviceId]);
    }

    public function updatedIsActive($value, MqttService $mqttService)
    {
        try {
            $this->device->update(['is_active' => $value]);
            $mqttService->deviceCommand($this->model, 'device', 'status', $value ? '1' : '0');
            session()->flash('message', 'Device status updated and command sent!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update device status: ' . $e->getMessage());
        }
    }

    public function updatedIp()
    {
        $this->checkAndSetModel();
    }

    public function updatedPort()
    {
        $this->checkAndSetModel();
    }

    private function checkAndSetModel()
    {
        if (empty($this->ip) || empty($this->port)) {
            return;
        }

        $deviceService = app(DeviceService::class);
        $result = $deviceService->checkConnection($this->ip, (int) $this->port);

        if ($result) {
            $this->model = $result['model'];
            $this->name = $result['name'];

            // Auto update model and name in database if successful
            $this->device->update([
                'model' => $this->model,
                'name' => $this->name,
                'ip' => $this->ip,
                'port' => $this->port,
            ]);

            session()->flash('message', "Connection successful! Model updated to: {$this->model}");
        } else {
            session()->flash('error', "Connection failed to {$this->ip}:{$this->port}");
        }
    }

    // ─── Device-Scoped MQTT Actions ─────────────────────────────

    public function syncNtp(MqttService $mqttService)
    {
        try {
            $device = $this->device;
            $mqttService->ntpUpdate($device);

            $server = $device->ntp_server ?? config('app.url');
            $tz = $device->timezone ?? 'utc';
            $interval = $device->ntp_interval ?? 3600;

            session()->flash('message', "NTP sync command sent! (Server: {$server}, TZ: {$tz}, Interval: {$interval}s)");
        } catch (\Exception $e) {
            session()->flash('error', 'NTP sync failed: ' . $e->getMessage());
        }
    }

    public function initPump(MqttService $mqttService, $pumpIndex = 0)
    {
        try {
            $mqttService->pumpInit($this->model, (int) $pumpIndex);
            session()->flash('message', "Pump {$pumpIndex} init command sent!");
        } catch (\Exception $e) {
            session()->flash('error', 'Pump init failed: ' . $e->getMessage());
        }
    }

    public function enableTec(MqttService $mqttService, $state)
    {
        try {
            $mqttService->tecEnable($this->model, (bool) $state);
            session()->flash('message', 'TEC ' . ($state ? 'enabled' : 'disabled') . ' command sent!');
        } catch (\Exception $e) {
            session()->flash('error', 'TEC command failed: ' . $e->getMessage());
        }
    }

    public function stopStirrer(MqttService $mqttService)
    {
        try {
            $mqttService->stirrerStop($this->model);
            session()->flash('message', 'Stirrer stop command sent!');
        } catch (\Exception $e) {
            session()->flash('error', 'Stirrer stop failed: ' . $e->getMessage());
        }
    }

    public function sendManualCommand(MqttService $mqttService)
    {
        $this->validate([
            'manual_component' => 'required|string|max:64',
            'manual_action' => 'required|string|max:64',
            'manual_payload' => 'nullable|string|max:1024',
        ]);

        try {
            $mqttService->deviceCommand(
                $this->model,
                $this->manual_component,
                $this->manual_action,
                $this->manual_payload ?? ''
            );

            session()->flash('message', "Manual command '{$this->manual_component}/{$this->manual_action}' sent!");
        } catch (\Exception $e) {
            session()->flash('error', 'Manual command failed: ' . $e->getMessage());
        }
    }

    // Microvalve configuration methods
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

    public function saveMicrovalveConfiguration()
    {
        // Validate inputs
        $this->validate([
            'microvalveCount' => 'required|integer|min:1|max:16',
            'microvalveStart' => 'required|integer|min:0|max:15',
        ]);

        // Check if the range doesn't exceed maximum index
        if ($this->microvalveStart + $this->microvalveCount - 1 > 15) {
            session()->flash('error', 'Microvalve range cannot exceed index 15. Please adjust count or start index.');
            return;
        }

        // Update device configuration - only microvalves
        $this->device->setConfig('microvalves.count', (int) $this->microvalveCount);
        $this->device->setConfig('microvalves.start', (int) $this->microvalveStart);
        $this->device->setConfig('microvalves.description', "Microvalves {$this->microvalveStart}-" . ($this->microvalveStart + $this->microvalveCount - 1) . " are present");
        
        // Update available microvalves
        $this->updateAvailableMicrovalves();
        
        session()->flash('message', 'Microvalve configuration saved successfully.');
    }

    public function resetMicrovalvesToDefault()
    {
        $this->microvalveCount = 6;  // Default: 6 microvalves (0-5)
        $this->microvalveStart = 0;  // Default: start from 0
        $this->updateAvailableMicrovalves();
        
        session()->flash('message', 'Microvalve configuration reset to default (Microvalves 0-5).');
    }

    public function render()
    {
        return view('livewire.devices.setting', [
            'device' => $this->device
        ])->layout('components.layouts.device-detail.index');
    }
}
