<div>
    <div class="container">
        <!-- Page Header -->
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between">
            <div class="clearfix">
                <h1 class="app-page-title">Import Data Siswa</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('sekolah.dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('sekolah.project.index') }}">Project</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('sekolah.project.data', $project) }}">{{ $project->name }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">Import</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sekolah.project.data', $project) }}" class="btn btn-outline-secondary waves-effect">
                    <i class="fi fi-rr-arrow-left me-1"></i> Kembali
                </a>
                <button wire:click="downloadTemplate" class="btn btn-primary waves-effect waves-light">
                    <i class="fi fi-rr-download me-1"></i> Download Template
                </button>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="row">
            <div class="col-xxl-4 col-lg-6 col-md-6">
                <div class="card card-action action-border-primary p-1">
                    <div class="card-body d-flex gap-3 align-items-center p-4">
                        <div class="clearfix pe-2 text-primary">
                            <i class="fi fi-sr-folder fs-1"></i>
                        </div>
                        <div class="clearfix">
                            <div class="mb-1">Project</div>
                            <h6 class="mb-0 fw-bold">{{ $project->name }}</h6>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xxl-4 col-lg-6 col-md-6">
                <div class="card card-action action-border-info p-1">
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
            <div class="col-xxl-4 col-lg-12 col-md-12">
                <div class="card card-action action-border-success p-1">
                    <div class="card-body d-flex gap-3 align-items-center p-4">
                        <div class="clearfix pe-2 text-success">
                            <i class="fi fi-sr-document fs-1"></i>
                        </div>
                        <div class="clearfix">
                            <div class="mb-1">Format File</div>
                            <h6 class="mb-0 fw-bold">.xlsx, .xls, .csv (max 2MB)</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Card -->
        <div class="card">
            <div class="card-header border-0 pb-0">
                <h6 class="card-title mb-0">Upload File Excel</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="fi fi-rr-info mt-1"></i>
                        <div>
                            <strong class="d-block mb-1">Petunjuk Import:</strong>
                            <ul class="mb-0 ps-3 small">
                                <li>Download template terlebih dahulu untuk format yang benar</li>
                                <li>Isi data siswa sesuai kolom yang tersedia</li>
                                <li>Pastikan NIS unik untuk setiap siswa</li>
                                <li>Format tanggal lahir: DD/MM/YYYY atau YYYY-MM-DD</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="upload-zone border border-2 border-dashed rounded-4 p-5 text-center bg-light mb-3">
                    <div class="text-primary mb-3">
                        <i class="fi fi-rr-cloud-upload-alt fs-1"></i>
                    </div>
                    <h6 class="mb-2">Pilih File Excel</h6>
                    <p class="text-muted small mb-3">Drag & drop file atau klik untuk browse</p>
                    <input type="file" class="form-control mx-auto @error('file') is-invalid @enderror"
                        style="max-width: 400px;" wire:model="file" accept=".xlsx,.xls,.csv">
                    @error('file') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                </div>

                <div wire:loading wire:target="file" class="w-100 mb-3">
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary w-100"
                            role="progressbar"></div>
                    </div>
                    <small class="text-muted d-block mt-2 text-center">
                        <i class="fi fi-rr-spinner-alt me-1"></i> Mengupload dan memproses file...
                    </small>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        @if($isUploaded && count($previewData) > 0)
            <div class="card">
                <div class="card-header d-flex gap-3 flex-wrap align-items-center justify-content-between">
                    <h6 class="card-title mb-0">Preview Data Import</h6>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success-subtle text-success px-3 py-2">
                            <i class="fi fi-rr-check me-1"></i> Valid: {{ count($validData) }}
                        </span>
                        <span class="badge bg-danger-subtle text-danger px-3 py-2">
                            <i class="fi fi-rr-cross me-1"></i> Invalid: {{ count($invalidData) }}
                        </span>
                    </div>
                </div>
                <div class="table-responsive" style="max-height: 500px;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width: 60px;">Row</th>
                                <th>NIS</th>
                                <th>Nama</th>
                                <th>Jurusan</th>
                                <th>Kelas</th>
                                <th>Email</th>
                                <th>WhatsApp</th>
                                <th>Tgl Lahir</th>
                                <th>Status</th>
                                <th>Pesan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewData as $data)
                                <tr class="{{ $data['status'] == 'Invalid' ? 'table-danger' : '' }}">
                                    <td>
                                        <span class="badge bg-secondary rounded-pill">{{ $data['row'] }}</span>
                                    </td>
                                    <td class="fw-medium">{{ $data['nis'] }}</td>
                                    <td>{{ $data['name'] }}</td>
                                    <td>
                                        <span class="badge bg-info-subtle text-info">{{ $data['major'] }}</span>
                                    </td>
                                    <td>
                                        @if($data['grade'])
                                            {{ $data['grade'] }} {{ $data['class_name'] }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $data['email'] ?? '-' }}</td>
                                    <td>{{ $data['whatsapp'] ?? '-' }}</td>
                                    <td>{{ $data['birth_date'] ? \Carbon\Carbon::parse($data['birth_date'])->locale('id')->translatedFormat('d M Y') : '-' }}
                                    </td>
                                    <td>
                                        @if($data['status'] == 'Valid')
                                            <span class="badge bg-success">
                                                <i class="fi fi-rr-check me-1"></i> Valid
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fi fi-rr-cross me-1"></i> Invalid
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($data['errors'])
                                            <small class="text-danger">{{ $data['errors'] }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer border-top d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Total: {{ count($previewData) }} data | Valid: {{ count($validData) }} | Invalid:
                        {{ count($invalidData) }}
                    </div>
                    <div class="d-flex gap-2">
                        <button wire:click="cancel" class="btn btn-light waves-effect">
                            <i class="fi fi-rr-cross me-1"></i> Batal
                        </button>
                        @if(count($validData) > 0)
                            <button type="button" class="btn btn-success waves-effect waves-light"
                                onclick="confirmImport({{ count($validData) }})">
                                <span wire:loading.remove wire:target="import">
                                    <i class="fi fi-rr-check me-1"></i> Import {{ count($validData) }} Data
                                </span>
                                <span wire:loading wire:target="import">
                                    <i class="fi fi-rr-spinner-alt me-1"></i> Processing...
                                </span>
                            </button>
                        @else
                            <button class="btn btn-secondary" disabled>
                                <i class="fi fi-rr-ban me-1"></i> Tidak Ada Data Valid
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function confirmImport(count) {
            Swal.fire({
                title: 'Konfirmasi Import',
                html: `Yakin ingin mengimport <strong>${count}</strong> data siswa?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fi fi-rr-check me-1"></i> Ya, Import!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    @this.call('import');
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