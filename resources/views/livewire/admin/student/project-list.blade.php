<div>
    <div class="container">
        <!-- Page Title -->
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
            <div class="clearfix">
                <h1 class="app-page-title">Data Project</h1>
                <span class="text-muted">Pilih project untuk melihat daftar siswa</span>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-box">
                            <input type="text" class="form-control" placeholder="Cari Project atau Sekolah..."
                                wire:model.live.debounce.300ms="search">
                        </div>
                    </div>
                    <div class="col-md-auto ms-auto">
                        <select class="form-select" wire:model.live="perPage">
                            <option value="10">10 Data per halaman</option>
                            <option value="25">25 Data per halaman</option>
                            <option value="50">50 Data per halaman</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects Table -->
        <div class="card">
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
                                    <select class="form-select form-select-sm w-auto"
                                        wire:change="toggleStatus({{ $project->id }}, $event.target.value)"
                                        style="min-width: 120px;">
                                        <option value="draft" {{ $project->status === 'draft' ? 'selected' : '' }}>Draft
                                        </option>
                                        <option value="active" {{ $project->status === 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="completed" {{ $project->status === 'completed' ? 'selected' : '' }}>
                                            Completed</option>
                                    </select>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.project.show', $project->id) }}"
                                        class="btn btn-sm btn-primary">
                                        <i class="fi fi-rr-users-alt me-1"></i> Lihat Siswa
                                    </a>
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