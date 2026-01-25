<?php

namespace App\Livewire\Auth;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class AccountActivation extends Component
{
    public $step = 1;
    public $type = 'siswa'; // 'siswa' or 'guru'

    // Joint properties
    public $identifier; // NISN or NIP
    public $password;
    public $whatsapp; // Only for Siswa

    // Display info
    public $name;
    public $schoolName;
    public $isExistingUser = false; // For Guru context
    public $targetUserId;

    public function rules()
    {
        return [
            'identifier' => 'required|numeric',
            'password' => 'required|min:6',
            'whatsapp' => 'required|numeric|digits_between:10,13', // Required for both now
            'type' => 'required|in:siswa,guru',
        ];
    }

    public function updatedType()
    {
        $this->reset(['step', 'identifier', 'name', 'schoolName', 'password', 'whatsapp', 'isExistingUser', 'targetUserId']);
        $this->resetErrorBag();
    }

    public function checkAccount()
    {
        $this->validate([
            'identifier' => 'required|numeric',
            'type' => 'required|in:siswa,guru',
        ]);

        if ($this->type === 'siswa') {
            // 1. Check if user already exists
            if (User::where('username', $this->identifier)->exists()) {
                $this->addError('identifier', 'Akun dengan NISN ini sudah aktif. Silakan login.');
                return;
            }

            // 2. Check student data
            $student = Student::with('school')->where('nisn', $this->identifier)->first();

            if (!$student) {
                $this->addError('identifier', 'Data siswa dengan NISN ini tidak ditemukan.');
                return;
            }

            $this->name = $student->name;
            $this->schoolName = $student->school->name ?? 'Sekolah Tidak Diketahui';

        } else {
            // Guru Logic
            $teacher = Teacher::with('school', 'user')->where('nip', $this->identifier)->first();

            if (!$teacher) {
                $this->addError('identifier', 'Data guru dengan NIP ini tidak ditemukan.');
                return;
            }

            // Check One-Time Activation using is_activated flag
            if ($teacher->is_activated) {
                $this->addError('identifier', 'Akun guru sudah diaktivasi. Silakan login.');
                return;
            }

            // Also check if user exists (should exist)
            if (!$teacher->user) {
                // Determine what to do if user missing? (Edge case)
                // We assume user exists as per Admin logic.
                $this->addError('identifier', 'Data user guru tidak valid. Hubungi admin.');
                return;
            }

            $this->name = $teacher->user->name ?? 'Guru (Tanpa Nama)';
            $this->schoolName = $teacher->school->name ?? 'Sekolah Tidak Diketahui';
            $this->isExistingUser = true;
            $this->targetUserId = $teacher->user_id;
        }

        $this->step = 2;
    }

    public function resetStep()
    {
        $this->reset(['step', 'identifier', 'name', 'schoolName', 'password', 'whatsapp', 'isExistingUser', 'targetUserId']);
        // Keep type same
    }

    public function activate()
    {
        $this->validate([
            'password' => 'required|min:6',
            'whatsapp' => 'required|numeric|digits_between:10,13',
        ]);

        if ($this->step !== 2)
            return;

        $user = null;

        if ($this->type === 'siswa') {
            $student = Student::where('nisn', $this->identifier)->first();

            // Create User
            $user = User::create([
                'name' => $this->name,
                'username' => $this->identifier,
                'email' => $student->email ?? $this->identifier . '@student.masamuda.id',
                'password' => Hash::make($this->password),
                'role' => 'siswa',
                'is_active' => true,
            ]);

            // Update Student
            Student::where('nisn', $this->identifier)->update([
                'whatsapp' => $this->whatsapp
            ]);

        } else {
            // Guru - Update Existing User & Teacher
            $user = User::find($this->targetUserId);
            if ($user) {
                $user->update([
                    'password' => Hash::make($this->password),
                ]);
            }
        }

        if ($user) {
            Auth::login($user);
            return redirect()->route($this->type . '.dashboard');
        }
    }

    public function render()
    {
        return view('livewire.auth.account-activation')
            ->layout('layouts.auth')
            ->title('Aktivasi Akun');
    }
}
