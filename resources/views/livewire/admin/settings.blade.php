<div>
    <div class="container">
        <!-- Page Title -->
        <div class="app-page-head d-flex flex-wrap gap-3 align-items-center justify-content-between mb-4">
            <div class="clearfix">
                <h1 class="app-page-title">Pengaturan Aplikasi</h1>
                <span class="text-muted">Kelola identitas, logo, dan SEO aplikasi</span>
            </div>
        </div>

        <div class="row">
            <div class="col-12 col-lg-10 mx-auto">
                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs nav-tabs-custom mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button wire:click="setTab('general')" 
                            class="nav-link {{ $activeTab === 'general' ? 'active' : '' }}" type="button">
                            <i class="fi fi-rr-settings me-2"></i>Umum
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="setTab('logo')" 
                            class="nav-link {{ $activeTab === 'logo' ? 'active' : '' }}" type="button">
                            <i class="fi fi-rr-picture me-2"></i>Logo & Favicon
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button wire:click="setTab('seo')" 
                            class="nav-link {{ $activeTab === 'seo' ? 'active' : '' }}" type="button">
                            <i class="fi fi-rr-search me-2"></i>SEO
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- General Tab -->
                    @if($activeTab === 'general')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fi fi-rr-settings me-2"></i>Pengaturan Umum</h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveGeneral">
                                <div class="mb-3">
                                    <label class="form-label">Nama Aplikasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('appName') is-invalid @enderror" 
                                        wire:model.live="appName" maxlength="60">
                                    <div class="form-text">Ditampilkan di title bar dan header. Preview: <strong>{{ $appName }}</strong></div>
                                    @error('appName') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Tagline Aplikasi</label>
                                    <input type="text" class="form-control @error('appTagline') is-invalid @enderror" 
                                        wire:model="appTagline" maxlength="150" placeholder="Deskripsi singkat aplikasi...">
                                    <div class="form-text">Opsional. Muncul di bawah nama aplikasi.</div>
                                    @error('appTagline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <span wire:loading.remove wire:target="saveGeneral">
                                            <i class="fi fi-rr-disk me-1"></i> Simpan
                                        </span>
                                        <span wire:loading wire:target="saveGeneral">Menyimpan...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- Logo Tab -->
                    @if($activeTab === 'logo')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fi fi-rr-picture me-2"></i>Logo & Favicon</h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveLogo">
                                <!-- Favicon -->
                                <div class="mb-4 border-bottom pb-4">
                                    <h6 class="mb-3"><i class="fi fi-rr-browser me-2"></i>Favicon</h6>
                                    <div class="row align-items-center">
                                        <div class="col-md-3 text-center mb-3 mb-md-0">
                                            @if($faviconUpload)
                                                <div class="border p-2 rounded bg-light d-inline-block">
                                                    <img src="{{ $faviconUpload->temporaryUrl() }}" class="img-fluid" style="max-height: 48px; max-width: 48px;">
                                                </div>
                                                <div class="small text-muted mt-1">Preview</div>
                                            @elseif($favicon)
                                                <div class="border p-2 rounded bg-light d-inline-block position-relative">
                                                    <img src="{{ asset('storage/' . $favicon) }}" class="img-fluid" style="max-height: 48px; max-width: 48px;">
                                                    <button type="button" wire:click="deleteLogo('favicon')" class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle rounded-circle p-1" style="width: 24px; height: 24px; line-height: 1;">
                                                        <i class="fi fi-rr-cross-small"></i>
                                                    </button>
                                                </div>
                                                <div class="small text-muted mt-1">Saat Ini</div>
                                            @else
                                                <div class="border p-3 rounded bg-light d-inline-block text-muted">
                                                    <i class="fi fi-rr-browser fs-3"></i>
                                                    <div class="small">Belum ada</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-9">
                                            <input type="file" class="form-control @error('faviconUpload') is-invalid @enderror" wire:model="faviconUpload" accept=".png,.ico,.svg">
                                            <div class="form-text">Rekomendasi: 32x32px atau 48x48px. Format: PNG, ICO, SVG. Maks: 512KB.</div>
                                            @error('faviconUpload') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Logo Header -->
                                <div class="mb-4 border-bottom pb-4">
                                    <h6 class="mb-3"><i class="fi fi-rr-sign-in-alt me-2"></i>Logo Header (Login/Public)</h6>
                                    <div class="row align-items-center">
                                        <div class="col-md-3 text-center mb-3 mb-md-0">
                                            @if($logoHeaderUpload)
                                                <div class="border p-2 rounded bg-light d-inline-block">
                                                    <img src="{{ $logoHeaderUpload->temporaryUrl() }}" class="img-fluid" style="max-height: 80px;">
                                                </div>
                                                <div class="small text-muted mt-1">Preview</div>
                                            @elseif($logoHeader)
                                                <div class="border p-2 rounded bg-light d-inline-block position-relative">
                                                    <img src="{{ asset('storage/' . $logoHeader) }}" class="img-fluid" style="max-height: 80px;">
                                                    <button type="button" wire:click="deleteLogo('logo_header')" class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle rounded-circle p-1" style="width: 24px; height: 24px; line-height: 1;">
                                                        <i class="fi fi-rr-cross-small"></i>
                                                    </button>
                                                </div>
                                                <div class="small text-muted mt-1">Saat Ini</div>
                                            @else
                                                <div class="border p-4 rounded bg-light d-inline-block text-muted">
                                                    <i class="fi fi-rr-picture fs-1"></i>
                                                    <div class="small">Belum ada</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-9">
                                            <input type="file" class="form-control @error('logoHeaderUpload') is-invalid @enderror" wire:model="logoHeaderUpload" accept="image/*">
                                            <div class="form-text">Format: PNG, JPG, SVG, WebP. Maks: 2MB.</div>
                                            @error('logoHeaderUpload') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Logo Sidebar Full -->
                                <div class="mb-4 border-bottom pb-4">
                                    <h6 class="mb-3"><i class="fi fi-rr-menu-burger me-2"></i>Logo Sidebar (Besar)</h6>
                                    <div class="row align-items-center">
                                        <div class="col-md-3 text-center mb-3 mb-md-0">
                                            @if($logoSidebarFullUpload)
                                                <div class="border p-2 rounded bg-dark d-inline-block">
                                                    <img src="{{ $logoSidebarFullUpload->temporaryUrl() }}" class="img-fluid" style="max-height: 50px;">
                                                </div>
                                                <div class="small text-muted mt-1">Preview</div>
                                            @elseif($logoSidebarFull)
                                                <div class="border p-2 rounded bg-dark d-inline-block position-relative">
                                                    <img src="{{ asset('storage/' . $logoSidebarFull) }}" class="img-fluid" style="max-height: 50px;">
                                                    <button type="button" wire:click="deleteLogo('logo_sidebar_full')" class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle rounded-circle p-1" style="width: 24px; height: 24px; line-height: 1;">
                                                        <i class="fi fi-rr-cross-small"></i>
                                                    </button>
                                                </div>
                                                <div class="small text-muted mt-1">Saat Ini</div>
                                            @else
                                                <div class="border p-4 rounded bg-light d-inline-block text-muted">
                                                    <i class="fi fi-rr-picture fs-1"></i>
                                                    <div class="small">Belum ada</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-9">
                                            <input type="file" class="form-control @error('logoSidebarFullUpload') is-invalid @enderror" wire:model="logoSidebarFullUpload" accept="image/*">
                                            <div class="form-text">Rekomendasi: 200x50px. Format: PNG, JPG, SVG. Maks: 2MB.</div>
                                            @error('logoSidebarFullUpload') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Logo Sidebar Small -->
                                <div class="mb-4">
                                    <h6 class="mb-3"><i class="fi fi-rr-apps me-2"></i>Logo Sidebar (Kecil/Icon)</h6>
                                    <div class="row align-items-center">
                                        <div class="col-md-3 text-center mb-3 mb-md-0">
                                            @if($logoSidebarSmallUpload)
                                                <div class="border p-2 rounded bg-dark d-inline-block">
                                                    <img src="{{ $logoSidebarSmallUpload->temporaryUrl() }}" class="img-fluid" style="max-height: 50px; max-width: 50px;">
                                                </div>
                                                <div class="small text-muted mt-1">Preview</div>
                                            @elseif($logoSidebarSmall)
                                                <div class="border p-2 rounded bg-dark d-inline-block position-relative">
                                                    <img src="{{ asset('storage/' . $logoSidebarSmall) }}" class="img-fluid" style="max-height: 50px; max-width: 50px;">
                                                    <button type="button" wire:click="deleteLogo('logo_sidebar_small')" class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle rounded-circle p-1" style="width: 24px; height: 24px; line-height: 1;">
                                                        <i class="fi fi-rr-cross-small"></i>
                                                    </button>
                                                </div>
                                                <div class="small text-muted mt-1">Saat Ini</div>
                                            @else
                                                <div class="border p-4 rounded bg-light d-inline-block text-muted">
                                                    <i class="fi fi-rr-picture fs-1"></i>
                                                    <div class="small">Belum ada</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-9">
                                            <input type="file" class="form-control @error('logoSidebarSmallUpload') is-invalid @enderror" wire:model="logoSidebarSmallUpload" accept="image/*">
                                            <div class="form-text">Rekomendasi: 50x50px. Format: PNG, JPG, SVG. Maks: 2MB.</div>
                                            @error('logoSidebarSmallUpload') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <span wire:loading.remove wire:target="saveLogo, faviconUpload, logoHeaderUpload, logoSidebarFullUpload, logoSidebarSmallUpload">
                                            <i class="fi fi-rr-disk me-1"></i> Simpan
                                        </span>
                                        <span wire:loading wire:target="saveLogo, faviconUpload, logoHeaderUpload, logoSidebarFullUpload, logoSidebarSmallUpload">Menyimpan...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif

                    <!-- SEO Tab -->
                    @if($activeTab === 'seo')
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fi fi-rr-search me-2"></i>Pengaturan SEO</h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="saveSeo">
                                <div class="mb-3">
                                    <label class="form-label">Meta Description</label>
                                    <textarea class="form-control @error('metaDescription') is-invalid @enderror" 
                                        wire:model="metaDescription" rows="3" maxlength="160" 
                                        placeholder="Deskripsi singkat website untuk mesin pencari..."></textarea>
                                    <div class="form-text">Maks 160 karakter. Tampil di hasil pencarian Google.</div>
                                    @error('metaDescription') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control @error('metaKeywords') is-invalid @enderror" 
                                        wire:model="metaKeywords" maxlength="255" 
                                        placeholder="kata kunci, dipisah, dengan koma">
                                    <div class="form-text">Kata kunci yang relevan, dipisah dengan koma.</div>
                                    @error('metaKeywords') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Meta Author</label>
                                    <input type="text" class="form-control @error('metaAuthor') is-invalid @enderror" 
                                        wire:model="metaAuthor" maxlength="100" placeholder="Nama penulis/perusahaan">
                                    <div class="form-text">Nama pembuat atau pemilik website.</div>
                                    @error('metaAuthor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- OG Image -->
                                <div class="mb-4 border-bottom pb-4">
                                    <label class="form-label">Open Graph Image</label>
                                    <div class="row align-items-center">
                                        <div class="col-md-4 text-center mb-3 mb-md-0">
                                            @if($ogImageUpload)
                                                <div class="border p-2 rounded bg-light d-inline-block">
                                                    <img src="{{ $ogImageUpload->temporaryUrl() }}" class="img-fluid" style="max-height: 100px;">
                                                </div>
                                                <div class="small text-muted mt-1">Preview</div>
                                            @elseif($ogImage)
                                                <div class="border p-2 rounded bg-light d-inline-block position-relative">
                                                    <img src="{{ asset('storage/' . $ogImage) }}" class="img-fluid" style="max-height: 100px;">
                                                    <button type="button" wire:click="deleteLogo('og_image')" class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle rounded-circle p-1" style="width: 24px; height: 24px; line-height: 1;">
                                                        <i class="fi fi-rr-cross-small"></i>
                                                    </button>
                                                </div>
                                                <div class="small text-muted mt-1">Saat Ini</div>
                                            @else
                                                <div class="border p-3 rounded bg-light d-inline-block text-muted">
                                                    <i class="fi fi-rr-share fs-3"></i>
                                                    <div class="small">Belum ada</div>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="col-md-8">
                                            <input type="file" class="form-control @error('ogImageUpload') is-invalid @enderror" wire:model="ogImageUpload" accept="image/*">
                                            <div class="form-text">Gambar untuk share di sosial media. Rekomendasi: 1200x630px.</div>
                                            @error('ogImageUpload') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label">Google Analytics ID</label>
                                    <input type="text" class="form-control @error('googleAnalyticsId') is-invalid @enderror" 
                                        wire:model="googleAnalyticsId" maxlength="50" placeholder="UA-XXXXX-X atau G-XXXXXXX">
                                    <div class="form-text">ID tracking Google Analytics untuk monitoring traffic.</div>
                                    @error('googleAnalyticsId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <span wire:loading.remove wire:target="saveSeo, ogImageUpload">
                                            <i class="fi fi-rr-disk me-1"></i> Simpan
                                        </span>
                                        <span wire:loading wire:target="saveSeo, ogImageUpload">Menyimpan...</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
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
