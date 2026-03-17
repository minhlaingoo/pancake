<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\DeviceComponent;
use App\Models\MqttMessage;
use App\Services\MqttService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class MqttMessageHandlingTest extends TestCase
{
    use RefreshDatabase;

    private function createMockMqttService(): MqttService
    {
        return $this->getMockBuilder(MqttService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['publishMessage'])
            ->getMock();
    }

    public function test_handle_incoming_message_creates_mqtt_message_record()
    {
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('debug')->atLeast()->once();
        Event::fake();

        $mqttService = $this->createMockMqttService();
        $mqttService->handleIncomingMessage('adc/controller/adc-001/status/tec/temperature', '36.5');

        $this->assertDatabaseHas('mqtt_messages', [
            'topic' => 'adc/controller/adc-001/status/tec/temperature',
        ]);
    }

    public function test_handle_incoming_message_updates_device_component()
    {
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('debug')->atLeast()->once();
        Event::fake();

        $device = Device::create([
            'name' => 'Test Device',
            'model' => 'adc-001',
            'ip' => '127.0.0.1',
            'port' => 1883,
            'is_active' => true,
        ]);

        $component = DeviceComponent::create([
            'device_id' => $device->id,
            'name' => 'tec',
            'type' => 'temperature',
            'unit' => '°C',
            'status' => 'offline',
            'last_value' => '0',
            'is_sensor' => true,
        ]);

        $mqttService = $this->createMockMqttService();
        $mqttService->handleIncomingMessage(
            'adc/controller/adc-001/status/tec',
            '36.5'
        );

        $component->refresh();
        $this->assertEquals('36.5', $component->last_value);
        $this->assertEquals('online', $component->status);
    }

    public function test_handle_incoming_message_with_json_device_component_id()
    {
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('debug')->atLeast()->once();
        Event::fake();

        $device = Device::create([
            'name' => 'JSON Device',
            'model' => 'adc-json',
            'ip' => '127.0.0.1',
            'port' => 1883,
            'is_active' => true,
        ]);

        $component = DeviceComponent::create([
            'device_id' => $device->id,
            'name' => 'pump',
            'type' => 'pump',
            'unit' => 'µL',
            'status' => 'offline',
            'last_value' => '0',
            'is_sensor' => false,
        ]);

        $mqttService = $this->createMockMqttService();
        $message = json_encode([
            'device_component_id' => $component->id,
            'value' => '1500',
            'status' => 'online',
        ]);

        $mqttService->handleIncomingMessage('adc/controller/adc-json/status/pump', $message);

        $component->refresh();
        $this->assertEquals('1500', $component->last_value);
        $this->assertEquals('online', $component->status);
    }

    public function test_tec_setpoint_clamps_to_valid_range()
    {
        $mqttService = $this->createMockMqttService();

        // Should clamp to 50
        $mqttService->expects($this->once())
            ->method('publishMessage')
            ->with(
                $this->equalTo('adc/controller/adc-001/command/tec/setpoint'),
                $this->equalTo('50')
            );

        $mqttService->tecSetSetpoint('adc-001', 100.0);
    }

    public function test_tec_setpoint_clamps_negative_to_zero()
    {
        $mqttService = $this->createMockMqttService();

        $mqttService->expects($this->once())
            ->method('publishMessage')
            ->with(
                $this->equalTo('adc/controller/adc-001/command/tec/setpoint'),
                $this->equalTo('0')
            );

        $mqttService->tecSetSetpoint('adc-001', -10.0);
    }

    public function test_stirrer_speed_clamps_to_valid_range()
    {
        $mqttService = $this->createMockMqttService();

        $mqttService->expects($this->once())
            ->method('publishMessage')
            ->with(
                $this->equalTo('adc/controller/adc-001/command/stirrer/speed'),
                $this->equalTo('1000')
            );

        $mqttService->stirrerSetSpeed('adc-001', 2000);
    }

    public function test_send_phase_commands_sends_all_commands()
    {
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('debug')->atLeast()->once();
        Event::fake();

        $mqttService = $this->createMockMqttService();

        $mqttService->expects($this->exactly(2))
            ->method('publishMessage');

        $phase = [
            'label' => 'Test Phase',
            'commands' => [
                ['controller' => 'tec', 'action' => 'setpoint', 'value' => '37', 'type' => 'float'],
                ['controller' => 'stirrer', 'action' => 'speed', 'value' => '500', 'type' => 'int'],
            ],
        ];

        $mqttService->sendPhaseCommands('adc-001', $phase);
    }

    public function test_send_phase_commands_skips_incomplete_commands()
    {
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('warning')->atLeast()->once();
        Event::fake();

        $mqttService = $this->createMockMqttService();

        // Only 1 valid command should be published (the second has no controller)
        $mqttService->expects($this->once())
            ->method('publishMessage');

        $phase = [
            'label' => 'Skip Phase',
            'commands' => [
                ['controller' => 'tec', 'action' => 'enable', 'value' => '1', 'type' => 'int'],
                ['controller' => '', 'action' => 'speed', 'value' => '500', 'type' => 'int'],
            ],
        ];

        $mqttService->sendPhaseCommands('adc-001', $phase);
    }

    public function test_publish_to_device_rejects_empty_model()
    {
        Log::shouldReceive('error')->once();

        $device = Device::create([
            'name' => 'No Model',
            'model' => '',
            'ip' => '127.0.0.1',
            'port' => 1883,
            'is_active' => true,
        ]);

        $mqttService = $this->createMockMqttService();
        $mqttService->expects($this->never())->method('publishMessage');

        $mqttService->publishToDevice($device, 'tec', 'enable', '1');
    }
}
