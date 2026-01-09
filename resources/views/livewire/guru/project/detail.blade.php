<div>
    <div class="container-fluid p-0">
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
            <div class="clearfix">
                <h1 class="app-page-title mb-1">{{ $project->name }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('guru.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('guru.project.index') }}">Project</a>
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
                        <i class="fi fi-rr-download me-1"></i> Unduh Semua Foto
                    </button>
                @endif
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-sm-6">
                <div class="card card-action action-border-primary h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="avatar avatar-lg bg-primary-subtle text-primary rounded-circle me-3">
                            <i class="fi fi-sr-folder fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Nama Project</div>
                            <h6 class="mb-0 fw-bold">{{ Str::limit($project->name, 25) }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-action action-border-info h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="avatar avatar-lg bg-info-subtle text-info rounded-circle me-3">
                            <i class="fi fi-sr-calendar fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Tahun Ajaran</div>
                            <h6 class="mb-0 fw-bold">{{ $project->academicYear->year_name ?? '-' }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-action action-border-warning h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="avatar avatar-lg bg-warning-subtle text-warning rounded-circle me-3">
                            <i class="fi fi-sr-users fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Total Siswa</div>
                            <h4 class="mb-0 fw-bold">{{ number_format($totalStudents) }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card card-action action-border-success h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="avatar avatar-lg bg-success-subtle text-success rounded-circle me-3">
                            <i class="fi fi-sr-picture fs-3"></i>
                        </div>
                        <div>
                            <div class="text-muted small mb-1">Siswa dengan Foto</div>
                            <h4 class="mb-0 fw-bold">{{ number_format($withPhotoCount) }}
                                @if($totalStudents > 0)
                                    <small
                                        class="text-muted fs-6 font-weight-normal">({{ round(($withPhotoCount / $totalStudents) * 100) }}%)</small>
                                @endif
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <h5 class="card-title mb-0">Daftar Siswa</h5>
                <div class="d-flex align-items-center gap-2">
                    <div class="search-box">
                        <input type="text" class="form-control form-control-sm" placeholder="Search..."
                            wire:model.live.debounce.300ms="search">
                    </div>
                    <select class="form-select form-select-sm w-auto" wire:model.live="filterGrade">
                        <option value="">Semua Kelas</option>
                        @foreach($availableGrades as $grade)
                            <option value="{{ $grade }}">Kelas {{ $grade }}</option>
                        @endforeach
                    </select>
                    <select class="form-select form-select-sm w-auto" wire:model.live="perPage">
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
                                <td>{{ $student->nis }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-2">
                                            <i class="fi fi-rr-user"></i>
                                        </div>
                                        <span class="fw-medium">{{ $student->name }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $student->grade }} {{ $student->class_name }}</span>
                                </td>
                                <td class="text-center">
                                    @if($student->photos_count > 0)
                                        <span class="badge bg-success">{{ $student->photos_count }}</span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">0</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($student->photos_count > 0)
                                        <button wire:click="downloadStudentPhotos({{ $student->id }})"
                                            class="btn btn-sm btn-subtle-success btn-icon waves-effect" title="Unduh Foto">
                                            <i class="fi fi-rr-download"></i>
                                        </button>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fi fi-rr-search fs-1 d-block mb-2"></i>
                                    Tidak ada data siswa ditemukan.
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
    </div>
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