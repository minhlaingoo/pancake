<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Services\MqttService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Log;

class MqttServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_ntp_update_uses_device_data()
    {
        Log::shouldReceive('info')->atLeast()->once();

        $device = Device::create([
            'name' => 'Test Device',
            'model' => 'T1000',
            'ip' => '127.0.0.1',
            'port' => 1883,
            'ntp_server' => 'ntp.test.com',
            'timezone' => 'UTC',
            'ntp_interval' => 1000,
            'is_active' => true,
        ]);

        // Mock MqttService to avoid real connection
        $mqttService = $this->getMockBuilder(MqttService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['publishMessage'])
            ->getMock();

        $mqttService->expects($this->once())
            ->method('publishMessage')
            ->with(
                $this->equalTo('ntp/update'),
                $this->callback(function ($payload) {
                    $decoded = json_decode($payload, true);
                    return $decoded['ntp_server'] === 'ntp.test.com' &&
                        $decoded['timezone'] === 'UTC' &&
                        $decoded['ntp_interval'] === 1000;
                })
            );

        $mqttService->ntpUpdate($device);
    }

    public function test_publish_to_device_uses_device_id()
    {
        $device = Device::create([
            'name' => 'Test Device',
            'model' => 'dev-999',
            'ip' => '127.0.0.1',
            'port' => 1883,
            'is_active' => true,
        ]);

        $mqttService = $this->getMockBuilder(MqttService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['publishMessage'])
            ->getMock();

        // deviceCommand calls publishMessage
        // Topic pattern: adc/controller/{deviceId}/command/{device}/{action}
        $expectedTopic = 'adc/controller/dev-999/command/tec/enable';

        $mqttService->expects($this->once())
            ->method('publishMessage')
            ->with($this->equalTo($expectedTopic), $this->equalTo('1'));

        $mqttService->publishToDevice($device, 'tec', 'enable', '1');
    }
}
