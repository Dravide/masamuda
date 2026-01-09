<?php

namespace App\Livewire\Admin\Student;

use App\Models\AcademicYear;
use App\Models\Project;
use App\Models\School;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    // Modal & Form
    public $showModal = false;
    public $isEdit = false;
    public $projectId;

    // Form Fields
    public $school_id;
    public $name;
    public $academic_year_id;
    public $type;
    public $description;
    public $date;
    public $status = 'draft';

    // Helpers
    public $schools = [];
    public $academicYears = [];
    public $projectTypes = [];
    public $projectStatuses = [];
    public $activeAcademicYear = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        $this->schools = School::orderBy('name')->get();
        $this->academicYears = AcademicYear::orderBy('year_name', 'desc')->get();
        $this->projectTypes = Project::TYPES;
        $this->projectStatuses = Project::STATUSES;
        $this->activeAcademicYear = AcademicYear::where('is_active', true)->first();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $projects = Project::query()
            ->with('school', 'academicYear')
            ->withCount('students')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhereHas('school', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->latest()
            ->paginate($this->perPage);

        // Statistics
        $totalProjects = Project::count();
        $draftProjects = Project::where('status', 'draft')->count();
        $activeProjects = Project::where('status', 'active')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $totalStudents = \App\Models\Student::count();
        $totalSchools = School::count();

        return view('livewire.admin.student.project-list', [
            'projects' => $projects,
            'totalProjects' => $totalProjects,
            'draftProjects' => $draftProjects,
            'activeProjects' => $activeProjects,
            'completedProjects' => $completedProjects,
            'totalStudents' => $totalStudents,
            'totalSchools' => $totalSchools,
        ])
            ->layout('layouts.dashboard')
            ->title('Data Project');
    }

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        // Auto-set to active academic year
        if ($this->activeAcademicYear) {
            $this->academic_year_id = $this->activeAcademicYear->id;
        }
        $this->showModal = true;
    }

    public function edit($id)
    {
        $project = Project::findOrFail($id);

        $this->projectId = $project->id;
        $this->school_id = $project->school_id;
        $this->name = $project->name;
        $this->academic_year_id = $project->academic_year_id;
        $this->type = $project->type;
        $this->description = $project->description;
        $this->date = $project->date?->format('Y-m-d');
        $this->status = $project->status;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate([
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'type' => 'required|string|in:' . implode(',', Project::TYPES),
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'status' => 'required|string|in:' . implode(',', array_keys(Project::STATUSES)),
        ]);

        Project::create([
            'school_id' => $this->school_id,
            'name' => $this->name,
            'academic_year_id' => $this->academic_year_id,
            'type' => $this->type,
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
            'school_id' => 'required|exists:schools,id',
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'type' => 'required|string|in:' . implode(',', Project::TYPES),
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'status' => 'required|string|in:' . implode(',', array_keys(Project::STATUSES)),
        ]);

        $project = Project::findOrFail($this->projectId);

        $project->update([
            'school_id' => $this->school_id,
            'name' => $this->name,
            'academic_year_id' => $this->academic_year_id,
            'type' => $this->type,
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
        $project = Project::findOrFail($id);
        $project->delete();

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Terhapus!',
            'text' => 'Project berhasil dihapus.',
        ]);
    }

    public function toggleStatus($projectId, $newStatus)
    {
        $project = Project::findOrFail($projectId);

        if (!in_array($newStatus, ['draft', 'active', 'completed'])) {
            return;
        }

        $project->update(['status' => $newStatus]);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => "Status project berhasil diubah menjadi " . ucfirst($newStatus) . ".",
        ]);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['school_id', 'name', 'academic_year_id', 'type', 'description', 'date', 'projectId']);
        $this->status = 'draft';
    }
}
