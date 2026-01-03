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

    // Modal & Form
    public $showModal = false;
    public $isEdit = false;
    public $projectId;

    // Form Fields
    public $name;
    public $academic_year_id;
    public $type;
    public $description;
    public $date;
    public $status = 'draft';

    // Helpers
    public $school;
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
        $this->school = School::where('user_id', Auth::id())->firstOrFail();
        $this->academicYears = AcademicYear::orderBy('year_name', 'desc')->get();
        $this->projectTypes = Project::TYPES;
        $this->projectStatuses = Project::STATUSES;
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
            ->with('academicYear');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('type', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%');
            });
        }

        $projects = $query->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.sekolah.project.index', [
            'projects' => $projects
        ])->layout('layouts.dashboard')->title('Project');
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
        $project = Project::where('school_id', $this->school->id)->findOrFail($id);

        $this->projectId = $project->id;
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
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'type' => 'required|string|in:' . implode(',', Project::TYPES),
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'status' => 'required|string|in:' . implode(',', array_keys(Project::STATUSES)),
        ]);

        Project::create([
            'school_id' => $this->school->id,
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
            'text' => 'Project Berhasil Disimpan.',
        ]);

        $this->closeModal();
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'academic_year_id' => 'required|exists:academic_years,id',
            'type' => 'required|string|in:' . implode(',', Project::TYPES),
            'description' => 'nullable|string',
            'date' => 'nullable|date',
            'status' => 'required|string|in:' . implode(',', array_keys(Project::STATUSES)),
        ]);

        $project = Project::where('school_id', $this->school->id)->findOrFail($this->projectId);

        $project->update([
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
            'text' => 'Project Berhasil Diperbarui.',
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
            'text' => 'Project Berhasil Dihapus.',
        ]);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['name', 'academic_year_id', 'type', 'description', 'date', 'projectId']);
        $this->status = 'draft';
    }
}
