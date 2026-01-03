<?php

namespace App\Livewire\Public\Student;

use App\Models\Student;
use Livewire\Component;

class Profile extends Component
{
    public $student;
    public $photos = [];

    public function mount($token)
    {
        $this->student = Student::where('magic_token', $token)->with(['school', 'project.academicYear'])->firstOrFail();

        // Load photos for this student's project
        $this->photos = $this->student->photos()
            ->where('project_id', $this->student->project_id)
            ->get();
    }

    public function render()
    {
        return view('livewire.public.student.profile', [
            'photos' => $this->photos
        ])
            ->layout('layouts.guest')
            ->title('Profil Siswa - ' . $this->student->name);
    }
}
