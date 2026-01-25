<div class="auth-cover-wrapper">
    <div class="row g-0">
        <div class="col-lg-6">
            <div class="auth-cover"
                style="background-image: url({{ asset('template/assets/images/auth/auth-cover-bg.png') }});">
                <div class="clearfix">
                    <img src="{{ asset('template/assets/images/auth/auth.png') }}" alt=""
                        class="img-fluid cover-img ms-5">
                    <div class="auth-content">
                        <h1 class="display-6 fw-bold">Selamat Datang!</h1>
                        <p>Sistem Informasi Masamuda untuk pengelolaan data sekolah dan siswa.</p>
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
                    <h5 class="mb-1">Login ke Masamuda</h5>
                    <p>Masukan username dan password untuk melanjutkan.</p>
                </div>
                <form wire:submit.prevent="login">
                    <div class="mb-4">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" class="form-control @error('username') is-invalid @enderror" id="username"
                            wire:model="username" placeholder="Username">
                        @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                            id="password" wire:model="password" placeholder="********">
                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox" id="rememberMe" wire:model="remember">
                                <label class="form-check-label" for="rememberMe"> Remember Me </label>
                            </div>
                            <a href="#">Lupa Password?</a>
                        </div>
                    </div>
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary waves-effect waves-light w-100"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove>Login</span>
                            <span wire:loading>Loading...</span>
                        </button>
                    </div>
                    <div class="mt-4 text-center">
                        <p class="mb-0">Belum punya akun? <a href="{{ route('activation') }}"
                                class="fw-medium text-primary">Aktivasi Siswa</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>