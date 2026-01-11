<div>
    <div class="container">
        <!-- Page Header -->
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <div class="clearfix">
                <h1 class="app-page-title">Data Project</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Data Project</li>
                    </ol>
                </nav>
            </div>
            <button wire:click="create" class="btn btn-primary waves-effect waves-light">
                <i class="fi fi-rr-plus me-1"></i> Tambah Project
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-xxl-12">
                <div class="row">
                    <div class="col-xxl-2 col-lg-4 col-sm-6">
                        <div class="card card-action action-border-primary p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-primary">
                                    <i class="fi fi-sr-folder fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Total Project</div>
                                    <h3 class="mb-0 fw-bold">{{ number_format($totalProjects) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-lg-4 col-sm-6">
                        <div class="card card-action action-border-secondary p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-secondary">
                                    <i class="fi fi-sr-edit fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Draft</div>
                                    <h3 class="mb-0 fw-bold">{{ number_format($draftProjects) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-lg-4 col-sm-6">
                        <div class="card card-action action-border-success p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-success">
                                    <i class="fi fi-ss-badget-check-alt fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Active</div>
                                    <h3 class="mb-0 fw-bold">{{ number_format($activeProjects) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-lg-4 col-sm-6">
                        <div class="card card-action action-border-info p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-info">
                                    <i class="fi fi-sr-check-circle fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Completed</div>
                                    <h3 class="mb-0 fw-bold">{{ number_format($completedProjects) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-2 col-lg-4 col-sm-6">
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
                    <div class="col-xxl-2 col-lg-4 col-sm-6">
                        <div class="card card-action action-border-danger p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-danger">
                                    <i class="fi fi-sr-school fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Total Sekolah</div>
                                    <h3 class="mb-0 fw-bold">{{ number_format($totalSchools) }}</h3>
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
                <h6 class="card-title mb-0">Daftar Project</h6>
                <div class="clearfix d-flex align-items-center gap-3">
                    <div class="search-box">
                        <input type="text" class="form-control" placeholder="Cari Project atau Sekolah..."
                            wire:model.live.debounce.300ms="search">
                    </div>
                    <select class="form-select w-auto" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Project</th>
                            <th>Sekolah</th>
                            <th>Tahun Ajaran</th>
                            <th class="text-center">Jumlah Siswa</th>
                            <th>Status</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-3">
                                            <i class="fi fi-rr-folder"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $project->name }}</h6>
                                            <small class="text-muted">
                                                <span class="badge bg-info-subtle text-info">{{ $project->type }}</span>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $project->school->name ?? '-' }}</td>
                                <td>{{ $project->academicYear->year_name ?? '-' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-info-subtle text-info fs-6">{{ $project->students_count }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match ($project->status) {
                                            'draft' => 'bg-secondary',
                                            'active' => 'bg-success',
                                            'completed' => 'bg-info',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <div class="dropdown">
                                        <button
                                            class="btn btn-sm {{ $statusClass }} text-white dropdown-toggle waves-effect"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            {{ ucfirst($project->status) }}
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item {{ $project->status === 'draft' ? 'active' : '' }}"
                                                    href="javascript:void(0);"
                                                    wire:click="toggleStatus({{ $project->id }}, 'draft')">
                                                    <i class="fi fi-rr-edit me-2"></i> Draft
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ $project->status === 'active' ? 'active' : '' }}"
                                                    href="javascript:void(0);"
                                                    wire:click="toggleStatus({{ $project->id }}, 'active')">
                                                    <i class="fi fi-rr-check-circle me-2"></i> Active
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item {{ $project->status === 'completed' ? 'active' : '' }}"
                                                    href="javascript:void(0);"
                                                    wire:click="toggleStatus({{ $project->id }}, 'completed')">
                                                    <i class="fi fi-rr-badge-check me-2"></i> Completed
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.project.show', $project->id) }}"
                                        class="btn btn-sm btn-outline-primary waves-effect">
                                        <i class="fi fi-rr-users-alt me-1"></i> Siswa
                                    </a>
                                    <button wire:click="edit({{ $project->id }})"
                                        class="btn btn-sm btn-icon btn-outline-warning waves-effect">
                                        <i class="fi fi-rr-edit"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $project->id }})"
                                        class="btn btn-sm btn-icon btn-outline-danger waves-effect">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fi fi-rr-folder fs-1 d-block mb-2"></i>
                                        Tidak ada data project ditemukan.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($projects->hasPages())
                <div class="card-footer border-top">
                    {{ $projects->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5); overflow-y: auto;"
            tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? 'Edit Project' : 'Tambah Project Baru' }}</h5>
                        <button wire:click="closeModal" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                            <div class="mb-3">
                                <label class="form-label">Sekolah <span class="text-danger">*</span></label>
                                <select class="form-select @error('school_id') is-invalid @enderror" wire:model="school_id">
                                    <option value="">Pilih Sekolah</option>
                                    @foreach($schools as $school)
                                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                                    @endforeach
                                </select>
                                @error('school_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Project <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model="name" placeholder="Nama Project">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tahun Pelajaran <span class="text-danger">*</span></label>
                                    <select class="form-select @error('academic_year_id') is-invalid @enderror"
                                        wire:model="academic_year_id">
                                        <option value="">Pilih Tahun Pelajaran</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}">{{ $year->year_name }} - Semester
                                                {{ ucfirst($year->semester) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Jenis Project <span class="text-danger">*</span></label>
                                    <select class="form-select @error('type') is-invalid @enderror" wire:model="type">
                                        <option value="">Pilih Jenis</option>
                                        @foreach($projectTypes as $t)
                                            <option value="{{ $t }}">{{ $t }}</option>
                                        @endforeach
                                    </select>
                                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                    wire:model="description" rows="3" placeholder="Deskripsi Project (opsional)"></textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" class="form-control @error('date') is-invalid @enderror"
                                        wire:model="date">
                                    @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                        @foreach($projectStatuses as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button wire:click="closeModal" type="button"
                                    class="btn btn-light waves-effect">Batal</button>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <span wire:loading.remove>{{ $isEdit ? 'Update' : 'Simpan' }}</span>
                                    <span wire:loading>Loading...</span>
                                </button>
                            </div>
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

            Livewire.on('swal:confirm-delete', (data) => {
                Swal.fire({
                    title: data[0].title,
                    text: data[0].text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('destroy', data[0].id);
                    }
                });
            });
        });
    </script>
</div>