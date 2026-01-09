<?php

namespace App\Livewire\Guru;

use Livewire\Component;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        $projectsCount = 0;
        $schoolName = '';

        if ($teacher && $teacher->school) {
            $schoolName = $teacher->school->name;
            $projectsCount = Project::where('school_id', $teacher->school_id)->count();
        }

        return view('livewire.guru.dashboard', [
            'projectsCount' => $projectsCount,
            'schoolName' => $schoolName,
        ])->layout('layouts.dashboard');
    }
}
