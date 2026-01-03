<div>
    <div class="container">
        <!-- Breadcrumb -->
        <div class="row mb-3">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('sekolah.dashboard') }}" class="text-primary">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Input Data Siswa</li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Page Title -->
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
            <div class="clearfix">
                <h1 class="app-page-title">Input Data Siswa Baru</h1>
                <span class="text-muted">Formulir pendaftaran siswa baru</span>
            </div>
        </div>

        <!-- Form Card -->
        <div class="row">
            <div class="col-12 col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-header border-bottom">
                        <h5 class="card-title mb-0">Form Data Siswa</h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="save">
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

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" wire:click="resetForm" class="btn btn-light waves-effect">
                                    <i class="fi fi-rr-refresh me-1"></i> Reset
                                </button>
                                <button type="submit" class="btn btn-primary waves-effect waves-light">
                                    <span wire:loading.remove>
                                        <i class="fi fi-rr-disk me-1"></i> Simpan Data
                                    </span>
                                    <span wire:loading>Processing...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    </script>
</div>
