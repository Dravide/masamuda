<?php

namespace App\Livewire\Siswa\Photo;

use App\Models\Student;
use App\Models\StudentPhoto;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public $groupedPhotos = [];

    public function mount()
    {
        $user = Auth::user();

        // Find ALL student records associated with this NISN (since NISN is the unique identifier for activation)
        // Ideally we should show photos from ALL projects this NISN is involved in.

        // Get all student IDs with this NISN
        $studentIds = Student::where('nisn', $user->username)->pluck('id');

        if ($studentIds->isNotEmpty()) {
            $this->groupedPhotos = StudentPhoto::whereIn('student_id', $studentIds)
                ->with('project', 'student') // Load project and student (to distinguish if multiple records)
                ->get()
                ->groupBy(function ($photo) {
                    return $photo->project->name ?? 'Project Tanpa Nama';
                })
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.siswa.photo.index')
            ->layout('layouts.dashboard')
            ->title('Galeri Foto Saya');
    }
}
