<?php

namespace App\Livewire\Admin\Student;

use App\Models\Project;
use App\Models\Student;
use App\Models\StudentPhoto;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProjectStudentsExport;
use App\Models\Major;

class ByProject extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public Project $project;
    public $search = '';
    public $perPage = 10;
    public $sortColumn = 'name';
    public $sortDirection = 'asc';
    public $filterPhoto = '';

    // Propagation Properties
    public $propagationFile;
    public $photoType = 'Formal';
    public $showPropagationModal = false;
    public $previewData = [];
    public $isProcessing = false;
    public $processingMessage = '';
    public $uploadProgress = 0;

    // Photo Detail Modal Properties
    public $showPhotoModal = false;
    public $selectedStudent = null;
    public $studentPhotos = [];

    // Student Form Properties
    public $showModal = false;
    public $isEdit = false;
    public $studentId;

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

    public $availableMajors = [];
    public $isSmp = false;
    public $isGuru = false;

    public $allStudents = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortColumn' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
        'filterPhoto' => ['except' => ''],
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->isSmp = $project->school->jenjang === 'smp';
        $this->isGuru = $project->target === 'guru';

        if (!$this->isSmp && !$this->isGuru) {
            $this->availableMajors = Major::where('is_active', true)->pluck('name')->toArray();
        } else if ($this->isSmp) {
            $this->major = 'UMUM';
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function exportExcel()
    {
        return Excel::download(new ProjectStudentsExport($this->project->id), 'Daftar_Siswa_' . Str::slug($this->project->name) . '.xlsx');
    }

    public function openPhotoModal($studentId)
    {
        $this->selectedStudent = Student::with('photos')->find($studentId);

        if ($this->selectedStudent) {
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
                'is_main' => false
            ]);
        }
    }

    public function deletePhoto($path, $photoId = null)
    {
        if (!$this->selectedStudent)
            return;

        if ($this->selectedStudent->photo === $path) {
            $this->selectedStudent->update(['photo' => null]);

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        if ($photoId) {
            $photo = StudentPhoto::find($photoId);
            if ($photo) {
                $photo->delete();
                if ($photo->file_path !== $path && Storage::disk('public')->exists($photo->file_path)) {
                    Storage::disk('public')->delete($photo->file_path);
                }
            }
        } else {
            StudentPhoto::where('file_path', $path)->delete();
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $this->dispatch('alert', [['type' => 'success', 'title' => 'Terhapus', 'text' => 'Foto berhasil dihapus.']]);

        $this->selectedStudent->refresh();
        $this->loadStudentPhotos();
    }

    public function closePhotoModal()
    {
        $this->showPhotoModal = false;
        $this->reset(['selectedStudent', 'studentPhotos']);
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

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $student = Student::where('project_id', $this->project->id)->findOrFail($id);

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
        $this->birth_date = $student->birth_date ? $student->birth_date->format('Y-m-d') : null;
        $this->major = $student->major;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function store()
    {
        // Conditional validation based on target
        if ($this->isGuru) {
            $this->validate([
                'nis' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'major' => 'nullable|string',
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
            'school_id' => $this->project->school_id,
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
        // Conditional validation based on target
        if ($this->isGuru) {
            $this->validate([
                'nis' => 'required|string|max:50',
                'name' => 'required|string|max:255',
                'whatsapp' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:255',
                'major' => 'nullable|string',
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

        $student = Student::where('project_id', $this->project->id)->findOrFail($this->studentId);

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
        $student = Student::where('project_id', $this->project->id)->findOrFail($id);

        // Check for photos
        if ($student->photos()->count() > 0) {
            // Delete photos first? Or warn?
            // Assuming cascade deletion or manual cleanup is okay for now
            // But let's just delete the photos too
            foreach ($student->photos as $photo) {
                if (Storage::disk('public')->exists($photo->file_path)) {
                    Storage::disk('public')->delete($photo->file_path);
                }
                $photo->delete();
            }
        }

        // Check main photo
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
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

    public function openPropagationModal()
    {
        $this->reset(['propagationFile', 'previewData', 'processingMessage', 'photoType']);
        $this->photoType = 'Formal';

        $this->allStudents = $this->project->students()->select('id', 'name', 'nis', 'nisn')->orderBy('name')->get()->toArray();

        $this->showPropagationModal = true;
    }

    public function closePropagationModal()
    {
        $this->showPropagationModal = false;
        $this->reset(['propagationFile', 'previewData', 'processingMessage', 'photoType']);
    }

    public function updatedPropagationFile()
    {
        $this->reset(['previewData']);

        $this->validate([
            'propagationFile' => 'required|file|mimes:zip|max:51200',
        ]);

        $this->processZipPreview();
    }

    public function processZipPreview()
    {
        $this->isProcessing = true;
        $this->processingMessage = 'Menganalisis file ZIP...';
        $this->previewData = [];

        try {
            $zip = new ZipArchive;
            $res = $zip->open($this->propagationFile->getRealPath());

            if ($res === TRUE) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $filename = $zip->getNameIndex($i);
                    $fileInfo = pathinfo($filename);

                    if (substr($filename, -1) == '/' || strpos($filename, '__MACOSX') !== false || strpos($fileInfo['basename'], '.') === 0) {
                        continue;
                    }

                    if (!in_array(strtolower($fileInfo['extension'] ?? ''), ['jpg', 'jpeg', 'png'])) {
                        continue;
                    }

                    $parts = explode('_', $fileInfo['filename'], 2);
                    $identifier = $parts[0];
                    $nameFromFile = isset($parts[1]) ? $parts[1] : '';

                    $student = $this->project->students()
                        ->where(function ($q) use ($identifier) {
                            $q->where('nis', $identifier)
                                ->orWhere('nisn', $identifier);
                        })
                        ->first();

                    $status = 'Siswa Tidak Ditemukan';
                    $statusClass = 'danger';
                    $valid = false;
                    $similarity = 0;
                    $matchType = 'Identifier';

                    if (!$student) {
                        $potentialName = $fileInfo['filename'];

                        $studentsByName = $this->project->students()
                            ->where('name', $potentialName)
                            ->get();

                        if ($studentsByName->count() === 1) {
                            $student = $studentsByName->first();
                            $valid = true;
                            $status = 'Valid (Match by Name)';
                            $statusClass = 'success';
                            $similarity = 100;
                            $matchType = 'Name';
                            $nameFromFile = $potentialName;
                            $identifier = '-';
                        } elseif ($studentsByName->count() > 1) {
                            $status = 'Invalid (Nama Duplikat)';
                            $statusClass = 'danger';
                            $nameFromFile = $potentialName;
                        }
                    }

                    if ($student && $matchType === 'Identifier') {
                        $cleanNameFromFile = pathinfo($nameFromFile, PATHINFO_FILENAME);

                        similar_text(strtolower($cleanNameFromFile), strtolower($student->name), $similarity);

                        $valid = true;
                        if ($similarity < 50) {
                            $status = 'Valid (Nama Berbeda ' . round($similarity) . '%)';
                            $statusClass = 'warning';
                        } else {
                            $status = 'Valid (' . round($similarity) . '%)';
                            $statusClass = 'success';
                        }
                    }

                    $this->previewData[] = [
                        'index' => count($this->previewData),
                        'filename' => $filename,
                        'identifier' => $identifier,
                        'name_in_file' => $nameFromFile,
                        'matched_student' => $student ? $student->name . ' (' . ($student->nisn ? $student->nisn : '-') . ' / ' . ($student->nis ? $student->nis : '-') . ')' : '-',
                        'student_id' => $student ? $student->id : null,
                        'status' => $status,
                        'status_class' => $statusClass,
                        'valid' => $valid,
                        'similarity' => $similarity
                    ];
                }
                $zip->close();
            } else {
                $this->addError('propagationFile', 'Gagal membuka file ZIP.');
            }
        } catch (\Exception $e) {
            $this->addError('propagationFile', 'Terjadi kesalahan saat memproses file: ' . $e->getMessage());
        }

        $this->isProcessing = false;
    }

    public function updateManualMatch($index, $studentId)
    {
        if (!isset($this->previewData[$index]))
            return;

        if (empty($studentId)) {
            $this->previewData[$index]['student_id'] = null;
            $this->previewData[$index]['matched_student'] = '-';
            $this->previewData[$index]['status'] = 'Siswa Tidak Ditemukan';
            $this->previewData[$index]['status_class'] = 'danger';
            $this->previewData[$index]['valid'] = false;
            return;
        }

        $student = collect($this->allStudents)->firstWhere('id', $studentId);

        if ($student) {
            $this->previewData[$index]['student_id'] = $student['id'];
            $this->previewData[$index]['matched_student'] = $student['name'] . ' (' . ($student['nisn'] ? $student['nisn'] : '-') . ' / ' . ($student['nis'] ? $student['nis'] : '-') . ')';
            $this->previewData[$index]['status'] = 'Manual Match';
            $this->previewData[$index]['status_class'] = 'info';
            $this->previewData[$index]['valid'] = true;
        }
    }

    public function submitPropagation()
    {
        $validItems = collect($this->previewData)->where('valid', true);

        if ($validItems->isEmpty()) {
            $this->dispatch('alert', [['type' => 'error', 'title' => 'Gagal', 'text' => 'Tidak ada data valid untuk diproses.']]);
            return;
        }

        $this->isProcessing = true;
        $this->processingMessage = 'Menyimpan foto siswa...';

        DB::beginTransaction();
        try {
            $zip = new ZipArchive;
            if ($zip->open($this->propagationFile->getRealPath()) === TRUE) {
                $extractPath = storage_path('app/temp/propagation/' . Str::random(10));

                foreach ($validItems as $item) {
                    $zip->extractTo($extractPath, $item['filename']);

                    $sourcePath = $extractPath . '/' . $item['filename'];
                    $extension = pathinfo($item['filename'], PATHINFO_EXTENSION);
                    $newFilename = 'student-photos/' . $this->project->school_id . '/' . $item['student_id'] . '_' . Str::random(8) . '.' . $extension;

                    Storage::disk('public')->put($newFilename, file_get_contents($sourcePath));

                    if ($this->photoType === 'Formal') {
                        Student::where('id', $item['student_id'])->update(['photo' => $newFilename]);
                    }

                    StudentPhoto::create([
                        'student_id' => $item['student_id'],
                        'project_id' => $this->project->id,
                        'photo_type' => $this->photoType,
                        'file_path' => $newFilename
                    ]);
                }

                $zip->close();

                $this->rrmdir($extractPath);

                if ($this->propagationFile) {
                    $this->propagationFile->delete();
                }
            }

            DB::commit();
            $this->closePropagationModal();
            $this->dispatch('alert', [['type' => 'success', 'title' => 'Berhasil', 'text' => 'Foto siswa berhasil dipropagasi.']]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('alert', [['type' => 'error', 'title' => 'Error', 'text' => 'Gagal menyimpan data: ' . $e->getMessage()]]);
        }

        $this->isProcessing = false;
    }

    public function resetPropagation()
    {
        $photos = StudentPhoto::where('project_id', $this->project->id)->get();

        foreach ($photos as $photo) {
            if (Storage::disk('public')->exists($photo->file_path)) {
                Storage::disk('public')->delete($photo->file_path);
            }
            $photo->delete();
        }

        $students = $this->project->students()->whereNotNull('photo')->get();
        foreach ($students as $student) {
            if (str_contains($student->photo, 'student-photos/' . $this->project->school_id . '/')) {
                $student->update(['photo' => null]);
            }
        }

        $this->dispatch('alert', [['type' => 'success', 'title' => 'Reset Berhasil', 'text' => 'Semua foto propagasi project ini telah dihapus.']]);
    }

    private function rrmdir($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . "/" . $object))
                        $this->rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                    else
                        unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
            rmdir($dir);
        }
    }

    public function render()
    {
        $students = $this->project->students()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nis', 'like', '%' . $this->search . '%')
                        ->orWhere('nisn', 'like', '%' . $this->search . '%');
                });
            })
            ->withCount([
                'photos' => function ($query) {
                    $query->where('project_id', $this->project->id);
                }
            ])
            ->when($this->filterPhoto === 'with', function ($q) {
                $q->whereHas('photos', function ($q) {
                    $q->where('project_id', $this->project->id);
                });
            })
            ->when($this->filterPhoto === 'without', function ($q) {
                $q->whereDoesntHave('photos', function ($q) {
                    $q->where('project_id', $this->project->id);
                });
            })
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        $label = $this->isGuru ? 'Guru' : 'Siswa';
        return view('livewire.admin.student.by-project', [
            'students' => $students
        ])
            ->layout('layouts.dashboard')
            ->title("Data {$label} - " . $this->project->name);
    }
}
