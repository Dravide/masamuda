<div>
    <div class="container">
        <!-- Page Title -->
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
            <div class="clearfix">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('sekolah.project.index') }}">Project</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $project->name }}</li>
                    </ol>
                </nav>
                <h1 class="app-page-title">Data {{ $isGuru ? 'Guru' : 'Siswa' }} - {{ $project->name }}</h1>
                <span class="text-muted">
                    <span class="badge bg-info-subtle text-info">{{ $project->type }}</span>
                    <span
                        class="badge bg-{{ $isGuru ? 'warning' : 'primary' }}-subtle text-{{ $isGuru ? 'warning' : 'primary' }}">{{ $isGuru ? 'Guru' : 'Siswa' }}</span>
                    <span class="badge bg-secondary">{{ $project->academicYear->year_name ?? '-' }}</span>
                </span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sekolah.project.index') }}" class="btn btn-outline-secondary waves-effect">
                    <i class="fi fi-rr-arrow-left me-1"></i> Kembali
                </a>
                @if($project->status !== 'completed')
                    @if(!$isGuru)
                        <button wire:click="openCopyModal" class="btn btn-outline-success waves-effect">
                            <i class="fi fi-rr-copy me-1"></i> Salin dari Project Lain
                        </button>
                        <a href="{{ route('sekolah.project.data.import', $project) }}"
                            class="btn btn-outline-primary waves-effect">
                            <i class="fi fi-rr-file-import me-1"></i> Import Siswa
                        </a>
                    @endif
                    <button wire:click="create" class="btn btn-primary waves-effect waves-light">
                        <i class="fi fi-rr-plus me-1"></i> Tambah {{ $isGuru ? 'Guru' : 'Siswa' }}
                    </button>
                @endif
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <!-- Total Students/Teachers -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div
                                class="avatar avatar-lg bg-{{ $isGuru ? 'warning' : 'primary' }}-subtle text-{{ $isGuru ? 'warning' : 'primary' }} rounded-circle me-3">
                                <i class="fi fi-rr-{{ $isGuru ? 'chalkboard-user' : 'users' }} fs-4"></i>
                            </div>
                            <div>
                                <h3 class="mb-0 fw-bold">{{ $totalStudents }}</h3>
                                <p class="text-muted mb-0 small">Total {{ $isGuru ? 'Guru' : 'Siswa' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- By Grade - Only for Siswa -->
            @if(!$isGuru)
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar avatar-lg bg-info-subtle text-info rounded-circle me-3">
                                    <i class="fi fi-rr-ranking-podium fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Per Kelas</h5>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($gradeStats as $grade => $count)
                                    <span class="badge bg-info-subtle text-info">{{ $grade }}: {{ $count }}</span>
                                @empty
                                    <span class="text-muted small">Belum ada data</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- By Major - Only for Siswa -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar avatar-lg bg-warning-subtle text-warning rounded-circle me-3">
                                    <i class="fi fi-rr-graduation-cap fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="mb-0 fw-bold">Per Jurusan</h5>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($majorStats as $major => $count)
                                    <span class="badge bg-warning-subtle text-warning">{{ $major }}: {{ $count }}</span>
                                @empty
                                    <span class="text-muted small">Belum ada data</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Photo Status -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar avatar-lg bg-success-subtle text-success rounded-circle me-3">
                                <i class="fi fi-rr-picture fs-4"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold">Status Foto</h5>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge bg-success-subtle text-success">
                                <i class="fi fi-rr-check me-1"></i>{{ $withPhotoCount }} ada foto
                            </span>
                            <span class="badge bg-danger-subtle text-danger">
                                <i class="fi fi-rr-cross me-1"></i>{{ $withoutPhotoCount }} belum
                            </span>
                        </div>
                        @if($totalStudents > 0)
                            <div class="progress mt-2" style="height: 6px;">
                                <div class="progress-bar bg-success"
                                    style="width: {{ round(($withPhotoCount / $totalStudents) * 100) }}%"></div>
                            </div>
                            <small class="text-muted">{{ round(($withPhotoCount / $totalStudents) * 100) }}% lengkap</small>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div
                        class="card-header d-flex flex-wrap gap-3 align-items-center justify-content-between border-0 pb-0">
                        <div class="d-flex gap-2 align-items-center flex-wrap">
                            <div class="search-box">
                                <input type="text" class="form-control"
                                    placeholder="Cari {{ $isGuru ? 'Guru, NIP' : 'Siswa, NIS' }}..."
                                    wire:model.live.debounce.300ms="search">
                            </div>
                            <select class="form-select w-auto" wire:model.live="perPage">
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>

                            @if(!$isGuru)
                                <!-- Grade Filter - Only for Siswa -->
                                <select class="form-select w-auto" wire:model.live="filterGrade">
                                    <option value="">Semua Kelas</option>
                                    @foreach($availableGrades as $grade)
                                        <option value="{{ $grade }}">Kelas {{ $grade }}</option>
                                    @endforeach
                                </select>

                                <!-- Major Filter - Only for Siswa -->
                                @if(!$isSmp)
                                    <select class="form-select w-auto" wire:model.live="filterMajor">
                                        <option value="">Semua Jurusan</option>
                                        @foreach($availableMajorsFilter as $major)
                                            <option value="{{ $major }}">{{ $major }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            @endif

                            <!-- Photo Filter -->
                            <select class="form-select w-auto" wire:model.live="filterPhoto">
                                <option value="">Semua Foto</option>
                                <option value="with">Ada Foto</option>
                                <option value="without">Belum Ada Foto</option>
                            </select>

                            @if($filterGrade || $filterMajor || $filterPhoto)
                                <button
                                    wire:click="$set('filterGrade', ''); $set('filterMajor', ''); $set('filterPhoto', '')"
                                    class="btn btn-sm btn-outline-secondary">
                                    <i class="fi fi-rr-cross-small"></i> Reset Filter
                                </button>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th wire:click="sortBy('nis')" style="cursor: pointer;">
                                            {{ $isGuru ? 'NIP' : 'NIS' }}
                                            @if($sortColumn == 'nis') <i
                                                class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        @if(!$isGuru)
                                            <th wire:click="sortBy('nisn')" style="cursor: pointer;">
                                                NISN
                                                @if($sortColumn == 'nisn') <i
                                                    class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </th>
                                        @endif
                                        <th wire:click="sortBy('name')" style="cursor: pointer;">
                                            Nama Lengkap
                                            @if($sortColumn == 'name') <i
                                                class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                            @endif
                                        </th>
                                        @if(!$isGuru)
                                            <th wire:click="sortBy('grade')" style="cursor: pointer;">
                                                Kelas
                                                @if($sortColumn == 'grade') <i
                                                    class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </th>
                                            <th wire:click="sortBy('major')" style="cursor: pointer;">
                                                Jurusan
                                                @if($sortColumn == 'major') <i
                                                    class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </th>
                                            <th>TTL</th>
                                        @else
                                            <th wire:click="sortBy('major')" style="cursor: pointer;">
                                                Bidang/Mapel
                                                @if($sortColumn == 'major') <i
                                                    class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </th>
                                        @endif
                                        <th>WhatsApp</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $student)
                                        <tr>
                                            <td>{{ $student->nis }}</td>
                                            @if(!$isGuru)
                                                <td>{{ $student->nisn }}</td>
                                            @endif
                                            <td class="fw-bold">{{ $student->name }}</td>
                                            @if(!$isGuru)
                                                <td>
                                                    @if($student->grade)
                                                        <span class="badge bg-secondary">{{ $student->grade }}
                                                            {{ $student->class_name }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info-subtle text-info">{{ $student->major }}</span>
                                                </td>
                                                <td>
                                                    {{ $student->birth_place }},
                                                    {{ \Carbon\Carbon::parse($student->birth_date)->locale('id')->translatedFormat('d F Y') }}
                                                </td>
                                            @else
                                                <td>
                                                    @if($student->major)
                                                        <span
                                                            class="badge bg-warning-subtle text-warning">{{ $student->major }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            @endif

                                            <td>
                                                @if($student->whatsapp)
                                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $student->whatsapp)) }}"
                                                        target="_blank" class="text-success text-decoration-none">
                                                        <i class="fi fi-brands-whatsapp me-1"></i> {{ $student->whatsapp }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <button
                                                    onclick="showMagicLinkModal('{{ $student->name }}', '{{ route('student.public.profile', $student->magic_token) }}')"
                                                    class="btn btn-sm btn-icon btn-action-info" title="Magic Link">
                                                    <i class="fi fi-rr-link"></i>
                                                </button>
                                                @if($project->status !== 'completed')
                                                    <button wire:click="edit({{ $student->id }})"
                                                        class="btn btn-sm btn-icon btn-action-primary">
                                                        <i class="fi fi-rr-edit"></i>
                                                    </button>
                                                    <button onclick="confirmDelete({{ $student->id }})"
                                                        class="btn btn-sm btn-icon btn-action-danger">
                                                        <i class="fi fi-rr-trash"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $isGuru ? 5 : 8 }}" class="text-center py-4">
                                                <div class="text-muted">Tidak ada data {{ $isGuru ? 'guru' : 'siswa' }}
                                                    ditemukan untuk project ini.
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center flex-wrap">
                                <small class="text-muted mb-2 mb-md-0">
                                    Menampilkan {{ $students->firstItem() ?? 0 }} sampai
                                    {{ $students->lastItem() ?? 0 }} dari {{ $students->total() }} data
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
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5); overflow-y: auto;"
            tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $isEdit ? 'Edit Data ' . ($isGuru ? 'Guru' : 'Siswa') : 'Tambah ' . ($isGuru ? 'Guru' : 'Siswa') . ' Baru' }}
                        </h5>
                        <button wire:click="closeModal" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                            @if($isGuru)
                                <!-- GURU FORM -->
                                <div class="mb-3">
                                    <label class="form-label">NIP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nis') is-invalid @enderror" wire:model="nis"
                                        placeholder="Nomor Induk Pegawai">
                                    @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" placeholder="Nama Lengkap Guru">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Bidang / Mata Pelajaran</label>
                                    <input type="text" class="form-control @error('major') is-invalid @enderror"
                                        wire:model="major" placeholder="Contoh: Matematika, Bahasa Indonesia">
                                    @error('major') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <hr class="my-4 text-muted">
                                <h6 class="mb-3 text-muted">Informasi Kontak (Opsional)</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nomor WhatsApp</label>
                                        <input type="text" class="form-control @error('whatsapp') is-invalid @enderror"
                                            wire:model="whatsapp" placeholder="Contoh: 08123456789">
                                        @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            wire:model="email" placeholder="Contoh: guru@sekolah.com">
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @else
                                <!-- SISWA FORM -->
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NIS <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nis') is-invalid @enderror"
                                            wire:model="nis" placeholder="Nomor Induk Sekolah">
                                        @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">NISN <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('nisn') is-invalid @enderror"
                                            wire:model="nisn" placeholder="Nomor Induk Siswa Nasional">
                                        @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" placeholder="Nama Lengkap Siswa">
                                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                                        <input type="text" class="form-control @error('class_name') is-invalid @enderror"
                                            wire:model="class_name" placeholder="Contoh: A, B, 1, IPA 1">
                                        <div class="form-text small">Masukkan nama rombel/paralel, misal: A, B, 1, atau IPA 1.
                                        </div>
                                        @error('class_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
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

                                <hr class="my-4 text-muted">
                                <h6 class="mb-3 text-muted">Informasi Tambahan (Opsional)</h6>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tempat Lahir</label>
                                        <input type="text" class="form-control @error('birth_place') is-invalid @enderror"
                                            wire:model="birth_place" placeholder="Contoh: Jakarta">
                                        @error('birth_place') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Lahir</label>
                                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                            wire:model="birth_date">
                                        @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nomor WhatsApp</label>
                                        <input type="text" class="form-control @error('whatsapp') is-invalid @enderror"
                                            wire:model="whatsapp" placeholder="Contoh: 08123456789">
                                        @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            wire:model="email" placeholder="Contoh: siswa@sekolah.com">
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alamat Lengkap</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" wire:model="address"
                                        rows="3" placeholder="Alamat Tempat Tinggal"></textarea>
                                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
        function confirmDelete(id) {
            Swal.fire({
                title: 'Hapus Data {{ $isGuru ? "Guru" : "Siswa" }}?',
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

    <!-- Copy Students Modal -->
    @if($showCopyModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fi fi-rr-copy me-2"></i>Salin Siswa dari Project Lain</h5>
                        <button wire:click="closeCopyModal" type="button" class="btn-close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Source Project Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Project Sumber</label>
                            <select class="form-select" wire:model.live="sourceProjectId">
                                <option value="">-- Pilih Project --</option>
                                @foreach($otherProjects as $proj)
                                    <option value="{{ $proj['id'] }}">
                                        {{ $proj['name'] }} ({{ $proj['students_count'] }} siswa)
                                    </option>
                                @endforeach
                            </select>
                            @if(count($otherProjects) === 0)
                                <div class="text-muted mt-2 small">Tidak ada project lain yang tersedia.</div>
                            @endif
                        </div>

                        <!-- Students List -->
                        @if($sourceProjectId && count($sourceStudents) > 0)
                            <div class="border rounded">
                                <div class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll"
                                            wire:model.live="selectAll">
                                        <label class="form-check-label fw-bold" for="selectAll">Pilih Semua
                                            ({{ count($sourceStudents) }} siswa tersedia)</label>
                                    </div>
                                    <span class="badge bg-primary">{{ count($selectedStudents) }} dipilih</span>
                                </div>
                                <div style="max-height: 300px; overflow-y: auto;">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th style="width: 40px;"></th>
                                                <th>NIS</th>
                                                <th>Nama</th>
                                                <th>Kelas</th>
                                                <th>Jurusan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sourceStudents as $student)
                                                <tr>
                                                    <td>
                                                        <input class="form-check-input" type="checkbox" value="{{ $student['id'] }}"
                                                            wire:model.live="selectedStudents">
                                                    </td>
                                                    <td>{{ $student['nis'] }}</td>
                                                    <td>{{ $student['name'] }}</td>
                                                    <td>{{ $student['grade'] }} {{ $student['class_name'] }}</td>
                                                    <td><span class="badge bg-info-subtle text-info">{{ $student['major'] }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif($sourceProjectId && count($sourceStudents) === 0)
                            <div class="alert alert-info mb-0">
                                <i class="fi fi-rr-info me-2"></i>Semua siswa dari project ini sudah ada di project saat ini,
                                atau project sumber tidak memiliki siswa.
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button wire:click="closeCopyModal" type="button" class="btn btn-secondary">Batal</button>
                        <button wire:click="copyStudents" type="button" class="btn btn-success" {{ count($selectedStudents) === 0 ? 'disabled' : '' }}>
                            <i class="fi fi-rr-copy me-1"></i> Salin {{ count($selectedStudents) }} Siswa
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Magic Link Modal -->
    <div class="modal fade" id="magicLinkModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fi fi-rr-link me-2"></i>Magic Link</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="mb-3" id="magicLinkStudentName"></h6>

                    <!-- QR Code -->
                    <div id="qrcode" class="d-flex justify-content-center mb-4"></div>

                    <!-- Link Text -->
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="magicLinkUrl" readonly>
                        <button class="btn btn-primary" type="button" onclick="copyMagicLink()">
                            <i class="fi fi-rr-copy"></i> Copy
                        </button>
                    </div>

                    <small class="text-muted">Siswa dapat mengakses profilnya melalui link atau scan QR Code di
                        atas.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- QRCode.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <script>
        let qrInstance = null;

        function showMagicLinkModal(studentName, url) {
            document.getElementById('magicLinkStudentName').textContent = studentName;
            document.getElementById('magicLinkUrl').value = url;

            // Clear previous QR code
            const qrContainer = document.getElementById('qrcode');
            qrContainer.innerHTML = '';

            // Generate new QR code
            qrInstance = new QRCode(qrContainer, {
                text: url,
                width: 200,
                height: 200,
                colorDark: '#000000',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('magicLinkModal'));
            modal.show();
        }

        function copyMagicLink() {
            const urlInput = document.getElementById('magicLinkUrl');
            urlInput.select();
            urlInput.setSelectionRange(0, 99999);

            navigator.clipboard.writeText(urlInput.value).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Link berhasil disalin ke clipboard.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }).catch(() => {
                // Fallback for older browsers
                document.execCommand('copy');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Link berhasil disalin ke clipboard.',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }
    </script>
</div>