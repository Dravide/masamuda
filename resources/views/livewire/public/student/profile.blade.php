@section('meta_description', 'Profil ' . $student->name . ' - Siswa ' . ($student->school->name ?? 'Sekolah') . ' | Kelas ' . $student->grade . ' ' . $student->class_name)

@if($photos->count() > 0)
@section('og_image', asset('storage/' . $photos->first()->file_path))
@endif

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Profile Card -->
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <div class="avatar avatar-xl bg-white text-primary rounded-circle mx-auto mb-3"
                        style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                        <i class="fi fi-rr-user fs-1"></i>
                    </div>
                    <h3 class="mb-0">{{ $student->name }}</h3>
                    <p class="mb-0 opacity-75">{{ $student->school->name ?? 'Sekolah' }}</p>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <!-- NIS -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fi fi-rr-id-badge fs-4 text-primary me-3"></i>
                                <div>
                                    <small class="text-muted d-block">NIS</small>
                                    <strong>{{ $student->nis }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- NISN -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fi fi-rr-id-card-clip-alt fs-4 text-primary me-3"></i>
                                <div>
                                    <small class="text-muted d-block">NISN</small>
                                    <strong>{{ $student->nisn ?? '-' }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Kelas -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <i class="fi fi-rr-school fs-4 text-primary me-3"></i>
                                <div>
                                    <small class="text-muted d-block">Kelas</small>
                                    <strong>{{ $student->grade }} {{ $student->class_name }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Jurusan -->
                        @if($student->major)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fi fi-rr-graduation-cap fs-4 text-primary me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Jurusan</small>
                                        <strong>{{ $student->major }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Tanggal Lahir -->
                        @if($student->birth_date)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fi fi-rr-cake-birthday fs-4 text-primary me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Tanggal Lahir</small>
                                        <strong>{{ $student->birth_date->format('d F Y') }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Email -->
                        @if($student->email)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fi fi-rr-envelope fs-4 text-primary me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Email</small>
                                        <strong>{{ $student->email }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- WhatsApp -->
                        @if($student->whatsapp)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <i class="fi fi-brands-whatsapp fs-4 text-success me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">WhatsApp</small>
                                        <strong>{{ $student->whatsapp }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Alamat -->
                        @if($student->address)
                            <div class="col-12">
                                <div class="d-flex align-items-start p-3 bg-light rounded">
                                    <i class="fi fi-rr-marker fs-4 text-primary me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">Alamat</small>
                                        <strong>{{ $student->address }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Project Info -->
                    @if($student->project)
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="text-muted mb-3">Informasi Project</h6>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-info-subtle text-info me-2">{{ $student->project->type }}</span>
                                <span
                                    class="badge bg-secondary">{{ $student->project->academicYear->year_name ?? '-' }}</span>
                                <span class="ms-2 text-muted">{{ $student->project->name }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Photos Section -->
                    @if($photos->count() > 0)
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="text-muted mb-3"><i class="fi fi-rr-picture me-2"></i>Foto Anda</h6>
                            <div class="row g-3">
                                @foreach($photos as $photo)
                                    <div class="col-6 col-md-4">
                                        <div class="card h-100 border shadow-sm">
                                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center p-2"
                                                style="height: 180px;">
                                                <img src="{{ asset('storage/' . $photo->file_path) }}"
                                                    alt="{{ $photo->photo_type }}" class="img-fluid rounded"
                                                    style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                            </div>
                                            <div class="card-footer bg-white text-center py-2">
                                                <span
                                                    class="badge bg-info-subtle text-info mb-2 d-block">{{ $photo->photo_type }}</span>
                                                <a href="{{ asset('storage/' . $photo->file_path) }}"
                                                    download="{{ $student->nis }}_{{ $photo->photo_type }}.{{ pathinfo($photo->file_path, PATHINFO_EXTENSION) }}"
                                                    class="btn btn-sm btn-success w-100">
                                                    <i class="fi fi-rr-download me-1"></i> Unduh
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-light text-center py-3">
                    <small class="text-muted">
                        <i class="fi fi-rr-shield-check me-1"></i>
                        Data ini bersifat rahasia dan hanya dapat diakses melalui link khusus.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>