<?php

namespace App\Livewire\Admin\Student;

use App\Models\School;
use App\Models\Student;
use App\Models\StudentPhoto;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;
use Illuminate\Support\Facades\DB;

class BySchool extends Component
{
    use WithPagination, WithFileUploads;

    public School $school;
    public $search = '';
    public $perPage = 10;
    public $sortColumn = 'name';
    public $sortDirection = 'asc';

    // Propagation Properties
    public $propagationFile;
    public $photoType = 'Formal'; // Default type
    public $showPropagationModal = false;
    public $previewData = [];
    public $isProcessing = false;
    public $processingMessage = '';
    public $uploadProgress = 0;

    // Photo Detail Modal Properties
    public $showPhotoModal = false;
    public $selectedStudent = null;
    public $studentPhotos = [];

    // Helper for manual match dropdown
    public $allStudents = []; // To populate dropdown in modal

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortColumn' => ['except' => 'name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount(School $school)
    {
        $this->school = $school;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    // Photo Detail Methods
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
        // Collect all photos including main photo and additional photos
        $this->studentPhotos = collect();
        
        // Add main photo if exists
        if ($this->selectedStudent->photo) {
            $this->studentPhotos->push([
                'id' => null, // Main photo doesn't have ID in student_photos table directly, handled via student update
                'url' => asset('storage/' . $this->selectedStudent->photo),
                'path' => $this->selectedStudent->photo,
                'type' => 'Formal (Utama)',
                'is_main' => true
            ]);
        }

        // Add additional photos
        foreach ($this->selectedStudent->photos as $photo) {
            // Skip if it's the same file as main photo to avoid duplicate display
            if ($this->selectedStudent->photo !== $photo->file_path) {
                $this->studentPhotos->push([
                    'id' => $photo->id,
                    'url' => asset('storage/' . $photo->file_path),
                    'path' => $photo->file_path,
                    'type' => $photo->photo_type,
                    'is_main' => false
                ]);
            }
        }
    }

    public function deletePhoto($path, $photoId = null)
    {
        if (!$this->selectedStudent) return;

        // Check if it's the main photo
        if ($this->selectedStudent->photo === $path) {
            // Update student record
            $this->selectedStudent->update(['photo' => null]);
            
            // Delete file
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } 
        
        // Check if it's in student_photos table (either main or additional)
        // If photoId is provided, delete by ID
        if ($photoId) {
            $photo = StudentPhoto::find($photoId);
            if ($photo) {
                $photo->delete();
                // File deletion is handled above if it was main, or we do it here if it's different file
                if ($photo->file_path !== $path && Storage::disk('public')->exists($photo->file_path)) {
                     Storage::disk('public')->delete($photo->file_path);
                } elseif ($photo->file_path === $path && !$this->selectedStudent->photo) {
                     // If it was main and we already cleared main, file might still exist if we didn't check
                     // But usually we delete file by path.
                     // Simpler approach: Just delete file by path if exists.
                }
            }
        } else {
            // If no ID (main photo case from array structure), try to find in student_photos by path
            StudentPhoto::where('file_path', $path)->delete();
        }

        // Ensure file is deleted if not already
        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $this->dispatch('alert', [['type' => 'success', 'title' => 'Terhapus', 'text' => 'Foto berhasil dihapus.']]);
        
        // Refresh photos
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

    // Propagation Methods
    public function openPropagationModal()
    {
        $this->reset(['propagationFile', 'previewData', 'processingMessage', 'photoType']);
        $this->photoType = 'Formal'; // Reset to default
        
        // Load all students for dropdown (id and name only to be lightweight)
        $this->allStudents = $this->school->students()->select('id', 'name', 'nis', 'nisn')->orderBy('name')->get()->toArray();
        
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
            'propagationFile' => 'required|file|mimes:zip|max:51200', // Max 50MB
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
                    
                    // Skip directories and macOS hidden files
                    if (substr($filename, -1) == '/' || strpos($filename, '__MACOSX') !== false || strpos($fileInfo['basename'], '.') === 0) {
                        continue;
                    }

                    // Check allowed extensions
                    if (!in_array(strtolower($fileInfo['extension']), ['jpg', 'jpeg', 'png'])) {
                        continue;
                    }

                    // Parse filename: NISN_Nama.ext or NIS_Nama.ext or Just_Nama.ext
                    $parts = explode('_', $fileInfo['filename'], 2);
                    $identifier = $parts[0]; // Can be NIS, NISN, or Name
                    $nameFromFile = isset($parts[1]) ? $parts[1] : '';

                    // Find student match
                    $student = $this->school->students()
                        ->where(function($q) use ($identifier) {
                            $q->where('nis', $identifier)
                              ->orWhere('nisn', $identifier);
                        })
                        ->first();

                    $status = 'Siswa Tidak Ditemukan';
                    $statusClass = 'danger';
                    $valid = false;
                    $similarity = 0;
                    $matchType = 'Identifier';

                    // Fallback: Match by Name if Identifier Match fails
                    if (!$student) {
                        // Try exact name match with the whole filename (assuming file is named "Name.ext")
                        // or the first part if underscore exists but didn't match NIS
                        $potentialName = $fileInfo['filename'];
                        
                        // Check exact match first
                        $studentsByName = $this->school->students()
                            ->where('name', $potentialName)
                            ->get();

                        if ($studentsByName->count() === 1) {
                            $student = $studentsByName->first();
                            $valid = true;
                            $status = 'Valid (Match by Name)';
                            $statusClass = 'success';
                            $similarity = 100;
                            $matchType = 'Name';
                            $nameFromFile = $potentialName; // Override name from file for display
                            $identifier = '-'; // No identifier found
                        } elseif ($studentsByName->count() > 1) {
                            $status = 'Invalid (Nama Duplikat)';
                            $statusClass = 'danger';
                            $nameFromFile = $potentialName;
                        }
                    }

                    if ($student && $matchType === 'Identifier') {
                        // Calculate similarity between filename name and database name
                        // Remove extension from name part if it exists
                        $cleanNameFromFile = pathinfo($nameFromFile, PATHINFO_FILENAME);
                        
                        // Use similar_text for percentage
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
                        'index' => count($this->previewData), // Add index for easy update
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
        if (!isset($this->previewData[$index])) return;
        
        if (empty($studentId)) {
            // If cleared
            $this->previewData[$index]['student_id'] = null;
            $this->previewData[$index]['matched_student'] = '-';
            $this->previewData[$index]['status'] = 'Siswa Tidak Ditemukan';
            $this->previewData[$index]['status_class'] = 'danger';
            $this->previewData[$index]['valid'] = false;
            return;
        }

        // Find selected student in loaded list
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
                
                // Extract only valid files
                foreach ($validItems as $item) {
                    $zip->extractTo($extractPath, $item['filename']);
                    
                    $sourcePath = $extractPath . '/' . $item['filename'];
                    $extension = pathinfo($item['filename'], PATHINFO_EXTENSION);
                    $newFilename = 'student-photos/' . $this->school->id . '/' . $item['student_id'] . '_' . Str::random(8) . '.' . $extension;
                    
                    // Store to public disk
                    Storage::disk('public')->put($newFilename, file_get_contents($sourcePath));
                    
                    // Update main photo if formal
                    if ($this->photoType === 'Formal') {
                        Student::where('id', $item['student_id'])->update(['photo' => $newFilename]);
                    }

                    // Save to student_photos table
                    StudentPhoto::create([
                        'student_id' => $item['student_id'],
                        'photo_type' => $this->photoType,
                        'file_path' => $newFilename
                    ]);
                }
                
                $zip->close();
                
                // Cleanup temp
                Storage::deleteDirectory('temp/propagation'); // Clean specific temp dir manually if needed or use File facade
                // Simple cleanup since extractPath is absolute
                $this->rrmdir($extractPath);
                
                // Delete the uploaded ZIP file
                if ($this->propagationFile) {
                    Storage::delete($this->propagationFile->getRealPath());
                    // Since Livewire uses temporary upload, we might need to delete from the livewire-tmp directory
                    // But typically getRealPath points to the tmp file which might be managed by PHP
                    // However, we can also delete the Livewire temporary file using the store method return value logic if we saved it, 
                    // but here it is a TemporaryUploadedFile object.
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

    // Helper to recursively remove directory
    private function rrmdir($dir) { 
        if (is_dir($dir)) { 
            $objects = scandir($dir); 
            foreach ($objects as $object) { 
                if ($object != "." && $object != "..") { 
                    if (is_dir($dir. DIRECTORY_SEPARATOR .$object) && !is_link($dir."/".$object))
                        $this->rrmdir($dir. DIRECTORY_SEPARATOR .$object);
                    else
                        unlink($dir. DIRECTORY_SEPARATOR .$object); 
                } 
            } 
            rmdir($dir); 
        } 
    }

    public function render()
    {
        $students = $this->school->students()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('nis', 'like', '%' . $this->search . '%')
                      ->orWhere('nisn', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.student.by-school', [
            'students' => $students
        ])
        ->layout('layouts.dashboard')
        ->title('Data Siswa - ' . $this->school->name);
    }
}
