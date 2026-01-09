<?php

namespace App\Livewire\Sekolah\Teacher;

use App\Models\Limit; // Assuming Limit model exists or similar logic needed, but here we likely don't need limits for teachers yet.
use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

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
        'nip' => 'required|string|max:50', // Unique check done manually to scope to user table logic
        'email' => 'required|email|max:255', // Unique check done manually
    ];

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

        $school = School::where('user_id', Auth::id())->firstOrFail();

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
        Teacher::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
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

        $teacher = Teacher::with('user')->findOrFail($id);

        // Ensure teacher belongs to this school
        $school = School::where('user_id', Auth::id())->firstOrFail();
        if ($teacher->school_id !== $school->id) {
            abort(403);
        }

        $this->teacherId = $teacher->id;
        $this->name = $teacher->user->name;
        $this->nip = $teacher->nip; // NIP also acts as username
        $this->email = $teacher->user->email;
        $this->isActive = $teacher->user->is_active;

        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $teacher = Teacher::with('user')->findOrFail($this->teacherId);
        $user = $teacher->user;

        // Ensure teacher belongs to this school
        $school = School::where('user_id', Auth::id())->firstOrFail();
        if ($teacher->school_id !== $school->id) {
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
        $teacher = Teacher::with('user')->findOrFail($id);
        $school = School::where('user_id', Auth::id())->firstOrFail();

        if ($teacher->school_id !== $school->id) {
            abort(403);
        }

        // Delete User (Cascade delete handler in DB usually handles this, but let's be safe)
        // Check if DB cascade is set up in migration. 
        // Migration: $table->foreignId('user_id')->constrained()->onDelete('cascade'); -> Yes for teacher table.
        // But we want to delete the USER, not just the teacher record.

        $user = $teacher->user;
        $user->delete(); // This will delete the teacher record via cascade if user_id FK has cascade.

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data guru berhasil dihapus.',
        ]);
    }

    public function confirmResetPassword($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        $school = School::where('user_id', Auth::id())->firstOrFail();

        if ($teacher->school_id !== $school->id) {
            abort(403);
        }

        $this->resetTeacherId = $teacher->id;
        $this->resetTeacherName = $teacher->user->name;
        $this->showResetModal = true;
    }

    public function resetPassword()
    {
        $teacher = Teacher::with('user')->findOrFail($this->resetTeacherId);

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
        $user = Auth::user();
        $school = School::where('user_id', $user->id)->first();

        // Fallback checks roughly
        if (!$school) {
            return view('livewire.sekolah.teacher.index', ['teachers' => collect([])])->layout('layouts.dashboard');
        }

        $teachers = Teacher::with('user')
            ->where('school_id', $school->id)
            ->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%') // NIP
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.sekolah.teacher.index', [
            'teachers' => $teachers,
        ])
            ->layout('layouts.dashboard')
            ->title('Data Guru');
    }
}
