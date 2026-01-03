<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class ChangePassword extends Component
{
    public $current_password;
    public $password;
    public $password_confirmation;

    public function render()
    {
        return view('livewire.auth.change-password')
            ->layout('layouts.dashboard');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|min:8|confirmed|different:current_password',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($this->password);
        $user->password_change_required = false;
        $user->save();

        session()->flash('message', 'Password berhasil diubah. Silakan lanjutkan.');
        
        return redirect()->route($user->role . '.dashboard');
    }
}
