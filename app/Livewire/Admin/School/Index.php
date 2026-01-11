<?php

namespace App\Livewire\Admin\School;

use App\Models\School;
use App\Models\User;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

class Index extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $statusFilter = 'all'; // all, negeri, swasta
    public $perPage = 10;

    // Form Properties
    public $npsn;
    public $name;
    public $status = 'negeri';
    public $logo;
    public $newLogo;
    public $address;
    public $district;
    public $city;
    public $province;
    public $postal_code;
    public $rt_rw;
    public $latitude;
    public $longitude;
    public $phone;
    public $email;
    public $is_verified = false;

    public $schoolId;
    public $isEdit = false;
    public $showModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = School::query();

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('npsn', 'like', '%' . $this->search . '%')
                    ->orWhere('city', 'like', '%' . $this->search . '%')
                    ->orWhere('district', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $schools = $query->orderBy('created_at', 'desc')->paginate($this->perPage);

        return view('livewire.admin.school.index', ['schools' => $schools])
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
            'npsn' => 'required|numeric|digits:8|unique:schools,npsn',
            'name' => 'required|string|max:255',
            'status' => 'required|in:negeri,swasta',
            'logo' => 'nullable|image|max:2048', // 2MB Max
            'address' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|numeric|digits:5',
            'rt_rw' => 'nullable|regex:/^\d{1,3}\/\d{1,3}$/',
            'latitude' => 'nullable|numeric|between:-11,6',
            'longitude' => 'nullable|numeric|between:95,141',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $logoPath = null;
        if ($this->logo) {
            $logoPath = $this->logo->store('schools/logos', 'public');
        }

        $school = School::create([
            'npsn' => $this->npsn,
            'name' => $this->name,
            'status' => $this->status,
            'logo' => $logoPath,
            'address' => $this->address,
            'district' => $this->district,
            'city' => $this->city,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'rt_rw' => $this->rt_rw,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_verified' => $this->is_verified,
        ]);

        $this->logAudit('create', 'Created School: ' . $school->name . ' (' . $school->npsn . ')');

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data Sekolah Berhasil Ditambahkan.',
        ]);

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $school = School::findOrFail($id);
        $this->schoolId = $id;
        $this->npsn = $school->npsn;
        $this->name = $school->name;
        $this->status = $school->status;
        $this->address = $school->address;
        $this->district = $school->district;
        $this->city = $school->city;
        $this->province = $school->province;
        $this->postal_code = $school->postal_code;
        $this->rt_rw = $school->rt_rw;
        $this->latitude = $school->latitude;
        $this->longitude = $school->longitude;
        $this->phone = $school->phone;
        $this->email = $school->email;
        $this->is_verified = $school->is_verified;
        // Store old logo path in temporary property if needed, but we use newLogo for upload
        $this->logo = $school->logo; // For display

        $this->isEdit = true;
        $this->showModal = true;
    }

    public function update()
    {
        $this->validate([
            'npsn' => ['required', 'numeric', 'digits:8', Rule::unique('schools')->ignore($this->schoolId)],
            'name' => 'required|string|max:255',
            'status' => 'required|in:negeri,swasta',
            'newLogo' => 'nullable|image|max:2048',
            'address' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|numeric|digits:5',
            'rt_rw' => 'nullable|regex:/^\d{1,3}\/\d{1,3}$/',
            'latitude' => 'nullable|numeric|between:-11,6',
            'longitude' => 'nullable|numeric|between:95,141',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $school = School::findOrFail($this->schoolId);

        $logoPath = $school->logo;
        if ($this->newLogo) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }
            $logoPath = $this->newLogo->store('schools/logos', 'public');
        }

        $school->update([
            'npsn' => $this->npsn,
            'name' => $this->name,
            'status' => $this->status,
            'logo' => $logoPath,
            'address' => $this->address,
            'district' => $this->district,
            'city' => $this->city,
            'province' => $this->province,
            'postal_code' => $this->postal_code,
            'rt_rw' => $this->rt_rw,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'phone' => $this->phone,
            'email' => $this->email,
            'is_verified' => $this->is_verified,
        ]);

        $this->logAudit('update', 'Updated School: ' . $school->name . ' (' . $school->npsn . ')');

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Berhasil!',
            'text' => 'Data Sekolah Berhasil Diupdate.',
        ]);

        $this->closeModal();
        $this->resetInputFields();
    }

    public function confirmDelete($id)
    {
        $school = School::findOrFail($id);
        $this->dispatch('swal:confirm-delete', [
            'id' => $id,
            'title' => 'Hapus Sekolah?',
            'text' => 'Data sekolah ' . $school->name . ' beserta user dan logo akan dihapus permanen.',
        ]);
    }

    public function destroy($id)
    {
        $school = School::findOrFail($id);
        if ($school->logo) {
            Storage::disk('public')->delete($school->logo);
        }

        // Delete associated user account if exists
        if ($school->user_id) {
            $user = User::find($school->user_id);
            if ($user) {
                $user->delete();
            }
        }

        $school->delete();

        $this->logAudit('delete', 'Deleted School: ' . $school->name . ' (' . $school->npsn . ')');

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Terhapus!',
            'text' => 'Data Sekolah Berhasil Dihapus.',
        ]);
    }

    public function generateAccount($id)
    {
        $school = School::findOrFail($id);

        if ($school->user_id) {
            $this->dispatch('alert', [
                'type' => 'error',
                'title' => 'Gagal!',
                'text' => 'Akun sekolah sudah ada.',
            ]);
            return;
        }

        // Check if username/email already exists
        $username = $school->npsn;
        $email = $school->npsn . '@masamudastudio.id';

        if (User::where('username', $username)->exists() || User::where('email', $email)->exists()) {
            $this->dispatch('alert', [
                'type' => 'error',
                'title' => 'Gagal!',
                'text' => 'Username atau Email sudah digunakan.',
            ]);
            return;
        }

        $user = User::create([
            'name' => $school->name,
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($username), // Initial password same as username
            'role' => 'sekolah',
            'is_active' => true,
            'password_change_required' => true,
        ]);

        $school->update(['user_id' => $user->id]);

        $this->logAudit('create', 'Generated Account for School: ' . $school->name . ' (' . $username . ')');

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Akun Dibuat!',
            'text' => "Username: {$username}\nPassword: {$username}",
        ]);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInputFields();
    }

    public function export($format)
    {
        // Simple export implementation logic placeholder
        // In a real app, use Maatwebsite/Excel or native CSV generation
        return response()->streamDownload(function () {
            $schools = School::all();
            echo "NPSN,Nama Sekolah,Status,Kota,Provinsi\n";
            foreach ($schools as $school) {
                echo "{$school->npsn},\"{$school->name}\",{$school->status},\"{$school->city}\",\"{$school->province}\"\n";
            }
        }, 'schools-export-' . date('Y-m-d') . '.csv');
    }

    private function resetInputFields()
    {
        $this->npsn = '';
        $this->name = '';
        $this->status = 'negeri';
        $this->logo = null;
        $this->newLogo = null;
        $this->address = '';
        $this->district = '';
        $this->city = '';
        $this->province = '';
        $this->postal_code = '';
        $this->rt_rw = '';
        $this->latitude = '';
        $this->longitude = '';
        $this->phone = '';
        $this->email = '';
        $this->is_verified = false;
        $this->schoolId = null;
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
