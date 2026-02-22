<?php

namespace App\Livewire\Protocols;

use App\Models\ProtocolProcess;
use Livewire\Component;

class HistoryList extends Component
{
    public $perPage = 10;

    public function render()
    {
        return view(
            'livewire.protocols.history-list',
            [
                'protocol_processes' => ProtocolProcess::paginate($this->perPage)
            ]
        );
    }
}
