<?php

namespace App\Livewire\Admin\School;

use App\Models\School;
use App\Models\Teacher as TeacherModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Teacher extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public School $school;
    public $search = '';
    public $perPage = 10;

    // Form properties
    public $teacherId;
    public $name;
    public $nip;
    public $email;
    public $isActive = true;

    // Modal state
    public $showModal = false;
    public $isEdit = false;

    // Password reset modal
    public $showResetModal = false;
    public $resetTeacherId;
    public $resetTeacherName;

    protected $rules = [
        'name' => 'required|string|max:255',
        'nip' => 'required|string|max:50',
        'email' => 'required|email|max:255',
    ];

    public function mount(School $school)
    {
        $this->school = $school;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->isEdit = false;
        $this->showModal = true;
    }

    public function store()
    {
        $this->validate();

        // Manual validation for unique user fields
        if (User::where('username', $this->nip)->exists()) {
            $this->addError('nip', 'NIP (Username) sudah digunakan.');
            return;
        }
        if (User::where('email', $this->email)->exists()) {
            $this->addError('email', 'Email sudah digunakan.');
            return;
        }

        // Create User
        $user = User::create([
            'name' => $this->name,
            'username' => $this->nip,
            'email' => $this->email,
            'password' => Hash::make($this->nip), // Default password is NIP
            'role' => 'guru',
            'is_active' => $this->isActive,
        ]);

        // Create Teacher Linked to School
        TeacherModel::create([
            'user_id' => $user->id,
            'school_id' => $this->school->id,
            'nip' => $this->nip,
        ]);

        $this->showModal = false;
        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data guru berhasil ditambahkan. Password default adalah NIP.',
        ]);
    }

    public function edit($id)
    {
        $this->resetForm();
        $this->isEdit = true;

        $teacher = TeacherModel::with('user')->findOrFail($id);

        if ($teacher->school_id !== $this->school->id) {
            abort(403);
        }

        $this->teacherId = $teacher->id;
        $this->name = $teacher->user->name;
        $this->nip = $teacher->nip;
        $this->email = $teacher->user->email;
        $this->isActive = $teacher->user->is_active;

        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $teacher = TeacherModel::with('user')->findOrFail($this->teacherId);
        $user = $teacher->user;

        if ($teacher->school_id !== $this->school->id) {
            abort(403);
        }

        // Validate Unique Constraints (Ignore current user)
        if (User::where('username', $this->nip)->where('id', '!=', $user->id)->exists()) {
            $this->addError('nip', 'NIP (Username) sudah digunakan user lain.');
            return;
        }
        if (User::where('email', $this->email)->where('id', '!=', $user->id)->exists()) {
            $this->addError('email', 'Email sudah digunakan user lain.');
            return;
        }

        // Update User
        $user->update([
            'name' => $this->name,
            'username' => $this->nip,
            'email' => $this->email,
            'is_active' => $this->isActive,
        ]);

        // Update Teacher
        $teacher->update([
            'nip' => $this->nip,
        ]);

        $this->showModal = false;
        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data guru berhasil diperbarui.',
        ]);
    }

    public function delete($id)
    {
        $teacher = TeacherModel::with('user')->findOrFail($id);

        if ($teacher->school_id !== $this->school->id) {
            abort(403);
        }

        $user = $teacher->user;
        $user->delete();

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data guru berhasil dihapus.',
        ]);
    }

    public function confirmResetPassword($id)
    {
        $teacher = TeacherModel::with('user')->findOrFail($id);

        if ($teacher->school_id !== $this->school->id) {
            abort(403);
        }

        $this->resetTeacherId = $teacher->id;
        $this->resetTeacherName = $teacher->user->name;
        $this->showResetModal = true;
    }

    public function resetPassword()
    {
        $teacher = TeacherModel::with('user')->findOrFail($this->resetTeacherId);

        // Reset to NIP
        $newPassword = $teacher->nip;
        if (empty($newPassword)) {
            $newPassword = 'masamuda2026';
        }

        $teacher->user->update([
            'password' => Hash::make($newPassword)
        ]);

        $this->showResetModal = false;
        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => "Password berhasil direset menjadi: {$newPassword}",
        ]);
    }

    public function resetForm()
    {
        $this->teacherId = null;
        $this->name = '';
        $this->nip = '';
        $this->email = '';
        $this->isActive = true;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showResetModal = false;
    }

    public function render()
    {
        $teachers = TeacherModel::with('user')
            ->where('school_id', $this->school->id)
            ->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.school.teacher', [
            'teachers' => $teachers,
        ])
            ->layout('layouts.dashboard')
            ->title('Kelola Guru - ' . $this->school->name);
    }
}
