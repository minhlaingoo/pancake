<?php

namespace App\Livewire\Profile\Partial;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class UserSetting extends Component
{

    #[Validate('required')]
    public $name;

    public function mount(){
        $this->name = Auth::user()->name;
    }

    public function updateName(){
        $this->validate();
        $user = Auth::user();
        $user->name = $this->name;
        $user->save();
        session()->flash('message', 'Name updated successfully');
    }
    
    public function render()
    {
        return view('livewire.profile.partial.user-setting', [
            'user' => Auth::user()
        ]);
    }
}
