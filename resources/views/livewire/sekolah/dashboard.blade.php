<div class="container">

    <!-- Page Header -->
    <div class="app-page-head d-flex align-items-center justify-content-between">
        <div class="clearfix">
            <h1 class="app-page-title">Dashboard</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
        @if($activeAcademicYear)
            <span class="badge bg-primary-subtle text-primary fs-6 py-2 px-3">
                <i class="fi fi-rr-calendar me-1"></i> {{ $activeAcademicYear->year_name }}
                ({{ $activeAcademicYear->semester == 'ganjil' ? 'Ganjil' : 'Genap' }})
            </span>
        @endif
    </div>

    <div class="row">

        <!-- Welcome Card -->
        <div class="col-xl-6">
            <div class="card bg-warning bg-opacity-25 shadow-none border-0">
                <div class="card-body px-4 pb-0 pt-2">
                    <div class="row g-0">
                        <div class="col-sm-7 py-3 px-2">
                            <h6 class="card-title fw-bold mb-2">Selamat Datang!</h6>
                            <h2 class="text-secondary fs-1 fw-bolder mb-3">{{ $school->name ?? 'Sekolah' }}</h2>
                            <p class="text-dark fw-semibold mb-0">
                                Anda memiliki <strong class="text-primary">{{ number_format($totalProjects) }}
                                    project</strong>
                                dengan total <strong class="text-primary">{{ number_format($totalStudents) }}
                                    siswa</strong> terdaftar.
                            </p>
                        </div>
                        <div class="col-sm-5 text-center text-sm-end align-self-center">
                            @if($school && $school->logo)
                                <img src="{{ asset('storage/' . $school->logo) }}" class="img-fluid rounded-3"
                                    alt="{{ $school->name }}" style="max-height: 120px; object-fit: contain;">
                            @else
                                <div class="avatar avatar-xxl bg-primary-subtle text-primary rounded-circle mx-auto">
                                    <i class="fi fi-sr-school fs-1"></i>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Status Summary -->
        <div class="col-xl-6">
            <div class="card bg-info bg-opacity-25 shadow-none border-0">
                <div class="card-body px-4 py-2">
                    <div class="row g-0 align-items-center">
                        <div class="col-md-5 py-3 px-2">
                            <h6 class="card-title fw-bold mb-2">Status Project</h6>
                            <p class="text-dark mb-4">
                                <strong class="text-info">{{ $activeProjects }}</strong>
                                project aktif dapat menerima input data siswa.
                            </p>
                            <a href="{{ route('sekolah.project.index') }}"
                                class="btn btn-info waves-effect waves-light">
                                <i class="fi fi-rr-folder me-1"></i> Kelola Project
                            </a>
                        </div>
                        <div class="col-md-7 text-center py-3">
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="bg-white rounded-3 p-3">
                                        <h3 class="mb-0 fw-bold text-secondary">{{ $draftProjects }}</h3>
                                        <small class="text-muted">Draft</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-white rounded-3 p-3">
                                        <h3 class="mb-0 fw-bold text-success">{{ $activeProjects }}</h3>
                                        <small class="text-muted">Active</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="bg-white rounded-3 p-3">
                                        <h3 class="mb-0 fw-bold text-info">{{ $completedProjects }}</h3>
                                        <small class="text-muted">Completed</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="col-xxl-12">
            <div class="row">
                <div class="col-6 col-md-3">
                    <div class="card bg-primary bg-opacity-05 shadow-none border-0">
                        <div class="card-body">
                            <div class="avatar bg-primary shadow-primary rounded-circle text-white mb-3">
                                <i class="fi fi-sr-folder"></i>
                            </div>
                            <h3>{{ number_format($totalProjects) }}</h3>
                            <h6 class="mb-0">Total Project</h6>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card bg-success bg-opacity-05 shadow-none border-0">
                        <div class="card-body">
                            <div class="avatar bg-success shadow-success rounded-circle text-white mb-3">
                                <i class="fi fi-sr-users"></i>
                            </div>
                            <h3>{{ number_format($totalStudents) }}</h3>
                            <h6 class="mb-0">Total Siswa</h6>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card bg-info bg-opacity-05 shadow-none border-0">
                        <div class="card-body">
                            <div class="avatar bg-info shadow-info rounded-circle text-white mb-3">
                                <i class="fi fi-sr-picture"></i>
                            </div>
                            <h3>{{ number_format($totalPhotos) }}</h3>
                            <h6 class="mb-0">Total Foto</h6>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card bg-warning bg-opacity-05 shadow-none border-0">
                        <div class="card-body">
                            <div class="avatar bg-warning shadow-warning rounded-circle text-white mb-3">
                                <i class="fi fi-sr-badge-check"></i>
                            </div>
                            <h3>{{ number_format($activeProjects) }}</h3>
                            <h6 class="mb-0">Project Aktif</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0 mb-3">
                    <h6 class="card-title mb-0">Project Terbaru</h6>
                    <a href="{{ route('sekolah.project.index') }}" class="btn btn-sm btn-outline-primary waves-effect">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Project</th>
                                    <th>Tahun Ajaran</th>
                                    <th class="text-center">Siswa</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentProjects as $project)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle">
                                                    <i class="fi fi-rr-folder"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ Str::limit($project->name, 30) }}</h6>
                                                    <small class="text-muted">{{ $project->type }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-secondary">{{ $project->academicYear->year_name ?? '-' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge bg-info-subtle text-info">{{ $project->students_count }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass = match ($project->status) {
                                                    'draft' => 'secondary',
                                                    'active' => 'success',
                                                    'completed' => 'info',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($project->status) }}</span>
                                        </td>
                                        <td class="text-end">
                                            @if($project->status === 'active')
                                                <a href="{{ route('sekolah.project.data', $project->id) }}"
                                                    class="btn btn-sm btn-success waves-effect">
                                                    <i class="fi fi-rr-users me-1"></i> Input Data
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" disabled>
                                                    <i class="fi fi-rr-lock me-1"></i> Locked
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="fi fi-rr-folder fs-1 d-block mb-2"></i>
                                            Belum ada project. <a href="{{ route('sekolah.project.index') }}">Buat project
                                                baru</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Forced Password Change Modal -->
    @if($showPasswordChangeModal)
        <div class="modal fade show" id="forcePasswordChangeModal" data-bs-backdrop="static" data-bs-keyboard="false"
            tabindex="-1" aria-labelledby="forcePasswordChangeModalLabel" aria-hidden="true"
            style="display: block; background-color: rgba(0,0,0,0.7); z-index: 9999;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0 justify-content-center">
                        <div class="text-center">
                            <img src="{{ asset('template/assets/images/logo.svg') }}" alt="Logo" height="40" class="mb-3">
                            <h5 class="modal-title" id="forcePasswordChangeModalLabel">Ganti Password Wajib</h5>
                        </div>
                    </div>
                    <div class="modal-body pt-2">
                        <p class="text-center text-muted mb-4">
                            Demi keamanan akun Anda, silakan ganti password saat login pertama kali.
                            <br>
                            <small class="text-danger">* Anda tidak dapat menutup modal ini sebelum mengganti
                                password.</small>
                        </p>

                        <form wire:submit.prevent="updatePassword">
                            <div class="mb-3">
                                <label class="form-label">Password Saat Ini</label>
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                    wire:model="current_password" placeholder="Masukkan password saat ini">
                                @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    wire:model="password" placeholder="Min. 8 karakter, Huruf Besar, Kecil & Angka">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" wire:model="password_confirmation"
                                    placeholder="Ulangi password baru">
                            </div>

                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <span wire:loading.remove>Simpan Password Baru</span>
                                    <span wire:loading>Processing...</span>
                                </button>

                                <button type="button" class="btn btn-outline-danger waves-effect"
                                    onclick="event.preventDefault(); document.getElementById('logout-form-modal').submit();">
                                    Logout
                                </button>
                            </div>
                        </form>

                        <form id="logout-form-modal" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

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