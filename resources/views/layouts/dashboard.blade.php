<!DOCTYPE html>
<html lang="en">

<head>
  @php
    $settings = \App\Models\Setting::pluck('value', 'key');
    $appName = $settings['app_name'] ?? config('app.name');
    $favicon = $settings['favicon'] ?? null;
    $metaDescription = $settings['meta_description'] ?? '';
    $metaKeywords = $settings['meta_keywords'] ?? '';
    $metaAuthor = $settings['meta_author'] ?? '';
    $ogImage = $settings['og_image'] ?? null;
  @endphp

  <meta charset="utf-8">
  <meta name="theme-color" content="#316AFF">
  <meta name="robots" content="index, follow">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  @if($metaDescription)
    <meta name="description" content="{{ $metaDescription }}">
  @endif
  @if($metaKeywords)
    <meta name="keywords" content="{{ $metaKeywords }}">
  @endif
  @if($metaAuthor)
    <meta name="author" content="{{ $metaAuthor }}">
  @endif

  <!-- Open Graph -->
  <meta property="og:title" content="@yield('title', 'Dashboard') — {{ $appName }}">
  @if($metaDescription)
    <meta property="og:description" content="{{ $metaDescription }}">
  @endif
  @if($ogImage)
    <meta property="og:image" content="{{ asset('storage/' . $ogImage) }}">
  @endif
  <meta property="og:type" content="website">

  <title>@yield('title', 'Dashboard') — {{ $appName }}</title>

  <!-- Favicon -->
  @if($favicon)
    <link rel="icon" href="{{ asset('storage/' . $favicon) }}">
  @else
    <link rel="icon" type="image/png" href="{{ asset('template/assets/images/favicon.png') }}">
  @endif

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
    rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('template/assets/libs/flaticon/css/all/all.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/libs/lucide/lucide.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/libs/fontawesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/libs/simplebar/simplebar.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/libs/node-waves/waves.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/libs/bootstrap-select/css/bootstrap-select.min.css') }}">

  <!-- Extra CSS from index.html -->
  <link rel="stylesheet" href="{{ asset('template/assets/libs/flatpickr/flatpickr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/libs/datatables/datatables.min.css') }}">

  <link rel="stylesheet" href="{{ asset('template/assets/css/styles.css') }}">

  @livewireStyles
</head>

<body>
  <div class="page-layout">

    @include('layouts.partials.header')
    @include('layouts.partials.sidebar')

    <!-- begin::GXON Sidebar right -->
    <div class="app-sidebar-end">
      <ul class="sidebar-list">
        <li>
          <a href="#">
            <div class="avatar avatar-sm bg-warning shadow-sharp-warning rounded-circle text-white mx-auto mb-2">
              <i class="fi fi-rr-to-do"></i>
            </div>
            <span class="text-dark">Task</span>
          </a>
        </li>
        <li>
          <a href="#">
            <div class="avatar avatar-sm bg-secondary shadow-sharp-secondary rounded-circle text-white mx-auto mb-2">
              <i class="fi fi-rr-interrogation"></i>
            </div>
            <span class="text-dark">Help</span>
          </a>
        </li>
        <li>
          <a href="#">
            <div class="avatar avatar-sm bg-info shadow-sharp-info rounded-circle text-white mx-auto mb-2">
              <i class="fi fi-rr-calendar"></i>
            </div>
            <span class="text-dark">Event</span>
          </a>
        </li>
        <li>
          <a href="#">
            <div class="avatar avatar-sm bg-gray shadow-sharp-gray rounded-circle text-white mx-auto mb-2">
              <i class="fi fi-rr-settings"></i>
            </div>
            <span class="text-dark">Settings</span>
          </a>
        </li>
      </ul>
    </div>
    <!-- end::GXON Sidebar right -->

    <main class="app-wrapper">
      {{ $slot }}
    </main>

    @include('layouts.partials.footer')

  </div>

  <!-- Scripts -->
  <script src="{{ asset('template/assets/libs/global/global.min.js') }}"></script>
  <script src="{{ asset('template/assets/libs/sortable/Sortable.min.js') }}"></script>
  <script src="{{ asset('template/assets/libs/chartjs/chart.js') }}"></script>
  <script src="{{ asset('template/assets/libs/flatpickr/flatpickr.min.js') }}"></script>
  <script src="{{ asset('template/assets/libs/apexcharts/apexcharts.min.js') }}"></script>
  <script src="{{ asset('template/assets/libs/datatables/datatables.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- These might need to be conditional or moved to specific views if they run specific logic -->
  <script src="{{ asset('template/assets/js/dashboard.js') }}"></script>
  <script src="{{ asset('template/assets/js/todolist.js') }}"></script>

  <script src="{{ asset('template/assets/js/appSettings.js') }}"></script>
  <script src="{{ asset('template/assets/js/main.js') }}"></script>

  @livewireScripts
  @stack('scripts')
</body>

</html>