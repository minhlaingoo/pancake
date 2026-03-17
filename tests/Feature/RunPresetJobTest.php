<?php

namespace Tests\Feature;

use App\Jobs\RunPresetJob;
use App\Models\Device;
use App\Models\Preset;
use App\Services\MqttService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class RunPresetJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_sends_all_preset_commands()
    {
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('debug')->atLeast()->once();

        $device = Device::create([
            'name' => 'Job Device',
            'model' => 'adc-job',
            'ip' => '127.0.0.1',
            'port' => 1883,
            'is_active' => true,
        ]);

        $preset = Preset::create([
            'name' => 'Job Preset',
            'version' => '1.0',
            'status' => 'Active',
            'author' => 'Test',
            'commands' => [
                ['controller' => 'tec', 'action' => 'enable', 'value' => '1', 'type' => 'int', 'delay' => 0, 'retry_count' => 0, 'timeout' => 30],
                ['controller' => 'stirrer', 'action' => 'speed', 'value' => '500', 'type' => 'int', 'delay' => 0, 'retry_count' => 0, 'timeout' => 30],
            ],
        ]);

        $mqttService = $this->getMockBuilder(MqttService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['publishMessage'])
            ->getMock();

        $mqttService->expects($this->exactly(2))
            ->method('publishMessage');

        $job = new RunPresetJob($device->id, $preset->id);
        $job->handle($mqttService);
    }

    public function test_job_skips_when_device_not_found()
    {
        Log::shouldReceive('warning')->atLeast()->once();

        $preset = Preset::create([
            'name' => 'Orphan Preset',
            'version' => '1.0',
            'status' => 'Active',
            'author' => 'Test',
            'commands' => [
                ['controller' => 'tec', 'action' => 'enable', 'value' => '1', 'type' => 'int'],
            ],
        ]);

        $mqttService = $this->getMockBuilder(MqttService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['publishMessage'])
            ->getMock();

        $mqttService->expects($this->never())
            ->method('publishMessage');

        $job = new RunPresetJob(99999, $preset->id);
        $job->handle($mqttService);
    }

    public function test_job_skips_empty_model_device()
    {
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('error')->atLeast()->once();

        $device = Device::create([
            'name' => 'No Model Device',
            'model' => '',
            'ip' => '127.0.0.1',
            'port' => 1883,
            'is_active' => true,
        ]);

        $preset = Preset::create([
            'name' => 'Model Preset',
            'version' => '1.0',
            'status' => 'Active',
            'author' => 'Test',
            'commands' => [
                ['controller' => 'tec', 'action' => 'enable', 'value' => '1', 'type' => 'int'],
            ],
        ]);

        $mqttService = $this->getMockBuilder(MqttService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['publishMessage'])
            ->getMock();

        $mqttService->expects($this->never())
            ->method('publishMessage');

        $job = new RunPresetJob($device->id, $preset->id);
        $job->handle($mqttService);
    }

    public function test_job_skips_incomplete_commands()
    {
        Log::shouldReceive('info')->atLeast()->once();
        Log::shouldReceive('debug')->atLeast()->once();
        Log::shouldReceive('warning')->atLeast()->once();

        $device = Device::create([
            'name' => 'Skip Device',
            'model' => 'adc-skip',
            'ip' => '127.0.0.1',
            'port' => 1883,
            'is_active' => true,
        ]);

        $preset = Preset::create([
            'name' => 'Skip Preset',
            'version' => '1.0',
            'status' => 'Active',
            'author' => 'Test',
            'commands' => [
                ['controller' => 'tec', 'action' => 'enable', 'value' => '1', 'type' => 'int', 'delay' => 0],
                ['controller' => '', 'action' => '', 'value' => '', 'type' => 'string', 'delay' => 0],
            ],
        ]);

        $mqttService = $this->getMockBuilder(MqttService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['publishMessage'])
            ->getMock();

        // Only 1 valid command should be published
        $mqttService->expects($this->once())
            ->method('publishMessage');

        $job = new RunPresetJob($device->id, $preset->id);
        $job->handle($mqttService);
    }
}
