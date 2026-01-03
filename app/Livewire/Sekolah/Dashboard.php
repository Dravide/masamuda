<?php

namespace App\Livewire\Sekolah;

use App\Models\AcademicYear;
use App\Models\Project;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentPhoto;
use App\Models\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Dashboard extends Component
{
    public $showPasswordChangeModal = false;

    // Password Change Properties
    public $current_password;
    public $password;
    public $password_confirmation;

    public $school;

    public function mount()
    {
        $this->school = School::where('user_id', Auth::id())->first();

        if (Auth::check() && Auth::user()->password_change_required) {
            $this->showPasswordChangeModal = true;
        }
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|current_password',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'different:current_password',
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
            ],
        ], [
            'password.regex' => 'Password baru harus mengandung huruf besar, huruf kecil, dan angka.',
            'password.different' => 'Password baru tidak boleh sama dengan password lama.',
        ]);

        $user = Auth::user();
        $user->password = Hash::make($this->password);
        $user->password_change_required = false;
        $user->save();

        // Audit Log
        AuditTrail::create([
            'user_id' => $user->id,
            'activity' => 'password_change',
            'description' => 'User changed password via forced modal',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->showPasswordChangeModal = false;
        $this->reset(['current_password', 'password', 'password_confirmation']);

        $this->dispatch('alert', [
            'type' => 'success',
            'title' => 'Sukses!',
            'text' => 'Password berhasil diubah.',
        ]);
    }

    public function render()
    {
        // Get school and related statistics
        $school = $this->school;
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        // Statistics
        $totalProjects = 0;
        $draftProjects = 0;
        $activeProjects = 0;
        $completedProjects = 0;
        $totalStudents = 0;
        $totalPhotos = 0;
        $recentProjects = collect();

        if ($school) {
            $totalProjects = Project::where('school_id', $school->id)->count();
            $draftProjects = Project::where('school_id', $school->id)->where('status', 'draft')->count();
            $activeProjects = Project::where('school_id', $school->id)->where('status', 'active')->count();
            $completedProjects = Project::where('school_id', $school->id)->where('status', 'completed')->count();
            $totalStudents = Student::where('school_id', $school->id)->count();
            $totalPhotos = StudentPhoto::whereHas('student', function ($q) use ($school) {
                $q->where('school_id', $school->id);
            })->count();

            $recentProjects = Project::where('school_id', $school->id)
                ->with('academicYear')
                ->withCount('students')
                ->latest()
                ->take(5)
                ->get();
        }

        return view('livewire.sekolah.dashboard', [
            'school' => $school,
            'activeAcademicYear' => $activeAcademicYear,
            'totalProjects' => $totalProjects,
            'draftProjects' => $draftProjects,
            'activeProjects' => $activeProjects,
            'completedProjects' => $completedProjects,
            'totalStudents' => $totalStudents,
            'totalPhotos' => $totalPhotos,
            'recentProjects' => $recentProjects,
        ])->layout('layouts.dashboard')->title('Dashboard Sekolah');
    }
}
