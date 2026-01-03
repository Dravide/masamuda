<?php

namespace App\Livewire\Admin\Student;

use App\Models\School;
use Livewire\Component;
use Livewire\WithPagination;

class SchoolList extends Component
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
        $schools = School::query()
            ->withCount('students')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('address', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.student.school-list', [
            'schools' => $schools
        ])
            ->layout('layouts.dashboard')
            ->title('Data Siswa per Sekolah');
    }
}
