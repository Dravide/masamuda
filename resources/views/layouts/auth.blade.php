<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="theme-color" content="#316AFF">
  <meta name="robots" content="index, follow">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <title>@yield('title', 'Login') | Masamuda</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('template/assets/images/favicon.png') }}">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('template/assets/libs/flaticon/css/all/all.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/libs/lucide/lucide.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/libs/fontawesome/css/all.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/libs/simplebar/simplebar.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/libs/node-waves/waves.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/libs/bootstrap-select/css/bootstrap-select.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/css/styles.css') }}">
  
  @livewireStyles
</head>

<body>
  <div class="page-layout">
    {{ $slot }}
  </div>

  <!-- Scripts -->
  <script src="{{ asset('template/assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('template/assets/libs/simplebar/simplebar.min.js') }}"></script>
  <script src="{{ asset('template/assets/libs/node-waves/waves.min.js') }}"></script>
  <script src="{{ asset('template/assets/js/appSettings.js') }}"></script>
  <script src="{{ asset('template/assets/js/main.js') }}"></script>
  
  @livewireScripts
</body>

</html>
