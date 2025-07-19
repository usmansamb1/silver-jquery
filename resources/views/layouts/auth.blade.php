<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    <link href="{{ asset('theme_files/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/swiper.css') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/dark.css') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/font-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/magnific-popup.css') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/colors.php?color=0073bd') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/fonts.css') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/select2.css') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('theme_files/css/fontawesome/css/all.min.css') }}" rel="stylesheet">

    @stack('styles')
</head>
<body>
    <div id="wrapper">
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('theme_files/js/jquery.js') }}"></script>
    <script src="{{ asset('theme_files/js/plugins.min.js') }}"></script>
    <script src="{{ asset('theme_files/js/functions.js') }}"></script>
    <script src="{{ asset('theme_files/js/select2.min.js') }}"></script>
    <script src="{{ asset('theme_files/js/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    @stack('scripts')
</body>
</html> 