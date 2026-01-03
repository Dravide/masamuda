<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="card shadow-sm" style="width: 100%; max-width: 450px;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <img src="{{ asset('template/assets/images/logo.svg') }}" alt="Logo" height="40" class="mb-3">
                <h4 class="card-title">Ganti Password</h4>
                <p class="text-muted">Untuk keamanan, Anda diwajibkan mengganti password saat login pertama kali.</p>
            </div>

            <form wire:submit.prevent="updatePassword">
                <div class="mb-3">
                    <label class="form-label">Password Saat Ini</label>
                    <input type="password" class="form-control @error('current_password') is-invalid @enderror" wire:model="current_password" placeholder="Masukkan password saat ini">
                    @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password" placeholder="Minimal 8 karakter">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" class="form-control" wire:model="password_confirmation" placeholder="Ulangi password baru">
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <span wire:loading.remove>Simpan Password Baru</span>
                        <span wire:loading>Processing...</span>
                    </button>
                    
                    <button type="button" wire:click="$dispatch('logout')" class="btn btn-outline-danger waves-effect">
                        Logout
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
