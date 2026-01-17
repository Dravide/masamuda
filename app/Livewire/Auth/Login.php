<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\AuditTrail;
use Illuminate\Http\Request;

class Login extends Component
{
    public $username;
    public $password;
    public $remember = false;

    protected $rules = [
        'username' => 'required|regex:/^\S*$/',
        'password' => 'required|min:8',
    ];

    protected $messages = [
        'username.regex' => 'Username tidak boleh mengandung spasi.',
        'username.required' => 'Username wajib diisi.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 8 karakter.',
    ];

    public function login()
    {
        $this->validate();

        $ip = request()->ip();
        $userAgent = request()->userAgent();

        // Rate Limiting
        $key = 'login-attempt:' . $this->username . '|' . $ip;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            $this->addError('username', "Terlalu banyak percobaan login. Silakan coba lagi dalam $seconds detik.");
            return;
        }

        if (Auth::attempt(['username' => $this->username, 'password' => $this->password], $this->remember)) {
            RateLimiter::clear($key);
            session()->regenerate();

            // Audit Trail
            AuditTrail::create([
                'user_id' => Auth::id(),
                'activity' => 'login',
                'description' => 'User logged in',
                'ip_address' => $ip,
                'user_agent' => $userAgent,
            ]);

            $role = Auth::user()->role;
            return redirect()->intended(route($role . '.dashboard'));
        }

        RateLimiter::hit($key);
        $this->addError('username', 'Username atau password salah.');
    }

    public function render()
    {
        $loginTitle = \App\Models\Setting::where('key', 'login_title')->value('value') ?? 'Selamat Datang';
        return view('livewire.auth.login')
            ->layout('layouts.auth')
            ->title($loginTitle);
    }
}
