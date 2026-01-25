<?php

namespace App\Livewire\Auth;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class StudentActivation extends Component
{
    public $nisn;
    public $password;
    public $whatsapp;

    public $step = 1;
    public $studentName;
    public $schoolName;

    protected $rules = [
        'nisn' => 'required|numeric',
        'password' => 'required|min:6',
        'whatsapp' => 'required|numeric|digits_between:10,13',
    ];

    public function checkNisn()
    {
        $this->validate(['nisn' => 'required|numeric']);

        // 1. Check if user already exists
        if (User::where('username', $this->nisn)->exists()) {
            $this->addError('nisn', 'Akun dengan NISN ini sudah aktif. Silakan login.');
            return;
        }

        // 2. Check if student data exists
        $student = Student::with('school')->where('nisn', $this->nisn)->first();

        if (!$student) {
            $this->addError('nisn', 'Data siswa dengan NISN ini tidak ditemukan.');
            return;
        }

        $this->studentName = $student->name;
        $this->schoolName = $student->school->name ?? 'Sekolah Tidak Diketahui';
        $this->step = 2;
    }

    public function resetStep()
    {
        $this->reset(['step', 'studentName', 'schoolName', 'password', 'whatsapp']);
    }

    public function activate()
    {
        $this->validate([
            'password' => 'required|min:6',
            'whatsapp' => 'required|numeric|digits_between:10,13',
        ]);

        // Double check just in case
        if ($this->step !== 2) {
            return;
        }

        // 3. Create User
        // We need to pick a name. Let's take the name from the first student record found.
        $studentData = Student::where('nisn', $this->nisn)->first();

        $user = User::create([
            'name' => $studentData->name,
            'username' => $this->nisn,
            'email' => $studentData->email ?? $this->nisn . '@student.masamuda.id',
            'password' => Hash::make($this->password),
            'role' => 'siswa',
            'is_active' => true,
        ]);

        // 4. Update WhatsApp for ALL student records with this NISN
        Student::where('nisn', $this->nisn)->update([
            'whatsapp' => $this->whatsapp
        ]);

        // 5. Login
        Auth::login($user);

        // 6. Redirect
        return redirect()->route('siswa.dashboard');
    }

    public function render()
    {
        return view('livewire.auth.student-activation')
            ->layout('layouts.auth')
            ->title('Aktivasi Akun Siswa');
    }
}
