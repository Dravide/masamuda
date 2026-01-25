<div>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Galeri Foto Saya</h4>
            </div>
        </div>
    </div>

    @if(count($groupedPhotos) > 0)
        @foreach($groupedPhotos as $projectName => $photos)
            <div class="row mb-4">
                <div class="col-12">
                    <h5 class="mb-3 text-primary"><i class="fi fi-rr-folder me-2"></i>{{ $projectName }}</h5>
                    <div class="row">
                        @foreach($photos as $photo)
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-img-top-wrapper"
                                        style="height: 200px; overflow: hidden; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                                        <a href="{{ asset('storage/' . $photo['file_path']) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $photo['file_path']) }}" class="card-img-top"
                                                alt="Foto Siswa" style="width: 100%; height: 100%; object-fit: cover;">
                                        </a>
                                    </div>
                                    <div class="card-body p-2 text-center">
                                        <span class="badge bg-secondary mb-1">{{ ucfirst($photo['photo_type']) }}</span>
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $photo['file_path']) }}" download
                                                class="btn btn-sm btn-outline-primary waves-effect waves-light">
                                                <i class="fi fi-rr-download me-1"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="mb-4">
                            <i class="fi fi-rr-picture text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h5>Belum ada foto</h5>
                        <p class="text-muted">Foto project Anda akan muncul di sini setelah diunggah oleh sekolah.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>