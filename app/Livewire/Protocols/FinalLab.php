<?php

namespace App\Livewire\Protocols;

use App\Models\Protocol;
use Livewire\Component;
use Illuminate\Support\Str;

class FinalLab extends Component
{
    public $sample_id;
    public $description;
    public $protocol_value;
    public $mAb_volume;
    public $phases = [];

    public $controllerCommands = [
        'tec' => [
            'setpoint' => 'float',
            'enable' => 'int',
        ],
        'stirrer' => [
            'speed' => 'int',
            'stop' => 'string',
        ],
        'microvalve' => [
            'set' => 'string',
            'open' => 'int',
            'close' => 'int',
        ],
        'pump' => [
            'init' => 'string',
            'aspirate' => 'float',
            'dispense' => 'float',
            'home' => 'string',
        ],
    ];

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

    public $phaseFormData = [
        "label" => '',
        "duration" => 1,
        "loop" => 1,
    ];

    public function addPhase()
    {
        $newPhase = [
            'id' => Str::random(),
            'commands' => [],
            ...$this->phaseFormData
        ];

        // Ensure new phases are added BEFORE the "End" phase
        $endIndex = collect($this->phases)->search(fn($p) => ($p['is_end'] ?? false) === true);

        if ($endIndex !== false) {
            array_splice($this->phases, $endIndex, 0, [$newPhase]);
        } else {
            $this->phases[] = $newPhase;
        }

        $this->phaseFormData['label'] = '';
        $this->phaseFormData['duration'] = 1;
    }

    public function addCommand($phaseIndex)
    {
        if (!isset($this->phases[$phaseIndex]['commands'])) {
            $this->phases[$phaseIndex]['commands'] = [];
        }

        $this->phases[$phaseIndex]['commands'][] = [
            'controller' => '',
            'action' => '',
            'value' => '',
            'type' => 'string' // Default type
        ];
    }

    public function updatedPhases($value, $key)
    {
        // Extract indices: phases.{phaseIndex}.commands.{cmdIndex}.{property}
        if (Str::contains($key, 'commands')) {
            $parts = explode('.', $key);

            // Expected format: phases.0.commands.1.action
            if (count($parts) < 5)
                return;

            $phaseIndex = $parts[1];
            $cmdIndex = $parts[3];
            $property = $parts[4];

            // Ensure the indices are numeric to avoid issues with newly added elements during Livewire lifecycle
            if (!is_numeric($phaseIndex) || !is_numeric($cmdIndex))
                return;

            if (!isset($this->phases[$phaseIndex]['commands'][$cmdIndex]))
                return;

            $command = &$this->phases[$phaseIndex]['commands'][$cmdIndex];

            if ($property === 'controller') {
                $command['action'] = '';
                $command['type'] = 'string';
                $command['value'] = ''; // Reset value on controller change
            }

            if ($property === 'action') {
                $controller = $command['controller'];
                $action = $value;

                if (isset($this->controllerCommands[$controller][$action])) {
                    $command['type'] = $this->controllerCommands[$controller][$action];
                    // Potential reset value if type mismatch, but usually just update type
                }
            }
        }
    }

    public function removeCommand($phaseIndex, $commandIndex)
    {
        unset($this->phases[$phaseIndex]['commands'][$commandIndex]);
        $this->phases[$phaseIndex]['commands'] = array_values($this->phases[$phaseIndex]['commands']);
    }

    public function removePhase($id)
    {
        $this->phases = array_filter($this->phases, function ($p) use ($id) {
            // Prevent removing the "End" phase
            return $p['id'] != $id || !($p['is_end'] ?? false);
        });
        $this->phases = array_values($this->phases);
    }

    public function mount($sample_id, \App\Services\ProtocolService $protocolService)
    {
        $this->sample_id = $sample_id;
        $protocol = Protocol::where('sample_id', $this->sample_id)->first();
        if (!$protocol) {
            session()->flash('error', 'Protocol not found.');
            return redirect()->route('protocols.create');
        }

        $this->phases = $protocol->phases ?? [];

        // Remove any old "is_setup" phases (Start phases)
        $this->phases = array_filter($this->phases, fn($p) => !($p['is_setup'] ?? false));

        // Ensure an "End" phase exists
        $hasEnd = collect($this->phases)->contains(fn($p) => ($p['is_end'] ?? false) === true);
        if (!$hasEnd) {
            $this->phases[] = $protocolService->createDefaultEndPhase();
        }

        // Backfill 'commands' key for any existing phases that might miss it
        $this->phases = array_map(function ($phase) {
            if (!isset($phase['commands'])) {
                $phase['commands'] = [];
            }
            return $phase;
        }, array_values($this->phases));

        $this->description = $protocol->description;

        $initialData = $protocolService->getInitialSetupData();
        $storedValue = json_decode($protocol->value, true) ?: [];

        // Merge initial structure with stored values
        $this->formData = array_replace_recursive($initialData, $storedValue);

        // Ensure mAb_volume reflects the deep structure for backward compatibility/simplicity in view if needed
        $this->mAb_volume = $this->formData['mAb']['volume'] ?? 0;
    }

    public function save(\App\Services\ProtocolService $protocolService)
    {
        // Sanitize phase metadata and simple validation for commands
        foreach ($this->phases as $pIndex => $phase) {
            $this->phases[$pIndex]['duration'] = (int) ($phase['duration'] ?: 1);
            $this->phases[$pIndex]['loop'] = (int) ($phase['loop'] ?: 1);

            foreach ($phase['commands'] ?? [] as $cIndex => $command) {
                if (empty($command['controller']) || empty($command['action'])) {
                    session()->flash('error', "Phase '" . ($phase['label'] ?: $pIndex + 1) . "' has incomplete actions.");
                    return;
                }

                $type = $command['type'] ?? 'string';
                $value = $command['value'];

                // Validate and cast value based on type
                if ($type === 'int') {
                    if (!is_numeric($value)) {
                        session()->flash('error', "Value for action '{$command['action']}' must be an integer.");
                        return;
                    }
                    $this->phases[$pIndex]['commands'][$cIndex]['value'] = (int) $value;
                } elseif ($type === 'float') {
                    if (!is_numeric($value)) {
                        session()->flash('error', "Value for action '{$command['action']}' must be a number.");
                        return;
                    }
                    $this->phases[$pIndex]['commands'][$cIndex]['value'] = (float) $value;
                } elseif ($type === 'bool') {
                    $this->phases[$pIndex]['commands'][$cIndex]['value'] = (bool) $value;
                }
            }
        }

        $protocol = Protocol::where('sample_id', $this->sample_id)->first();

        // Sync mAb volume back to nested structure if manually edited via $mAb_volume
        $this->formData['mAb']['volume'] = $this->mAb_volume;

        $protocol->update([
            'phases' => $protocolService->formatPhases($this->phases),
            'value' => json_encode($this->formData)
        ]);

        session()->flash('message', 'Protocol updated successfully.');
        return to_route('protocols.index');
    }

    public function render()
    {
        return view('livewire.protocols.final-lab');
    }
}
