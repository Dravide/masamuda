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
                                <select wire:model="school_id" class="form-select @error('school_id') is-invalid @enderror">
                                    <option value="">-- Pilih Sekolah --</option>
                                    @foreach ($schools as $school)
                                        <option value="{{ $school->id }}">{{ $school->name }}</option>
                                    @endforeach
                                </select>
                                @error('school_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        @if ($target === 'project')
                            <div class="mb-4">
                                <label class="form-label">Pilih Project</label>
                                <select wire:model="project_id" class="form-select @error('project_id') is-invalid @enderror">
                                    <option value="">-- Pilih Project --</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->school->name ?? '-' }})</option>
                                    @endforeach
                                </select>
                                 @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        @endif

                        @if ($target === 'student')
                            <div class="mb-4">
                                <label class="form-label">Pilih Siswa</label>
                                {{-- Simple Select for now --}}
                                <select wire:model="student_id" class="form-select @error('student_id') is-invalid @enderror">
                                    <option value="">-- Pilih Siswa --</option>
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}">{{ $student->name }} - {{ $student->school->name ?? '' }}</option>
                                    @endforeach
                                </select>
                                 @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <div class="form-text">*Menampilkan 100 siswa pertama</div>
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