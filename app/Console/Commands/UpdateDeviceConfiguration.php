<?php

namespace App\Console\Commands;

use App\Models\Device;
use Illuminate\Console\Command;

class UpdateDeviceConfiguration extends Command
{
    protected $signature = 'device:update-config';
    protected $description = 'Update devices with default configuration (microvalves 0-5)';

    public function handle()
    {
        $devices = Device::all();
        
        foreach ($devices as $device) {
            if (empty($device->configuration)) {
                $device->configuration = Device::getDefaultConfiguration();
                $device->save();
                $this->info("✅ Updated device: {$device->name} with default configuration");
            } else {
                // Ensure microvalve defaults are set even if configuration exists
                if (!$device->getConfig('microvalves.count')) {
                    $device->setConfig('microvalves.count', 6);
                    $device->setConfig('microvalves.start', 0);
                    $device->setConfig('microvalves.description', 'Microvalves 0-5 are present (default)');
                    $this->info("✅ Updated microvalve config for: {$device->name}");
                } else {
                    $this->line("✓ Device {$device->name} already has microvalve configuration");
                }
            }
        }
        
        $this->info("\n🎯 All devices now have proper microvalve defaults (0-5)");
        return 0;
    }
}