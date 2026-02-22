<?php

namespace App\Livewire\Auth;

use Exception;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public bool $agree = false;

    /**
     * Handle an incoming authentication request.
     */

    protected $rules = [
        'email' => 'required|string|email',
        'password' => 'required|min:4',
    ];
    public function mount(){
        setting('general');
    }

    public function login()
    {

        $validated_data = $this->validate();

        $this->ensureIsNotRateLimited();

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            activity('authentication')
                ->log("Too Many Log in attempts has made")
                ->event('login')
                ->properties([
                    ...$validated_data
                ])
                ->status('fail')
                ->save();
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        activity('authentication')
            ->log("Log in has been made")
            ->event('login')
            ->properties([
                'email' => $this->email
            ])
            ->status('success')
            ->save();
        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
        // $this->redirect(route('dashboard'));
    }


    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email) . '|' . request()->ip());
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.auth');
    }
}
