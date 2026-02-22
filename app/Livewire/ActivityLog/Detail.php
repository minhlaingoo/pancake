<?php

namespace App\Livewire\ActivityLog;

use App\Http\Resources\ActivityLog\LogResource;
use App\Models\ActivityLog;
use Livewire\Component;

class Detail extends Component
{

    public $log;
    public $log_info;

    public function mount($id)
    {
        $this->log = ActivityLog::find($id);
        $this->log_info = (new LogResource($this->log))->resolve();
    }

    public function render()
    {
        return view('livewire.activity-log.detail');
    }
}
