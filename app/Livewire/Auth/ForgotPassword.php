<?php

namespace App\Livewire\Auth;

use App\Models\Student;
use App\Models\User;
use App\Services\OneApiService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class ForgotPassword extends Component
{
    public $nisn;
    public $step = 1; // 1: input NISN, 2: konfirmasi & kirim
    public $studentName;
    public $whatsapp;
    public $whatsappMasked;

    protected function rules()
    {
        return [
            'nisn' => 'required|numeric',
        ];
    }

    protected $messages = [
        'nisn.required' => 'NISN wajib diisi.',
        'nisn.numeric' => 'NISN harus berupa angka.',
    ];

    public function checkNisn()
    {
        $this->validate(['nisn' => 'required|numeric']);

        // Cari user siswa dengan username = NISN
        $user = User::where('username', $this->nisn)->where('role', 'siswa')->first();

        if (! $user) {
            $this->addError('nisn', 'Akun dengan NISN ini tidak ditemukan atau belum diaktivasi.');
            return;
        }

        if (! $user->is_active) {
            $this->addError('nisn', 'Akun ini sedang tidak aktif. Hubungi administrator.');
            return;
        }

        // Ambil data student untuk dapatkan WhatsApp
        $student = Student::where('nisn', $this->nisn)->first();

        if (! $student || ! $student->whatsapp) {
            $this->addError('nisn', 'Nomor WhatsApp tidak ditemukan untuk akun ini. Hubungi administrator.');
            return;
        }

        $this->studentName = $student->name;
        $this->whatsapp = $student->whatsapp;
        $this->whatsappMasked = $this->maskPhone($student->whatsapp);
        $this->step = 2;
    }

    public function sendNewPassword()
    {
        if ($this->step !== 2) {
            return;
        }

        $user = User::where('username', $this->nisn)->where('role', 'siswa')->first();

        if (! $user) {
            $this->addError('nisn', 'Akun tidak ditemukan.');
            $this->step = 1;
            return;
        }

        // Generate password baru (8 karakter, mix huruf & angka)
        $newPassword = Str::random(8);

        // Update password user
        $user->update([
            'password' => Hash::make($newPassword),
            'password_change_required' => true,
        ]);

        // Kirim via WhatsApp
        $message = "*Masamuda - Reset Password*\n\n"
            . "Halo *{$this->studentName}*,\n\n"
            . "Password baru Anda: *{$newPassword}*\n\n"
            . "Silakan login menggunakan password di atas. Demi keamanan, Anda akan diminta mengganti password setelah login.\n\n"
            . "Jika Anda tidak merasa meminta reset password, abaikan pesan ini.";

        $oneApi = app(OneApiService::class);
        $result = $oneApi->sendMessage($this->whatsapp, $message);

        if (! $result['success']) {
            $this->addError('nisn', 'Gagal mengirim password baru via WhatsApp. Silakan coba lagi.');
            return;
        }

        $this->step = 3; // Success
    }

    public function resetForm()
    {
        $this->reset(['nisn', 'step', 'studentName', 'whatsapp', 'whatsappMasked']);
        $this->resetErrorBag();
    }

    private function maskPhone(string $phone): string
    {
        $len = strlen($phone);
        if ($len <= 4) {
            return str_repeat('*', $len);
        }
        return substr($phone, 0, 2) . str_repeat('*', $len - 6) . substr($phone, -4);
    }

    public function render()
    {
        $loginTitle = \App\Models\Setting::where('key', 'login_title')->value('value') ?? 'Lupa Password';
        return view('livewire.auth.forgot-password')
            ->layout('layouts.auth')
            ->title('Lupa Password');
    }
}
