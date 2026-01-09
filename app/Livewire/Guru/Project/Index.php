<?php

namespace App\Livewire\Guru\Project;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Project;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $academic_year_id;

    public function mount()
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if ($activeYear) {
            $this->academic_year_id = $activeYear->id;
        }
    }

    public function render()
    {
        $teacher = Auth::user()->teacher;

        if (!$teacher || !$teacher->school_id) {
            return view('livewire.guru.project.index', [
                'projects' => collect([]),
                'academicYears' => AcademicYear::orderBy('start_date', 'desc')->get(),
            ])->layout('layouts.dashboard');
        }

        $query = Project::where('school_id', $teacher->school_id);

        if ($this->academic_year_id) {
            $query->where('academic_year_id', $this->academic_year_id);
        }

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        $projects = $query->withCount('students')->latest()->paginate($this->perPage);

        return view('livewire.guru.project.index', [
            'projects' => $projects,
            'academicYears' => AcademicYear::orderBy('start_date', 'desc')->get(),
        ])->layout('layouts.dashboard');
    }
}
