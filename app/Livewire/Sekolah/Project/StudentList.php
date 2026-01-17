<?php

namespace App\Livewire\Sekolah\Project;

use App\Models\Major;
use App\Models\Project;
use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class StudentList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Route parameter
    public Project $project;

    // Table Filters & Sort
    public $search = '';
    public $perPage = 10;
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    // Additional Filters
    public $filterGrade = '';
    public $filterMajor = '';
    public $filterPhoto = '';

    // Modal & Form
    public $showModal = false;
    public $isEdit = false;
    public $studentId;

    // Form Fields
    public $nis;
    public $nisn;
    public $name;
    public $whatsapp;
    public $email;
    public $grade;
    public $class_name;
    public $address;
    public $birth_place;
    public $birth_date;
    public $major;

    // Helpers
    public $school;
    public $isSmp = false;
    public $isGuru = false;
    public $availableMajors = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'filterGrade' => ['except' => ''],
        'filterMajor' => ['except' => ''],
        'filterPhoto' => ['except' => ''],
    ];

    public function mount(Project $project)
    {
        $this->school = School::where('user_id', Auth::id())->firstOrFail();

        // Verify project belongs to this school
        if ($project->school_id !== $this->school->id) {
            abort(403);
        }

        // Verify project is active or completed
        if (!in_array($project->status, ['active', 'completed'])) {
            abort(403, 'Project belum diaktifkan oleh Admin.');
        }

        $this->project = $project;
        $this->isSmp = $this->school->jenjang === 'smp';
        $this->isGuru = $project->target === 'guru';

        if (!$this->isSmp && !$this->isGuru) {
            // Fetch active majors from database
            $this->availableMajors = Major::getActive()->pluck('name')->toArray();
        }
    }

    public function getIsReadOnlyProperty()
    {
        return $this->project->status === 'completed';
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
        // Base query for statistics (before filters)
        $baseQuery = Student::where('school_id', $this->school->id)
            ->where('project_id', $this->project->id);

        // Calculate statistics
        $totalStudents = $baseQuery->count();

        $gradeStats = Student::where('school_id', $this->school->id)
            ->where('project_id', $this->project->id)
            ->selectRaw('grade, COUNT(*) as count')
            ->groupBy('grade')
            ->orderBy('grade')
            ->pluck('count', 'grade')
            ->toArray();

        $majorStats = Student::where('school_id', $this->school->id)
            ->where('project_id', $this->project->id)
            ->selectRaw('major, COUNT(*) as count')
            ->groupBy('major')
            ->orderBy('count', 'desc')
            ->pluck('count', 'major')
            ->toArray();

        // Count students with photos for this project
        $withPhotoCount = Student::where('school_id', $this->school->id)
            ->where('project_id', $this->project->id)
            ->whereHas('photos', function ($q) {
                $q->where('project_id', $this->project->id);
            })
            ->count();

        $withoutPhotoCount = $totalStudents - $withPhotoCount;

        // Available grades and majors for filter dropdowns
        $availableGrades = array_keys($gradeStats);
        $availableMajorsFilter = array_keys($majorStats);

        // Query with filters
        $query = Student::where('school_id', $this->school->id)
            ->where('project_id', $this->project->id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('nis', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%')
                    ->orWhere('major', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterGrade) {
            $query->where('grade', $this->filterGrade);
        }

        if ($this->filterMajor) {
            $query->where('major', $this->filterMajor);
        }

        if ($this->filterPhoto === 'with') {
            $query->whereHas('photos', function ($q) {
                $q->where('project_id', $this->project->id);
            });
        } elseif ($this->filterPhoto === 'without') {
            $query->whereDoesntHave('photos', function ($q) {
                $q->where('project_id', $this->project->id);
            });
        }

        $students = $query->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        $targetLabel = $this->isGuru ? 'Guru' : 'Siswa';
        return view('livewire.sekolah.project.student', [
            'students' => $students,
            'totalStudents' => $totalStudents,
            'gradeStats' => $gradeStats,
            'majorStats' => $majorStats,
            'withPhotoCount' => $withPhotoCount,
            'withoutPhotoCount' => $withoutPhotoCount,
            'availableGrades' => $availableGrades,
            'availableMajorsFilter' => $availableMajorsFilter,
        ])->layout('layouts.dashboard')->title($targetLabel . ' - ' . $this->project->name);
    }

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $student = Student::where('school_id', $this->school->id)
            ->where('project_id', $this->project->id)
            ->findOrFail($id);

        $this->studentId = $student->id;
        $this->nis = $student->nis;
        $this->nisn = $student->nisn;
        $this->name = $student->name;
        $this->whatsapp = $student->whatsapp;
        $this->email = $student->email;
        $this->grade = $student->grade;
        $this->class_name = $student->class_name;
        $this->address = $student->address;
        $this->birth_place = $student->birth_place;
        $this->birth_date = $student->birth_date->format('Y-m-d');
        $this->major = $student->major;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function store()
    {
        if ($this->isReadOnly) {
            $this->dispatch('alert', [
                'type' => 'error',
                'title' => 'Error!',
                'text' => 'Project sudah selesai. Tidak dapat menambah siswa.',
            ]);
            return;
        }
        // Different validation rules for guru vs siswa
        if ($this->isGuru) {
            $this->validate([
                'nis' => 'required|string|max:50', // Used as NIP for guru
                'name' => 'required|string|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'major' => 'nullable|string', // Used as Bidang/Mata Pelajaran for guru
            ]);
        } else {
            $this->validate([
                'nis' => 'required|string|max:50',
                'nisn' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'grade' => 'required|string|max:10',
                'class_name' => 'required|string|max:50',
                'address' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'major' => 'required|string',
            ]);
        }

        Student::create([
            'school_id' => $this->school->id,
            'project_id' => $this->project->id,
            'nis' => $this->nis,
            'nisn' => $this->isGuru ? null : $this->nisn,
            'name' => $this->name,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'grade' => $this->isGuru ? null : $this->grade,
            'class_name' => $this->isGuru ? null : $this->class_name,
            'address' => $this->isGuru ? null : $this->address,
            'birth_place' => $this->isGuru ? null : $this->birth_place,
            'birth_date' => $this->isGuru ? null : $this->birth_date,
            'major' => $this->major,
        ]);

        $label = $this->isGuru ? 'Guru' : 'Siswa';
        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => "Data {$label} Berhasil Disimpan.",
        ]);

        $this->closeModal();
    }

    public function update()
    {
        if ($this->isReadOnly) {
            $this->dispatch('alert', [
                'type' => 'error',
                'title' => 'Error!',
                'text' => 'Project sudah selesai. Tidak dapat mengubah data.',
            ]);
            return;
        }
        // Different validation rules for guru vs siswa
        if ($this->isGuru) {
            $this->validate([
                'nis' => 'required|string|max:50', // Used as NIP for guru
                'name' => 'required|string|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'major' => 'nullable|string', // Used as Bidang/Mata Pelajaran for guru
            ]);
        } else {
            $this->validate([
                'nis' => 'required|string|max:50',
                'nisn' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'grade' => 'required|string|max:10',
                'class_name' => 'required|string|max:50',
                'address' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'major' => 'required|string',
            ]);
        }

        $student = Student::where('school_id', $this->school->id)
            ->where('project_id', $this->project->id)
            ->findOrFail($this->studentId);

        $student->update([
            'nis' => $this->nis,
            'nisn' => $this->isGuru ? null : $this->nisn,
            'name' => $this->name,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'grade' => $this->isGuru ? null : $this->grade,
            'class_name' => $this->isGuru ? null : $this->class_name,
            'address' => $this->isGuru ? null : $this->address,
            'birth_place' => $this->isGuru ? null : $this->birth_place,
            'birth_date' => $this->isGuru ? null : $this->birth_date,
            'major' => $this->major,
        ]);

        $label = $this->isGuru ? 'Guru' : 'Siswa';
        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => "Data {$label} Berhasil Diperbarui.",
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        if ($this->isReadOnly) {
            $this->dispatch('alert', [
                'type' => 'error',
                'title' => 'Error!',
                'text' => 'Project sudah selesai. Tidak dapat menghapus data.',
            ]);
            return;
        }
        $student = Student::where('school_id', $this->school->id)
            ->findOrFail($id);

        // Verify student belongs to this project
        if ($student->project_id !== $this->project->id) {
            return;
        }

        $student->delete();

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Terhapus!',
            'text' => 'Data Siswa Berhasil Dihapus.',
        ]);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset(['nis', 'nisn', 'name', 'whatsapp', 'email', 'grade', 'class_name', 'address', 'birth_place', 'birth_date', 'studentId']);

        if (!$this->isSmp) {
            $this->reset('major');
        } else {
            $this->major = 'UMUM';
        }
    }

    // Copy Students Properties
    public $showCopyModal = false;
    public $sourceProjectId = null;
    public $sourceStudents = [];
    public $selectedStudents = [];
    public $selectAll = false;
    public $otherProjects = [];

    public function openCopyModal()
    {
        $this->reset(['sourceProjectId', 'sourceStudents', 'selectedStudents', 'selectAll']);

        // Get other projects from same school
        $this->otherProjects = Project::where('school_id', $this->school->id)
            ->where('id', '!=', $this->project->id)
            ->withCount('students')
            ->orderBy('created_at', 'desc')
            ->get()
            ->toArray();

        $this->showCopyModal = true;
    }

    public function closeCopyModal()
    {
        $this->showCopyModal = false;
        $this->reset(['sourceProjectId', 'sourceStudents', 'selectedStudents', 'selectAll']);
    }

    public function updatedSourceProjectId($value)
    {
        $this->selectedStudents = [];
        $this->selectAll = false;

        if ($value) {
            // Get students from source project that are NOT already in current project (by NIS or NISN)
            $existingNis = $this->project->students()->pluck('nis')->toArray();
            $existingNisn = $this->project->students()->pluck('nisn')->filter()->toArray();

            $this->sourceStudents = Student::where('project_id', $value)
                ->where('school_id', $this->school->id)
                ->whereNotIn('nis', $existingNis)
                ->when(!empty($existingNisn), function ($q) use ($existingNisn) {
                    $q->whereNotIn('nisn', $existingNisn);
                })
                ->orderBy('name')
                ->get()
                ->toArray();
        } else {
            $this->sourceStudents = [];
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedStudents = collect($this->sourceStudents)->pluck('id')->toArray();
        } else {
            $this->selectedStudents = [];
        }
    }

    public function copyStudents()
    {
        if ($this->isReadOnly) {
            $this->dispatch('alert', [
                'type' => 'error',
                'title' => 'Error!',
                'text' => 'Project sudah selesai. Tidak dapat menyalin siswa.',
            ]);
            return;
        }
        if (empty($this->selectedStudents)) {
            $this->dispatch('alert', [
                'type' => 'error',
                'title' => 'Error!',
                'text' => 'Pilih minimal satu siswa untuk disalin.',
            ]);
            return;
        }

        $copied = 0;
        $students = Student::whereIn('id', $this->selectedStudents)
            ->where('school_id', $this->school->id)
            ->get();

        foreach ($students as $student) {
            // Create copy of student for current project
            $newStudent = $student->replicate();
            $newStudent->project_id = $this->project->id;
            $newStudent->created_at = now();
            $newStudent->updated_at = now();
            $newStudent->save();
            $copied++;
        }

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => "{$copied} siswa berhasil disalin ke project ini.",
        ]);

        $this->closeCopyModal();
    }

    // Photo Modal Properties
    public $showPhotoModal = false;
    public $selectedStudent = null;
    public $studentPhotos = [];

    public function openPhotoModal($studentId)
    {
        $this->selectedStudent = Student::find($studentId);

        if ($this->selectedStudent && $this->selectedStudent->school_id === $this->school->id) {
            $this->loadStudentPhotos();
            $this->showPhotoModal = true;
        }
    }

    public function loadStudentPhotos()
    {
        $this->studentPhotos = collect();

        // Only show photos from student_photos table filtered by project_id
        $photos = $this->selectedStudent->photos()
            ->where('project_id', $this->project->id)
            ->get();

        foreach ($photos as $photo) {
            $this->studentPhotos->push([
                'id' => $photo->id,
                'url' => asset('storage/' . $photo->file_path),
                'path' => $photo->file_path,
                'type' => $photo->photo_type,
            ]);
        }
    }

    public function closePhotoModal()
    {
        $this->showPhotoModal = false;
        $this->reset(['selectedStudent', 'studentPhotos']);
    }
}
