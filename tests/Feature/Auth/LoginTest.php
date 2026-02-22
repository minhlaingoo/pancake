<?php
use App\Livewire\Auth\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Livewire;
use App\Models\User;

uses(RefreshDatabase::class);

test('login validation errors are shown', function () {
    Livewire::test(Login::class)
        ->call('login')
        ->assertHasErrors(['email', 'password']);
});


test('login fails with incorrect credentials', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'wrongpassword')
        ->call('login')
        ->assertHasErrors(['email' => __('auth.failed')]);
});


test('rate limiting works after too many failed login attempts', function () {
    $email = 'test@example.com';
    $ip = '127.0.0.1';
    $throttleKey = Str::transliterate(Str::lower($email) . '|' . $ip);

    RateLimiter::clear($throttleKey);

    for ($i = 0; $i < 5; $i++) {
        Livewire::test(Login::class)
            ->set('email', $email)
            ->set('password', 'wrongpassword')
            ->call('login');
    }

    Livewire::test(Login::class)
        ->set('email', $email)
        ->set('password', 'wrongpassword')
        ->call('login')
        ->assertHasErrors(['email' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($throttleKey), 'minutes' => ceil(RateLimiter::availableIn($throttleKey) / 60)])]);
});


test('user can login successfully', function () {
    $user = User::factory()->create(['password' => bcrypt('password123')]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password123')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
    expect(Session::isStarted())->toBeTrue();
});

