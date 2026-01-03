<div>
    <div class="container">
        <!-- Page Title -->
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
            <div class="clearfix">
                <h1 class="app-page-title">Data Siswa per Sekolah</h1>
                <span class="text-muted">Pilih sekolah untuk melihat daftar siswa</span>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-box">
                            <input type="text" class="form-control" placeholder="Cari Sekolah atau Alamat..." wire:model.live.debounce.300ms="search">
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

        <!-- Schools Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Sekolah</th>
                            <th>Alamat</th>
                            <th class="text-center">Jumlah Siswa</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schools as $school)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-3">
                                        <i class="fi fi-rr-school"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $school->name }}</h6>
                                        <small class="text-muted">{{ $school->npsn ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ Str::limit($school->address, 50) }}</td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info fs-6">{{ $school->students_count }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.data-siswa.show', $school->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fi fi-rr-users-alt me-1"></i> Lihat Siswa
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fi fi-rr-school fs-1 d-block mb-2"></i>
                                    Tidak ada data sekolah ditemukan.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($schools->hasPages())
            <div class="card-footer border-top">
                {{ $schools->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
