<?php

namespace App\Livewire\Sekolah;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use App\Models\AuditTrail;

class Dashboard extends Component
{
    public $showPasswordChangeModal = false;
    
    // Password Change Properties
    public $current_password;
    public $password;
    public $password_confirmation;

    public function mount()
    {
        if (Auth::check() && Auth::user()->password_change_required) {
            $this->showPasswordChangeModal = true;
        }
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'different:current_password',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
            ],
        ], [
            'password.regex' => 'Password baru harus mengandung huruf besar, huruf kecil, dan angka.',
            'password.different' => 'Password baru tidak boleh sama dengan password lama.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($this->password);
        $user->password_change_required = false;
        $user->save();

        // Audit Log
        AuditTrail::create([
            'user_id' => $user->id,
            'activity' => 'password_change',
            'description' => 'User changed password via forced modal',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->showPasswordChangeModal = false;
        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Sukses!',
            'text' => 'Password berhasil diubah.',
        ]);
    }

    public function render()
    {
        return view('livewire.sekolah.dashboard')->layout('layouts.dashboard');
    }
}
