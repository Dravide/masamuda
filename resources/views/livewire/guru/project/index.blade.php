@section('title', 'Data Project')

<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Project</h5>
            <div>
                <select wire:model.live="academic_year_id" class="form-select form-select-sm d-inline-block w-auto">
                    <option value="">Semua Tahun</option>
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}">{{ $year->name }} ({{ $year->semester }})</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Cari project...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Project</th>
                            <th>Tahun Pelajaran</th>
                            <th>Status</th>
                            <th>Jumlah Siswa</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $project)
                            <tr>
                                <td>{{ $project->name }}</td>
                                <td>{{ $project->academicYear->name ?? '-' }}</td>
                                <td>
                                    @if($project->status == 'active')
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $project->students_count }} Siswa</td>
                                <td>
                                    <a href="{{ route('guru.project.detail', $project->id) }}" class="btn btn-sm btn-info">
                                        <i class="fi fi-rr-users-alt me-1"></i> Data Siswa & Foto
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data project.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</div>