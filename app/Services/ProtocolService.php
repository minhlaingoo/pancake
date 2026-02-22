<?php

namespace App\Services;

use App\Models\Protocol;
use Illuminate\Support\Str;

class ProtocolService
{
    /**
     * Get the default/initial setup data for a protocol.
     *
     * @return array
     */
    public function getInitialSetupData(): array
    {
        return [
            'mAb' => [
                'volume' => 0,
                'volume_unit' => 'mL',
                'concentration' => 0,
                'concentration_unit' => 'mg/mL',
                'molecular_weight' => 0,
                'molar_absorbing_coefficient' => 0,
                'volume_to_add' => 0,
                'volume_to_add_unit' => 'mL',
            ],
            'payload' => [
                'volume_available' => 0,
                'volume_available_unit' => 'mL',
                'concentration' => 0,
                'concentration_unit' => 'mg/mL',
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
                'desired_final_volume' => 0,
                'desired_final_volume_unit' => 'mL',
                'buffer_level_checked' => false,
            ],
        ];
    }

    /**
     * Create a default "End" phase.
     *
     * @return array
     */
    public function createDefaultEndPhase(): array
    {
        return [
            'id' => Str::random(),
            'label' => 'End Of Protocol',
            'duration' => 0,
            'loop' => 1,
            'is_end' => true,
            'commands' => [],
        ];
    }

    /**
     * Sync protocol data with the provided input.
     *
     * @param Protocol $protocol
     * @param array $data
     * @return Protocol
     */
    public function updateProtocolData(Protocol $protocol, array $data): Protocol
    {
        $protocol->update($data);
        return $protocol;
    }

    /**
     * Format protocol phases ensuring setup phase is first.
     *
     * @param array $phases
     * @return array
     */
    public function formatPhases(array $phases): array
    {
        // For now, we just ensure each phase has an ID
        return array_map(function ($phase) {
            if (!isset($phase['id'])) {
                $phase['id'] = Str::random();
            }
            return $phase;
        }, $phases);
    }
}
