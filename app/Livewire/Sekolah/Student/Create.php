<?php

namespace App\Livewire\Sekolah\Student;

use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Create extends Component
{
    public $nis;
    public $nisn;
    public $name;
    public $whatsapp;
    public $email;
    public $grade;
    public $class_name;
    public $address;
    public $birth_date;
    public $major;
    
    public $school;
    public $availableMajors = [];
    public $isSmp = false;

    public function mount()
    {
        // Get the school associated with the authenticated user
        // Assuming user has a 'school' relationship or we find it via user_id
        $this->school = School::where('user_id', Auth::id())->firstOrFail();
        
        $this->isSmp = $this->school->jenjang === 'smp';

        if ($this->isSmp) {
            $this->major = 'UMUM';
        } else {
            // Define majors for SMA/SMK
            $this->availableMajors = [
                'IPA',
                'IPS',
                'Bahasa',
                'Teknik Komputer dan Jaringan',
                'Rekayasa Perangkat Lunak',
                'Multimedia',
                'Akuntansi',
                'Perkantoran',
                'Pemasaran',
                'Perhotelan',
                'Tata Boga',
                'Tata Busana',
                'Teknik Kendaraan Ringan',
                'Teknik Sepeda Motor',
                // Add more as needed
            ];
        }
    }

    public function save()
    {
        $this->validate([
            'nis' => 'required|string|max:50',
            'nisn' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'grade' => 'required|string|max:10',
            'class_name' => 'required|string|max:50',
            'address' => 'required|string',
            'birth_date' => 'required|date',
            'major' => 'required|string',
        ]);

        Student::create([
            'school_id' => $this->school->id,
            'nis' => $this->nis,
            'nisn' => $this->nisn,
            'name' => $this->name,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'grade' => $this->grade,
            'class_name' => $this->class_name,
            'address' => $this->address,
            'birth_date' => $this->birth_date,
            'major' => $this->major,
        ]);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data Siswa Berhasil Disimpan.',
        ]);

        $this->reset(['nis', 'nisn', 'name', 'whatsapp', 'email', 'grade', 'class_name', 'address', 'birth_date']);
        
        if (!$this->isSmp) {
            $this->reset('major');
        } else {
            $this->major = 'UMUM';
        }
    }

    public function resetForm()
    {
        $this->reset(['nis', 'nisn', 'name', 'whatsapp', 'email', 'grade', 'class_name', 'address', 'birth_date']);
        
        if (!$this->isSmp) {
            $this->reset('major');
        } else {
            $this->major = 'UMUM';
        }
    }

    public function render()
    {
        return view('livewire.sekolah.student.create')
            ->layout('layouts.dashboard')
            ->title('Input Data Siswa Baru');
    }
}
