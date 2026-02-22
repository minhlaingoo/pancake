<?php

namespace App\Livewire\Devices;

use App\Models\Device;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view(
            'livewire.devices.index',
            [
                'devices' => Device::all()
            ]
        );
    }
}
