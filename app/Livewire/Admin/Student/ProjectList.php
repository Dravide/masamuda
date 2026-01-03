<?php

namespace App\Livewire\Admin\Student;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

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

        return view('livewire.admin.student.project-list', [
            'projects' => $projects
        ])
            ->layout('layouts.dashboard')
            ->title('Data Project');
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
}
