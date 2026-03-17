<?php

namespace App\Livewire\Presets;

use App\Models\Preset;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public int $perPage = 10;

    public function delete($id)
    {
        if (!checkPermission('preset', 'delete')) {
            abort(403);
        }

        Preset::findOrFail($id)->delete();
        session()->flash('message', 'Preset deleted successfully.');
    }

    public function render()
    {
        return view('livewire.presets.index', [
            'presets' => Preset::paginate($this->perPage),
        ]);
    }
}
