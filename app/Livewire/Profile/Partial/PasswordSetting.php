<?php

namespace App\Livewire\Profile\Partial;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PasswordSetting extends Component
{

    public $old_password, $password, $password_confirmation;

    public function rules()
    {
        return [
            'password' => 'required|confirmed'
        ];
    }

    public function updatePassword()
    {
        $this->validate();
        $user = Auth::user();
        if(!password_verify($this->old_password, $user->password)){
            $this->addError('old_password', 'Password is incorrect');
            return;
        }
        $user->password = bcrypt($this->password);
        $user->save();
        $this->reset();
        session()->flash('message', 'Password updated successfully');
    }   

    public function render()
    {
        return view('livewire.profile.partial.password-setting');
    }
}
