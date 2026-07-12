<div class="auth-cover-wrapper">
    <div class="row g-0">
        <div class="col-lg-6">
            <div class="auth-cover"
                style="background-image: url({{ asset('template/assets/images/auth/auth-cover-bg.png') }});">
                <div class="clearfix">
                    <img src="{{ asset('template/assets/images/auth/auth.png') }}" alt=""
                        class="img-fluid cover-img ms-5">
                    <div class="auth-content">
                        <h1 class="display-6 fw-bold">Lupa Password?</h1>
                        <p>Masukkan NISN Anda untuk mereset password. Password baru akan dikirim via WhatsApp.</p>
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
                    <h5 class="mb-1">Lupa Password</h5>
                    <p>Masukkan NISN untuk mereset password Anda.</p>
                </div>

                {{-- Step 1: Input NISN --}}
                @if ($step === 1)
                    <form wire:submit.prevent="checkNisn">
                        <div class="mb-4">
                            <label class="form-label" for="nisn">NISN</label>
                            <input type="text" class="form-control @error('nisn') is-invalid @enderror" id="nisn"
                                wire:model="nisn" placeholder="Masukkan NISN Anda" inputmode="numeric">
                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary waves-effect waves-light w-100"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>Lanjutkan</span>
                                <span wire:loading>Memeriksa...</span>
                            </button>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="mb-0"><a href="{{ route('login') }}" class="fw-medium text-primary">Kembali ke Login</a></p>
                        </div>
                    </form>
                @endif

                {{-- Step 2: Konfirmasi & Kirim --}}
                @if ($step === 2)
                    <form wire:submit.prevent="sendNewPassword">
                        <div class="alert alert-info mb-4">
                            <div class="d-flex">
                                <i class="flaticon-information me-2 mt-1"></i>
                                <div>
                                    <strong>{{ $studentName }}</strong><br>
                                    Password baru akan dikirim ke nomor WhatsApp:<br>
                                    <strong>{{ $whatsappMasked }}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary waves-effect waves-light w-100"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>Kirim Password Baru</span>
                                <span wire:loading>Mengirim...</span>
                            </button>
                        </div>
                        <div class="mt-3 text-center">
                            <a href="#" wire:click.prevent="resetForm" class="fw-medium text-muted">Batal</a>
                        </div>
                    </form>
                @endif

                {{-- Step 3: Success --}}
                @if ($step === 3)
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="flaticon-check-mark text-success" style="font-size: 64px;"></i>
                        </div>
                        <h5 class="mb-3">Password Baru Terkirim!</h5>
                        <p class="text-muted mb-4">
                            Password baru telah dikirim ke WhatsApp <strong>{{ $whatsappMasked }}</strong>.
                            Silakan cek pesan WhatsApp Anda.
                        </p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-primary waves-effect waves-light">
                                Kembali ke Login
                            </a>
                            <a href="#" wire:click.prevent="resetForm" class="btn btn-outline-secondary waves-effect waves-light">
                                Reset Lagi
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
