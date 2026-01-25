<div class="auth-cover-wrapper">
    <div class="row g-0">
        <div class="col-lg-6">
            <div class="auth-cover"
                style="background-image: url({{ asset('template/assets/images/auth/auth-cover-bg.png') }});">
                <div class="clearfix">
                    <img src="{{ asset('template/assets/images/auth/auth.png') }}" alt=""
                        class="img-fluid cover-img ms-5">
                    <div class="auth-content">
                        <h1 class="display-6 fw-bold">Aktivasi Siswa</h1>
                        <p>Aktifkan akun siswa anda untuk mengakses layanan Masamuda dan terhubung dengan project.</p>
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
                <div class="text-center mb-5">
                    <h5 class="mb-1">Aktivasi Akun</h5>
                    @if($step === 1)
                        <p>Masukkan NISN untuk memeriksa data siswa Anda.</p>
                    @else
                        <p>Konfirmasi data diri Anda dan buat akun.</p>
                    @endif
                </div>

                @if($step === 1)
                    <div wire:key="step-1">
                        <form wire:submit.prevent="checkNisn">
                            <div class="mb-4">
                                <label class="form-label" for="nisn">NISN</label>
                                <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn"
                                    wire:model="nisn" placeholder="Masukkan NISN" autofocus>
                                @error('nisn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary waves-effect waves-light w-100"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove>Lanjut / Cek Data</span>
                                    <span wire:loading>Memeriksa...</span>
                                </button>
                            </div>
                            <div class="mt-4 text-center">
                                <p class="mb-0">Sudah punya akun? <a href="{{ route('login') }}"
                                        class="fw-medium text-primary">Login Disini</a></p>
                            </div>
                        </form>
                    </div>
                @else
                    <div wire:key="step-2">
                        <div class="alert alert-success mb-4 text-center" role="alert">
                            <i class="mdi mdi-check-circle-outline me-1"></i> Data Ditemukan!
                            <div class="mt-2 text-dark">
                                <strong>{{ $studentName }}</strong><br>
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
                            <div class="mt-4 text-center">
                                <p class="mb-0">Sudah punya akun? <a href="{{ route('login') }}"
                                        class="fw-medium text-primary">Login Disini</a></p>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>