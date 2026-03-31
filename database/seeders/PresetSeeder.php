<?php

namespace Database\Seeders;

use App\Models\Preset;
use Illuminate\Database\Seeder;

class PresetSeeder extends Seeder
{
    /**
     * Seed only the initialization preset with 62 steps as per Excel specification.
     * Each command has an explicit 'step' number to enforce sequential execution order.
     * Updated for OC requirements: pump_0, pump_1, rotary_valve_1, rotary_valve_2, microvalve 0-5
     */
    public function run(): void
    {
        $commands = [
            // Step 1: Pump 0 Init
            ['step' => 1, 'controller' => 'pump_0', 'action' => 'init', 'value' => '', 'type' => 'none', 'delay' => 3, 'retry_count' => 2, 'timeout' => 30],
            // Step 2: Pump 0 Home
            ['step' => 2, 'controller' => 'pump_0', 'action' => 'home', 'value' => '', 'type' => 'none', 'delay' => 3, 'retry_count' => 1, 'timeout' => 30],
            // Step 3: Pump 1 Init
            ['step' => 3, 'controller' => 'pump_1', 'action' => 'init', 'value' => '', 'type' => 'none', 'delay' => 3, 'retry_count' => 2, 'timeout' => 30],
            // Step 4: Pump 1 Home
            ['step' => 4, 'controller' => 'pump_1', 'action' => 'home', 'value' => '', 'type' => 'none', 'delay' => 3, 'retry_count' => 1, 'timeout' => 30],
            // Step 5: Rotary Valve 1 Init
            ['step' => 5, 'controller' => 'rotary_valve_1', 'action' => 'init', 'value' => '', 'type' => 'none', 'delay' => 3, 'retry_count' => 2, 'timeout' => 30],
            // Step 6: Rotary Valve 1 Home
            ['step' => 6, 'controller' => 'rotary_valve_1', 'action' => 'home', 'value' => '', 'type' => 'none', 'delay' => 3, 'retry_count' => 1, 'timeout' => 30],
            // Step 7: Rotary Valve 2 Init
            ['step' => 7, 'controller' => 'rotary_valve_2', 'action' => 'init', 'value' => '', 'type' => 'none', 'delay' => 3, 'retry_count' => 2, 'timeout' => 30],
            // Step 8: Rotary Valve 2 Home
            ['step' => 8, 'controller' => 'rotary_valve_2', 'action' => 'home', 'value' => '', 'type' => 'none', 'delay' => 3, 'retry_count' => 1, 'timeout' => 30],
            // Step 9: Stirrer Stop
            ['step' => 9, 'controller' => 'stirrer', 'action' => 'stop', 'value' => '', 'type' => 'none', 'delay' => 2, 'retry_count' => 0, 'timeout' => 10],
            // Step 10: TEC Initial Setpoint
            ['step' => 10, 'controller' => 'tec', 'action' => 'setpoint', 'value' => 25.0, 'type' => 'float', 'delay' => 2, 'retry_count' => 0, 'timeout' => 15],
            // Step 11-16: Close Microvalves 0-5
            ['step' => 11, 'controller' => 'microvalve', 'action' => 'close', 'value' => '0', 'type' => 'microvalve_select', 'delay' => 1, 'retry_count' => 0, 'timeout' => 10],
            ['step' => 12, 'controller' => 'microvalve', 'action' => 'close', 'value' => '1', 'type' => 'microvalve_select', 'delay' => 1, 'retry_count' => 0, 'timeout' => 10],
            ['step' => 13, 'controller' => 'microvalve', 'action' => 'close', 'value' => '2', 'type' => 'microvalve_select', 'delay' => 1, 'retry_count' => 0, 'timeout' => 10],
            ['step' => 14, 'controller' => 'microvalve', 'action' => 'close', 'value' => '3', 'type' => 'microvalve_select', 'delay' => 1, 'retry_count' => 0, 'timeout' => 10],
            ['step' => 15, 'controller' => 'microvalve', 'action' => 'close', 'value' => '4', 'type' => 'microvalve_select', 'delay' => 1, 'retry_count' => 0, 'timeout' => 10],
            ['step' => 16, 'controller' => 'microvalve', 'action' => 'close', 'value' => '5', 'type' => 'microvalve_select', 'delay' => 1, 'retry_count' => 0, 'timeout' => 10],
            // Step 17-22: Rotary Valve 1 Position Tests
            ['step' => 17, 'controller' => 'rotary_valve_1', 'action' => 'position', 'value' => 1, 'type' => 'int', 'delay' => 2, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 18, 'controller' => 'rotary_valve_1', 'action' => 'position', 'value' => 2, 'type' => 'int', 'delay' => 2, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 19, 'controller' => 'rotary_valve_1', 'action' => 'position', 'value' => 3, 'type' => 'int', 'delay' => 2, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 20, 'controller' => 'rotary_valve_1', 'action' => 'position', 'value' => 4, 'type' => 'int', 'delay' => 2, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 21, 'controller' => 'rotary_valve_1', 'action' => 'position', 'value' => 5, 'type' => 'int', 'delay' => 2, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 22, 'controller' => 'rotary_valve_1', 'action' => 'position', 'value' => 6, 'type' => 'int', 'delay' => 2, 'retry_count' => 1, 'timeout' => 15],
            // Step 23-28: Rotary Valve 2 Position Tests
            ['step' => 23, 'controller' => 'rotary_valve_2', 'action' => 'position', 'value' => 1, 'type' => 'int', 'delay' => 2, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 24, 'controller' => 'rotary_valve_2', 'action' => 'position', 'value' => 2, 'type' => 'int', 'delay' => 2, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 25, 'controller' => 'rotary_valve_2', 'action' => 'position', 'value' => 3, 'type' => 'int', 'delay' => 2, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 26, 'controller' => 'rotary_valve_2', 'action' => 'position', 'value' => 4, 'type' => 'int', 'delay' => 2, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 27, 'controller' => 'rotary_valve_2', 'action' => 'position', 'value' => 5, 'type' => 'int', 'delay' => 2, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 28, 'controller' => 'rotary_valve_2', 'action' => 'position', 'value' => 6, 'type' => 'int', 'delay' => 2, 'retry_count' => 1, 'timeout' => 15],
            // Step 29-34: Microvalve Open Tests (0-5)
            ['step' => 29, 'controller' => 'microvalve', 'action' => 'open', 'value' => '0', 'type' => 'microvalve_select', 'delay' => 2, 'retry_count' => 1, 'timeout' => 10],
            ['step' => 30, 'controller' => 'microvalve', 'action' => 'open', 'value' => '1', 'type' => 'microvalve_select', 'delay' => 2, 'retry_count' => 1, 'timeout' => 10],
            ['step' => 31, 'controller' => 'microvalve', 'action' => 'open', 'value' => '2', 'type' => 'microvalve_select', 'delay' => 2, 'retry_count' => 1, 'timeout' => 10],
            ['step' => 32, 'controller' => 'microvalve', 'action' => 'open', 'value' => '3', 'type' => 'microvalve_select', 'delay' => 2, 'retry_count' => 1, 'timeout' => 10],
            ['step' => 33, 'controller' => 'microvalve', 'action' => 'open', 'value' => '4', 'type' => 'microvalve_select', 'delay' => 2, 'retry_count' => 1, 'timeout' => 10],
            ['step' => 34, 'controller' => 'microvalve', 'action' => 'open', 'value' => '5', 'type' => 'microvalve_select', 'delay' => 2, 'retry_count' => 1, 'timeout' => 10],
            // Step 35-40: TEC Temperature Tests
            ['step' => 35, 'controller' => 'tec', 'action' => 'setpoint', 'value' => 20.0, 'type' => 'float', 'delay' => 5, 'retry_count' => 0, 'timeout' => 30],
            ['step' => 36, 'controller' => 'tec', 'action' => 'setpoint', 'value' => 30.0, 'type' => 'float', 'delay' => 5, 'retry_count' => 0, 'timeout' => 30],
            ['step' => 37, 'controller' => 'tec', 'action' => 'setpoint', 'value' => 37.0, 'type' => 'float', 'delay' => 5, 'retry_count' => 0, 'timeout' => 30],
            ['step' => 38, 'controller' => 'tec', 'action' => 'enable', 'value' => 1, 'type' => 'int', 'delay' => 2, 'retry_count' => 0, 'timeout' => 10],
            ['step' => 39, 'controller' => 'tec', 'action' => 'enable', 'value' => 0, 'type' => 'int', 'delay' => 2, 'retry_count' => 0, 'timeout' => 10],
            ['step' => 40, 'controller' => 'tec', 'action' => 'setpoint', 'value' => 25.0, 'type' => 'float', 'delay' => 2, 'retry_count' => 0, 'timeout' => 15],
            // Step 41-46: Stirrer Speed Tests
            ['step' => 41, 'controller' => 'stirrer', 'action' => 'speed', 'value' => 100, 'type' => 'int', 'delay' => 3, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 42, 'controller' => 'stirrer', 'action' => 'speed', 'value' => 300, 'type' => 'int', 'delay' => 3, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 43, 'controller' => 'stirrer', 'action' => 'speed', 'value' => 500, 'type' => 'int', 'delay' => 3, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 44, 'controller' => 'stirrer', 'action' => 'speed', 'value' => 800, 'type' => 'int', 'delay' => 3, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 45, 'controller' => 'stirrer', 'action' => 'speed', 'value' => 1000, 'type' => 'int', 'delay' => 3, 'retry_count' => 1, 'timeout' => 15],
            ['step' => 46, 'controller' => 'stirrer', 'action' => 'stop', 'value' => '', 'type' => 'none', 'delay' => 2, 'retry_count' => 0, 'timeout' => 10],
            // Step 47-52: Pump 0 Volume Tests
            ['step' => 47, 'controller' => 'pump_0', 'action' => 'aspirate', 'value' => 50.0, 'type' => 'float', 'delay' => 4, 'retry_count' => 2, 'timeout' => 30],
            ['step' => 48, 'controller' => 'pump_0', 'action' => 'dispense', 'value' => 50.0, 'type' => 'float', 'delay' => 4, 'retry_count' => 2, 'timeout' => 30],
            ['step' => 49, 'controller' => 'pump_0', 'action' => 'aspirate', 'value' => 100.0, 'type' => 'float', 'delay' => 6, 'retry_count' => 2, 'timeout' => 45],
            ['step' => 50, 'controller' => 'pump_0', 'action' => 'dispense', 'value' => 100.0, 'type' => 'float', 'delay' => 6, 'retry_count' => 2, 'timeout' => 45],
            ['step' => 51, 'controller' => 'pump_0', 'action' => 'aspirate', 'value' => 200.0, 'type' => 'float', 'delay' => 8, 'retry_count' => 2, 'timeout' => 60],
            ['step' => 52, 'controller' => 'pump_0', 'action' => 'dispense', 'value' => 200.0, 'type' => 'float', 'delay' => 8, 'retry_count' => 2, 'timeout' => 60],
            // Step 53-58: Pump 1 Volume Tests
            ['step' => 53, 'controller' => 'pump_1', 'action' => 'aspirate', 'value' => 75.0, 'type' => 'float', 'delay' => 5, 'retry_count' => 2, 'timeout' => 35],
            ['step' => 54, 'controller' => 'pump_1', 'action' => 'dispense', 'value' => 75.0, 'type' => 'float', 'delay' => 5, 'retry_count' => 2, 'timeout' => 35],
            ['step' => 55, 'controller' => 'pump_1', 'action' => 'aspirate', 'value' => 150.0, 'type' => 'float', 'delay' => 7, 'retry_count' => 2, 'timeout' => 50],
            ['step' => 56, 'controller' => 'pump_1', 'action' => 'dispense', 'value' => 150.0, 'type' => 'float', 'delay' => 7, 'retry_count' => 2, 'timeout' => 50],
            ['step' => 57, 'controller' => 'pump_1', 'action' => 'aspirate', 'value' => 250.0, 'type' => 'float', 'delay' => 10, 'retry_count' => 2, 'timeout' => 75],
            ['step' => 58, 'controller' => 'pump_1', 'action' => 'dispense', 'value' => 250.0, 'type' => 'float', 'delay' => 10, 'retry_count' => 2, 'timeout' => 75],
            // Step 59-62: Final System Reset
            ['step' => 59, 'controller' => 'pump_0', 'action' => 'home', 'value' => '', 'type' => 'none', 'delay' => 3, 'retry_count' => 1, 'timeout' => 30],
            ['step' => 60, 'controller' => 'pump_1', 'action' => 'home', 'value' => '', 'type' => 'none', 'delay' => 3, 'retry_count' => 1, 'timeout' => 30],
            ['step' => 61, 'controller' => 'rotary_valve_1', 'action' => 'home', 'value' => '', 'type' => 'none', 'delay' => 3, 'retry_count' => 1, 'timeout' => 30],
            ['step' => 62, 'controller' => 'rotary_valve_2', 'action' => 'home', 'value' => '', 'type' => 'none', 'delay' => 3, 'retry_count' => 1, 'timeout' => 30],
        ];

        $initializationPreset = [
            'name' => 'Initialization',
            'description' => 'Complete device initialization sequence with 62 steps. Steps are executed sequentially by step number, not grouped by component.',
            'version' => '0.33',
            'author' => 'System',
            'status' => 'Active',
            'commands' => $commands,
        ];

        // Create/update the single initialization preset
        Preset::updateOrCreate(
            ['name' => $initializationPreset['name']],
            $initializationPreset
        );

        $commandCount = count($commands);
        
        $this->command->info("✅ Seeded single initialization preset:");
        $this->command->info("   - Name: {$initializationPreset['name']}");
        $this->command->info("   - Version: {$initializationPreset['version']}");
        $this->command->info("   - Steps: {$commandCount} commands (step 1 → {$commandCount})");
        $this->command->info("   - Execution order: sequential by step number");
        $this->command->info("   - Components: pump_0/1, rotary_valve_1/2, microvalves 0-5, stirrer, tec");
        
        if ($commandCount === 62) {
            $this->command->info("   ✓ Matches Excel specification (62 steps)");
        } else {
            $this->command->warn("   ⚠ Expected 62 steps, got {$commandCount}");
        }
    }
}