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
            <button wire:click="openPropagationModal" class="btn btn-primary waves-effect waves-light">
                <i class="fi fi-rr-picture me-1"></i> Propagasi Foto
            </button>
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
                                    <i class="fi fi-sr-users fs-1"></i>
                                </div>
                                <div class="clearfix">
                                    <div class="mb-1">Jumlah Siswa</div>
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
                            <h5 class="modal-title">Propagasi Foto Siswa</h5>
                            <button wire:click="closePropagationModal" type="button" class="btn-close"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if(!$isProcessing && empty($previewData))
                                <div class="alert alert-info">
                                    <h6 class="alert-heading fw-bold mb-1"><i class="fi fi-rr-info me-1"></i> Petunjuk Upload
                                    </h6>
                                    <ul class="mb-0 small ps-3">
                                        <li>Upload file <strong>.ZIP</strong> (Maks. 50MB) berisi foto siswa.</li>
                                        <li>Format nama file foto: <strong>NIS_Nama.jpg</strong> atau
                                            <strong>NISN_Nama.png</strong>.
                                        </li>
                                        <li>Contoh: <code>12345_Budi Santoso.jpg</code></li>
                                        <li>Sistem akan mencocokkan berdasarkan NIS atau NISN di awal nama file.</li>
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
                                                <th>Identifier (NIS/NISN)</th>
                                                <th>Nama di File</th>
                                                <th>Match Siswa</th>
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
                                                                    <option value="">-- Pilih Siswa Manual --</option>
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
                                            Tidak ada foto tersedia untuk siswa ini.
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
                <h6 class="card-title mb-0">Daftar Siswa</h6>
                <div class="clearfix d-flex align-items-center gap-3">
                    <div class="search-box">
                        <input type="text" class="form-control" placeholder="Cari Siswa, NIS, NISN..."
                            wire:model.live.debounce.300ms="search">
                    </div>
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
                                NIS @if($sortColumn == 'nis') <i
                                class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                            </th>
                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                Nama Lengkap @if($sortColumn == 'name') <i
                                class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                            </th>
                            <th wire:click="sortBy('grade')" style="cursor: pointer;">
                                Kelas @if($sortColumn == 'grade') <i
                                class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                            </th>
                            <th wire:click="sortBy('major')" style="cursor: pointer;">
                                Jurusan @if($sortColumn == 'major') <i
                                class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                            </th>
                            <th>Tanggal Lahir</th>
                            <th>WhatsApp</th>
                            <th>Foto</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td>
                                    <span class="fw-medium">{{ $student->nis }}</span>
                                    <div class="small text-muted">{{ $student->nisn }}</div>
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
                                <td>{{ \Carbon\Carbon::parse($student->birth_date)->locale('id')->translatedFormat('d F Y') }}
                                </td>
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
                                    <button wire:click="openPhotoModal('{{ $student->id }}')"
                                        class="btn btn-sm {{ $student->photo || $student->photos->count() > 0 ? 'btn-outline-primary' : 'btn-outline-secondary disabled' }}"
                                        {{ !$student->photo && $student->photos->count() == 0 ? 'disabled' : '' }}>
                                        <i class="fi fi-rr-picture me-1"></i> Detail Foto
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fi fi-rr-user fs-1 d-block mb-2"></i>
                                        Tidak ada data siswa ditemukan untuk project ini.
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

    <script>
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
    </script>
</div>