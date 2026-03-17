<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Device;
use App\Services\MqttService;

class InstrumentDashboard extends Component
{
    public $deviceId;
    public $setpoint = 37.0;
    public $stirrerSpeed = 500;

    public function mount($deviceId = null)
    {
        $this->deviceId = $deviceId ?? Device::first()?->id;
    }

    #[Computed]
    public function devices()
    {
        // Eager load components for the dashboard status cards
        return Device::with('deviceComponents')->get();
    }

    #[Computed]
    public function activeDevice()
    {
        if (!$this->deviceId) {
            return null;
        }

        return Device::with('deviceComponents')->find($this->deviceId);
    }

    public function tecSetSetpoint(MqttService $mqtt)
    {
        $device = $this->activeDevice;
        if (!$device) return;

        try {
            $mqtt->tecSetSetpoint($device->model, $this->setpoint);
            $this->dispatch('notify', ['message' => 'TEC Setpoint sent!', 'type' => 'info']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Failed to send TEC setpoint: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function stirrerStart(MqttService $mqtt)
    {
        $device = $this->activeDevice;
        if (!$device) return;

        try {
            $mqtt->stirrerSetSpeed($device->model, $this->stirrerSpeed);
            $this->dispatch('notify', ['message' => 'Stirrer speed sent!', 'type' => 'info']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Failed to send stirrer speed: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function stirrerStop(MqttService $mqtt)
    {
        $device = $this->activeDevice;
        if (!$device) return;

        try {
            $mqtt->stirrerStop($device->model);
            $this->dispatch('notify', ['message' => 'Stirrer stop sent!', 'type' => 'warning']);
        } catch (\Exception $e) {
            $this->dispatch('notify', ['message' => 'Failed to stop stirrer: ' . $e->getMessage(), 'type' => 'error']);
        }
    }

    public function render()
    {
        return view('livewire.instrument-dashboard', [
            'devices' => $this->devices,
            'activeDevice' => $this->activeDevice,
        ]);
    }
}
