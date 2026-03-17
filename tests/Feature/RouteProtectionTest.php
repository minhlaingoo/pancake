<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('redirects unauthenticated users to login', function () {
    $routes = [
        '/dashboard',
        '/devices',
        '/protocols',
        '/presets',
        '/users',
        '/role-permissions',
        '/activity-logs',
        '/broker-setting',
        '/setting',
    ];

    foreach ($routes as $route) {
        $this->get($route)->assertRedirect(route('login'));
    }
});

it('authenticated user can access dashboard', function () {
    $user = User::factory()->create(['role_id' => 1]);
    $this->actingAs($user);

    $this->get(route('dashboard'))->assertStatus(200);
});

it('authenticated user can access settings', function () {
    $user = User::factory()->create(['role_id' => 1]);
    $this->actingAs($user);

    $this->get(route('setting'))->assertStatus(200);
});

it('authenticated user can access broker settings', function () {
    $user = User::factory()->create(['role_id' => 1]);
    $this->actingAs($user);

    $this->get(route('broker-setting'))->assertStatus(200);
});
