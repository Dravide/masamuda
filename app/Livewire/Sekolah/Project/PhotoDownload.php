<?php

namespace App\Livewire\Sekolah\Project;

use App\Models\Project;
use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class PhotoDownload extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $project = null;
    public $school;
    public $search = '';
    public $perPage = 25;
    public $filterGrade = '';
    public $projects = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 25],
        'filterGrade' => ['except' => ''],
    ];

    public function mount(?Project $project = null)
    {
        $this->school = School::where('user_id', Auth::id())->firstOrFail();

        // Load active projects for this school
        $this->projects = Project::where('school_id', $this->school->id)
            ->whereIn('status', ['active', 'completed'])
            ->with('academicYear')
            ->withCount([
                'students' => function ($q) {
                    $q->whereHas('photos');
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($project && $project->id) {
            // Verify project belongs to this school
            if ($project->school_id !== $this->school->id) {
                abort(403);
            }
            $this->project = $project;
        }
    }

    public function selectProject($projectId)
    {
        return redirect()->route('sekolah.foto.show', $projectId);
    }

    public function backToProjects()
    {
        return redirect()->route('sekolah.foto.index');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function downloadStudentPhotos($studentId)
    {
        return redirect()->route('sekolah.foto.download-student', [
            'project' => $this->project->id,
            'student' => $studentId,
        ]);
    }

    public function downloadAllPhotos()
    {
        return redirect()->route('sekolah.foto.download-all', [
            'project' => $this->project->id,
        ]);
    }

    public function render()
    {
        $data = [
            'projects' => $this->projects,
        ];

        if ($this->project) {
            // Get available grades for filter
            $availableGrades = Student::where('school_id', $this->school->id)
                ->where('project_id', $this->project->id)
                ->distinct()
                ->pluck('grade')
                ->sort()
                ->toArray();

            // Build query
            $query = Student::where('school_id', $this->school->id)
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
            $totalStudents = Student::where('school_id', $this->school->id)
                ->where('project_id', $this->project->id)
                ->count();

            $withPhotoCount = Student::where('school_id', $this->school->id)
                ->where('project_id', $this->project->id)
                ->whereHas('photos', function ($q) {
                    $q->where('project_id', $this->project->id);
                })
                ->count();

            $students = $query->orderBy('name')->paginate($this->perPage);

            $data = array_merge($data, [
                'students' => $students,
                'availableGrades' => $availableGrades,
                'totalStudents' => $totalStudents,
                'withPhotoCount' => $withPhotoCount,
            ]);
        }

        return view('livewire.sekolah.project.photo-download', $data)
            ->layout('layouts.dashboard')
            ->title($this->project ? 'Unduh Foto - ' . $this->project->name : 'Unduh Foto');
    }
}
