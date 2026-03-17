<?php

namespace Database\Seeders;

use App\Models\Preset;
use Illuminate\Database\Seeder;

class PresetSeeder extends Seeder
{
    /**
     * Seed only the initialization preset with 62 steps as per Excel specification.
     * Updated for OC requirements: pump_0, pump_1, rotary_valve_1, rotary_valve_2, microvalve 0-5
     */
    public function run(): void
    {
        // Single initialization preset with 62 steps as per Excel
        $initializationPreset = [
            'name' => 'Initialization',
            'description' => 'Complete device initialization sequence with 62 steps as per Excel specification. Initializes pumps, rotary valves, microvalves, stirrer, and TEC controller.',
            'version' => '0.32',
            'author' => 'System',
            'status' => 'Active',
            'commands' => [
                // Step 1-2: Pump 0 Initialization
                [
                    'controller' => 'pump_0',
                    'action' => 'init',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 3,
                    'retry_count' => 2,
                    'timeout' => 30,
                ],
                [
                    'controller' => 'pump_0',
                    'action' => 'home',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 30,
                ],

                // Step 3-4: Pump 1 Initialization
                [
                    'controller' => 'pump_1',
                    'action' => 'init',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 3,
                    'retry_count' => 2,
                    'timeout' => 30,
                ],
                [
                    'controller' => 'pump_1',
                    'action' => 'home',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 30,
                ],

                // Step 5-6: Rotary Valve 1 Initialization
                [
                    'controller' => 'rotary_valve_1',
                    'action' => 'init',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 3,
                    'retry_count' => 2,
                    'timeout' => 30,
                ],
                [
                    'controller' => 'rotary_valve_1',
                    'action' => 'home',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 30,
                ],

                // Step 7-8: Rotary Valve 2 Initialization
                [
                    'controller' => 'rotary_valve_2',
                    'action' => 'init',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 3,
                    'retry_count' => 2,
                    'timeout' => 30,
                ],
                [
                    'controller' => 'rotary_valve_2',
                    'action' => 'home',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 30,
                ],

                // Step 9: Stirrer Stop
                [
                    'controller' => 'stirrer',
                    'action' => 'stop',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 2,
                    'retry_count' => 0,
                    'timeout' => 10,
                ],

                // Step 10: TEC Initial Setpoint
                [
                    'controller' => 'tec',
                    'action' => 'setpoint',
                    'value' => 25.0,
                    'type' => 'float',
                    'delay' => 2,
                    'retry_count' => 0,
                    'timeout' => 15,
                ],

                // Step 11-16: Initialize Microvalves 0-5 (close all)
                [
                    'controller' => 'microvalve',
                    'action' => 'close',
                    'value' => '0',
                    'type' => 'microvalve_select',
                    'delay' => 1,
                    'retry_count' => 0,
                    'timeout' => 10,
                ],
                [
                    'controller' => 'microvalve',
                    'action' => 'close',
                    'value' => '1',
                    'type' => 'microvalve_select',
                    'delay' => 1,
                    'retry_count' => 0,
                    'timeout' => 10,
                ],
                [
                    'controller' => 'microvalve',
                    'action' => 'close',
                    'value' => '2',
                    'type' => 'microvalve_select',
                    'delay' => 1,
                    'retry_count' => 0,
                    'timeout' => 10,
                ],
                [
                    'controller' => 'microvalve',
                    'action' => 'close',
                    'value' => '3',
                    'type' => 'microvalve_select',
                    'delay' => 1,
                    'retry_count' => 0,
                    'timeout' => 10,
                ],
                [
                    'controller' => 'microvalve',
                    'action' => 'close',
                    'value' => '4',
                    'type' => 'microvalve_select',
                    'delay' => 1,
                    'retry_count' => 0,
                    'timeout' => 10,
                ],
                [
                    'controller' => 'microvalve',
                    'action' => 'close',
                    'value' => '5',
                    'type' => 'microvalve_select',
                    'delay' => 1,
                    'retry_count' => 0,
                    'timeout' => 10,
                ],

                // Step 17-22: Rotary Valve 1 Position Tests
                [
                    'controller' => 'rotary_valve_1',
                    'action' => 'position',
                    'value' => 1,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'rotary_valve_1',
                    'action' => 'position',
                    'value' => 2,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'rotary_valve_1',
                    'action' => 'position',
                    'value' => 3,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'rotary_valve_1',
                    'action' => 'position',
                    'value' => 4,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'rotary_valve_1',
                    'action' => 'position',
                    'value' => 5,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'rotary_valve_1',
                    'action' => 'position',
                    'value' => 6,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],

                // Step 23-28: Rotary Valve 2 Position Tests
                [
                    'controller' => 'rotary_valve_2',
                    'action' => 'position',
                    'value' => 1,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'rotary_valve_2',
                    'action' => 'position',
                    'value' => 2,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'rotary_valve_2',
                    'action' => 'position',
                    'value' => 3,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'rotary_valve_2',
                    'action' => 'position',
                    'value' => 4,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'rotary_valve_2',
                    'action' => 'position',
                    'value' => 5,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'rotary_valve_2',
                    'action' => 'position',
                    'value' => 6,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],

                // Step 29-34: Microvalve Open/Close Tests (0-5)
                [
                    'controller' => 'microvalve',
                    'action' => 'open',
                    'value' => '0',
                    'type' => 'microvalve_select',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 10,
                ],
                [
                    'controller' => 'microvalve',
                    'action' => 'open',
                    'value' => '1',
                    'type' => 'microvalve_select',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 10,
                ],
                [
                    'controller' => 'microvalve',
                    'action' => 'open',
                    'value' => '2',
                    'type' => 'microvalve_select',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 10,
                ],
                [
                    'controller' => 'microvalve',
                    'action' => 'open',
                    'value' => '3',
                    'type' => 'microvalve_select',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 10,
                ],
                [
                    'controller' => 'microvalve',
                    'action' => 'open',
                    'value' => '4',
                    'type' => 'microvalve_select',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 10,
                ],
                [
                    'controller' => 'microvalve',
                    'action' => 'open',
                    'value' => '5',
                    'type' => 'microvalve_select',
                    'delay' => 2,
                    'retry_count' => 1,
                    'timeout' => 10,
                ],

                // Step 35-40: TEC Temperature Tests
                [
                    'controller' => 'tec',
                    'action' => 'setpoint',
                    'value' => 20.0,
                    'type' => 'float',
                    'delay' => 5,
                    'retry_count' => 0,
                    'timeout' => 30,
                ],
                [
                    'controller' => 'tec',
                    'action' => 'setpoint',
                    'value' => 30.0,
                    'type' => 'float',
                    'delay' => 5,
                    'retry_count' => 0,
                    'timeout' => 30,
                ],
                [
                    'controller' => 'tec',
                    'action' => 'setpoint',
                    'value' => 37.0,
                    'type' => 'float',
                    'delay' => 5,
                    'retry_count' => 0,
                    'timeout' => 30,
                ],
                [
                    'controller' => 'tec',
                    'action' => 'enable',
                    'value' => 1,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 0,
                    'timeout' => 10,
                ],
                [
                    'controller' => 'tec',
                    'action' => 'enable',
                    'value' => 0,
                    'type' => 'int',
                    'delay' => 2,
                    'retry_count' => 0,
                    'timeout' => 10,
                ],
                [
                    'controller' => 'tec',
                    'action' => 'setpoint',
                    'value' => 25.0,
                    'type' => 'float',
                    'delay' => 2,
                    'retry_count' => 0,
                    'timeout' => 15,
                ],

                // Step 41-46: Stirrer Speed Tests
                [
                    'controller' => 'stirrer',
                    'action' => 'speed',
                    'value' => 100,
                    'type' => 'int',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'stirrer',
                    'action' => 'speed',
                    'value' => 300,
                    'type' => 'int',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'stirrer',
                    'action' => 'speed',
                    'value' => 500,
                    'type' => 'int',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'stirrer',
                    'action' => 'speed',
                    'value' => 800,
                    'type' => 'int',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'stirrer',
                    'action' => 'speed',
                    'value' => 1000,
                    'type' => 'int',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 15,
                ],
                [
                    'controller' => 'stirrer',
                    'action' => 'stop',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 2,
                    'retry_count' => 0,
                    'timeout' => 10,
                ],

                // Step 47-52: Pump 0 Volume Tests
                [
                    'controller' => 'pump_0',
                    'action' => 'aspirate',
                    'value' => 50.0,
                    'type' => 'float',
                    'delay' => 4,
                    'retry_count' => 2,
                    'timeout' => 30,
                ],
                [
                    'controller' => 'pump_0',
                    'action' => 'dispense',
                    'value' => 50.0,
                    'type' => 'float',
                    'delay' => 4,
                    'retry_count' => 2,
                    'timeout' => 30,
                ],
                [
                    'controller' => 'pump_0',
                    'action' => 'aspirate',
                    'value' => 100.0,
                    'type' => 'float',
                    'delay' => 6,
                    'retry_count' => 2,
                    'timeout' => 45,
                ],
                [
                    'controller' => 'pump_0',
                    'action' => 'dispense',
                    'value' => 100.0,
                    'type' => 'float',
                    'delay' => 6,
                    'retry_count' => 2,
                    'timeout' => 45,
                ],
                [
                    'controller' => 'pump_0',
                    'action' => 'aspirate',
                    'value' => 200.0,
                    'type' => 'float',
                    'delay' => 8,
                    'retry_count' => 2,
                    'timeout' => 60,
                ],
                [
                    'controller' => 'pump_0',
                    'action' => 'dispense',
                    'value' => 200.0,
                    'type' => 'float',
                    'delay' => 8,
                    'retry_count' => 2,
                    'timeout' => 60,
                ],

                // Step 53-58: Pump 1 Volume Tests
                [
                    'controller' => 'pump_1',
                    'action' => 'aspirate',
                    'value' => 75.0,
                    'type' => 'float',
                    'delay' => 5,
                    'retry_count' => 2,
                    'timeout' => 35,
                ],
                [
                    'controller' => 'pump_1',
                    'action' => 'dispense',
                    'value' => 75.0,
                    'type' => 'float',
                    'delay' => 5,
                    'retry_count' => 2,
                    'timeout' => 35,
                ],
                [
                    'controller' => 'pump_1',
                    'action' => 'aspirate',
                    'value' => 150.0,
                    'type' => 'float',
                    'delay' => 7,
                    'retry_count' => 2,
                    'timeout' => 50,
                ],
                [
                    'controller' => 'pump_1',
                    'action' => 'dispense',
                    'value' => 150.0,
                    'type' => 'float',
                    'delay' => 7,
                    'retry_count' => 2,
                    'timeout' => 50,
                ],
                [
                    'controller' => 'pump_1',
                    'action' => 'aspirate',
                    'value' => 250.0,
                    'type' => 'float',
                    'delay' => 10,
                    'retry_count' => 2,
                    'timeout' => 75,
                ],
                [
                    'controller' => 'pump_1',
                    'action' => 'dispense',
                    'value' => 250.0,
                    'type' => 'float',
                    'delay' => 10,
                    'retry_count' => 2,
                    'timeout' => 75,
                ],

                // Step 59-62: Final System Reset
                [
                    'controller' => 'pump_0',
                    'action' => 'home',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 30,
                ],
                [
                    'controller' => 'pump_1',
                    'action' => 'home',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 30,
                ],
                [
                    'controller' => 'rotary_valve_1',
                    'action' => 'home',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 30,
                ],
                [
                    'controller' => 'rotary_valve_2',
                    'action' => 'home',
                    'value' => '',
                    'type' => 'none',
                    'delay' => 3,
                    'retry_count' => 1,
                    'timeout' => 30,
                ],
            ],
        ];

        // Create/update the single initialization preset
        Preset::updateOrCreate(
            ['name' => $initializationPreset['name'], 'version' => $initializationPreset['version']],
            $initializationPreset
        );

        $commandCount = count($initializationPreset['commands']);
        
        $this->command->info("✅ Seeded single initialization preset:");
        $this->command->info("   - Name: {$initializationPreset['name']}");
        $this->command->info("   - Version: {$initializationPreset['version']}");
        $this->command->info("   - Steps: {$commandCount} commands");
        $this->command->info("   - Updated for OC requirements: pump_0/1, rotary_valve_1/2, microvalves 0-5");
        
        if ($commandCount === 62) {
            $this->command->info("   ✓ Matches Excel specification (62 steps)");
        } else {
            $this->command->warn("   ⚠ Expected 62 steps, got {$commandCount}");
        }
    }
}