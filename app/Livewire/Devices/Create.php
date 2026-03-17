<?php

namespace App\Livewire\Devices;

use App\Models\Device;
use Livewire\Component;

class Create extends Component
{
    public $name;
    public $model;
    public $ip;
    public $port;

    public function rules()
    {
        return [
            'name' => 'required',
            'model' => 'required',
            'ip' => 'required|ipv4',
            'port' => 'required|integer|min:1|max:65535',
        ];
    }

    public function store()
    {
        $data = $this->validate();

        // Optional connection check (prepared but not used for now)
        // $deviceName = $this->checkConnection($this->ip, $this->port);
        // if ($deviceName) {
        //     $this->model = $deviceName;
        // }

        // Create device with default configuration (microvalves 0-5)
        $device = Device::create($data);
        $device->configuration = Device::getDefaultConfiguration();
        $device->save();
        
        $this->reset();
        session()->flash('message', "Device created successfully with default microvalve configuration (0-5)!");
        return to_route('devices.index');
    }

    private function checkConnection($ip, $port)
    {
        $timeout = 2; // seconds
        $connection = @fsockopen($ip, $port, $errno, $errstr, $timeout);

        if (is_resource($connection)) {
            fclose($connection);
            return "Detected Model Name"; // Placeholder as requested
        }

        return null;
    }

    public function render()
    {
        return view('livewire.devices.create');
    }
}
