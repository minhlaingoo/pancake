<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Device;
use App\Models\DeviceComponent;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeder for testing environment devices.
     */
    public function run(): void
    {
        // Create test device with configuration
        $device = Device::create([
            'name' => 'Test Controller',
            'model' => 'adc_v1',
            'ip' => '192.168.1.100',
            'port' => 80,
            'mac' => '00:11:22:33:44:55',
            'is_active' => true,
            'ntp_server' => 'pool.ntp.org',
            'timezone' => 'UTC',
            'ntp_interval' => 3600,
            'configuration' => Device::getDefaultConfiguration(),
        ]);

        // Define device components based on OC requirements
        $components = [
            // TEC (Temperature Control)
            [
                'type' => 'tec',
                'unit' => '°C',
                'name' => 'Temperature Controller',
                'is_sensor' => true,
            ],
            
            // Stirrer
            [
                'type' => 'stirrer',
                'unit' => 'RPM',
                'name' => 'Magnetic Stirrer',
                'is_sensor' => false,
            ],

            // Microvalves (0-5 active, 6-15 not present)
            [
                'type' => 'microvalve_0',
                'unit' => null,
                'name' => 'Microvalve 0',
                'is_sensor' => false,
            ],
            [
                'type' => 'microvalve_1',
                'unit' => null,
                'name' => 'Microvalve 1',
                'is_sensor' => false,
            ],
            [
                'type' => 'microvalve_2',
                'unit' => null,
                'name' => 'Microvalve 2',
                'is_sensor' => false,
            ],
            [
                'type' => 'microvalve_3',
                'unit' => null,
                'name' => 'Microvalve 3',
                'is_sensor' => false,
            ],
            [
                'type' => 'microvalve_4',
                'unit' => null,
                'name' => 'Microvalve 4',
                'is_sensor' => false,
            ],
            [
                'type' => 'microvalve_5',
                'unit' => null,
                'name' => 'Microvalve 5',
                'is_sensor' => false,
            ],

            // Pumps (pump_0 and pump_1)
            [
                'type' => 'pump_0',
                'unit' => 'μL',
                'name' => 'Pump 0',
                'is_sensor' => false,
            ],
            [
                'type' => 'pump_1',
                'unit' => 'μL',
                'name' => 'Pump 1',
                'is_sensor' => false,
            ],

            // Rotary Valves (rotary_valve_1 and rotary_valve_2)
            [
                'type' => 'rotary_valve_1',
                'unit' => 'position',
                'name' => 'Rotary Valve 1',
                'is_sensor' => false,
            ],
            [
                'type' => 'rotary_valve_2',
                'unit' => 'position',
                'name' => 'Rotary Valve 2',
                'is_sensor' => false,
            ],
        ];

        // Create device components
        foreach ($components as $component) {
            DeviceComponent::create([
                'device_id' => $device->id,
                'type' => $component['type'],
                'unit' => $component['unit'],
                'name' => $component['name'],
                'last_value' => null,
                'status' => 'offline',
                'is_sensor' => $component['is_sensor'],
            ]);
        }

        $this->command->info('✅ Test device created with updated components:');
        $this->command->info('   - TEC temperature controller');
        $this->command->info('   - Stirrer');
        $this->command->info('   - Microvalves 0-5 (active)');
        $this->command->info('   - Pump 0 and Pump 1');
        $this->command->info('   - Rotary Valve 1 and Rotary Valve 2');
    }
}