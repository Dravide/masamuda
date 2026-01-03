<!DOCTYPE html>
<html lang="en">

<head>
  @php
    $settings = \App\Models\Setting::pluck('value', 'key');
    $appName = $settings['app_name'] ?? config('app.name');
    $favicon = $settings['favicon'] ?? null;
    $defaultDescription = $settings['meta_description'] ?? '';
    $metaAuthor = $settings['meta_author'] ?? '';
  @endphp

  <meta charset="utf-8">
  <meta name="theme-color" content="#316AFF">
  <meta name="robots" content="noindex, nofollow">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Dynamic SEO Meta Tags -->
  @hasSection('meta_description')
    <meta name="description" content="@yield('meta_description')">
  @elseif($defaultDescription)
    <meta name="description" content="{{ $defaultDescription }}">
  @endif

  @if($metaAuthor)
    <meta name="author" content="{{ $metaAuthor }}">
  @endif

  <!-- Open Graph -->
  <meta property="og:title" content="{{ $title ?? $appName }}">
  @hasSection('meta_description')
    <meta property="og:description" content="@yield('meta_description')">
  @endif
  @hasSection('og_image')
    <meta property="og:image" content="@yield('og_image')">
  @endif
  <meta property="og:type" content="profile">

  <title>{{ $title ?? $appName }}</title>

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
  <link rel="stylesheet" href="{{ asset('template/assets/libs/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/css/styles.css') }}">

  @livewireStyles
</head>

<body class="bg-light">

  <main class="d-flex align-items-center min-vh-100 py-3 py-md-0">
    <div class="container">
      {{ $slot }}
    </div>
  </main>

  <!-- Scripts -->
  <script src="{{ asset('template/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('template/assets/js/main.js') }}"></script>

  @livewireScripts
</body>

</html>