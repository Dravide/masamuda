<div>
    <div class="container">
        @if(!$project)
            <!-- Project List View -->
            <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
                <div class="clearfix">
                    <h1 class="app-page-title">Unduh Foto</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekolah.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Unduh Foto</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row g-4">
                @forelse($projects as $proj)
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-action action-border-primary h-100 position-relative">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="avatar avatar-lg bg-primary-subtle text-primary rounded-circle me-3">
                                        <i class="fi fi-sr-folder fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 fw-bold">{{ $proj->name }}</h5>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <span class="badge bg-info-subtle text-info">{{ $proj->type }}</span>
                                            <span class="badge bg-secondary">{{ $proj->academicYear->year_name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar-sm bg-success-subtle text-success rounded-circle">
                                            <i class="fi fi-rr-users"></i>
                                        </span>
                                        <span class="text-muted small">{{ $proj->students_count }} siswa</span>
                                    </div>
                                    <button wire:click="selectProject({{ $proj->id }})"
                                        class="btn btn-sm btn-primary waves-effect waves-light">
                                        <i class="fi fi-rr-download me-1"></i> Pilih
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="fi fi-rr-folder-open fs-1 text-muted mb-3 d-block"></i>
                                <h5 class="text-muted">Tidak ada project aktif</h5>
                                <p class="text-muted mb-0">Hubungi admin untuk mengaktifkan project Anda.</p>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>

        @else
            <!-- Student Download View -->
            <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
                <div class="clearfix">
                    <h1 class="app-page-title">{{ $project->name }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekolah.dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('sekolah.foto.index') }}">Unduh Foto</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $project->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <button wire:click="backToProjects" class="btn btn-outline-secondary waves-effect">
                        <i class="fi fi-rr-arrow-left me-1"></i> Kembali
                    </button>
                    @if($withPhotoCount > 0)
                        <button wire:click="downloadAllPhotos" class="btn btn-success waves-effect waves-light">
                            <i class="fi fi-rr-download me-1"></i> Unduh Semua ({{ $withPhotoCount }} siswa)
                        </button>
                    @endif
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-xxl-12">
                    <div class="row">
                        <div class="col-xxl-3 col-lg-6 col-sm-6">
                            <div class="card card-action action-border-primary p-1 position-relative">
                                <div class="card-body d-flex gap-3 align-items-center p-4">
                                    <div class="clearfix pe-2 text-primary">
                                        <i class="fi fi-sr-folder fs-1"></i>
                                    </div>
                                    <div class="clearfix">
                                        <div class="mb-1">Project</div>
                                        <h6 class="mb-0 fw-bold">{{ Str::limit($project->name, 20) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-lg-6 col-sm-6">
                            <div class="card card-action action-border-info p-1 position-relative">
                                <div class="card-body d-flex gap-3 align-items-center p-4">
                                    <div class="clearfix pe-2 text-info">
                                        <i class="fi fi-sr-calendar fs-1"></i>
                                    </div>
                                    <div class="clearfix">
                                        <div class="mb-1">Tahun Ajaran</div>
                                        <h6 class="mb-0 fw-bold">{{ $project->academicYear->year_name ?? '-' }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-lg-6 col-sm-6">
                            <div class="card card-action action-border-warning p-1 position-relative">
                                <div class="card-body d-flex gap-3 align-items-center p-4">
                                    <div class="clearfix pe-2 text-warning">
                                        <i class="fi fi-sr-users fs-1"></i>
                                    </div>
                                    <div class="clearfix">
                                        <div class="mb-1">Total Siswa</div>
                                        <h3 class="mb-0 fw-bold">{{ number_format($totalStudents) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-lg-6 col-sm-6">
                            <div class="card card-action action-border-success p-1 position-relative">
                                <div class="card-body d-flex gap-3 align-items-center p-4">
                                    <div class="clearfix pe-2 text-success">
                                        <i class="fi fi-sr-picture fs-1"></i>
                                    </div>
                                    <div class="clearfix">
                                        <div class="mb-1">Siswa dengan Foto</div>
                                        <h3 class="mb-0 fw-bold">{{ number_format($withPhotoCount) }}
                                            @if($totalStudents > 0)
                                                <small
                                                    class="text-muted fs-6">({{ round(($withPhotoCount / $totalStudents) * 100) }}%)</small>
                                            @endif
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card">
                <div class="card-header d-flex gap-3 flex-wrap align-items-center justify-content-between">
                    <h6 class="card-title mb-0">Daftar Siswa</h6>
                    <div class="clearfix d-flex align-items-center gap-3">
                        <div class="search-box">
                            <input type="text" class="form-control" placeholder="Cari Siswa, NIS..."
                                wire:model.live.debounce.300ms="search">
                        </div>
                        <select class="form-select w-auto" wire:model.live="filterGrade">
                            <option value="">Semua Kelas</option>
                            @foreach($availableGrades as $grade)
                                <option value="{{ $grade }}">Kelas {{ $grade }}</option>
                            @endforeach
                        </select>
                        <select class="form-select w-auto" wire:model.live="perPage">
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th class="text-center">Jumlah Foto</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ $student->nis }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-2">
                                                <i class="fi fi-rr-user"></i>
                                            </div>
                                            <span class="fw-bold">{{ $student->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $student->grade }} {{ $student->class_name }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($student->photos_count > 0)
                                            <span class="badge bg-success fs-6">{{ $student->photos_count }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger">0</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($student->photos_count > 0)
                                            <button wire:click="downloadStudentPhotos({{ $student->id }})"
                                                class="btn btn-sm btn-success waves-effect">
                                                <i class="fi fi-rr-download me-1"></i> Unduh
                                            </button>
                                        @else
                                            <span class="text-muted small">Tidak ada foto</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fi fi-rr-users fs-1 d-block mb-2"></i>
                                            Tidak ada data siswa ditemukan.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($students->hasPages())
                    <div class="card-footer border-top">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('alert', (data) => {
                const alertData = data[0];
                Swal.fire({
                    icon: alertData.type,
                    title: alertData.title,
                    text: alertData.text,
                    confirmButtonColor: '#3085d6',
                });
            });
        });
    </script>
</div>