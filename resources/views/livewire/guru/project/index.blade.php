@section('title', 'Data Project')

<div class="container">
    <!-- Page Header -->
    <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
        <div class="clearfix">
            <h1 class="app-page-title">Project</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Project</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex align-items-center gap-2">
            <select wire:model.live="academic_year_id" class="form-select w-auto">
                <option value="">Semua Tahun</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}">{{ $year->name }} ({{ $year->semester }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="card-title mb-0">Daftar Project</h6>
                        <div class="search-box">
                            <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                                placeholder="Cari project...">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Project</th>
                                    <th>Target</th>
                                    <th>Tahun Pelajaran</th>
                                    <th>Status</th>
                                    <th>Data</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($projects as $project)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle">
                                                    <i class="fi fi-rr-folder"></i>
                                                </div>
                                                <div class="fw-medium">{{ $project->name }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($project->target == 'guru')
                                                <span class="badge bg-warning-subtle text-warning">
                                                    <i class="fi fi-rs-chalkboard-user me-1"></i> Guru
                                                </span>
                                            @else
                                                <span class="badge bg-primary-subtle text-primary">
                                                    <i class="fi fi-rs-student me-1"></i> Siswa
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $project->academicYear->name ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusClass = match ($project->status) {
                                                    'active' => 'success',
                                                    'completed' => 'info',
                                                    'draft' => 'secondary',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusClass }}">{{ ucfirst($project->status) }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted small">
                                                <i class="fi fi-rr-users me-1"></i>
                                                {{ $project->students_count }}
                                                {{ $project->target == 'guru' ? 'Guru' : 'Siswa' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('guru.project.detail', $project->id) }}"
                                                class="btn btn-sm btn-outline-primary waves-effect">
                                                <i class="fi fi-rr-eye me-1"></i> Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fi fi-rr-folder-cross fs-1 d-block mb-2 text-secondary"></i>
                                            <p class="mb-0">Tidak ada data project ditemukan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $projects->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>