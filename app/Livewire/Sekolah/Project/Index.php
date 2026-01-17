<?php

namespace App\Livewire\Sekolah\Project;

use App\Models\AcademicYear;
use App\Models\Project;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Table Filters & Sort
    public $search = '';
    public $perPage = 10;
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    // Helpers
    public $school;
    public $projectStatuses = [];
    public $academicYears = [];
    public $projectTypes = [];
    public $projectTargets = [];
    public $activeAcademicYear = null;

    // Modal & Form
    public $showModal = false;
    public $isEdit = false;
    public $projectId;

    // Form Fields
    public $name;
    public $academic_year_id;
    public $type;
    public $target = 'siswa';
    public $description;
    public $date;
    public $status = 'draft';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        $this->school = School::where('user_id', Auth::id())->firstOrFail();
        $this->projectStatuses = Project::STATUSES;
        $this->projectTypes = Project::TYPES;
        $this->projectTargets = Project::TARGETS;
        $this->academicYears = AcademicYear::orderBy('year_name', 'desc')->get();
        $this->activeAcademicYear = AcademicYear::where('is_active', true)->first();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($column)
    {
        if ($this->sortColumn === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortColumn = $column;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Project::where('school_id', $this->school->id)
            ->with('academicYear')
            ->withCount('students');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('type', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%');
            });
        }

        $projects = $query->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        // Statistics
        $totalProjects = Project::where('school_id', $this->school->id)->count();
        $draftProjects = Project::where('school_id', $this->school->id)->where('status', 'draft')->count();
        $activeProjects = Project::where('school_id', $this->school->id)->where('status', 'active')->count();
        $completedProjects = Project::where('school_id', $this->school->id)->where('status', 'completed')->count();

        return view('livewire.sekolah.project.index', [
            'projects' => $projects,
            'totalProjects' => $totalProjects,
            'draftProjects' => $draftProjects,
            'activeProjects' => $activeProjects,
            'completedProjects' => $completedProjects,
        ])->layout('layouts.dashboard')->title('Project');
    }
    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        if ($this->activeAcademicYear) {
            $this->academic_year_id = $this->activeAcademicYear->id;
        }
        $this->showModal = true;
    }

    public function edit($id)
    {
        $project = Project::where('school_id', $this->school->id)->findOrFail($id);

        $this->projectId = $project->id;
        $this->name = $project->name;
        $this->academic_year_id = $project->academic_year_id;
        $this->type = $project->type;
        $this->target = $project->target;
        $this->description = $project->description;
        $this->date = $project->date?->format('Y-m-d');
        $this->status = $project->status;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'type' => 'required|string|in:' . implode(',', Project::TYPES),
            'target' => 'required|string|in:' . implode(',', array_keys(Project::TARGETS)),
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'status' => 'required|string|in:' . implode(',', array_keys(Project::STATUSES)),
        ]);

        Project::create([
            'school_id' => $this->school->id,
            'name' => $this->name,
            'academic_year_id' => $this->academic_year_id,
            'type' => $this->type,
            'target' => $this->target,
            'description' => $this->description,
            'date' => $this->date,
            'status' => $this->status,
        ]);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Project berhasil ditambahkan.',
        ]);

        $this->closeModal();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'type' => 'required|string|in:' . implode(',', Project::TYPES),
            'target' => 'required|string|in:' . implode(',', array_keys(Project::TARGETS)),
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'status' => 'required|string|in:' . implode(',', array_keys(Project::STATUSES)),
        ]);

        $project = Project::where('school_id', $this->school->id)->findOrFail($this->projectId);

        $project->update([
            'name' => $this->name,
            'academic_year_id' => $this->academic_year_id,
            'type' => $this->type,
            'target' => $this->target,
            'description' => $this->description,
            'date' => $this->date,
            'status' => $this->status,
        ]);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Project berhasil diperbarui.',
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        $project = Project::where('school_id', $this->school->id)->findOrFail($id);
        $project->delete();

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Terhapus!',
            'text' => 'Project berhasil dihapus.',
        ]);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['name', 'academic_year_id', 'type', 'target', 'description', 'date', 'projectId']);
        $this->status = 'draft';
        $this->target = 'siswa';
    }
}
