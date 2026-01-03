<div>
    <div class="container">
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <div class="clearfix">
                <h1 class="app-page-title">Data Sekolah</h1>
                <span class="text-muted">Kelola data sekolah terdaftar</span>
            </div>
            <button wire:click="create" class="btn btn-primary waves-effect waves-light">
                <i class="fi fi-rr-plus me-1"></i> Tambah Sekolah
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
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <div class="search-box">
                                <input type="text" class="form-control" placeholder="Cari Sekolah, NPSN, Kota..." wire:model.live.debounce.300ms="search">
                            </div>
                            <select class="form-select w-auto" wire:model.live="statusFilter">
                                <option value="all">Semua Status</option>
                                <option value="negeri">Negeri</option>
                                <option value="swasta">Swasta</option>
                            </select>
                            <select class="form-select w-auto" wire:model.live="perPage">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <button wire:click="export('csv')" class="btn btn-outline-success">
                                <i class="fi fi-rr-file-export me-1"></i> Export CSV
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Logo</th>
                                        <th>NPSN</th>
                                        <th>Nama Sekolah</th>
                                        <th>Status</th>
                                        <th>Kota/Kabupaten</th>
                                        <th>Akun Sekolah</th>
                                        <th>Verifikasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($schools as $school)
                                        <tr>
                                            <td>
                                                <div class="avatar avatar-md rounded">
                                                    <img src="{{ $school->logo ? asset('storage/' . $school->logo) : asset('template/assets/images/logo.svg') }}" alt="Logo" class="object-fit-contain">
                                                </div>
                                            </td>
                                            <td>{{ $school->npsn }}</td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $school->name }}</div>
                                                <small class="text-muted d-block">{{ Str::limit($school->address, 30) }}</small>
                                            </td>
                                            <td>
                                                @if($school->status == 'negeri')
                                                    <span class="badge bg-primary-subtle text-primary">Negeri</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">Swasta</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>{{ $school->city }}</div>
                                                <small class="text-muted">{{ $school->province }}</small>
                                            </td>
                                            <td>
                                                @if($school->user_id)
                                                    <span class="badge bg-success-subtle text-success">
                                                        <i class="fi fi-rr-check me-1"></i> Aktif
                                                    </span>
                                                    <div class="small text-muted mt-1">{{ $school->user->username }}</div>
                                                @else
                                                    <button type="button" onclick="confirmGenerate({{ $school->id }}, '{{ $school->name }}')" class="btn btn-xs btn-outline-primary">
                                                        <i class="fi fi-rr-key me-1"></i> Generate
                                                    </button>
                                                @endif
                                            </td>
                                            <td>
                                                @if($school->is_verified)
                                                    <span class="badge bg-success-subtle text-success">Terverifikasi</span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger">Belum</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button wire:click="edit({{ $school->id }})" class="btn btn-sm btn-icon btn-action-primary">
                                                    <i class="fi fi-rr-edit"></i>
                                                </button>
                                                <button wire:click="delete({{ $school->id }})" class="btn btn-sm btn-icon btn-action-danger" onclick="confirm('Hapus data sekolah ini?') || event.stopImmediatePropagation()">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-4">
                                                <div class="text-muted">Tidak ada data sekolah ditemukan.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <small class="text-muted mb-2 mb-md-0">
                                    Menampilkan {{ $schools->firstItem() ?? 0 }} sampai {{ $schools->lastItem() ?? 0 }} dari {{ $schools->total() }} data
                                </small>
                                {{ $schools->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Form -->
    @if($showModal)
    <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5); overflow-y: auto;" tabindex="-1" role="dialog" aria-modal="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Data Sekolah' : 'Tambah Sekolah Baru' }}</h5>
                    <button wire:click="closeModal" type="button" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NPSN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('npsn') is-invalid @enderror" wire:model="npsn" maxlength="8">
                                @error('npsn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Sekolah <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" wire:model="status">
                                    <option value="negeri">Negeri</option>
                                    <option value="swasta">Swasta</option>
                                </select>
                                @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Logo Sekolah</label>
                                <input type="file" class="form-control @error('logo') is-invalid @enderror" wire:model="{{ $isEdit ? 'newLogo' : 'logo' }}">
                                <div wire:loading wire:target="logo, newLogo" class="text-sm text-muted mt-1">Uploading...</div>
                                @if ($logo && !$newLogo && $isEdit)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/'.$logo) }}" width="50" class="rounded">
                                    </div>
                                @elseif ($logo && !$isEdit)
                                     <div class="mt-2">
                                        <img src="{{ $logo->temporaryUrl() }}" width="50" class="rounded">
                                    </div>
                                @elseif ($newLogo)
                                    <div class="mt-2">
                                        <img src="{{ $newLogo->temporaryUrl() }}" width="50" class="rounded">
                                    </div>
                                @endif
                                @error('logo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                @error('newLogo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h6 class="mt-3 mb-3 border-bottom pb-2">Alamat & Kontak</h6>

                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap (Jalan/Desa) <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" wire:model="address" rows="2"></textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('district') is-invalid @enderror" wire:model="district">
                                @error('district') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kabupaten/Kota <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" wire:model="city">
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Provinsi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('province') is-invalid @enderror" wire:model="province">
                                @error('province') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" wire:model="postal_code" maxlength="5">
                                @error('postal_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">RT/RW</label>
                                <input type="text" class="form-control @error('rt_rw') is-invalid @enderror" wire:model="rt_rw" placeholder="00/00">
                                @error('rt_rw') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Telepon</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" wire:model="phone">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <h6 class="mt-3 mb-3 border-bottom pb-2">Lokasi & Koordinat</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Latitude</label>
                                <input type="text" class="form-control @error('latitude') is-invalid @enderror" wire:model="latitude" placeholder="-6.175392">
                                @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Longitude</label>
                                <input type="text" class="form-control @error('longitude') is-invalid @enderror" wire:model="longitude" placeholder="106.827153">
                                @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="text-end mt-4">
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
        });

        function confirmGenerate(id, name) {
            Swal.fire({
                title: 'Generate Akun?',
                text: "Akun untuk sekolah " + name + " akan dibuat menggunakan NPSN sebagai username & password.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Generate!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('generateAccount', id);
                }
            })
        }
    </script>
</div>
