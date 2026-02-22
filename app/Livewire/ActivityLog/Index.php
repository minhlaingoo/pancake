<?php

namespace App\Livewire\ActivityLog;

use App\Models\ActivityLog;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view(
            'livewire.activity-log.index',
            [
                'logs' => ActivityLog::orderBy('created_at', 'desc')
                    ->paginate(8)
            ]
        );
    }
}
