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
        $this->device->update(['is_active' => $value]);
        $mqttService->deviceCommand($this->model, 'device', 'status', $value ? '1' : '0');
        session()->flash('message', 'Device status updated and command sent!');
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
        $device = $this->device;
        $mqttService->ntpUpdate($device);

        $server = $device->ntp_server ?? config('app.url');
        $tz = $device->timezone ?? 'utc';
        $interval = $device->ntp_interval ?? 3600;

        session()->flash('message', "NTP sync command sent! (Server: {$server}, TZ: {$tz}, Interval: {$interval}s)");
    }

    public function initPump(MqttService $mqttService, $pumpIndex = 0)
    {
        $mqttService->pumpInit($this->model, (int) $pumpIndex);
        session()->flash('message', "Pump {$pumpIndex} init command sent!");
    }

    public function enableTec(MqttService $mqttService, $state)
    {
        $mqttService->tecEnable($this->model, (bool) $state);
        session()->flash('message', 'TEC ' . ($state ? 'enabled' : 'disabled') . ' command sent!');
    }

    public function stopStirrer(MqttService $mqttService)
    {
        $mqttService->stirrerStop($this->model);
        session()->flash('message', 'Stirrer stop command sent!');
    }

    public function sendManualCommand(MqttService $mqttService)
    {
        $this->validate([
            'manual_component' => 'required',
            'manual_action' => 'required',
        ]);

        $mqttService->deviceCommand(
            $this->model,
            $this->manual_component,
            $this->manual_action,
            $this->manual_payload ?? ''
        );

        session()->flash('message', "Manual command '{$this->manual_component}/{$this->manual_action}' sent!");
    }

    public function render()
    {
        return view('livewire.devices.setting', [
            'device' => $this->device
        ])->layout('components.layouts.device-detail.index');
    }
}
