<div>
    <div class="container">
        <!-- Breadcrumb -->
        <div class="row mb-3">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-primary">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.data-siswa.index') }}" class="text-primary">Data Siswa</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $school->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- School Info Card -->
        <div class="card mb-4 border-0 shadow-sm bg-primary text-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-lg bg-white text-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                        <i class="fi fi-rr-school fs-2"></i>
                    </div>
                    <div>
                        <h4 class="mb-1 text-white">{{ $school->name }}</h4>
                        <div class="d-flex gap-3 text-white-50 small">
                            <span><i class="fi fi-rr-map-marker me-1"></i> {{ $school->address }}</span>
                            <span><i class="fi fi-rr-id-card-clip-alt me-1"></i> NPSN: {{ $school->npsn ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="search-box">
                            <input type="text" class="form-control" placeholder="Cari Siswa, NIS, NISN..." wire:model.live.debounce.300ms="search">
                        </div>
                    </div>
                    <div class="col-md-auto ms-auto d-flex gap-2">
                        <button wire:click="openPropagationModal" class="btn btn-primary">
                            <i class="fi fi-rr-picture me-1"></i> Propagasi Foto
                        </button>
                        <select class="form-select w-auto" wire:model.live="perPage">
                            <option value="10">10 Data</option>
                            <option value="25">25 Data</option>
                            <option value="50">50 Data</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Propagation Modal -->
        @if($showPropagationModal)
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5); overflow-y: auto;" tabindex="-1" role="dialog" aria-modal="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Propagasi Foto Siswa</h5>
                        <button wire:click="closePropagationModal" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if(!$isProcessing && empty($previewData))
                        <div class="alert alert-info">
                            <h6 class="alert-heading fw-bold mb-1"><i class="fi fi-rr-info me-1"></i> Petunjuk Upload</h6>
                            <ul class="mb-0 small ps-3">
                                <li>Upload file <strong>.ZIP</strong> (Maks. 50MB) berisi foto siswa.</li>
                                <li>Format nama file foto: <strong>NIS_Nama.jpg</strong> atau <strong>NISN_Nama.png</strong>.</li>
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
                            <input type="file" class="form-control @error('propagationFile') is-invalid @enderror" wire:model="propagationFile" accept=".zip">
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
                                <span class="badge bg-success me-2">Valid: {{ collect($previewData)->where('valid', true)->count() }}</span>
                                <span class="badge bg-danger">Invalid: {{ collect($previewData)->where('valid', false)->count() }}</span>
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
                                    <tr class="{{ $item['valid'] ? '' : 'table-danger' }}" wire:key="preview-row-{{ $index }}">
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
                                            <span class="badge bg-{{ $item['status_class'] }}-subtle text-{{ $item['status_class'] }}">
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
        <div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5); overflow-y: auto;" tabindex="-1" role="dialog" aria-modal="true" wire:click.self="closePhotoModal" x-data @keydown.escape.window="$wire.closePhotoModal()">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Galeri Foto: {{ $selectedStudent->name }}</h5>
                        <button wire:click="closePhotoModal" type="button" class="btn-close" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="container-fluid">
                            @if(count($studentPhotos) > 0)
                                <div class="row g-3">
                                    @foreach($studentPhotos as $photo)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 shadow-sm border-0 group-hover">
                                            <div class="card-img-top bg-white d-flex align-items-center justify-content-center p-2 position-relative" style="height: 250px;">
                                                <img src="{{ $photo['url'] }}" alt="{{ $photo['type'] }}" class="img-fluid rounded" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                                
                                                <button class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 rounded-circle shadow-sm" 
                                                        wire:click="deletePhoto('{{ $photo['path'] }}', {{ $photo['id'] ?? 'null' }})"
                                                        wire:confirm="Yakin ingin menghapus foto ini?"
                                                        title="Hapus Foto">
                                                    <i class="fi fi-rr-trash"></i>
                                                </button>
                                            </div>
                                            <div class="card-footer bg-white border-top-0 text-center py-2">
                                                <span class="badge bg-{{ $photo['is_main'] ? 'primary' : 'secondary' }} rounded-pill">
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

        <!-- Students Table -->
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th wire:click="sortBy('nis')" style="cursor: pointer;">
                                NIS @if($sortColumn == 'nis') <i class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                            </th>
                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                Nama Lengkap @if($sortColumn == 'name') <i class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                            </th>
                            <th wire:click="sortBy('grade')" style="cursor: pointer;">
                                Kelas @if($sortColumn == 'grade') <i class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
                            </th>
                            <th wire:click="sortBy('major')" style="cursor: pointer;">
                                Jurusan @if($sortColumn == 'major') <i class="fi fi-rr-arrow-{{ $sortDirection == 'asc' ? 'up' : 'down' }}"></i> @endif
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
                                    <span class="fw-bold" id="student-name-{{ $student->id }}">{{ $student->name }}</span>
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
                            <td>{{ \Carbon\Carbon::parse($student->birth_date)->locale('id')->translatedFormat('d F Y') }}</td>
                            <td>
                                @if($student->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $student->whatsapp)) }}" target="_blank" class="text-success text-decoration-none">
                                        <i class="fi fi-brands-whatsapp me-1"></i> {{ $student->whatsapp }}
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <button wire:click="openPhotoModal('{{ $student->id }}')" class="btn btn-sm {{ $student->photo || $student->photos->count() > 0 ? 'btn-outline-primary' : 'btn-outline-secondary disabled' }}" {{ !$student->photo && $student->photos->count() == 0 ? 'disabled' : '' }}>
                                    <i class="fi fi-rr-picture me-1"></i> Detail Foto
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fi fi-rr-user fs-1 d-block mb-2"></i>
                                    Tidak ada data siswa ditemukan untuk sekolah ini.
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
            navigator.clipboard.writeText(text).then(function() {
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
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
</div>
