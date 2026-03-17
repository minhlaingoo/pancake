<?php

use App\Models\Protocol;
use App\Models\Preset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['role_id' => 1]);
    $this->actingAs($this->user);
});

it('can load final lab page with existing protocol', function () {
    $protocol = Protocol::create([
        'sample_id' => 'FL-001',
        'description' => 'Final lab test',
        'value' => json_encode(['mAb' => ['volume' => 10]]),
        'phases' => [],
    ]);

    $this->get(route('protocols.final-lab', ['sample_id' => 'FL-001']))
        ->assertStatus(200);
});

it('redirects when protocol not found', function () {
    Livewire::test(\App\Livewire\Protocols\FinalLab::class, ['sample_id' => 'NONEXISTENT'])
        ->assertRedirect(route('protocols.create'));
});

it('can add a phase', function () {
    $protocol = Protocol::create([
        'sample_id' => 'PHASE-001',
        'description' => 'Phase test',
        'value' => json_encode([]),
        'phases' => [],
    ]);

    $component = Livewire::test(\App\Livewire\Protocols\FinalLab::class, ['sample_id' => 'PHASE-001']);

    // Should have End phase automatically
    $initialCount = count($component->get('phases'));

    $component
        ->set('phaseFormData.label', 'Incubation')
        ->set('phaseFormData.duration', 300)
        ->call('addPhase');

    expect($component->get('phases'))->toHaveCount($initialCount + 1);
});

it('can add commands to a phase', function () {
    $protocol = Protocol::create([
        'sample_id' => 'CMD-001',
        'description' => 'Command test',
        'value' => json_encode([]),
        'phases' => [
            ['id' => 'test-phase', 'label' => 'Test', 'duration' => 60, 'loop' => 1, 'commands' => []],
        ],
    ]);

    $component = Livewire::test(\App\Livewire\Protocols\FinalLab::class, ['sample_id' => 'CMD-001']);

    $component->call('addCommand', 0);
    expect($component->get('phases.0.commands'))->toHaveCount(1);
});

it('can remove a command from a phase', function () {
    $protocol = Protocol::create([
        'sample_id' => 'RMCMD-001',
        'description' => 'Remove cmd test',
        'value' => json_encode([]),
        'phases' => [
            [
                'id' => 'rm-phase',
                'label' => 'Test',
                'duration' => 60,
                'loop' => 1,
                'commands' => [
                    ['controller' => 'tec', 'action' => 'setpoint', 'value' => '37', 'type' => 'float'],
                    ['controller' => 'stirrer', 'action' => 'speed', 'value' => '500', 'type' => 'int'],
                ],
            ],
        ],
    ]);

    $component = Livewire::test(\App\Livewire\Protocols\FinalLab::class, ['sample_id' => 'RMCMD-001']);
    $component->call('removeCommand', 0, 0);

    expect($component->get('phases.0.commands'))->toHaveCount(1);
});

it('cannot remove end phase', function () {
    $protocol = Protocol::create([
        'sample_id' => 'END-001',
        'description' => 'End phase test',
        'value' => json_encode([]),
        'phases' => [
            ['id' => 'end-phase', 'label' => 'End Of Protocol', 'duration' => 0, 'loop' => 1, 'is_end' => true, 'commands' => []],
        ],
    ]);

    $component = Livewire::test(\App\Livewire\Protocols\FinalLab::class, ['sample_id' => 'END-001']);
    $component->call('removePhase', 'end-phase');

    // End phase should still exist
    $phases = $component->get('phases');
    $hasEnd = collect($phases)->contains(fn($p) => ($p['is_end'] ?? false) === true);
    expect($hasEnd)->toBeTrue();
});

it('can save protocol with phases', function () {
    $protocol = Protocol::create([
        'sample_id' => 'SAVE-001',
        'description' => 'Save test',
        'value' => json_encode(['mAb' => ['volume' => 0]]),
        'phases' => [
            [
                'id' => 'save-phase',
                'label' => 'Step 1',
                'duration' => 120,
                'loop' => 2,
                'commands' => [
                    ['controller' => 'tec', 'action' => 'setpoint', 'value' => '37', 'type' => 'float'],
                ],
            ],
            ['id' => 'end-phase', 'label' => 'End Of Protocol', 'duration' => 0, 'loop' => 1, 'is_end' => true, 'commands' => []],
        ],
    ]);

    Livewire::test(\App\Livewire\Protocols\FinalLab::class, ['sample_id' => 'SAVE-001'])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('protocols.index'));
});

it('rejects save when command has missing controller', function () {
    $protocol = Protocol::create([
        'sample_id' => 'BADCMD-001',
        'description' => 'Bad command test',
        'value' => json_encode([]),
        'phases' => [
            [
                'id' => 'bad-phase',
                'label' => 'Bad Step',
                'duration' => 60,
                'loop' => 1,
                'commands' => [
                    ['controller' => '', 'action' => '', 'value' => '', 'type' => 'string'],
                ],
            ],
        ],
    ]);

    Livewire::test(\App\Livewire\Protocols\FinalLab::class, ['sample_id' => 'BADCMD-001'])
        ->call('save')
        ->assertSessionHas('error');
});

it('can apply a preset to a phase', function () {
    $preset = Preset::create([
        'name' => 'TEC Preset',
        'version' => '1.0',
        'status' => 'Active',
        'author' => 'Test',
        'commands' => [
            ['controller' => 'tec', 'action' => 'setpoint', 'value' => '37', 'type' => 'float'],
            ['controller' => 'tec', 'action' => 'enable', 'value' => '1', 'type' => 'int'],
        ],
    ]);

    $protocol = Protocol::create([
        'sample_id' => 'PRESET-001',
        'description' => 'Preset apply test',
        'value' => json_encode([]),
        'phases' => [
            ['id' => 'p1', 'label' => 'Step 1', 'duration' => 60, 'loop' => 1, 'commands' => []],
            ['id' => 'end', 'label' => 'End Of Protocol', 'duration' => 0, 'loop' => 1, 'is_end' => true, 'commands' => []],
        ],
    ]);

    $component = Livewire::test(\App\Livewire\Protocols\FinalLab::class, ['sample_id' => 'PRESET-001'])
        ->set('appliedPresetId', $preset->id)
        ->call('applyPreset', 0);

    expect($component->get('phases.0.commands'))->toHaveCount(2);
});
