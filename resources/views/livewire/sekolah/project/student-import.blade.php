<div>
    <div class="container">
        <!-- Breadcrumb -->
        <div class="row mb-3">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('sekolah.project.index') }}"
                                class="text-primary">Project</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('sekolah.project.siswa', $project) }}"
                                class="text-primary">{{ $project->name }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Import Siswa</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Page Title -->
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
            <div class="clearfix">
                <h1 class="app-page-title">Import Data Siswa</h1>
                <span class="text-muted">Upload file Excel untuk import data massal ke project
                    {{ $project->name }}</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('sekolah.project.siswa', $project) }}" class="btn btn-outline-secondary waves-effect">
                    <i class="fi fi-rr-arrow-left me-1"></i> Kembali
                </a>
                <button wire:click="downloadTemplate" class="btn btn-outline-primary waves-effect">
                    <i class="fi fi-rr-download me-1"></i> Download Template
                </button>
            </div>
        </div>

        <!-- Upload Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Upload File Excel</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror"
                                wire:model="file" accept=".xlsx,.xls,.csv">
                            @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="form-text">Format yang didukung: .xlsx, .xls, .csv. Maksimal 2MB.</div>
                        </div>

                        <div wire:loading wire:target="file" class="w-100">
                            <div class="progress" style="height: 5px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated w-100"
                                    role="progressbar"></div>
                            </div>
                            <small class="text-muted d-block mt-1">Mengupload dan memproses file...</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Section -->
        @if($isUploaded && count($previewData) > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Preview Data Import</h5>
                            <div>
                                <span class="badge bg-success me-2">Valid: {{ count($validData) }}</span>
                                <span class="badge bg-danger">Invalid: {{ count($invalidData) }}</span>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 500px;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Row</th>
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
                                                <td>{{ $data['row'] }}</td>
                                                <td>{{ $data['nis'] }}</td>
                                                <td>{{ $data['name'] }}</td>
                                                <td>{{ $data['major'] }}</td>
                                                <td>
                                                    @if($data['grade'])
                                                        {{ $data['grade'] }} {{ $data['class_name'] }}
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>{{ $data['email'] ?? '-' }}</td>
                                                <td>{{ $data['whatsapp'] ?? '-' }}</td>
                                                <td>{{ $data['birth_date'] ? \Carbon\Carbon::parse($data['birth_date'])->locale('id')->translatedFormat('d F Y') : '-' }}
                                                </td>
                                                <td>
                                                    @if($data['status'] == 'Valid')
                                                        <span class="badge bg-success-subtle text-success">Valid</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger">Invalid</span>
                                                    @endif
                                                </td>
                                                <td class="text-danger small">{{ $data['errors'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer border-top text-end">
                            <button wire:click="cancel" class="btn btn-light waves-effect me-2">Batal</button>

                            @if(count($validData) > 0)
                                <button wire:click="import" class="btn btn-primary waves-effect waves-light"
                                    onclick="confirmImport({{ count($validData) }})">
                                    <span wire:loading.remove wire:target="import">
                                        <i class="fi fi-rr-check me-1"></i> Submit Import
                                    </span>
                                    <span wire:loading wire:target="import">Processing...</span>
                                </button>
                            @else
                                <button class="btn btn-primary disabled" disabled>Tidak Ada Data Valid</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function confirmImport(count) {
            Swal.fire({
                title: 'Konfirmasi Import',
                text: `Yakin ingin mengimport ${count} data siswa?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Import!',
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