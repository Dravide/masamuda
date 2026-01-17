<?php

namespace App\Livewire\Guru\Project;

use App\Models\Project;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use ZipArchive;
use Illuminate\Support\Str;

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
        $teacher = Auth::user()->teacher;

        $student = Student::where('school_id', $teacher->school_id)
            ->where('project_id', $this->project->id)
            ->findOrFail($studentId);

        $photos = $student->photos()->where('project_id', $this->project->id)->get();

        if ($photos->isEmpty()) {
            $this->dispatch('alert', [
                'type' => 'error',
                'title' => 'Error!',
                'text' => 'Siswa ini belum memiliki foto.',
            ]);
            return;
        }

        if ($photos->count() === 1) {
            // Single file download
            $photo = $photos->first();
            return response()->download(storage_path('app/public/' . $photo->file_path));
        }

        // Multiple files - create zip
        $zipFileName = 'foto_' . Str::slug($student->name) . '_' . $student->nis . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($photos as $photo) {
                // Ensure file exists
                $filePath = storage_path('app/public/' . $photo->file_path);
                if (file_exists($filePath)) {
                    $extension = pathinfo($photo->file_path, PATHINFO_EXTENSION);
                    $zip->addFile($filePath, $photo->photo_type . '.' . $extension);
                }
            }
            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        $this->dispatch('alert', [
            'type' => 'error',
            'title' => 'Error!',
            'text' => 'Gagal membuat file zip.',
        ]);
    }

    public function downloadAllPhotos()
    {
        $teacher = Auth::user()->teacher;

        $students = Student::where('school_id', $teacher->school_id)
            ->where('project_id', $this->project->id)
            ->whereHas('photos', function ($q) {
                $q->where('project_id', $this->project->id);
            })
            ->with([
                'photos' => function ($q) {
                    $q->where('project_id', $this->project->id);
                }
            ])
            ->get();

        if ($students->isEmpty()) {
            $this->dispatch('alert', [
                'type' => 'error',
                'title' => 'Error!',
                'text' => 'Tidak ada foto untuk diunduh.',
            ]);
            return;
        }

        $zipFileName = 'foto_' . Str::slug($this->project->name) . '_' . date('Ymd_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($students as $student) {
                $folderName = $student->nis . '_' . Str::slug($student->name);
                foreach ($student->photos as $photo) {
                    // Ensure file exists
                    $filePath = storage_path('app/public/' . $photo->file_path);
                    if (file_exists($filePath)) {
                        $extension = pathinfo($photo->file_path, PATHINFO_EXTENSION);
                        $zip->addFile($filePath, $folderName . '/' . $photo->photo_type . '.' . $extension);
                    }
                }
            }
            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        $this->dispatch('alert', [
            'type' => 'error',
            'title' => 'Error!',
            'text' => 'Gagal membuat file zip.',
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
