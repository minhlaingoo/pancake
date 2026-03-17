<?php

use App\Models\Preset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['role_id' => 1]);
    $this->actingAs($this->user);
});

it('can create a preset', function () {
    Livewire::test(\App\Livewire\Presets\Create::class)
        ->set('name', 'Test Preset')
        ->set('version', '1.0')
        ->set('status', 'Draft')
        ->set('description', 'A test preset')
        ->set('commands', [
            [
                'controller' => 'tec',
                'action' => 'setpoint',
                'value' => '37.0',
                'type' => 'float',
                'delay' => 5,
                'retry_count' => 0,
                'timeout' => 30,
            ]
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('presets.index'));

    $this->assertDatabaseHas('presets', [
        'name' => 'Test Preset',
        'version' => '1.0',
    ]);
});

it('validates preset name is required', function () {
    Livewire::test(\App\Livewire\Presets\Create::class)
        ->set('name', '')
        ->set('commands', [
            ['controller' => 'tec', 'action' => 'setpoint', 'value' => '37', 'type' => 'float']
        ])
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

it('validates at least one command is required', function () {
    Livewire::test(\App\Livewire\Presets\Create::class)
        ->set('name', 'Empty Preset')
        ->set('version', '1.0')
        ->set('status', 'Draft')
        ->set('commands', [])
        ->call('save')
        ->assertHasErrors(['commands' => 'required']);
});

it('validates command controller and action are required', function () {
    Livewire::test(\App\Livewire\Presets\Create::class)
        ->set('name', 'Bad Commands')
        ->set('version', '1.0')
        ->set('status', 'Draft')
        ->set('commands', [
            ['controller' => '', 'action' => '', 'value' => '', 'type' => 'string']
        ])
        ->call('save')
        ->assertHasErrors(['commands.0.controller', 'commands.0.action']);
});

it('can update a preset', function () {
    $preset = Preset::create([
        'name' => 'Original',
        'version' => '1.0',
        'description' => 'Original desc',
        'status' => 'Draft',
        'author' => 'Test',
        'commands' => [
            ['controller' => 'tec', 'action' => 'setpoint', 'value' => '25', 'type' => 'float']
        ],
    ]);

    Livewire::test(\App\Livewire\Presets\Edit::class, ['preset' => $preset])
        ->set('name', 'Updated Preset')
        ->set('version', '2.0')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('presets.index'));

    $this->assertDatabaseHas('presets', [
        'id' => $preset->id,
        'name' => 'Updated Preset',
        'version' => '2.0',
    ]);
});

it('can delete a preset', function () {
    $preset = Preset::create([
        'name' => 'Delete Me',
        'version' => '1.0',
        'status' => 'Draft',
        'author' => 'Test',
        'commands' => [['controller' => 'tec', 'action' => 'enable', 'value' => '1', 'type' => 'int']],
    ]);

    Livewire::test(\App\Livewire\Presets\Index::class)
        ->call('delete', $preset->id);

    $this->assertDatabaseMissing('presets', ['id' => $preset->id]);
});

it('can add and remove commands', function () {
    $component = Livewire::test(\App\Livewire\Presets\Create::class);

    // Should start with one command from mount()
    expect($component->get('commands'))->toHaveCount(1);

    $component->call('addCommand');
    expect($component->get('commands'))->toHaveCount(2);

    $component->call('removeCommand', 0);
    expect($component->get('commands'))->toHaveCount(1);
});

it('resets action when controller changes', function () {
    $component = Livewire::test(\App\Livewire\Presets\Create::class)
        ->set('commands.0.controller', 'tec')
        ->set('commands.0.action', 'setpoint');

    // Change controller - action should reset
    $component->set('commands.0.controller', 'stirrer');

    expect($component->get('commands.0.action'))->toBe('');
});
