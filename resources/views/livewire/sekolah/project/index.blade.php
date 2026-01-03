<div>
    <div class="container">
        <!-- Page Header -->
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <div class="clearfix">
                <h1 class="app-page-title">Project</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('sekolah.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Project</li>
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
                    <div class="col-xxl-3 col-lg-6 col-sm-6">
                        <div class="card card-action action-border-primary p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-primary">
                                    <i class="fi fi-sr-folder fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Total Project</div>
                                    <h3 class="mb-0 fw-bold">{{ $projects->total() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-6 col-sm-6">
                        <div class="card card-action action-border-secondary p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-secondary">
                                    <i class="fi fi-sr-edit fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Draft</div>
                                    <h3 class="mb-0 fw-bold">{{ $projects->where('status', 'draft')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-6 col-sm-6">
                        <div class="card card-action action-border-success p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-success">
                                    <i class="fi fi-ss-badget-check-alt fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Active</div>
                                    <h3 class="mb-0 fw-bold">{{ $projects->where('status', 'active')->count() }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-6 col-sm-6">
                        <div class="card card-action action-border-info p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-info">
                                    <i class="fi fi-sr-check-circle fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Completed</div>
                                    <h3 class="mb-0 fw-bold">{{ $projects->where('status', 'completed')->count() }}</h3>
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
                        <input type="text" class="form-control" placeholder="Cari Project..."
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
                            <th style="width: 50px;">#</th>
                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                Nama Project
                                @if($sortColumn == 'name') <i
                                    class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('academic_year_id')" style="cursor: pointer;">
                                Tahun Pelajaran
                                @if($sortColumn == 'academic_year_id') <i
                                    class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('type')" style="cursor: pointer;">
                                Jenis
                                @if($sortColumn == 'type') <i
                                    class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('status')" style="cursor: pointer;">
                                Status
                                @if($sortColumn == 'status') <i
                                    class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $index => $project)
                            <tr>
                                <td>{{ $projects->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-2">
                                            <i class="fi fi-rr-folder"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $project->name }}</h6>
                                            @if($project->date)
                                                <small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($project->date)->locale('id')->translatedFormat('d M Y') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $project->academicYear->year_name ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info">{{ $project->type }}</span>
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
                                    <span class="badge bg-{{ $statusClass }}">
                                        {{ $projectStatuses[$project->status] ?? $project->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($project->status === 'active')
                                        <a href="{{ route('sekolah.project.siswa', $project) }}"
                                            class="btn btn-sm btn-success waves-effect waves-light">
                                            <i class="fi fi-rr-users me-1"></i> Input Data
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-secondary" disabled
                                            title="Project harus diaktifkan oleh Admin terlebih dahulu">
                                            <i class="fi fi-rr-lock me-1"></i> Input Data
                                        </button>
                                    @endif
                                    <button wire:click="edit({{ $project->id }})"
                                        class="btn btn-sm btn-icon btn-outline-primary waves-effect">
                                        <i class="fi fi-rr-edit"></i>
                                    </button>
                                    <button wire:click="delete({{ $project->id }})"
                                        class="btn btn-sm btn-icon btn-outline-danger waves-effect"
                                        onclick="confirm('Hapus project ini?') || event.stopImmediatePropagation()">
                                        <i class="fi fi-rr-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fi fi-rr-folder fs-1 d-block mb-2"></i>
                                        Tidak ada project ditemukan.
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
                                <label class="form-label">Nama Project <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model="name" placeholder="Nama Project">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tahun Pelajaran <span class="text-danger">*</span></label>
                                    @if(!$isEdit && $activeAcademicYear)
                                        <input type="text" class="form-control"
                                            value="{{ $activeAcademicYear->year_name }} - Semester {{ ucfirst($activeAcademicYear->semester) }}"
                                            readonly>
                                        <input type="hidden" wire:model="academic_year_id">
                                        <small class="text-muted">Otomatis menggunakan tahun ajaran aktif</small>
                                    @else
                                        <select class="form-select @error('academic_year_id') is-invalid @enderror"
                                            wire:model="academic_year_id">
                                            <option value="">Pilih Tahun Pelajaran</option>
                                            @foreach($academicYears as $year)
                                                <option value="{{ $year->id }}">{{ $year->year_name }} - Semester
                                                    {{ ucfirst($year->semester) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
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

                            <div class="mb-3">
                                <label class="form-label">Tanggal</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                    wire:model="date">
                                @error('date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            @if($isEdit)
                                <div class="alert alert-info mb-3">
                                    <i class="fi fi-rr-info me-2"></i>
                                    <strong>Status:</strong> {{ $projectStatuses[$status] ?? $status }}
                                    <br><small class="text-muted">Status project hanya dapat diubah oleh Admin.</small>
                                </div>
                            @else
                                <div class="alert alert-warning mb-3">
                                    <i class="fi fi-rr-exclamation me-2"></i>
                                    Project baru akan dibuat dengan status <strong>Draft</strong>. Hubungi Admin untuk
                                    mengaktifkan project.
                                </div>
                            @endif

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
                const alertData = data[0]; Swal.fire({
                    icon: alertData.type,
                    title: alertData.title,
                    text: alertData.text,
                    confirmButtonColor: '#3085d6',
                });
            });
        });
    </script>
</div>