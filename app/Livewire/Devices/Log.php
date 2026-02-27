<?php

namespace App\Livewire\Devices;

use App\Models\Device;
use App\Models\DeviceComponent;
use App\Models\DeviceComponentLog;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component as LivewireComponent;

class Log extends LivewireComponent
{
    public Device $device;



    public function mount($id)
    {
        $this->device = Device::query()->findOrFail($id);
    }

    #[On('echo:device.{device.id},TelemetryUpdated')]
    public function refreshDevice()
    {
        // Livewire refreshes automatically on event, but we can be explicit
        $this->dispatch('$refresh');
    }

    public function render()
    {
        return view(
            'livewire.devices.log',
            [
                'logs' => DeviceComponentLog::where('device_id', $this->device->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get()
            ]
        )->layout('components.layouts.device-detail.index');
    }
}
