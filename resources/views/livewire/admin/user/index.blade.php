<div>
    <div class="container">
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <div class="clearfix">
                <h1 class="app-page-title">User Management</h1>
                <span class="text-muted">Kelola data pengguna sistem</span>
            </div>
            <button wire:click="create" class="btn btn-primary waves-effect waves-light">
                <i class="fi fi-rr-plus me-1"></i> Tambah User
            </button>
        </div>

        <div class="row">
            <div class="col-12">
                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header d-flex flex-wrap gap-3 align-items-center justify-content-between border-0 pb-0">
                        <div class="d-flex gap-2 align-items-center">
                            <div class="search-box">
                                <input type="text" class="form-control" placeholder="Cari Nama, Email, Username..." wire:model.live.debounce.300ms="search">
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Filter Role
                                </button>
                                <ul class="dropdown-menu p-2">
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="admin" id="roleAdmin" wire:model.live="roleFilter">
                                            <label class="form-check-label" for="roleAdmin">Admin</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="sekolah" id="roleSekolah" wire:model.live="roleFilter">
                                            <label class="form-check-label" for="roleSekolah">Sekolah</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="siswa" id="roleSiswa" wire:model.live="roleFilter">
                                            <label class="form-check-label" for="roleSiswa">Siswa</label>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="guru" id="roleGuru" wire:model.live="roleFilter">
                                            <label class="form-check-label" for="roleGuru">Guru</label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <select class="form-select w-auto" wire:model.live="statusFilter">
                                <option value="all">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                            @if($search || !empty($roleFilter) || $statusFilter !== 'all')
                                <button wire:click="resetFilters" class="btn btn-sm btn-outline-danger">Reset</button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nama Lengkap</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Tanggal Daftar</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr class="{{ !$user->is_active ? 'bg-light text-muted' : '' }}">
                                            <td>USR-{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-sm rounded-circle me-2">
                                                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('template/assets/images/avatar/avatar1.webp') }}" alt="">
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $user->name }}</h6>
                                                        <small class="text-muted">{{ $user->username }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                @if($user->role == 'admin')
                                                    <span class="badge bg-primary-subtle text-primary">Admin</span>
                                                @elseif($user->role == 'sekolah')
                                                    <span class="badge bg-info-subtle text-info">Sekolah</span>
                                                @elseif($user->role == 'guru')
                                                    <span class="badge bg-purple-subtle text-purple">Guru</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">Siswa</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->created_at->locale('id')->isoFormat('D MMMM Y HH:mm') }}</td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" 
                                                        wire:click="toggleStatus({{ $user->id }})" 
                                                        {{ $user->is_active ? 'checked' : '' }} 
                                                        {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                                    <label class="form-check-label small">
                                                        {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                <button wire:click="resetPassword({{ $user->id }})" class="btn btn-sm btn-icon btn-action-warning" onclick="confirm('Reset password user ini? Password akan kembali ke NPSN (Sekolah), NISN (Siswa), atau Default.') || event.stopImmediatePropagation()" title="Reset Password" data-bs-toggle="tooltip">
                                                    <i class="fi fi-rr-key"></i>
                                                </button>
                                                <button wire:click="edit({{ $user->id }})" class="btn btn-sm btn-icon btn-action-primary">
                                                    <i class="fi fi-rr-edit"></i>
                                                </button>
                                                @if($user->id !== Auth::id())
                                                    <button wire:click="confirmDelete({{ $user->id }})" class="btn btn-sm btn-icon btn-action-danger">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">Tidak ada data pengguna ditemukan.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    Menampilkan {{ $users->firstItem() ?? 0 }} sampai {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} data
                                </small>
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit User' : 'Tambah User Baru' }}</h5>
                    <button wire:click="closeModal" type="button" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model="name" placeholder="Nama Lengkap">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" wire:model="username" placeholder="Username">
                                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model="email" placeholder="Email" {{ $isEdit ? 'readonly' : '' }}>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" wire:model="role">
                                <option value="">Pilih Role</option>
                                <option value="admin">Admin</option>
                                <option value="sekolah">Sekolah</option>
                                <option value="guru">Guru</option>
                                <option value="siswa">Siswa</option>
                            </select>
                            @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password {{ $isEdit ? '(Opsional)' : '<span class="text-danger">*</span>' }}</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" wire:model="password" placeholder="Password">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                                <input type="password" class="form-control" id="password_confirmation" wire:model="password_confirmation" placeholder="Ulangi Password">
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" wire:model="is_active">
                                <label class="form-check-label" for="is_active">Status Aktif</label>
                            </div>
                        </div>

                        <div class="text-end">
                            <button wire:click="closeModal" type="button" class="btn btn-light waves-effect">Batal</button>
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
