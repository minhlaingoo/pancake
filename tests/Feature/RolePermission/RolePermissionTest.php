<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use Database\Factories\RoleFactory;
use Database\Factories\RolePermissionFactory;
use Database\Seeders\DefaultRolePermissionSeeder;
use Database\Seeders\TestingSeeder;
use Illuminate\Database\Seeder;
use Livewire\Livewire;
use Illuminate\Foundation\Testing\RefreshDatabase;


uses(RefreshDatabase::class);

it('can open role permission page', function () {
    $user = User::factory()->create(['role_id' => 1]);
    $this->actingAs($user);
    $response = $this->get(route('role-permissions.index'));
    $response->assertStatus(200);
});

it('can create role and permission', function () {
    $user = User::factory()->create();
    $this->actingAs($user);
    $this->get(route('role-permissions.create'))->assertStatus(200);

    $response = Livewire::test('role-permissions.create')
        ->set('role', 'Testing')
        ->set('selected_permissions', [1, 2, 3, 4])
        ->call('store');

    $role = Role::where('name', 'Testing')->first();

    $this->assertCount(4, $role->permissions);

    $this->assertDatabaseHas('roles', [
        'name' => 'Testing'
    ]);

    $response->assertRedirect(route('role-permissions.index'));
});

it('can update role and permission', function () {
    $user = User::factory()->create();
    $role = Role::factory()->create();
    $permissions = Permission::all();
    $role->permissions()->attach($permissions->pluck('id')->toArray());
    $this->actingAs($user);
    $this->get(route('role-permissions.edit', ['role' => $role->id]))->assertStatus(200);

    Livewire::test(App\Livewire\RolePermissions\Edit::class, ['role' => $role->id])
        ->set('role', 'Testing Updated') 
        ->set('selected_permissions', [1,2,3]) 
        ->call('update') 
        ->assertRedirect(route('role-permissions.index'))
        ->assertSet('selected_permissions', [1,2,3]);

    $this->assertCount(3, $role->permissions);

    $this->assertDatabaseHas('roles', [
        'id' => $role->id,
        'name' => 'Testing Updated'
    ]);
});
