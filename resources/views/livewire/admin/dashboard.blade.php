<div class="container">

  <!-- Page Header -->
  <div class="app-page-head d-flex align-items-center justify-content-between">
    <div class="clearfix">
      <h1 class="app-page-title">Dashboard</h1>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
      </nav>
    </div>
    @if($activeAcademicYear)
      <span class="badge bg-primary-subtle text-primary fs-6 py-2 px-3">
        <i class="fi fi-rr-calendar me-1"></i> {{ $activeAcademicYear->year_name }}
        ({{ $activeAcademicYear->semester == 'ganjil' ? 'Ganjil' : 'Genap' }})
      </span>
    @endif
  </div>

  <div class="row">

    <!-- Welcome Card -->
    <div class="col-xl-6">
      <div class="card bg-warning bg-opacity-25 shadow-none border-0">
        <div class="card-body px-4 pb-0 pt-2">
          <div class="row g-0">
            <div class="col-sm-7 py-3 px-2">
              <h6 class="card-title fw-bold mb-2">Selamat Datang, {{ Auth::user()->name }}!</h6>
              <h2 class="text-secondary fs-1 fw-bolder mb-3">{{ number_format($totalProjects) }} Project</h2>
              <p class="text-dark fw-semibold mb-0">
                Saat ini terdapat <strong class="text-primary">{{ number_format($activeProjects) }} project
                  aktif</strong>
                dengan total <strong class="text-primary">{{ number_format($totalStudents) }} siswa</strong> terdaftar
                di sistem.
              </p>
            </div>
            <div class="col-sm-5 text-center text-sm-end align-self-end">
              <img src="{{ asset('template/assets/images/media/svg/media2.svg') }}" class="img-fluid" alt=""
                style="max-height: 150px;">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Project Status Summary -->
    <div class="col-xl-6">
      <div class="card bg-info bg-opacity-25 shadow-none border-0">
        <div class="card-body px-4 py-2">
          <div class="row g-0 align-items-center">
            <div class="col-md-5 py-3 px-2">
              <h6 class="card-title fw-bold mb-2">Status Project</h6>
              <p class="text-dark mb-4">
                <strong
                  class="text-info">{{ $totalProjects > 0 ? round($activeProjects / $totalProjects * 100) : 0 }}%</strong>
                project sedang aktif dan dapat menerima input data siswa.
              </p>
              <a href="{{ route('admin.project.index') }}" class="btn btn-info waves-effect waves-light">
                <i class="fi fi-rr-folder me-1"></i> Lihat Project
              </a>
            </div>
            <div class="col-md-7 text-center py-3">
              <div class="row g-2">
                <div class="col-4">
                  <div class="bg-white rounded-3 p-3">
                    <h3 class="mb-0 fw-bold text-secondary">{{ $draftProjects }}</h3>
                    <small class="text-muted">Draft</small>
                  </div>
                </div>
                <div class="col-4">
                  <div class="bg-white rounded-3 p-3">
                    <h3 class="mb-0 fw-bold text-success">{{ $activeProjects }}</h3>
                    <small class="text-muted">Active</small>
                  </div>
                </div>
                <div class="col-4">
                  <div class="bg-white rounded-3 p-3">
                    <h3 class="mb-0 fw-bold text-info">{{ $completedProjects }}</h3>
                    <small class="text-muted">Completed</small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="col-xxl-12">
      <div class="row">
        <div class="col-6 col-md-4 col-lg">
          <div class="card bg-primary bg-opacity-05 shadow-none border-0">
            <div class="card-body">
              <div class="avatar bg-primary shadow-primary rounded-circle text-white mb-3">
                <i class="fi fi-sr-folder"></i>
              </div>
              <h3>{{ number_format($totalProjects) }}</h3>
              <h6 class="mb-0">Total Project</h6>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
          <div class="card bg-success bg-opacity-05 shadow-none border-0">
            <div class="card-body">
              <div class="avatar bg-success shadow-success rounded-circle text-white mb-3">
                <i class="fi fi-sr-users"></i>
              </div>
              <h3>{{ number_format($totalStudents) }}</h3>
              <h6 class="mb-0">Total Siswa</h6>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-4 col-lg">
          <div class="card bg-warning bg-opacity-05 shadow-none border-0">
            <div class="card-body">
              <div class="avatar bg-warning shadow-warning rounded-circle text-white mb-3">
                <i class="fi fi-sr-school"></i>
              </div>
              <h3>{{ number_format($totalSchools) }}</h3>
              <h6 class="mb-0">Total Sekolah</h6>
            </div>
          </div>
        </div>
        <div class="col-6 col-md-6 col-lg">
          <div class="card bg-info bg-opacity-05 shadow-none border-0">
            <div class="card-body">
              <div class="avatar bg-info shadow-info rounded-circle text-white mb-3">
                <i class="fi fi-sr-picture"></i>
              </div>
              <h3>{{ number_format($totalPhotos) }}</h3>
              <h6 class="mb-0">Total Foto</h6>
            </div>
          </div>
        </div>
        <div class="col-12 col-md-6 col-lg">
          <div class="card bg-secondary bg-opacity-05 shadow-none border-0">
            <div class="card-body">
              <div class="avatar bg-secondary shadow-secondary rounded-circle text-white mb-3">
                <i class="fi fi-sr-badge-check"></i>
              </div>
              <h3>{{ number_format($activeProjects) }}</h3>
              <h6 class="mb-0">Project Aktif</h6>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Projects -->
    <div class="col-xxl-8 col-lg-7">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
          <h6 class="card-title mb-0">Project Terbaru</h6>
          <a href="{{ route('admin.project.index') }}" class="btn btn-sm btn-outline-light btn-shadow waves-effect">
            Lihat Semua
          </a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Project</th>
                  <th>Sekolah</th>
                  <th class="text-center">Siswa</th>
                  <th>Status</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentProjects as $project)
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-2">
                        <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle">
                          <i class="fi fi-rr-folder"></i>
                        </div>
                        <div>
                          <h6 class="mb-0">{{ Str::limit($project->name, 25) }}</h6>
                          <small class="text-muted">{{ $project->type }}</small>
                        </div>
                      </div>
                    </td>
                    <td>
                      <span class="d-inline-block text-truncate" style="max-width: 150px;">
                        {{ $project->school->name ?? '-' }}
                      </span>
                    </td>
                    <td class="text-center">
                      <span class="badge bg-info-subtle text-info">{{ $project->students_count }}</span>
                    </td>
                    <td>
                      @php
                        $statusClass = match ($project->status) {
                          'draft' => 'secondary',
                          'active' => 'success',
                          'completed' => 'info',
                          default => 'secondary'
                        };
                      @endphp
                      <span class="badge bg-{{ $statusClass }}">{{ ucfirst($project->status) }}</span>
                    </td>
                    <td class="text-end">
                      <a href="{{ route('admin.project.show', $project->id) }}"
                        class="btn btn-sm btn-icon btn-outline-primary waves-effect">
                        <i class="fi fi-rr-eye"></i>
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                      <i class="fi fi-rr-folder fs-1 d-block mb-2"></i>
                      Belum ada project
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Top Schools -->
    <div class="col-xxl-4 col-lg-5">
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between border-0 pb-0">
          <h6 class="card-title mb-0">Top Sekolah</h6>
          <div class="btn-group">
            <button class="btn btn-white btn-sm btn-shadow btn-icon waves-effect dropdown-toggle" type="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fi fi-rr-menu-dots"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="javascript:void(0);">By Students</a>
              </li>
            </ul>
          </div>
        </div>
        <div class="card-body p-0">
          <ul class="list-group list-group-flush">
            @forelse($topSchools as $index => $school)
              <li class="list-group-item d-flex justify-content-between align-items-center px-4 py-3">
                <div class="d-flex align-items-center gap-3">
                  @if($index < 3)
                    <span
                      class="avatar avatar-sm bg-{{ $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'danger') }} rounded-circle text-white fw-bold">
                      {{ $index + 1 }}
                    </span>
                  @else
                    <span class="avatar avatar-sm bg-light text-muted rounded-circle fw-bold">
                      {{ $index + 1 }}
                    </span>
                  @endif
                  <div>
                    <h6 class="mb-0">{{ Str::limit($school->name, 22) }}</h6>
                  </div>
                </div>
                <span class="badge bg-success-subtle text-success rounded-pill px-3">
                  {{ number_format($school->students_count) }} siswa
                </span>
              </li>
            @empty
              <li class="list-group-item text-center py-4 text-muted">
                <i class="fi fi-rr-school fs-1 d-block mb-2"></i>
                Belum ada data sekolah
              </li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

  </div>

</div>