<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ChangePassword;
use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\AcademicYear\Index as AcademicYearIndex;
use App\Livewire\Admin\User\Index as UserIndex;
use App\Livewire\Admin\School\Index as SchoolIndex;
use App\Livewire\Admin\Student\ProjectList as AdminStudentProjectList;
use App\Livewire\Admin\Student\ByProject as AdminStudentByProject;
use App\Livewire\Admin\Settings as AdminSettings;
use App\Livewire\Admin\Major\Index as MajorIndex;
use App\Livewire\Sekolah\Dashboard as SekolahDashboard;
use App\Livewire\Sekolah\Student\Index as StudentIndex;
use App\Livewire\Sekolah\Student\Import as StudentImport;
use App\Livewire\Sekolah\Project\Index as ProjectIndex;
use App\Livewire\Sekolah\Project\StudentList as ProjectStudentList;
use App\Livewire\Sekolah\Project\StudentImport as ProjectStudentImport;
use App\Livewire\Sekolah\Project\PhotoDownload as ProjectPhotoDownload;
use App\Livewire\Siswa\Dashboard as SiswaDashboard;
use App\Livewire\Public\Student\Profile as PublicStudentProfile;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    if (Auth::check()) {
        $role = Auth::user()->role;
        if (in_array($role, ['admin', 'sekolah', 'siswa', 'guru'])) {
            return redirect()->route($role . '.dashboard');
        }
    }
    return redirect()->route('login');
});

Route::get('/student/access/{token}', PublicStudentProfile::class)->name('student.public.profile');

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::post('/logout', function () {
    // Audit Trail for Logout
    if (Auth::check()) {
        \App\Models\AuditTrail::create([
            'user_id' => Auth::id(),
            'activity' => 'logout',
            'description' => 'User logged out',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/login');
})->name('logout')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/change-password', ChangePassword::class)->name('password.change');
    // You might need a separate route/method if you want standard controller action, 
    // but Livewire component handles submission internally. 
    // We keep route name consistent for middleware check.
});

Route::middleware(['auth', 'force.password.change'])->group(function () {

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
        Route::get('/tahun-pelajaran', AcademicYearIndex::class)->name('tahun-pelajaran.index');
        Route::get('/pengguna', UserIndex::class)->name('pengguna.index');
        Route::get('/sekolah', SchoolIndex::class)->name('sekolah.index');
        Route::get('/sekolah/{school}/guru', \App\Livewire\Admin\School\Teacher::class)->name('sekolah.guru');
        Route::get('/jurusan', MajorIndex::class)->name('jurusan.index');
        Route::get('/project', AdminStudentProjectList::class)->name('project.index');
        Route::get('/project/{project}', AdminStudentByProject::class)->name('project.show');
        Route::get('/project/{project}/siswa/import', \App\Livewire\Admin\Student\Import::class)->name('project.siswa.import');
        Route::get('/settings', AdminSettings::class)->name('settings');
    });

    Route::middleware(['role:sekolah'])->prefix('sekolah')->name('sekolah.')->group(function () {
        Route::get('/dashboard', SekolahDashboard::class)->name('dashboard');
        Route::get('/project', ProjectIndex::class)->name('project.index');
        Route::get('/project/{project}/siswa', ProjectStudentList::class)->name('project.siswa');
        Route::get('/project/{project}/siswa/import', ProjectStudentImport::class)->name('project.siswa.import');
        Route::get('/foto', ProjectPhotoDownload::class)->name('foto.index');
        Route::get('/foto/{project}', ProjectPhotoDownload::class)->name('foto.show');
        Route::get('/guru', \App\Livewire\Sekolah\Teacher\Index::class)->name('guru.index');
    });

    Route::middleware(['role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/dashboard', SiswaDashboard::class)->name('dashboard');
    });

    Route::middleware(['role:guru'])->prefix('guru')->name('guru.')->group(function () {
        Route::get('/dashboard', \App\Livewire\Guru\Dashboard::class)->name('dashboard');
        Route::get('/project', \App\Livewire\Guru\Project\Index::class)->name('project.index');
        Route::get('/project/{project}', \App\Livewire\Guru\Project\Detail::class)->name('project.detail');
    });

});
