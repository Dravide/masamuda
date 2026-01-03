<?php

namespace App\Livewire\Admin;

use App\Models\Project;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentPhoto;
use App\Models\AcademicYear;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        // Statistics
        $totalProjects = Project::count();
        $draftProjects = Project::where('status', 'draft')->count();
        $activeProjects = Project::where('status', 'active')->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $totalStudents = Student::count();
        $totalSchools = School::count();
        $totalPhotos = StudentPhoto::count();
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        // Recent projects
        $recentProjects = Project::with(['school', 'academicYear'])
            ->withCount('students')
            ->latest()
            ->take(5)
            ->get();

        // Projects by status for chart
        $projectsByStatus = [
            'draft' => $draftProjects,
            'active' => $activeProjects,
            'completed' => $completedProjects,
        ];

        // Top schools by students
        $topSchools = School::withCount('students')
            ->orderByDesc('students_count')
            ->take(5)
            ->get();

        return view('livewire.admin.dashboard', [
            'totalProjects' => $totalProjects,
            'draftProjects' => $draftProjects,
            'activeProjects' => $activeProjects,
            'completedProjects' => $completedProjects,
            'totalStudents' => $totalStudents,
            'totalSchools' => $totalSchools,
            'totalPhotos' => $totalPhotos,
            'activeAcademicYear' => $activeAcademicYear,
            'recentProjects' => $recentProjects,
            'projectsByStatus' => $projectsByStatus,
            'topSchools' => $topSchools,
        ])->layout('layouts.dashboard')->title('Dashboard Admin');
    }
}
