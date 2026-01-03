<?php

namespace App\Livewire\Admin\Major;

use App\Models\Major;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name;
    public $code;
    public $description;
    public $is_active = true;
    public $majorId;
    public $isEdit = false;
    public $showModal = false;

    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'nullable|string|max:50',
        'description' => 'nullable|string',
        'is_active' => 'boolean',
    ];

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Major::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        $majors = $query->orderBy('name')->paginate(10);

        return view('livewire.admin.major.index', ['majors' => $majors])
            ->layout('layouts.dashboard')
            ->title('Data Jurusan');
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

        // Check for duplicate name
        $exists = Major::where('name', $this->name)->exists();

        if ($exists) {
            $this->addError('name', 'Jurusan dengan nama ini sudah ada.');
            return;
        }

        $major = Major::create([
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        $this->logAudit('create', 'Created Major: ' . $major->name);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Jurusan berhasil ditambahkan.',
        ]);

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $major = Major::findOrFail($id);
        $this->majorId = $id;
        $this->name = $major->name;
        $this->code = $major->code;
        $this->description = $major->description;
        $this->is_active = $major->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        // Check for duplicate name (excluding current)
        $exists = Major::where('name', $this->name)
            ->where('id', '!=', $this->majorId)
            ->exists();

        if ($exists) {
            $this->addError('name', 'Jurusan dengan nama ini sudah ada.');
            return;
        }

        $major = Major::findOrFail($this->majorId);
        $major->update([
            'name' => $this->name,
            'code' => $this->code,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        $this->logAudit('update', 'Updated Major: ' . $major->name);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Jurusan berhasil diperbarui.',
        ]);

        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        $major = Major::findOrFail($id);
        $majorName = $major->name;
        $major->delete();

        $this->logAudit('delete', 'Deleted Major: ' . $majorName);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Terhapus!',
            'text' => 'Jurusan berhasil dihapus.',
        ]);
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->code = '';
        $this->description = '';
        $this->is_active = true;
        $this->majorId = null;
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
