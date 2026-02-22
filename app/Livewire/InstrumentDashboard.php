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
        if ($device) {
            $mqtt->tecSetSetpoint($device->topic ?? $device->id, $this->setpoint);
            $this->dispatch('notify', ['message' => 'TEC Setpoint sent!', 'type' => 'info']);
        }
    }

    public function stirrerStart(MqttService $mqtt)
    {
        $device = $this->activeDevice;
        if ($device) {
            $mqtt->stirrerSetSpeed($device->topic ?? $device->id, $this->stirrerSpeed);
            $this->dispatch('notify', ['message' => 'Stirrer speed sent!', 'type' => 'info']);
        }
    }

    public function stirrerStop(MqttService $mqtt)
    {
        $device = $this->activeDevice;
        if ($device) {
            $mqtt->stirrerStop($device->topic ?? $device->id);
            $this->dispatch('notify', ['message' => 'Stirrer stop sent!', 'type' => 'warning']);
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
