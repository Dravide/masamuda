<?php

namespace App\Livewire\Sekolah\Student;

use App\Models\School;
use App\Models\Student;
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
    public $birth_date;
    public $major;

    // Helpers
    public $school;
    public $isSmp = false;
    public $availableMajors = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        $this->school = School::where('user_id', Auth::id())->firstOrFail();
        $this->isSmp = $this->school->jenjang === 'smp';

        if (!$this->isSmp) {
            $this->availableMajors = [
                'IPA',
                'IPS',
                'Bahasa',
                'Teknik Komputer dan Jaringan',
                'Rekayasa Perangkat Lunak',
                'Multimedia',
                'Akuntansi',
                'Perkantoran',
                'Pemasaran',
                'Perhotelan',
                'Tata Boga',
                'Tata Busana',
                'Teknik Kendaraan Ringan',
                'Teknik Sepeda Motor',
            ];
        }
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
        $query = Student::where('school_id', $this->school->id);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('nis', 'like', '%' . $this->search . '%')
                    ->orWhere('nisn', 'like', '%' . $this->search . '%')
                    ->orWhere('major', 'like', '%' . $this->search . '%');
            });
        }

        $students = $query->orderBy($this->sortColumn, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.sekolah.student.index', [
            'students' => $students
        ])->layout('layouts.dashboard')->title('Data Siswa');
    }

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function edit($id)
    {
        $student = Student::where('school_id', $this->school->id)->findOrFail($id);

        $this->studentId = $student->id;
        $this->nis = $student->nis;
        $this->nisn = $student->nisn;
        $this->name = $student->name;
        $this->whatsapp = $student->whatsapp;
        $this->email = $student->email;
        $this->grade = $student->grade;
        $this->class_name = $student->class_name;
        $this->address = $student->address;
        $this->birth_date = $student->birth_date->format('Y-m-d');
        $this->major = $student->major;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate([
            'nis' => 'required|string|max:50',
            'nisn' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'grade' => 'required|string|max:10',
            'class_name' => 'required|string|max:50',
            'address' => 'required|string',
            'birth_date' => 'required|date',
            'major' => 'required|string',
        ]);

        Student::create([
            'school_id' => $this->school->id,
            'nis' => $this->nis,
            'nisn' => $this->nisn,
            'name' => $this->name,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'grade' => $this->grade,
            'class_name' => $this->class_name,
            'address' => $this->address,
            'birth_date' => $this->birth_date,
            'major' => $this->major,
        ]);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data Siswa Berhasil Disimpan.',
        ]);

        $this->closeModal();
    }

    public function update()
    {
        $this->validate([
            'nis' => 'required|string|max:50',
            'nisn' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'grade' => 'required|string|max:10',
            'class_name' => 'required|string|max:50',
            'address' => 'required|string',
            'birth_date' => 'required|date',
            'major' => 'required|string',
        ]);

        $student = Student::where('school_id', $this->school->id)->findOrFail($this->studentId);

        $student->update([
            'nis' => $this->nis,
            'nisn' => $this->nisn,
            'name' => $this->name,
            'whatsapp' => $this->whatsapp,
            'email' => $this->email,
            'grade' => $this->grade,
            'class_name' => $this->class_name,
            'address' => $this->address,
            'birth_date' => $this->birth_date,
            'major' => $this->major,
        ]);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data Siswa Berhasil Diperbarui.',
        ]);

        $this->closeModal();
    }

    public function delete($id)
    {
        $student = Student::where('school_id', $this->school->id)->findOrFail($id);
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
        $this->reset(['nis', 'nisn', 'name', 'whatsapp', 'email', 'grade', 'class_name', 'address', 'birth_date', 'studentId']);

        if (!$this->isSmp) {
            $this->reset('major');
        } else {
            $this->major = 'UMUM';
        }
    }

    public function export()
    {
        return response()->streamDownload(function () {
            $query = Student::where('school_id', $this->school->id);

            // Apply current filters if desired, or export all
            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('nis', 'like', '%' . $this->search . '%')
                        ->orWhere('nisn', 'like', '%' . $this->search . '%')
                        ->orWhere('major', 'like', '%' . $this->search . '%');
                });
            }

            $students = $query->orderBy($this->sortColumn, $this->sortDirection)->get();

            echo "NIS,NISN,Nama Lengkap,Jurusan,Tingkat,Kelas,Email,WhatsApp,Tanggal Lahir,Alamat\n";

            foreach ($students as $student) {
                echo "{$student->nis},{$student->nisn},\"{$student->name}\",\"{$student->major}\",{$student->grade},\"{$student->class_name}\",\"{$student->email}\",\"{$student->whatsapp}\",{$student->birth_date->format('Y-m-d')},\"{$student->address}\"\n";
            }
        }, 'data-siswa-' . date('Y-m-d') . '.csv');
    }
}
