<?php

namespace App\Livewire\Admin\AcademicYear;

use App\Models\AcademicYear;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $year_name;
    public $start_date;
    public $end_date;
    public $semester;
    public $is_active = false;
    public $academicYearId;
    public $isEdit = false;
    public $showModal = false;
    public $deleteId = null;

    protected $rules = [
        'year_name' => 'required|regex:/^\d{4}\/\d{4}$/',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'semester' => 'required|in:ganjil,genap',
        'is_active' => 'boolean',
    ];

    public function render()
    {
        $academicYears = AcademicYear::orderBy('created_at', 'desc')->paginate(10);
        return view('livewire.admin.academic-year.index', ['academicYears' => $academicYears])
            ->layout('layouts.dashboard');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        // Check for duplicate year and semester
        $exists = AcademicYear::where('year_name', $this->year_name)
            ->where('semester', $this->semester)
            ->exists();

        if ($exists) {
            $this->addError('year_name', 'Tahun pelajaran dan semester ini sudah ada.');
            return;
        }

        // Check for date overlap
        if ($this->checkOverlap($this->start_date, $this->end_date)) {
            $this->addError('start_date', 'Tanggal bertabrakan dengan tahun pelajaran lain.');
            return;
        }

        if ($this->is_active) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $academicYear = AcademicYear::create([
            'year_name' => $this->year_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'semester' => $this->semester,
            'is_active' => $this->is_active,
        ]);

        $this->logAudit('create', 'Created Academic Year: ' . $academicYear->year_name);

        session()->flash('message', 'Tahun Pelajaran Berhasil Ditambahkan.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $academicYear = AcademicYear::findOrFail($id);
        $this->academicYearId = $id;
        $this->year_name = $academicYear->year_name;
        $this->start_date = $academicYear->start_date->format('Y-m-d');
        $this->end_date = $academicYear->end_date->format('Y-m-d');
        $this->semester = $academicYear->semester;
        $this->is_active = $academicYear->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        // Check for duplicate year and semester (excluding current)
        $exists = AcademicYear::where('year_name', $this->year_name)
            ->where('semester', $this->semester)
            ->where('id', '!=', $this->academicYearId)
            ->exists();

        if ($exists) {
            $this->addError('year_name', 'Tahun pelajaran dan semester ini sudah ada.');
            return;
        }

        // Check for date overlap (excluding current)
        if ($this->checkOverlap($this->start_date, $this->end_date, $this->academicYearId)) {
            $this->addError('start_date', 'Tanggal bertabrakan dengan tahun pelajaran lain.');
            return;
        }

        if ($this->is_active) {
            AcademicYear::where('id', '!=', $this->academicYearId)->where('is_active', true)->update(['is_active' => false]);
        }

        $academicYear = AcademicYear::findOrFail($this->academicYearId);
        $academicYear->update([
            'year_name' => $this->year_name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'semester' => $this->semester,
            'is_active' => $this->is_active,
        ]);

        $this->logAudit('update', 'Updated Academic Year: ' . $academicYear->year_name);

        session()->flash('message', 'Tahun Pelajaran Berhasil Diupdate.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        $academicYear = AcademicYear::findOrFail($id);

        if ($academicYear->is_active) {
            session()->flash('error', 'Tidak dapat menghapus tahun pelajaran yang sedang aktif.');
            return;
        }

        $this->deleteId = $id;
        // Logic to show delete confirmation modal can be handled in frontend
        $academicYear->delete();
        $this->logAudit('delete', 'Deleted Academic Year: ' . $academicYear->year_name);
        session()->flash('message', 'Tahun Pelajaran Berhasil Dihapus.');
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    private function resetInputFields()
    {
        $this->year_name = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->semester = '';
        $this->is_active = false;
        $this->academicYearId = null;
    }

    private function checkOverlap($start, $end, $excludeId = null)
    {
        $query = AcademicYear::where(function ($query) use ($start, $end) {
            $query->whereBetween('start_date', [$start, $end])
                ->orWhereBetween('end_date', [$start, $end])
                ->orWhere(function ($query) use ($start, $end) {
                    $query->where('start_date', '<=', $start)
                        ->where('end_date', '>=', $end);
                });
        });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    private function logAudit($activity, $description)
    {
        AuditTrail::create([
            'user_id' => Auth::id(),
            'activity' => $activity,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
