<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MqttListenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mqtt:listen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen to MQTT status updates and update component shadows';

    /**
     * Execute the console command.
     */
    public function handle(\App\Services\MqttService $mqtt)
    {
        $this->info('Starting MQTT Listener...');
        $mqtt->subscribeToDeviceStatus();
    }
}
