<aside class="app-menubar" id="appMenubar">
    <div class="app-navbar-brand">
        @php
            $logoFull = \App\Models\Setting::where('key', 'logo_sidebar_full')->value('value');
            $logoSmall = \App\Models\Setting::where('key', 'logo_sidebar_small')->value('value');
            $defaultLogo = asset('images/logo.jpeg');
        @endphp
        <a class="navbar-brand-logo" href="{{ route(Auth::user()->role . '.dashboard') }}">
            <img src="{{ $logoFull ? asset('storage/' . $logoFull) : $defaultLogo }}" alt="Logo"
                style="height: 40px; object-fit: contain;">
        </a>
        <a class="navbar-brand-mini visible-light" href="{{ route(Auth::user()->role . '.dashboard') }}">
            <img src="{{ $logoSmall ? asset('storage/' . $logoSmall) : $defaultLogo }}" alt="Logo"
                style="height: 40px; object-fit: contain;">
        </a>
        <a class="navbar-brand-mini visible-dark" href="{{ route(Auth::user()->role . '.dashboard') }}">
            <img src="{{ $logoSmall ? asset('storage/' . $logoSmall) : $defaultLogo }}" alt="Logo"
                style="height: 40px; object-fit: contain;">
        </a>
    </div>
    <nav class="app-navbar" data-simplebar>
        <ul class="menubar">

            <!-- Dashboard (All Roles) -->
            <li class="menu-item {{ request()->routeIs('*.dashboard') ? 'active' : '' }}">
                <a class="menu-link" href="{{ route(Auth::user()->role . '.dashboard') }}">
                    <i class="fi fi-rr-apps"></i>
                    <span class="menu-label">Dashboard</span>
                </a>
            </li>

            <!-- Admin Menu -->
            @if(Auth::user()->hasRole('admin'))
                <li class="menu-heading">
                    <span class="menu-label">Master Data</span>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.tahun-pelajaran.index') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.tahun-pelajaran.index') }}">
                        <i class="fi fi-rr-calendar"></i>
                        <span class="menu-label">Tahun Pelajaran</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.jurusan.index') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.jurusan.index') }}">
                        <i class="fi fi-rr-graduation-cap"></i>
                        <span class="menu-label">Data Jurusan</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.sekolah.index') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.sekolah.index') }}">
                        <i class="fi fi-rr-school"></i>
                        <span class="menu-label">Data Sekolah</span>
                    </a>
                </li>

                <li class="menu-heading">
                    <span class="menu-label">Data</span>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.project.*') ? 'active' : '' }}">
                    <a class="menu-link {{ request()->routeIs('admin.project.*') ? 'active' : '' }}"
                        href="{{ route('admin.project.index') }}">
                        <i class="fi fi-rr-folder"></i>
                        <span class="menu-label">Data Project</span>
                    </a>
                </li>

                <li class="menu-heading">
                    <span class="menu-label">Komunikasi</span>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.broadcast.*') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.broadcast.index') }}">
                        <i class="fi fi-rr-envelope"></i>
                        <span class="menu-label">Broadcast</span>
                    </a>
                </li>

                <li class="menu-heading">
                    <span class="menu-label">Sistem</span>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.pengguna.index') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.pengguna.index') }}">
                        <i class="fi fi-rr-users"></i>
                        <span class="menu-label">User Management</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('admin.settings') }}">
                        <i class="fi fi-rr-settings"></i>
                        <span class="menu-label">Pengaturan</span>
                    </a>
                </li>
            @endif

            <!-- Sekolah Menu -->
            @if(Auth::user()->hasRole('sekolah'))
                <li class="menu-heading">
                    <span class="menu-label">Data</span>
                </li>
                <li
                    class="menu-item {{ request()->routeIs('sekolah.project.index', 'sekolah.project.data', 'sekolah.project.data.import') ? 'active' : '' }}">
                    <a class="menu-link {{ request()->routeIs('sekolah.project.index', 'sekolah.project.data', 'sekolah.project.data.import') ? 'active' : '' }}"
                        href="{{ route('sekolah.project.index') }}">
                        <i class="fi fi-rr-folder"></i>
                        <span class="menu-label">Project</span>
                    </a>
                </li>
                <li class="menu-item {{ request()->routeIs('sekolah.guru.index') ? 'active' : '' }}">
                    <a class="menu-link" href="{{ route('sekolah.guru.index') }}">
                        <i class="fi fi-rr-users-alt"></i>
                        <span class="menu-label">Data Guru</span>
                    </a>
                </li>

                <li class="menu-heading">
                    <span class="menu-label">Unduhan</span>
                </li>
                <li class="menu-item {{ request()->routeIs('sekolah.foto.*') ? 'active' : '' }}">
                    <a class="menu-link {{ request()->routeIs('sekolah.foto.*') ? 'active' : '' }}"
                        href="{{ route('sekolah.foto.index') }}">
                        <i class="fi fi-rr-picture"></i>
                        <span class="menu-label">Unduh Foto</span>
                    </a>
                </li>
            @endif

            <!-- Guru Menu -->
            @if(Auth::user()->hasRole('guru'))
                <li class="menu-heading">
                    <span class="menu-label">Menu Guru</span>
                </li>
                <li class="menu-item {{ request()->routeIs('guru.project.*') ? 'active' : '' }}">
                    <a class="menu-link {{ request()->routeIs('guru.project.*') ? 'active' : '' }}"
                        href="{{ route('guru.project.index') }}">
                        <i class="fi fi-rr-folder"></i>
                        <span class="menu-label">Project</span>
                    </a>
                </li>
            @endif



        </ul>
    </nav>
    <div class="app-footer">
        <a href="#" class="btn btn-outline-light waves-effect btn-shadow btn-app-nav w-100">
            <i class="fi fi-rs-interrogation text-primary"></i>
            <span class="nav-text">Help and Support</span>
        </a>
    </div>
</aside>