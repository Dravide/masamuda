<?php

namespace App\Livewire\Admin\Student;

use App\Imports\StudentsImport;
use App\Models\Project;
use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class Import extends Component
{
    use WithFileUploads;

    public Project $project;
    public $file;
    public $previewData = [];
    public $validData = [];
    public $invalidData = [];
    public $isUploaded = false;
    public $school;

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->school = $project->school;
    }

    public function downloadTemplate()
    {
        return response()->streamDownload(function () {
            echo "NIS,NISN,Nama Lengkap,Jurusan,Tingkat,Kelas\n";
            echo "12345,0012345678,Contoh Siswa,IPA,10,IPA 1\n";
        }, 'template-import-siswa-admin.csv');
    }

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        $this->isUploaded = true;
        $this->processPreview();
    }

    public function processPreview()
    {
        try {
            $collection = Excel::toCollection(new StudentsImport, $this->file)->first();

            $this->previewData = [];
            $this->validData = [];
            $this->invalidData = [];

            foreach ($collection as $index => $row) {
                // Skip empty rows
                if (!isset($row['nis']) && !isset($row['nama_lengkap']))
                    continue;

                $isValid = true;
                $errors = [];

                // Validation Logic
                if (empty($row['nis'])) {
                    $isValid = false;
                    $errors[] = 'NIS wajib diisi';
                } elseif (Student::where('school_id', $this->school->id)->where('nis', $row['nis'])->exists()) {
                    $isValid = false;
                    $errors[] = 'NIS sudah terdaftar';
                }

                if (empty($row['nisn'])) {
                    $isValid = false;
                    $errors[] = 'NISN wajib diisi';
                }

                if (empty($row['nama_lengkap'])) {
                    $isValid = false;
                    $errors[] = 'Nama wajib diisi';
                }

                if (empty($row['tingkat'])) {
                    $isValid = false;
                    $errors[] = 'Tingkat wajib diisi';
                }

                if (empty($row['kelas'])) {
                    $isValid = false;
                    $errors[] = 'Kelas wajib diisi';
                }

                if (empty($row['jurusan']) && $this->school->jenjang != 'smp') {
                    $isValid = false;
                    $errors[] = 'Jurusan wajib diisi';
                }

                // Date Validation (Optional)
                $birthDate = null;
                if (!empty($row['tanggal_lahir'])) {
                    try {
                        // Handle Excel date format or string
                        if (is_numeric($row['tanggal_lahir'])) {
                            $birthDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d');
                        } else {
                            $birthDate = Carbon::parse($row['tanggal_lahir'])->format('Y-m-d');
                        }
                    } catch (\Exception $e) {
                        // Optional
                        // $isValid = false;
                        // $errors[] = 'Format tanggal lahir salah';
                    }
                }

                $data = [
                    'row' => $index + 2, // Excel row number (1 header + 1 based index)
                    'nis' => $row['nis'],
                    'nisn' => $row['nisn'] ?? '',
                    'name' => $row['nama_lengkap'],
                    'major' => $row['jurusan'] ?? ($this->school->jenjang == 'smp' ? 'UMUM' : ''),
                    'grade' => $row['tingkat'] ?? null,
                    'class_name' => $row['kelas'] ?? null,
                    'email' => $row['email'] ?? null,
                    'whatsapp' => $row['whatsapp'] ?? null,
                    'birth_date' => $birthDate,
                    'address' => $row['alamat'] ?? '',
                    'status' => $isValid ? 'Valid' : 'Invalid',
                    'errors' => implode(', ', $errors),
                ];

                $this->previewData[] = $data;

                if ($isValid) {
                    $this->validData[] = $data;
                } else {
                    $this->invalidData[] = $data;
                }
            }

        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'title' => 'Error!',
                'text' => 'Gagal memproses file: ' . $e->getMessage(),
            ]);
            Log::error('Import Error: ' . $e->getMessage());
            $this->reset('file', 'isUploaded');
        }
    }

    public function import()
    {
        if (empty($this->validData)) {
            $this->dispatch('alert', [
                'type' => 'warning',
                'title' => 'Perhatian',
                'text' => 'Tidak ada data valid untuk diimport.',
            ]);
            return;
        }

        try {
            foreach ($this->validData as $data) {
                Student::create([
                    'school_id' => $this->school->id,
                    'project_id' => $this->project->id,
                    'nis' => $data['nis'],
                    'nisn' => $data['nisn'],
                    'name' => $data['name'],
                    'major' => $data['major'],
                    'grade' => $data['grade'],
                    'class_name' => $data['class_name'],
                    'email' => $data['email'],
                    'whatsapp' => $data['whatsapp'],
                    'birth_date' => $data['birth_date'],
                    'address' => $data['address'],
                ]);
            }

            $count = count($this->validData);
            Log::info("Admin ID " . Auth::id() . " imported {$count} students to project " . $this->project->id);

            $this->dispatch('alert', [
                'type' => 'success',
                'title' => 'Sukses!',
                'text' => "Berhasil mengimport {$count} data siswa.",
            ]);

            $this->reset('file', 'previewData', 'validData', 'invalidData', 'isUploaded');

            return redirect()->route('admin.project.show', $this->project);

        } catch (\Exception $e) {
            $this->dispatch('alert', [
                'type' => 'error',
                'title' => 'Gagal!',
                'text' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage(),
            ]);
            Log::error('Import Save Error: ' . $e->getMessage());
        }
    }

    public function cancel()
    {
        $this->reset('file', 'previewData', 'validData', 'invalidData', 'isUploaded');
        return redirect()->route('admin.project.show', $this->project);
    }

    public function render()
    {
        return view('livewire.admin.student.import')
            ->layout('layouts.dashboard')
            ->title('Import Siswa - ' . $this->project->name);
    }
}
