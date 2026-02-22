<?php

namespace App\Livewire;

use Exception;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class Setting extends Component
{
    use WithFileUploads;

    public $logo;
    public $logoPath;

    #[Validate('required|string|max:64')]
    public $appName;

    #[Validate('required|string|max:1024')]
    public $appDescription;

    public $setting;

    public function mount()
    {
        $this->setting = setting('general');
        $this->appName = $this->setting->appName ?? '';
        $this->appDescription = $this->setting->appDescription ?? '';
        $this->logoPath = $this->setting->logoPath ?? null;
    }

    public function updateAppSetting()
    {
        $this->validate();

        try {
            $data = [
                'appName' => $this->appName,
                'appDescription' => $this->appDescription,
                'logoPath' => $this->logoPath,
            ];

            if ($this->logo) {
                // Delete old logo if exists
                if ($this->logoPath && Storage::disk('public')->exists($this->logoPath)) {
                    Storage::disk('public')->delete($this->logoPath);
                }

                // Generate a unique filename to avoid browser caching issues
                $filename = 'logo_' . time() . '.' . $this->logo->getClientOriginalExtension();
                $path = $this->logo->storeAs('logo', $filename, 'public');
                $data['logoPath'] = $path;
                $this->logoPath = $path; // Update local state
            }

            saveSetting('general', $data);

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
