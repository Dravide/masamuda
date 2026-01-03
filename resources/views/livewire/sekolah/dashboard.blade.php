<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Sekolah Dashboard</h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    Welcome Sekolah! Manage your school data here.
                </div>
            </div>
        </div>
    </div>

    <!-- Forced Password Change Modal -->
    @if($showPasswordChangeModal)
    <div class="modal fade show" id="forcePasswordChangeModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="forcePasswordChangeModalLabel" aria-hidden="true" style="display: block; background-color: rgba(0,0,0,0.7); z-index: 9999;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 justify-content-center">
                    <div class="text-center">
                        <img src="{{ asset('template/assets/images/logo.svg') }}" alt="Logo" height="40" class="mb-3">
                        <h5 class="modal-title" id="forcePasswordChangeModalLabel">Ganti Password Wajib</h5>
                    </div>
                </div>
                <div class="modal-body pt-2">
                    <p class="text-center text-muted mb-4">
                        Demi keamanan akun Anda, silakan ganti password saat login pertama kali.
                        <br>
                        <small class="text-danger">* Anda tidak dapat menutup modal ini sebelum mengganti password.</small>
                    </p>

                    <form wire:submit.prevent="updatePassword">
                        <div class="mb-3">
                            <label class="form-label">Password Saat Ini</label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" wire:model="current_password" placeholder="Masukkan password saat ini">
                            @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror" wire:model="password" placeholder="Min. 8 karakter, Huruf Besar, Kecil & Angka">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" wire:model="password_confirmation" placeholder="Ulangi password baru">
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <span wire:loading.remove>Simpan Password Baru</span>
                                <span wire:loading>Processing...</span>
                            </button>
                            
                            <!-- Optional Logout Button if they are stuck -->
                            <button type="button" wire:click="$dispatch('logout')" class="btn btn-outline-danger waves-effect" onclick="event.preventDefault(); document.getElementById('logout-form-modal').submit();">
                                Logout
                            </button>
                        </div>
                    </form>
                    
                    <form id="logout-form-modal" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif

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
