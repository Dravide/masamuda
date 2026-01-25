<div class="container">
    <!-- Page Header -->
    <div class="app-page-head d-flex align-items-center justify-content-between mb-4">
        <div class="clearfix">
            <h1 class="app-page-title">Broadcast Pesan</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Broadcast</li>
                </ol>
            </nav>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fi fi-rr-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-0">Form Broadcast</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="send">
                        
                        {{-- Target Selection --}}
                        <div class="mb-4">
                            <label class="form-label d-block mb-3">Target Penerima</label>
                            <div class="d-flex gap-4 flex-wrap">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="target" value="all" id="targetAll">
                                    <label class="form-check-label" for="targetAll">
                                        Semua Siswa
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="target" value="school" id="targetSchool">
                                    <label class="form-check-label" for="targetSchool">
                                        Per Sekolah
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="target" value="project" id="targetProject">
                                    <label class="form-check-label" for="targetProject">
                                        Per Project
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="target" value="student" id="targetStudent">
                                    <label class="form-check-label" for="targetStudent">
                                        Perorangan
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Conditional Inputs --}}
                        @if ($target === 'school')
                            <div class="mb-4">
                                <label class="form-label">Pilih Sekolah</label>
                                <div wire:ignore>
                                    <select x-data x-init="
                                        $($el).select2({
                                            placeholder: '-- Pilih Sekolah --',
                                            width: '100%'
                                        });
                                        $($el).on('change', function (e) {
                                            @this.set('school_id', $(this).val());
                                        });
                                    " id="school-select" class="form-select">
                                        <option value="">-- Pilih Sekolah --</option>
                                        @foreach ($schools as $school)
                                            <option value="{{ $school->id }}">{{ $school->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('school_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        @if ($target === 'project')
                            <div class="mb-4">
                                <label class="form-label">Pilih Project</label>
                                <div wire:ignore>
                                    <select x-data x-init="
                                        $($el).select2({
                                            placeholder: '-- Pilih Project --',
                                            width: '100%'
                                        });
                                        $($el).on('change', function (e) {
                                            @this.set('project_id', $(this).val());
                                        });
                                    " id="project-select" class="form-select">
                                        <option value="">-- Pilih Project --</option>
                                        @foreach ($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->school->name ?? '-' }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                 @error('project_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        @if ($target === 'student')
                            <div class="mb-4">
                                <label class="form-label">Pilih Siswa</label>
                                <div wire:ignore>
                                    <select x-data x-init="
                                        $($el).select2({
                                            placeholder: '-- Pilih Siswa --',
                                            width: '100%'
                                        });
                                        $($el).on('change', function (e) {
                                            @this.set('student_id', $(this).val());
                                        });
                                    " id="student-select" class="form-select">
                                        <option value="">-- Pilih Siswa --</option>
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}">
                                                {{ $student->name }} ({{ $student->school->name ?? 'No School' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('student_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        {{-- Message Type --}}
                        <div class="mb-4">
                            <label class="form-label d-block mb-3">Tipe Pesan</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="type" value="message" id="typeMessage">
                                    <label class="form-check-label" for="typeMessage">
                                        Pesan Manual
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" wire:model.live="type" value="photo_link" id="typePhoto">
                                    <label class="form-check-label" for="typePhoto">
                                        Link Foto (Auto)
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Message Content --}}
                        <div class="mb-4">
                            <label class="form-label">
                                Isi Pesan 
                                @if($type === 'photo_link') <span class="text-muted fw-light">(Otomatis digenerate)</span> @endif
                            </label>
                            <textarea wire:model="message" rows="4" 
                                class="form-control @error('message') is-invalid @enderror"
                                @if($type === 'photo_link') disabled @endif
                                placeholder="{{ $type === 'photo_link' ? 'Halo [Nama], lihat foto kegiatan project kamu disini: [Link]' : 'Tulis pesan anda disini...' }}"></textarea>
                             @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" wire:loading.attr="disabled" class="btn btn-primary waves-effect waves-light">
                                <span wire:loading.remove><i class="fi fi-rr-paper-plane me-2"></i> Kirim Broadcast</span>
                                <span wire:loading>
                                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                    Mengirim...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        .select2-container--default .select2-selection--single {
            background-color: #fff;
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            height: calc(1.5em + 1rem + 2px) !important;
            padding: 0.375rem 2.25rem 0.375rem 0.75rem !important;
            display: flex;
            align-items: center;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5 !important;
            padding-left: 0 !important;
            color: #212529 !important;
            flex-grow: 1;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
            top: 0 !important;
            right: 0.75rem !important;
            width: auto !important;
            display: flex;
            align-items: center;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow b {
            display: none;
        }

        /* Custom Arrow */
        .select2-container--default .select2-selection--single .select2-selection__arrow::after {
            content: '';
            border: solid #6c757d;
            border-width: 0 2px 2px 0;
            display: inline-block;
            padding: 3px;
            transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
        }

        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
            outline: 0;
        }

        .select2-dropdown {
            border: 1px solid #ced4da !important;
            border-radius: 0.375rem !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            z-index: 9999;
        }

        .select2-results__option {
            padding: 0.375rem 0.75rem !important;
        }

        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background-color: #0d6efd !important;
            color: white !important;
        }

        /* Fix placeholder color */
        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #6c757d;
        }
    </style>
@endpush