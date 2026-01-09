<?php

namespace App\Livewire\Admin\User;

use App\Models\User;
use App\Models\School;
use App\Models\Student;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filter properties
    public $search = '';
    public $roleFilter = [];
    public $statusFilter = 'all'; // all, active, inactive

    // Form properties
    public $name;
    public $username;
    public $email;
    public $role;
    public $password;
    public $password_confirmation;
    public $is_active = true;
    public $userId;
    public $isEdit = false;
    public $showModal = false;
    public $deleteId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'roleFilter' => ['except' => []],
        'statusFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingRoleFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::query();

        // Search logic
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%');
            });
        }

        // Role Filter logic
        if (!empty($this->roleFilter)) {
            $query->whereIn('role', $this->roleFilter);
        }

        // Status Filter logic
        if ($this->statusFilter !== 'all') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin.user.index', ['users' => $users])
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
        $this->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'role' => 'required|in:admin,sekolah,siswa',
            'password' => 'required|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'role' => $this->role,
            'password' => Hash::make($this->password),
            'is_active' => $this->is_active,
        ]);

        $this->logAudit('create', 'Created User: ' . $user->username);

        session()->flash('message', 'User Berhasil Ditambahkan.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->username = $user->username;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->is_active = $user->is_active;

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($this->userId)],
            'role' => 'required|in:admin,sekolah,siswa',
            'is_active' => 'boolean',
        ];

        // Only validate password if it's being changed
        if (!empty($this->password)) {
            $rules['password'] = 'min:8|confirmed';
        }

        $this->validate($rules);

        $user = User::findOrFail($this->userId);
        $data = [
            'name' => $this->name,
            'username' => $this->username,
            'role' => $this->role,
            'is_active' => $this->is_active,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $this->logAudit('update', 'Updated User: ' . $user->username);

        session()->flash('message', 'User Berhasil Diupdate.');
        $this->closeModal();
        $this->resetInputFields();
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'Tidak dapat menghapus akun sendiri.');
            return;
        }

        $this->deleteId = $id;
        // Logic to show delete confirmation modal can be handled in frontend
        $user->delete();
        $this->logAudit('delete', 'Deleted User: ' . $user->username);
        session()->flash('message', 'User Berhasil Dihapus.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            session()->flash('error', 'Tidak dapat menonaktifkan akun sendiri.');
            return;
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->logAudit('update', "User {$user->username} {$status}");

        session()->flash('message', "User berhasil {$status}.");
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $newPassword = 'masamuda2026';

        if ($user->role === 'sekolah') {
            $school = School::where('user_id', $user->id)->first();
            if ($school && $school->npsn) {
                $newPassword = $school->npsn;
            }
        } elseif ($user->role === 'siswa') {
            $student = Student::where('email', $user->email)->first();
            if (!$student) {
                $student = Student::where('nis', $user->username)->orWhere('nisn', $user->username)->first();
            }

            if ($student && $student->nisn) {
                $newPassword = $student->nisn;
            }
        }

        $user->password = Hash::make($newPassword);
        $user->save();

        $this->logAudit('update', "Reset password user: {$user->username}");
        session()->flash('message', "Password berhasil direset menjadi: {$newPassword}");
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->roleFilter = [];
        $this->statusFilter = 'all';
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->role = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->is_active = true;
        $this->userId = null;
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
