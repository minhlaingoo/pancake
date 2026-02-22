<?php

namespace App\Livewire;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $totalUser, $totalRole, $selectedDevice, $selectedChamber;

    public function mount()
    {
        $this->totalUser = User::count();
        $this->totalRole = Role::count();
    }
    public function render()
    {
        return view('livewire.dashboard');
    }
}
