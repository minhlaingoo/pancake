<?php

namespace App\Livewire\DeviceComponents;

use App\Models\DeviceComponentLog;
use Livewire\Component as LivewireComponent;

class Log extends LivewireComponent
{
    public function checkForUpdate()
    {
        // This method is called by wire:poll in the view
        // It provides a hook for future live update logic (e.g. Echo/Websockets)
        // For now, it just triggers a re-render.
    }

    public function render()
    {
        return view('livewire.device-components.log', [
            'logs' => DeviceComponentLog::with(['deviceComponent.device'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        ]);
    }
}
