<?php

namespace App\Livewire\DeviceComponents;

use App\Models\DeviceComponent;
use Livewire\Component as LivewireComponent;

class Edit extends LivewireComponent
{
    public $name;
    public $type;
    public $unit;
    public DeviceComponent $deviceComponent;

    public function rules()
    {
        return [
            'name' => 'required',
            'type' => 'required',
            'unit' => 'required'
        ];
    }

    public function mount(DeviceComponent $deviceComponent)
    {
        $this->deviceComponent = $deviceComponent;
        $this->name = $deviceComponent->name;
        $this->type = $deviceComponent->type;
        $this->unit = $deviceComponent->unit;
    }

    public function update()
    {
        $data = $this->validate();
        $this->deviceComponent->fill($data);
        $this->deviceComponent->save();
        session()->flash('message', 'Component updated successfully.');
        return to_route('devices.detail', ['id' => $this->deviceComponent->device_id]);
    }

    public function render()
    {
        return view('livewire.device-components.edit')->layout('components.layouts.device-detail.index');
    }
}
