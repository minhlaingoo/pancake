<?php

namespace App\Livewire\Protocols;

use App\Models\Protocol;
use Livewire\Component;

class Create extends Component
{
    public $sample_id;
    public $description;

    public $formData = [
        'mAb' => [
            'volume' => 0,
            'volume_unit' => 'mL',
            'concentration' => 0,
            'concentration_unit' => 'mL',
            'molecular_weight' => 0,
            'molar_absorbing_coefficient' => 0,
            'volume_to_add' => 0,
            'volume_to_add_unit' => 'mL',
        ],
        'payload' => [
            'volume_available' => 0,
            'volume_available_unit' => 'mL',
            'concentration' => 0,
            'concentration_unit' => 'mL',
            'molecular_weight' => 0,
            'molar_equivalence' => 0,
            'molar_absorbing_coefficient' => 0,
            'volume_to_add' => 0,
            'volume_to_add_unit' => 'mL',
        ],
        'misc' => [
            'use_reducing_conditions' => false,
            'reduction_reservoir' => 0,
            'reduction_reservoir_unit' => 'mL',
            'additive_reservoir_a' => 0,
            'additive_reservoir_a_unit' => 'mL',
            'additive_reservoir_b' => 0,
            'additive_reservoir_b_unit' => 'mL',
            'additive_reservoir_c' => 0,
            'additive_reservoir_c_unit' => 'mL',
        ],
    ];

    public function rules()
    {
        return [
            'sample_id' => 'required',
        ];
    }


    public function mount(\App\Services\ProtocolService $protocolService)
    {
        $this->formData = $protocolService->getInitialSetupData();
    }

    public function finalizeProtocol(\App\Services\ProtocolService $protocolService)
    {
        $this->validate();

        $protocol = Protocol::create([
            'sample_id' => $this->sample_id,
            'description' => $this->description,
            'value' => json_encode($this->formData),
            'phases' => [
                $protocolService->createDefaultSetupPhase()
            ]
        ]);

        session()->flash('message', 'Protocol created successfully.');
        return to_route('protocols.final-lab', $protocol->sample_id);
    }

    public function render()
    {
        return view('livewire.protocols.create');
    }
}
