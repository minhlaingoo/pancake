<?php

use App\Models\Protocol;
use App\Models\ProtocolProcess;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create(['role_id' => 1]);
    $this->actingAs($this->user);
});

it('can view protocols index page', function () {
    $this->get(route('protocols.index'))->assertStatus(200);
});

it('can create a protocol', function () {
    Livewire::test(\App\Livewire\Protocols\Create::class)
        ->set('sample_id', 'SAMPLE-001')
        ->set('description', 'Test protocol description')
        ->call('finalizeProtocol')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('protocols', [
        'sample_id' => 'SAMPLE-001',
        'description' => 'Test protocol description',
    ]);
});

it('validates sample_id is required on create', function () {
    Livewire::test(\App\Livewire\Protocols\Create::class)
        ->set('sample_id', '')
        ->call('finalizeProtocol')
        ->assertHasErrors(['sample_id' => 'required']);
});

it('can delete a protocol', function () {
    $protocol = Protocol::create([
        'sample_id' => 'DEL-001',
        'description' => 'To be deleted',
        'value' => json_encode([]),
        'phases' => [],
    ]);

    Livewire::test(\App\Livewire\Protocols\Index::class)
        ->call('delete', $protocol->id);

    $this->assertDatabaseMissing('protocols', ['id' => $protocol->id]);
});

it('can create a protocol process from index', function () {
    $protocol = Protocol::create([
        'sample_id' => 'PROC-001',
        'description' => 'Process test',
        'value' => json_encode([]),
        'phases' => [],
    ]);

    $response = Livewire::test(\App\Livewire\Protocols\Index::class)
        ->call('createProcess', $protocol->id);

    $response->assertRedirect();
});

it('can edit protocol setup data', function () {
    $protocol = Protocol::create([
        'sample_id' => 'EDIT-001',
        'description' => 'Original',
        'value' => json_encode(['mAb' => ['volume' => 0]]),
        'phases' => [],
    ]);

    Livewire::test(\App\Livewire\Protocols\Edit::class, ['sample_id' => 'EDIT-001'])
        ->set('description', 'Updated description')
        ->call('updateProtocol')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('protocols', [
        'sample_id' => 'EDIT-001',
        'description' => 'Updated description',
    ]);
});

it('validates protocol edit fields', function () {
    $protocol = Protocol::create([
        'sample_id' => 'VAL-001',
        'description' => 'Validation test',
        'value' => json_encode([]),
        'phases' => [],
    ]);

    Livewire::test(\App\Livewire\Protocols\Edit::class, ['sample_id' => 'VAL-001'])
        ->set('sample_id', '')
        ->call('updateProtocol')
        ->assertHasErrors(['sample_id' => 'required']);
});
