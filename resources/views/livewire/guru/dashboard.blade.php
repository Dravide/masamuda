@section('title', 'Dashboard Guru')

<div>
    <div class="mb-4">
        <h3>Selamat Datang, {{ Auth::user()->name }}</h3>
        <p class="text-muted">Guru di {{ $schoolName }}</p>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fi fi-rr-folder fs-1 text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title">Total Project</h5>
                            <h2 class="mb-0">{{ $projectsCount }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>