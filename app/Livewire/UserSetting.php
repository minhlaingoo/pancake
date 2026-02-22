<?php

namespace App\Livewire;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;

class UserSetting extends Component
{
    #[Validate('required|string|max:255')]
    public $name;

    public $password;
    public $password_confirmation;

    public function mount()
    {
        $this->name = Auth::user()->name;
    }

    public function updateUserSetting()
    {
        $this->validate();

        if ($this->password) {
            $this->validate([
                'password' => 'required|min:8|confirmed',
            ]);
        }

        try {
            $user = Auth::user();
            $user->name = $this->name;

            if ($this->password) {
                $user->password = Hash::make($this->password);
            }

            $user->save();

            session()->flash('message', "User settings updated successfully.");

            if ($this->password) {
                $this->reset(['password', 'password_confirmation']);
            }
        } catch (Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user-setting');
    }
}
