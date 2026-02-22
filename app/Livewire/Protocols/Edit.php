<?php

namespace App\Livewire\Protocols;

use App\Models\Protocol;
use Livewire\Component;

class Edit extends Component
{
    public $protocol;
    public $sample_id;
    public $description;
    public $formData = [
        'mAb' => [
            'volume' => 0,
            'volume_unit' => 'ml',
            'concentration' => 0,
            'concentration_unit' => 'ml',
            'molecular_weight' => 0,
            'molar_absorbing_coefficient' => 0,
            'volume_to_add' => 0,
            'volume_to_add_unit' => 'ml',
        ],
        'payload' => [
            'volume_available' => 0,
            'volume_available_unit' => 'ml',
            'concentration' => 0,
            'concentration_unit' => 'ml',
            'molecular_weight' => 0,
            'molar_equivalence' => 0,
            'molar_absorbing_coefficient' => 0,
            'volume_to_add' => 0,
            'volume_to_add_unit' => 'ml',
        ],
        'misc' => [
            'use_reducing_conditions' => false,
            'reduction_reservoir' => 0,
            'reduction_reservoir_unit' => 'ml',
            'additive_reservoir_a' => 0,
            'additive_reservoir_a_unit' => 'ml',
            'additive_reservoir_b' => 0,
            'additive_reservoir_b_unit' => 'ml',
            'additive_reservoir_c' => 0,
            'additive_reservoir_c_unit' => 'ml',
        ],
    ];

    public function mount($sample_id)
    {
        $this->protocol = Protocol::where('sample_id',$sample_id)->first();
        $this->sample_id = $this->protocol->sample_id;
        $this->description = $this->protocol->description;
        $this->formData = json_decode($this->protocol->value, true);
    }

    public function updateProtocol()
    {
        $this->protocol->update([
            'sample_id' => $this->sample_id,
            'description' => $this->description,
            'value' => json_encode($this->formData),
        ]);

        session()->flash('message', 'Protocol updated successfully.');
        return to_route('protocols.final-lab', $this->protocol->sample_id);
    }

    public function render()
    {
        return view('livewire.protocols.edit');
    }
}