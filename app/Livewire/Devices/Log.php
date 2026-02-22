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

    #[On('echo:mqtt.1,MqttMessageReceived')]
    public function checkForUpdate()
    {
        if (Cache::get('device_needs_refresh_' . $this->device->id)) {
            $this->refreshDevice();
            Cache::forget('device_needs_refresh_' . $this->device->id);
        }
    }

    public function refreshDevice()
    {
        // You can reload data or just:
        $this->emitSelf('$refresh');
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
