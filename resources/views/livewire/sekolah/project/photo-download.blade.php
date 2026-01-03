<div>
    <div class="container">
        @if(!$project)
            <!-- Project List View -->
            <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
                <div class="clearfix">
                    <h1 class="app-page-title">Unduh Foto</h1>
                    <p class="text-muted mb-0">Pilih project untuk mengunduh foto siswa</p>
                </div>
            </div>

            <div class="row g-3">
                @forelse($projects as $proj)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="avatar avatar-lg bg-primary-subtle text-primary rounded-circle me-3">
                                        <i class="fi fi-rr-folder fs-4"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h5 class="mb-1 fw-bold">{{ $proj->name }}</h5>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <span class="badge bg-info-subtle text-info">{{ $proj->type }}</span>
                                            <span class="badge bg-secondary">{{ $proj->academicYear->year_name ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted small">
                                            <i class="fi fi-rr-users me-1"></i>{{ $proj->students_count }} siswa ada foto
                                        </span>
                                    </div>
                                    <button wire:click="selectProject({{ $proj->id }})"
                                        class="btn btn-sm btn-primary waves-effect">
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
            <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
                <div class="clearfix">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-1">
                            <li class="breadcrumb-item"><a href="{{ route('sekolah.foto.index') }}">Unduh Foto</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $project->name }}</li>
                        </ol>
                    </nav>
                    <h1 class="app-page-title">{{ $project->name }}</h1>
                    <span class="text-muted">
                        <span class="badge bg-info-subtle text-info">{{ $project->type }}</span>
                        <span class="badge bg-secondary">{{ $project->academicYear->year_name ?? '-' }}</span>
                    </span>
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

            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="avatar avatar-lg bg-primary-subtle text-primary rounded-circle me-3">
                                <i class="fi fi-rr-users fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $totalStudents }}</h3>
                                <p class="text-muted mb-0 small">Total Siswa</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="avatar avatar-lg bg-success-subtle text-success rounded-circle me-3">
                                <i class="fi fi-rr-picture fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $withPhotoCount }}</h3>
                                <p class="text-muted mb-0 small">Siswa dengan Foto</p>
                            </div>
                            @if($totalStudents > 0)
                                <div class="ms-auto">
                                    <span class="badge bg-{{ $withPhotoCount == $totalStudents ? 'success' : 'warning' }} fs-6">
                                        {{ round(($withPhotoCount / $totalStudents) * 100) }}%
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div
                            class="card-header d-flex flex-wrap gap-3 align-items-center justify-content-between border-0 pb-0">
                            <div class="d-flex gap-2 align-items-center flex-wrap">
                                <div class="search-box">
                                    <input type="text" class="form-control" placeholder="Cari Siswa, NIS..."
                                        wire:model.live.debounce.300ms="search">
                                </div>
                                <select class="form-select w-auto" wire:model.live="perPage">
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                                <select class="form-select w-auto" wire:model.live="filterGrade">
                                    <option value="">Semua Kelas</option>
                                    @foreach($availableGrades as $grade)
                                        <option value="{{ $grade }}">Kelas {{ $grade }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
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
                                                <td class="fw-bold">{{ $student->name }}</td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $student->grade }}
                                                        {{ $student->class_name }}</span>
                                                </td>
                                                <td class="text-center">
                                                    @if($student->photos_count > 0)
                                                        <span
                                                            class="badge bg-success-subtle text-success fs-6">{{ $student->photos_count }}</span>
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
                                <div class="mt-3">
                                    {{ $students->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
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