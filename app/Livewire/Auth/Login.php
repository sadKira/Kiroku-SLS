<?php

namespace App\Livewire\Auth;

use App\Enums\UserRole;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

use App\Models\User;


#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string')]
    public string $username = '';

    // #[SensitiveParameter]
    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Handle an incoming authentication request.
     */
    public function login()
    {
        $this->validate();

        $this->ensureIsNotRateLimited();

        $user = User::where('username', $this->username)->first();

        if (! $user) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        if (! Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        // Successful login: clear rate limiter and regenerate session before redirecting
        RateLimiter::clear($this->throttleKey());
        Session::regenerate();


        if (Auth::user()->role == UserRole::Logger) {
            return redirect()->route('logger_dashboard')->with('success', 'Logged in successfully!');
        }

        if (Auth::user()->role == UserRole::Admin || Auth::user()->role == UserRole::SuperAdmin) {
            return redirect()->route('admin_dashboard')->with('success', 'Logged in successfully!');
        }

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
            'username' => __('auth.throttle', [
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
        return Str::transliterate(Str::lower($this->username) . '|' . request()->ip());
    }
}
