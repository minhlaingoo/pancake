<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;

test('guest user will go to login page', function () {
    $response = $this->get('/');
    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});

test('user can login with correct credential', function () {
    $user = createSampleUser();

    $response = $this->post(route('login'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('user cannot login with incorrect credential', function () {
    createSampleUser();

    $response = $this->post(route('login'), [
        'email' => 'test@exampe.com',
        'password' => 'wrongpwd',
    ]);

    $response->assertSessionHasErrors();
});

test('authenticated user can logout', function () {
    $user = createSampleUser();

    $this->actingAs($user);

    $response = $this->post(route('logout'));

    $response->assertRedirect('/'); // currently using Auth::routes()

    $this->assertGuest();
});
