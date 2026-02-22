<?php


use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);


it('can create a user', function () {
    $role = Role::factory()->create();

    Livewire::test('users.create')
        ->set('name', 'John Doe')
        ->set('email', 'john@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->set('role_id', $role->id)
        ->call('store')
        ->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role_id' => $role->id,
    ]);
});

it('cannot go to admin route', function (){
    $admin = User::factory()->create();
    $this->actingAs($admin  );
    $this->get(route('users.edit', ['id' => $admin->id]))
        ->assertStatus(404);

    
});

it('can update a user', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $role = Role::factory()->create();

    $this->actingAs($user);

    $response = Livewire::test('users.edit', ['id' => $user->id])
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('role_id', $role->id)
        ->set('is_active', true)
        ->call('update');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'role_id' => $role->id,
    ]);
});

it('can delete a user', function () {
    $user = User::factory()->create();
    $target_user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test(\App\Livewire\Users\Index::class)
        ->call('delete', $target_user->id);

    $this->assertDatabaseMissing('users', [
        'id' => $target_user->id,
    ]);
});

it('validates user creation', function () {
    $role = Role::factory()->create();

    Livewire::test(\App\Livewire\Users\Create::class)
        ->set('name', '')
        ->set('email', 'invalid-email')
        ->set('password', 'short')
        ->set('password_confirmation', 'mismatch')
        ->set('role_id', '')
        ->call('store')
        ->assertHasErrors([
            'name' => 'required',
            'email' => 'email',
            'password' => 'min',
            'password' => 'confirmed',
            'role_id' => 'required',
        ]);
});

it('validates user update', function () {
    $admin = User::factory()->create();
    $user = User::factory()->create();
    $role = Role::factory()->create();

    Livewire::test(\App\Livewire\Users\Edit::class, ['id' => $user->id])
        ->set('name', '')
        ->set('email', 'invalid-email')
        ->set('password', 'short')
        ->set('password_confirmation', 'mismatch')
        ->set('role_id', '')
        ->call('update')
        ->assertHasErrors([
            'name' => 'required',
            'email' => 'email',
            'password' => 'min',
            'password' => 'confirmed',
            'role_id' => 'required',
        ]);
});
