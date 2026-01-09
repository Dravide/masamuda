<header class="app-header">
    <div class="app-header-inner">
        <button class="app-toggler" type="button" aria-label="app toggler">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="app-header-start d-none d-md-flex">
            <!-- Search removed for simplicity or add later -->
        </div>
        <div class="app-header-end">
            <div class="px-lg-3 px-2 ps-0 d-flex align-items-center">
                <!-- Theme Toggler -->
                <div class="dropdown">
                    <button
                        class="btn btn-icon btn-action-gray rounded-circle waves-effect waves-light position-relative"
                        id="ld-theme" type="button" data-bs-auto-close="outside" aria-expanded="false"
                        data-bs-toggle="dropdown">
                        <i class="fi fi-rr-brightness scale-1x theme-icon-active"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <button type="button" class="dropdown-item d-flex gap-2 align-items-center"
                                data-bs-theme-value="light" aria-pressed="false">
                                <i class="fi fi-rr-brightness scale-1x" data-theme="light"></i> Light
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex gap-2 align-items-center"
                                data-bs-theme-value="dark" aria-pressed="false">
                                <i class="fi fi-rr-moon scale-1x" data-theme="dark"></i> Dark
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item d-flex gap-2 align-items-center"
                                data-bs-theme-value="auto" aria-pressed="true">
                                <i class="fi fi-br-circle-half-stroke scale-1x" data-theme="auto"></i> Auto
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="vr my-3"></div>
            <div class="dropdown text-end ms-sm-3 ms-2 ms-lg-4">
                @php
                    $userSchool = null;
                    if (Auth::user()->role === 'sekolah') {
                        $userSchool = \App\Models\School::where('user_id', Auth::id())->first();
                    }
                @endphp
                <a href="#" class="d-flex align-items-center py-2" data-bs-toggle="dropdown"
                    data-bs-auto-close="outside" aria-expanded="true">
                    <div class="text-end me-2 d-none d-lg-inline-block">
                        <div class="fw-bold text-dark">{{ $userSchool ? $userSchool->name : Auth::user()->name }}</div>
                        <small class="text-body d-block lh-sm">
                            <i class="fi fi-rr-angle-down text-3xs me-1"></i> {{ ucfirst(Auth::user()->role) }}
                        </small>
                    </div>
                    @if($userSchool && $userSchool->logo)
                        <div class="avatar avatar-sm rounded-circle overflow-hidden">
                            <img src="{{ asset('storage/' . $userSchool->logo) }}" alt="{{ $userSchool->name }}" style="object-fit: cover;">
                        </div>
                    @else
                        <div class="avatar avatar-sm rounded-circle avatar-status-success">
                            <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('template/assets/images/avatar/avatar1.webp') }}"
                                alt="">
                        </div>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end w-225px mt-1">
                    <li>
                        <div class="d-flex align-items-center p-2">
                            <div class="avatar avatar-sm rounded-circle">
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : asset('template/assets/images/avatar/avatar1.webp') }}"
                                    alt="">
                            </div>
                            <div class="ms-2">
                                <div class="fw-bold text-dark">{{ Auth::user()->name }}</div>
                                <small class="text-body d-block lh-sm">{{ Auth::user()->username }}</small>
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="dropdown-divider my-1"></div>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="dropdown-item d-flex align-items-center gap-2 text-danger" type="submit">
                                <i class="fi fi-sr-exit scale-1x"></i> Log Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>