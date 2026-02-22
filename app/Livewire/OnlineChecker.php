<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OnlineChecker extends Component
{
    public function checkOnline()
    {
        
        $session = DB::table('sessions')
        ->where('id', request()->session()->getId())
        ->first();
        $lastSeen = Carbon::createFromTimestamp($session->last_activity);
        $timeDiff = $lastSeen->diffInMinutes(now());
        if($timeDiff > 10){
            Auth::logout();  
            session()->invalidate();
            session()->regenerateToken();
            return redirect()->route('login')->with('message', 'You have been logged out due to inactivity.');
        }
    }
    public function render()
    {
        return view('livewire.online-checker');
    }
}
