<?php

namespace App\Livewire\Guru\Photo;

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

        // Logic: Teachers participating in projects are stored in 'students' table with NIS == NIP.
        // We find all 'Student' records where matches the teacher's NIP (username).

        $nips = Student::where('nis', $user->username)->pluck('id');

        if ($nips->isNotEmpty()) {
            $this->groupedPhotos = StudentPhoto::whereIn('student_id', $nips)
                ->with('project')
                ->get()
                ->groupBy(function ($photo) {
                    return $photo->project->name ?? 'Project Tanpa Nama';
                })
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.guru.photo.index') // We might be able to reuse 'siswa.photo.index' if generic?
            // Actually, copying is safer to avoid role logic in view.
            ->layout('layouts.dashboard')
            ->title('Galeri Foto Saya');
    }
}
