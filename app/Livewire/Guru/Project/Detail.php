<?php

namespace App\Livewire\Guru\Project;

use App\Models\Project;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Detail extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $project;
    public $search = '';
    public $perPage = 25;
    public $filterGrade = '';
    public $isGuru = false;
    public $myStudentId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 25],
        'filterGrade' => ['except' => ''],
    ];

    public function mount(Project $project)
    {
        $teacher = Auth::user()->teacher;

        // Verify project belongs to the teacher's school
        if (!$teacher || $project->school_id !== $teacher->school_id) {
            abort(403, 'Unauthorized access to this project.');
        }

        $this->project = $project;
        $this->isGuru = $project->target == 'guru';

        // Check if current teacher is in this project
        if ($this->isGuru && $teacher->nip) {
            $myRecord = Student::where('school_id', $teacher->school_id)
                ->where('project_id', $this->project->id)
                ->where('nis', $teacher->nip)
                ->first();

            if ($myRecord) {
                $this->myStudentId = $myRecord->id;
            }
        }
    }

    public function backToProjects()
    {
        return redirect()->route('guru.project.index');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function downloadStudentPhotos($studentId)
    {
        return redirect()->route('guru.project.download-student', [
            'project' => $this->project->id,
            'student' => $studentId,
        ]);
    }

    public function downloadAllPhotos()
    {
        return redirect()->route('guru.project.download-all', [
            'project' => $this->project->id,
        ]);
    }

    public function render()
    {
        $teacher = Auth::user()->teacher;
        // Re-check just in case
        if (!$teacher || $this->project->school_id !== $teacher->school_id) {
            abort(403);
        }

        // Get available grades for filter
        $availableGrades = Student::where('school_id', $teacher->school_id)
            ->where('project_id', $this->project->id)
            ->distinct()
            ->pluck('grade')
            ->sort()
            ->toArray();

        // Build query
        $query = Student::where('school_id', $teacher->school_id)
            ->where('project_id', $this->project->id)
            ->withCount([
                'photos' => function ($q) {
                    $q->where('project_id', $this->project->id);
                }
            ]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('nis', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterGrade) {
            $query->where('grade', $this->filterGrade);
        }

        // Stats
        $totalStudents = Student::where('school_id', $teacher->school_id)
            ->where('project_id', $this->project->id)
            ->count();

        $withPhotoCount = Student::where('school_id', $teacher->school_id)
            ->where('project_id', $this->project->id)
            ->whereHas('photos', function ($q) {
                $q->where('project_id', $this->project->id);
            })
            ->count();

        $students = $query->orderBy('name')->paginate($this->perPage);

        return view('livewire.guru.project.detail', [
            'students' => $students,
            'availableGrades' => $availableGrades,
            'totalStudents' => $totalStudents,
            'withPhotoCount' => $withPhotoCount,
            'targetLabel' => $this->isGuru ? 'Guru' : 'Siswa',
        ])
            ->layout('layouts.dashboard')
            ->title('Detail Project - ' . $this->project->name);
    }
}
