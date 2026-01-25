<div class="auth-cover-wrapper">
    <div class="row g-0">
        <div class="col-lg-6">
            <div class="auth-cover"
                style="background-image: url({{ asset('template/assets/images/auth/auth-cover-bg.png') }});">
                <div class="clearfix">
                    <img src="{{ asset('template/assets/images/auth/auth.png') }}" alt=""
                        class="img-fluid cover-img ms-5">
                    <div class="auth-content">
                        <h1 class="display-6 fw-bold">Aktivasi Akun</h1>
                        <p>Aktifkan akun anda untuk mengakses layanan Masamuda.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 align-self-center">
            <div class="p-3 p-sm-5 maxw-450px m-auto auth-inner">
                <div class="mb-4 text-center">
                    <a href="#" aria-label="Logo">
                        @php
                            $logoHeader = \App\Models\Setting::where('key', 'logo_header')->value('value');
                            $defaultLogo = asset('images/logo.jpeg');
                        @endphp
                        <img src="{{ $logoHeader ? asset('storage/' . $logoHeader) : $defaultLogo }}" alt="Logo"
                            style="height: 60px; object-fit: contain;">
                    </a>
                </div>

                <div class="text-center mb-4">
                    <h5 class="mb-1">Aktivasi {{ ucfirst($type) }}</h5>
                    @if($step === 1)
                        <p>Masukkan {{ $type === 'siswa' ? 'NISN' : 'NIP / NIY / NIK' }} untuk memeriksa data.</p>
                    @else
                        <p>Konfirmasi data diri Anda dan buat password.</p>
                    @endif
                </div>

                <!-- Tabs -->
                @if($step === 1)
                    <div class="mb-4">
                        <ul class="nav nav-pills nav-justified bg-light rounded" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ $type === 'siswa' ? 'active' : '' }}" href="#"
                                    wire:click.prevent="$set('type', 'siswa')">
                                    Siswa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $type === 'guru' ? 'active' : '' }}" href="#"
                                    wire:click.prevent="$set('type', 'guru')">
                                    Guru
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div wire:key="step-1-{{ $type }}">
                        <form wire:submit.prevent="checkAccount">
                            <div class="mb-4">
                                <label class="form-label"
                                    for="identifier">{{ $type === 'siswa' ? 'NISN' : 'NIP / NIY / NIK' }}</label>
                                <input type="text" class="form-control @error('identifier') is-invalid @enderror"
                                    id="identifier" wire:model="identifier"
                                    placeholder="Masukkan {{ $type === 'siswa' ? 'NISN' : 'NIP / NIY / NIK' }}" autofocus>
                                @error('identifier') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary waves-effect waves-light w-100"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Lanjut / Cek Data</span>
                                    <span wire:loading>Memeriksa...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                @else
                    <div wire:key="step-2-{{ $type }}">
                        <div class="alert alert-success mb-4 text-center" role="alert">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Data Ditemukan!
                            <div class="mt-2 text-dark">
                                <strong>{{ $name }}</strong><br>
                                <small class="text-muted">{{ $schoolName }}</small>
                            </div>
                        </div>

                        <form wire:submit.prevent="activate">
                            <div class="mb-4">
                                <label class="form-label" for="whatsapp">Nomor WhatsApp</label>
                                <input type="text" class="form-control @error('whatsapp') is-invalid @enderror"
                                    id="whatsapp" wire:model="whatsapp" placeholder="Contoh: 08123456789" autofocus>
                                <div class="form-text">Nomor ini akan digunakan untuk info project.</div>
                                @error('whatsapp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label" for="password">Buat Password Baru</label>
                                <div class="input-group auth-pass-inputgroup">
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" wire:model="password" placeholder="Minimal 6 karakter"
                                        aria-label="Password" aria-describedby="password-addon">
                                    <button class="btn btn-light " type="button" id="password-addon"><i
                                            class="mdi mdi-eye-outline"></i></button>
                                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary waves-effect waves-light w-100"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Aktifkan Akun</span>
                                    <span wire:loading>Memproses...</span>
                                </button>
                            </div>

                            <div class="text-center mt-2">
                                <button type="button" wire:click="resetStep" class="btn btn-link text-muted btn-sm">
                                    <i class="mdi mdi-arrow-left me-1"></i> Bukan Anda? Kembali
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <div class="mt-4 text-center">
                    <p class="mb-0">Sudah punya akun? <a href="{{ route('login') }}"
                            class="fw-medium text-primary">Login Disini</a></p>
                </div>
            </div>
        </div>
    </div>
</div>