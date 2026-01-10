<div>
    <div class="container-fluid p-0">
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
            <div class="clearfix">
                <h1 class="app-page-title mb-1">Kelola Guru</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.sekolah.index') }}">Data Sekolah</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $school->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.sekolah.index') }}" class="btn btn-outline-secondary waves-effect">
                    <i class="fi fi-rr-arrow-left me-1"></i> Kembali
                </a>
                <button wire:click="create" class="btn btn-primary waves-effect waves-light">
                    <i class="fi fi-rr-plus me-1"></i> Tambah Guru
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="card-title mb-0">Daftar Guru - {{ $school->name }}</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <div class="search-box">
                        <input type="text" class="form-control form-control-sm" placeholder="Cari Nama, NIP..."
                            wire:model.live.debounce.300ms="search">
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama Guru</th>
                            <th>NIP / Username</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Terdaftar</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle me-2">
                                            <i class="fi fi-rr-user"></i>
                                        </div>
                                        <span class="fw-medium">{{ $teacher->user->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $teacher->nip ?? '-' }}</td>
                                <td>{{ $teacher->user->email }}</td>
                                <td>
                                    @if($teacher->user->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-danger">Non-Aktif</span>
                                    @endif
                                </td>
                                <td>{{ $teacher->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-icon btn-light waves-effect" type="button"
                                            data-bs-toggle="dropdown">
                                            <i class="fi fi-rr-menu-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    wire:click.prevent="edit({{ $teacher->id }})">
                                                    <i class="fi fi-rr-edit me-2"></i> Edit
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="#"
                                                    wire:click.prevent="confirmResetPassword({{ $teacher->id }})">
                                                    <i class="fi fi-rr-key me-2"></i> Reset Password
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <a class="dropdown-item text-danger" href="#"
                                                    onclick="confirm('Yakin ingin menghapus guru ini? User terkait juga akan dihapus.') || event.stopImmediatePropagation()"
                                                    wire:click.prevent="delete({{ $teacher->id }})">
                                                    <i class="fi fi-rr-trash me-2"></i> Hapus
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fi fi-rr-users fs-1 d-block mb-2"></i>
                                    Belum ada data guru.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($teachers->hasPages())
                <div class="card-footer border-top">
                    {{ $teachers->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5); overflow-y: auto;" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? 'Edit Guru' : 'Tambah Guru' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                            <div class="mb-3">
                                <label class="form-label required">Nama Lengkap</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    wire:model="name" placeholder="Nama Guru">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">NIP (Username)</label>
                                <input type="text" class="form-control @error('nip') is-invalid @enderror" wire:model="nip"
                                    placeholder="NIP / Nomor Induk">
                                <div class="form-text">Digunakan sebagai Username login & Password default.</div>
                                @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label required">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    wire:model="email" placeholder="example@email.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3 form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isActiveSwitch" wire:model="isActive">
                                <label class="form-check-label" for="isActiveSwitch">Akun Aktif</label>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" wire:click="closeModal">Batal</button>
                        <button type="button" class="btn btn-primary" wire:click="{{ $isEdit ? 'update' : 'store' }}">
                            {{ $isEdit ? 'Simpan Perubahan' : 'Simpan' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Reset Password Modal -->
    @if($showResetModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reset Password</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin mereset password untuk guru <strong>{{ $resetTeacherName }}</strong>?</p>
                        <p class="text-muted small mb-0">Password akan diubah menjadi NIP guru tersebut.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" wire:click="closeModal">Batal</button>
                        <button type="button" class="btn btn-warning" wire:click="resetPassword">Ya, Reset</button>
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