<div>
    <div class="container">
        <!-- Page Header -->
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <div class="clearfix">
                <h1 class="app-page-title">{{ $project->name }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.project.index') }}">Data Project</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $project->name }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <button wire:click="exportExcel" class="btn btn-success waves-effect waves-light">
                    <i class="fi fi-rr-file-excel me-1"></i> Export Excel
                </button>
                @if(!$isGuru)
                    <a href="{{ route('admin.project.siswa.import', $project) }}"
                        class="btn btn-outline-primary waves-effect waves-light">
                        <i class="fi fi-rr-file-import me-1"></i> Import Siswa
                    </a>
                @endif
                <button wire:click="create" class="btn btn-primary waves-effect waves-light">
                    <i class="fi fi-rr-plus me-1"></i> Tambah {{ $isGuru ? 'Guru' : 'Siswa' }}
                </button>
                <div class="vr"></div>
                <button wire:click="openPropagationModal" class="btn btn-primary waves-effect waves-light">
                    <i class="fi fi-rr-picture me-1"></i> Propagasi Foto
                </button>
                <button onclick="confirmResetPropagation()" class="btn btn-danger waves-effect waves-light">
                    <i class="fi fi-rr-trash me-1"></i> Reset Propagasi
                </button>
            </div>
        </div>

        <!-- Project Info Cards -->
        <div class="row">
            <div class="col-xxl-12">
                <div class="row">
                    <div class="col-xxl-3 col-lg-6 col-sm-6">
                        <div class="card card-action action-border-primary p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-primary">
                                    <i class="fi fi-sr-school fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Sekolah</div>
                                    <h6 class="mb-0 fw-bold">{{ $project->school->name ?? '-' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-6 col-sm-6">
                        <div class="card card-action action-border-info p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-info">
                                    <i class="fi fi-sr-calendar fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Tahun Ajaran</div>
                                    <h6 class="mb-0 fw-bold">{{ $project->academicYear->year_name ?? '-' }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-6 col-sm-6">
                        <div class="card card-action action-border-warning p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-warning">
                                    <i class="fi fi-sr-{{ $isGuru ? 'chalkboard-user' : 'users' }} fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Jumlah {{ $isGuru ? 'Guru' : 'Siswa' }}</div>
                                    <h3 class="mb-0 fw-bold">{{ number_format($students->total()) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-lg-6 col-sm-6">
                        @php
                            $statusColor = match ($project->status) {
                                'active' => 'success',
                                'draft' => 'secondary',
                                'completed' => 'info',
                                default => 'secondary'
                            };
                        @endphp
                        <div class="card card-action action-border-{{ $statusColor }} p-1 position-relative">
                            <div class="card-body d-flex gap-3 align-items-center p-4">
                                <div class="clearfix pe-2 text-{{ $statusColor }}">
                                    <i
                                        class="fi fi-sr-{{ $project->status === 'active' ? 'check-circle' : ($project->status === 'completed' ? 'badge-check' : 'edit') }} fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Status</div>
                                    <h6 class="mb-0 fw-bold">{{ ucfirst($project->status) }}</h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Propagation Modal -->
        @if($showPropagationModal)
            <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5); overflow-y: auto;"
                tabindex="-1" role="dialog" aria-modal="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Propagasi Foto {{ $isGuru ? 'Guru' : 'Siswa' }}</h5>
                            <button wire:click="closePropagationModal" type="button" class="btn-close"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if(!$isProcessing && empty($previewData))
                                <div class="alert alert-info">
                                    <h6 class="alert-heading fw-bold mb-1"><i class="fi fi-rr-info me-1"></i> Petunjuk Upload
                                    </h6>
                                    <ul class="mb-0 small ps-3">
                                        <li>Upload file <strong>.ZIP</strong> (Maks. 50MB) berisi foto {{ $isGuru ? 'guru' : 'siswa' }}.</li>
                                        <li>Format nama file foto: <strong>{{ $isGuru ? 'NIP' : 'NIS' }}_Nama.jpg</strong> atau
                                            <strong>{{ $isGuru ? 'NIP' : 'NISN' }}_Nama.png</strong>.
                                        </li>
                                        <li>Contoh: <code>{{ $isGuru ? '199112302025211070_Ahmad Surya.jpg' : '12345_Budi Santoso.jpg' }}</code></li>
                                        <li>Sistem akan mencocokkan berdasarkan {{ $isGuru ? 'NIP' : 'NIS atau NISN' }} di awal nama file.</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Jenis Foto</label>
                                    <select class="form-select @error('photoType') is-invalid @enderror" wire:model="photoType">
                                        <option value="Formal">Formal (Pas Foto)</option>
                                        <option value="Bebas">Bebas</option>
                                        <option value="Kegiatan">Kegiatan</option>
                                    </select>
                                    @error('photoType') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Pilih File ZIP</label>
                                    <input type="file" class="form-control @error('propagationFile') is-invalid @enderror"
                                        wire:model="propagationFile" accept=".zip">
                                    @error('propagationFile') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            @endif

                            @if($isProcessing)
                                <div class="text-center py-4">
                                    <div class="spinner-border text-primary mb-2" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mb-0 text-muted">{{ $processingMessage }}</p>
                                </div>
                            @endif

                            @if(!$isProcessing && !empty($previewData))
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Preview Hasil Pencocokan</h6>
                                    <div>
                                        <span class="badge bg-success me-2">Valid:
                                            {{ collect($previewData)->where('valid', true)->count() }}</span>
                                        <span class="badge bg-danger">Invalid:
                                            {{ collect($previewData)->where('valid', false)->count() }}</span>
                                    </div>
                                </div>
                                <div class="table-responsive border rounded" style="max-height: 400px;">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th>Nama File</th>
                                                <th>Identifier ({{ $isGuru ? 'NIP' : 'NIS/NISN' }})</th>
                                                <th>Nama di File</th>
                                                <th>Match {{ $isGuru ? 'Guru' : 'Siswa' }}</th>
                                                <th>Manual Match</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($previewData as $index => $item)
                                                <tr class="{{ $item['valid'] ? '' : 'table-danger' }}"
                                                    wire:key="preview-row-{{ $index }}">
                                                    <td>{{ $item['filename'] }}</td>
                                                    <td>{{ $item['identifier'] }}</td>
                                                    <td>{{ $item['name_in_file'] }}</td>
                                                    <td>{{ $item['matched_student'] }}</td>
                                                    <td>
                                                        @if(!$item['valid'] || $item['status'] == 'Manual Match' || $item['status_class'] == 'warning')
                                                            <div style="min-width: 200px;">
                                                                <select class="form-select form-select-sm"
                                                                    wire:change="updateManualMatch({{ $index }}, $event.target.value)">
                                                                    <option value="">-- Pilih {{ $isGuru ? 'Guru' : 'Siswa' }} Manual --</option>
                                                                    @foreach($allStudents as $student)
                                                                        <option value="{{ $student['id'] }}" {{ $item['student_id'] == $student['id'] ? 'selected' : '' }}>
                                                                            {{ $student['name'] }} ({{ $student['nis'] }})
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @else
                                                            <span class="text-muted small">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $item['status_class'] }}-subtle text-{{ $item['status_class'] }}">
                                                            {{ $item['status'] }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button wire:click="closePropagationModal" type="button" class="btn btn-light" {{ $isProcessing ? 'disabled' : '' }}>Batal</button>
                            @if(!empty($previewData) && collect($previewData)->where('valid', true)->count() > 0)
                                <button wire:click="submitPropagation" type="button" class="btn btn-primary" {{ $isProcessing ? 'disabled' : '' }}>
                                    <i class="fi fi-rr-check me-1"></i> Submit Propagasi
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Photo Detail Modal -->
        @if($showPhotoModal && $selectedStudent)
            <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5); overflow-y: auto;"
                tabindex="-1" role="dialog" aria-modal="true" wire:click.self="closePhotoModal" x-data
                @keydown.escape.window="$wire.closePhotoModal()">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Galeri Foto: {{ $selectedStudent->name }}</h5>
                            <button wire:click="closePhotoModal" type="button" class="btn-close"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body bg-light">
                            <div class="container-fluid">
                                @if(count($studentPhotos) > 0)
                                    <div class="row g-3">
                                        @foreach($studentPhotos as $photo)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="card h-100 shadow-sm border-0 group-hover">
                                                    <div class="card-img-top bg-white d-flex align-items-center justify-content-center p-2 position-relative"
                                                        style="height: 250px;">
                                                        <img src="{{ $photo['url'] }}" alt="{{ $photo['type'] }}"
                                                            class="img-fluid rounded"
                                                            style="max-height: 100%; max-width: 100%; object-fit: contain;">

                                                        <button
                                                            class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle shadow-sm"
                                                            onclick="confirmDeletePhoto('{{ $photo['path'] }}', {{ $photo['id'] ?? 'null' }})"
                                                            title="Hapus Foto">
                                                            <i class="fi fi-rr-trash"></i>
                                                        </button>
                                                    </div>
                                                    <div class="card-footer bg-white border-top-0 text-center py-2">
                                                        <span
                                                            class="badge bg-{{ $photo['is_main'] ? 'primary' : 'secondary' }} rounded-pill">
                                                            {{ $photo['type'] }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fi fi-rr-picture fs-1 d-block mb-2"></i>
                                            Tidak ada foto tersedia untuk {{ $isGuru ? 'guru' : 'siswa' }} ini.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Students Table Card -->
        <div class="card">
            <div class="card-header d-flex gap-3 flex-wrap align-items-center justify-content-between">
                <h6 class="card-title mb-0">Daftar {{ $isGuru ? 'Guru' : 'Siswa' }}</h6>
                <div class="clearfix d-flex align-items-center gap-3">
                    <div class="search-box">
                        <input type="text" class="form-control"
                            placeholder="Cari {{ $isGuru ? 'Guru, NIP' : 'Siswa, NIS, NISN' }}..."
                            wire:model.live.debounce.300ms="search">
                    </div>
                    <select class="form-select w-auto" wire:model.live="filterPhoto">
                        <option value="">Semua Foto</option>
                        <option value="with">Sudah Upload</option>
                        <option value="without">Belum Upload</option>
                    </select>
                    <select class="form-select w-auto" wire:model.live="perPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th wire:click="sortBy('nis')" style="cursor: pointer;">
                                {{ $isGuru ? 'NIP' : 'NIS' }} @if($sortColumn == 'nis') <i
                                class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                            </th>
                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                Nama Lengkap @if($sortColumn == 'name') <i
                                class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                            </th>
                            @if(!$isGuru)
                                <th wire:click="sortBy('grade')" style="cursor: pointer;">
                                    Kelas @if($sortColumn == 'grade') <i
                                    class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                                </th>
                            @endif
                            <th wire:click="sortBy('major')" style="cursor: pointer;">
                                {{ $isGuru ? 'Bidang/Mapel' : 'Jurusan' }} @if($sortColumn == 'major') <i
                                class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                            </th>
                            @if(!$isGuru)
                                <th>Tanggal Lahir</th>
                            @endif
                            <th>WhatsApp</th>
                            <th wire:click="sortBy('photos_count')" style="cursor: pointer;">
                                Jumlah Foto @if($sortColumn == 'photos_count') <i
                                class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                            </th>
                            <th>Foto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>
                                    <span class="fw-medium">{{ $student->nis }}</span>
                                    @if(!$isGuru)
                                        <div class="small text-muted">{{ $student->nisn }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold"
                                            id="student-name-{{ $student->id }}">{{ $student->name }}</span>
                                        <button class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle"
                                            onclick="copyToClipboard('{{ ($student->nisn ?? $student->nis) }}_{{ $student->name }}.jpg')"
                                            title="Copy Format Nama File">
                                            <i class="fi fi-rr-copy"></i>
                                        </button>
                                    </div>
                                </td>
                                @if(!$isGuru)
                                    <td>
                                        @if($student->grade)
                                            <span class="badge bg-secondary">{{ $student->grade }} {{ $student->class_name }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endif
                                <td>
                                    @if($student->major)
                                        <span
                                            class="badge bg-{{ $isGuru ? 'warning' : 'info' }}-subtle text-{{ $isGuru ? 'warning' : 'info' }}">{{ $student->major }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                @if(!$isGuru)
                                    <td>
                                        @if($student->birth_date)
                                            {{ \Carbon\Carbon::parse($student->birth_date)->locale('id')->translatedFormat('d F Y') }}
                                        @else
                                            -
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
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $student->photos_count > 0 ? 'primary' : 'secondary' }}">
                                        {{ $student->photos_count }} Foto
                                    </span>
                                </td>
                                <td>
                                    <button wire:click="openPhotoModal('{{ $student->id }}')"
                                        class="btn btn-sm {{ $student->photo || $student->photos->count() > 0 ? 'btn-outline-primary' : 'btn-outline-secondary disabled' }}"
                                        {{ !$student->photo && $student->photos->count() == 0 ? 'disabled' : '' }}>
                                        <i class="fi fi-rr-picture me-1"></i> Detail Foto
                                    </button>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end">
                                        <button wire:click="edit({{ $student->id }})"
                                            class="btn btn-sm btn-icon btn-action-primary" title="Edit">
                                            <i class="fi fi-rr-edit"></i>
                                        </button>
                                        <button onclick="confirmDelete({{ $student->id }})"
                                            class="btn btn-sm btn-icon btn-action-danger" title="Hapus">
                                            <i class="fi fi-rr-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isGuru ? 7 : 9 }}" class="text-center py-5">
                                    <div class="text-muted">
                                        <i
                                            class="fi fi-rr-{{ $isGuru ? 'chalkboard-user' : 'user' }} fs-1 d-block mb-2"></i>
                                        Tidak ada data {{ $isGuru ? 'guru' : 'siswa' }} ditemukan untuk project ini.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($students->hasPages())
                <div class="card-footer border-top">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Student Modal -->
    @if($showModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5); overflow-y: auto;"
            tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? 'Edit Data ' . ($isGuru ? 'Guru' : 'Siswa') : 'Tambah ' . ($isGuru ? 'Guru' : 'Siswa') . ' Baru' }}</h5>
                        <button wire:click="closeModal" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="{{ $isEdit ? 'update' : 'store' }}">
                            @if($isGuru)
                                <!-- GURU FORM -->
                                <div class="mb-3">
                                    <label class="form-label">NIP <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nis') is-invalid @enderror"
                                        wire:model="nis" placeholder="Nomor Induk Pegawai">
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

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                Toast.fire({
                    icon: 'success',
                    title: 'Format nama file berhasil disalin'
                })
            }, function (err) {
                console.error('Could not copy text: ', err);
            });
        }

        function confirmDeletePhoto(path, photoId) {
            Swal.fire({
                title: 'Hapus Foto?',
                text: 'Yakin ingin menghapus foto ini? Tindakan ini tidak dapat dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('deletePhoto', path, photoId);
                }
            });
        }

        function confirmResetPropagation() {
            Swal.fire({
                title: 'Reset Propagasi?',
                text: 'Yakin ingin menghapus SEMUA foto propagasi di project ini? Semua foto siswa yang diupload via project ini akan dihapus permanen. Siswa akan kembali tidak memiliki foto jika tidak ada backup.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Reset Semua!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('resetPropagation');
                }
            });
        }
    </script>
</div>