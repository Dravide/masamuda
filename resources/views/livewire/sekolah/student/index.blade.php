<div>
    <div class="container">
        <!-- Page Title -->
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
            <div class="clearfix">
                <h1 class="app-page-title">Data Siswa</h1>
                <span class="text-muted">Kelola data siswa terdaftar</span>
            </div>
            <button wire:click="create" class="btn btn-primary waves-effect waves-light">
                <i class="fi fi-rr-plus me-1"></i> Tambah Siswa
            </button>
        </div>

        <!-- Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex flex-wrap gap-3 align-items-center justify-content-between border-0 pb-0">
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <div class="search-box">
                                <input type="text" class="form-control" placeholder="Cari Siswa, NIS..." wire:model.live.debounce.300ms="search">
                            </div>
                            <select class="form-select w-auto" wire:model.live="perPage">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <button wire:click="export" class="btn btn-outline-success">
                                <i class="fi fi-rr-file-export me-1"></i> Export Excel
                            </button>
                            <a href="{{ route('sekolah.siswa.import') }}" class="btn btn-outline-primary">
                                <i class="fi fi-rr-file-import me-1"></i> Import Excel
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th wire:click="sortBy('nis')" style="cursor: pointer;">
                                            NIS 
                                            @if($sortColumn == 'nis') <i class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                                        </th>
                                        <th wire:click="sortBy('nisn')" style="cursor: pointer;">
                                            NISN
                                            @if($sortColumn == 'nisn') <i class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                                        </th>
                                        <th wire:click="sortBy('name')" style="cursor: pointer;">
                                            Nama Lengkap
                                            @if($sortColumn == 'name') <i class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                                        </th>
                                        <th wire:click="sortBy('grade')" style="cursor: pointer;">
                                            Kelas
                                            @if($sortColumn == 'grade') <i class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                                        </th>
                                        <th wire:click="sortBy('major')" style="cursor: pointer;">
                                            Jurusan
                                            @if($sortColumn == 'major') <i class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                                        </th>
                                        <th>Magic Link</th>
                                        <th>Tgl Lahir</th>
                                        <th>WhatsApp</th>
                                        <th>Alamat</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $student)
                                        <tr>
                                            <td>{{ $student->nis }}</td>
                                            <td>{{ $student->nisn }}</td>
                                            <td class="fw-bold">{{ $student->name }}</td>
                                            <td>
                                                @if($student->grade)
                                                    <span class="badge bg-secondary">{{ $student->grade }} {{ $student->class_name }}</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info-subtle text-info">{{ $student->major }}</span>
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm" style="width: 150px;">
                                                    <input type="text" class="form-control" value="{{ $student->magic_token ? route('student.public.profile', $student->magic_token) : 'Belum digenerate' }}" readonly id="magic-link-{{ $student->id }}">
                                                    @if($student->magic_token)
                                                    <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('magic-link-{{ $student->id }}')">
                                                        <i class="fi fi-rr-copy"></i>
                                                    </button>
                                                    @else
                                                    <button class="btn btn-outline-secondary disabled" type="button" disabled>
                                                        <i class="fi fi-rr-cross-circle"></i>
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($student->birth_date)->locale('id')->translatedFormat('d F Y') }}</td>
                                            <td>
                                                @if($student->whatsapp)
                                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $student->whatsapp)) }}" target="_blank" class="text-success text-decoration-none">
                                                        <i class="fi fi-brands-whatsapp me-1"></i> {{ $student->whatsapp }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ Str::limit($student->address, 30) }}</td>
                                            <td>
                                                <button wire:click="edit({{ $student->id }})" class="btn btn-sm btn-icon btn-action-primary">
                                                    <i class="fi fi-rr-edit"></i>
                                                </button>
                                                <button wire:click="delete({{ $student->id }})" class="btn btn-sm btn-icon btn-action-danger" onclick="confirm('Hapus data siswa ini?') || event.stopImmediatePropagation()">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <div class="text-muted">Tidak ada data siswa ditemukan.</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <small class="text-muted mb-2 mb-md-0">
                                    Menampilkan {{ $students->firstItem() ?? 0 }} sampai {{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} data
                                </small>
                                {{ $students->links() }}
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
                    <h5 class="modal-title">{{ $isEdit ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}</h5>
                    <button wire:click="closeModal" type="button" class="btn-close" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NIS <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nis') is-invalid @enderror" wire:model="nis" placeholder="Nomor Induk Sekolah">
                                @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">NISN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nisn') is-invalid @enderror" wire:model="nisn" placeholder="Nomor Induk Siswa Nasional">
                                @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name" placeholder="Nama Lengkap Siswa">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor WhatsApp</label>
                                <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" wire:model="whatsapp" placeholder="Contoh: 08123456789">
                                @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" wire:model="email" placeholder="Contoh: siswa@sekolah.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tingkat Kelas <span class="text-danger">*</span></label>
                                <select class="form-select @error('grade') is-invalid @enderror" wire:model="grade">
                                    <option value="">Pilih Tingkat</option>
                                    @for ($i = 1; $i <= 12; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('grade') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Kelas (Rombel) <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('class_name') is-invalid @enderror" wire:model="class_name" placeholder="Contoh: A, B, 1, IPA 1">
                                <div class="form-text small">Masukkan nama rombel/paralel, misal: A, B, 1, atau IPA 1.</div>
                                @error('class_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror" wire:model="address" rows="3" placeholder="Alamat Tempat Tinggal"></textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('birth_date') is-invalid @enderror" wire:model="birth_date">
                                @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jurusan <span class="text-danger">*</span></label>
                                @if($isSmp)
                                    <input type="text" class="form-control" value="UMUM" readonly disabled>
                                    <input type="hidden" wire:model="major" value="UMUM">
                                @else
                                    <select class="form-select @error('major') is-invalid @enderror" wire:model="major">
                                        <option value="">Pilih Jurusan</option>
                                        @foreach($availableMajors as $m)
                                            <option value="{{ $m }}">{{ $m }}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @error('major') <div class="invalid-feedback">{{ $message }}</div> @enderror
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

        function copyToClipboard(elementId) {
            var copyText = document.getElementById(elementId);
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices
            navigator.clipboard.writeText(copyText.value).then(function() {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Link berhasil disalin!',
                    showConfirmButton: false,
                    timer: 3000
                });
            }, function(err) {
                console.error('Async: Could not copy text: ', err);
            });
        }
    </script>
</div>
