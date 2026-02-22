<?php

namespace App\Livewire;

use Exception;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Setting extends Component
{
    use WithFileUploads;

    #[Validate('nullable|file|mimes:jpg,png,webp,svg|max:1024')]
    public $logo;

    #[Validate('required|string|max:64')]
    public $appName;
    
    #[Validate('required|string|max:1024')]
    public $appDescription;

    public $setting;

    public function mount()
    {
        $this->setting = setting('general');
        $this->appName = $this->setting->appName;
        $this->appDescription = $this->setting->appDescription;
    }

    public function updateAppSetting()
    {

        $this->validate();
        try {
            saveSetting('general', [
                'appName' => $this->appName,
                'appDescription' => $this->appDescription,
            ]);

            if($this->logo){
                $this->logo->storeAs('logo', 'logo.png', 'public');
            }
            session()->flash('message', "App Setting Updated Successful");
            $this->dispatch('refresh-page');
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
            return;
        }
    }

    public function render()
    {
        return view('livewire.setting');
    }
}
