<?php

use App\Models\Device;
use App\Models\DeviceComponent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['role_id' => 1]);
    $this->actingAs($this->user);
});

it('can view devices index page', function () {
    $this->get(route('devices.index'))->assertStatus(200);
});

it('can create a device', function () {
    Livewire::test(\App\Livewire\Devices\Create::class)
        ->set('name', 'Test Device')
        ->set('model', 'adc-test-001')
        ->set('ip', '192.168.1.100')
        ->set('port', 1883)
        ->call('store')
        ->assertHasNoErrors()
        ->assertRedirect(route('devices.index'));

    $this->assertDatabaseHas('devices', [
        'name' => 'Test Device',
        'model' => 'adc-test-001',
        'ip' => '192.168.1.100',
        'port' => 1883,
    ]);
});

it('validates device creation fields', function () {
    Livewire::test(\App\Livewire\Devices\Create::class)
        ->set('name', '')
        ->set('model', '')
        ->set('ip', 'not-an-ip')
        ->set('port', 99999)
        ->call('store')
        ->assertHasErrors(['name', 'model', 'ip', 'port']);
});

it('validates port range on creation', function () {
    Livewire::test(\App\Livewire\Devices\Create::class)
        ->set('name', 'Device')
        ->set('model', 'adc-001')
        ->set('ip', '192.168.1.1')
        ->set('port', 0)
        ->call('store')
        ->assertHasErrors(['port']);
});

it('can view device detail page', function () {
    $device = Device::create([
        'name' => 'Detail Device',
        'model' => 'adc-detail',
        'ip' => '192.168.1.50',
        'port' => 1883,
        'is_active' => true,
    ]);

    $this->get(route('devices.detail', ['id' => $device->id]))
        ->assertStatus(200);
});

it('can update device settings', function () {
    $device = Device::create([
        'name' => 'Old Name',
        'model' => 'adc-old',
        'ip' => '192.168.1.1',
        'port' => 1883,
        'is_active' => true,
    ]);

    Livewire::test(\App\Livewire\Devices\Setting::class, ['id' => $device->id])
        ->set('name', 'New Name')
        ->set('model', 'adc-new')
        ->set('ip', '10.0.0.1')
        ->set('port', 8883)
        ->set('is_active', true)
        ->call('update')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('devices', [
        'id' => $device->id,
        'name' => 'New Name',
        'model' => 'adc-new',
        'ip' => '10.0.0.1',
        'port' => 8883,
    ]);
});

it('validates device settings update', function () {
    $device = Device::create([
        'name' => 'Device',
        'model' => 'adc-val',
        'ip' => '192.168.1.1',
        'port' => 1883,
        'is_active' => true,
    ]);

    Livewire::test(\App\Livewire\Devices\Setting::class, ['id' => $device->id])
        ->set('ip', 'invalid-ip')
        ->set('port', 99999)
        ->call('update')
        ->assertHasErrors(['ip', 'port']);
});

it('initializes telemetry data from device components', function () {
    $device = Device::create([
        'name' => 'Telemetry Device',
        'model' => 'adc-tel',
        'ip' => '192.168.1.1',
        'port' => 1883,
        'is_active' => true,
    ]);

    DeviceComponent::create([
        'device_id' => $device->id,
        'name' => 'temperature',
        'type' => 'tec',
        'unit' => '°C',
        'status' => 'online',
        'last_value' => '25.5',
        'is_sensor' => true,
    ]);

    $component = Livewire::test(\App\Livewire\Devices\Detail::class, ['id' => $device->id]);

    expect($component->get('telemetryData'))->toHaveCount(1);
});
