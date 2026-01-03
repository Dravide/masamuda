<div>
    <div class="container">
        <!-- Page Title -->
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
            <div class="clearfix">
                <h1 class="app-page-title">Data Jurusan</h1>
                <span class="text-muted">Kelola data jurusan untuk siswa</span>
            </div>
            <button wire:click="create" class="btn btn-primary waves-effect waves-light">
                <i class="fi fi-rr-plus me-1"></i> Tambah Jurusan
            </button>
        </div>

        <!-- Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-header d-flex flex-wrap gap-3 align-items-center justify-content-between border-0 pb-0">
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <div class="search-box">
                                <input type="text" class="form-control" placeholder="Cari Jurusan..."
                                    wire:model.live.debounce.300ms="search">
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 50px;">#</th>
                                        <th>Nama Jurusan</th>
                                        <th>Kode</th>
                                        <th>Deskripsi</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($majors as $index => $major)
                                        <tr>
                                            <td>{{ $majors->firstItem() + $index }}</td>
                                            <td class="fw-bold">{{ $major->name }}</td>
                                            <td>
                                                @if($major->code)
                                                    <span class="badge bg-secondary">{{ $major->code }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($major->description, 50) ?? '-' }}</td>
                                            <td>
                                                @if($major->is_active)
                                                    <span class="badge bg-success-subtle text-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button wire:click="edit({{ $major->id }})"
                                                    class="btn btn-sm btn-icon btn-action-primary">
                                                    <i class="fi fi-rr-edit"></i>
                                                </button>
                                                <button onclick="confirmDelete({{ $major->id }})"
                                                    class="btn btn-sm btn-icon btn-action-danger">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">Tidak ada data jurusan ditemukan.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <small class="text-muted mb-2 mb-md-0">
                                    Menampilkan {{ $majors->firstItem() ?? 0 }} sampai
                                    {{ $majors->lastItem() ?? 0 }} dari {{ $majors->total() }} data
                                </small>
                                {{ $majors->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5); overflow-y: auto;"
            tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? 'Edit Jurusan' : 'Tambah Jurusan Baru' }}</h5>
                        <button wire:click="closeModal" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                            <div class="mb-3">
                                <label class="form-label">Nama Jurusan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model="name" placeholder="Contoh: Teknik Komputer dan Jaringan">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kode</label>
                                <input type="text" class="form-control @error('code') is-invalid @enderror"
                                    wire:model="code" placeholder="Contoh: TKJ">
                                @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror"
                                    wire:model="description" rows="3" placeholder="Deskripsi jurusan (opsional)"></textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActive">
                                    <label class="form-check-label" for="isActive">Status Aktif</label>
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
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Jurusan?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('delete', id);
                }
            });
        }

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