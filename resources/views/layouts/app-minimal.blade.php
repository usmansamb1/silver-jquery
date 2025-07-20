<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="author" content="Usman Developer at Aljeri">
    <meta name="description" content="Complete cross platform system developed by Aljeri IT Development Department">
    <meta name="robots" content="Aljeri Oil Yaseeir order system" />
    <meta property="og:title" content="Aljeri JOil Yaseeir System" />
    <meta property="og:description" content="Aljeri JOil Yaseeir System" />
    <meta property="og:image" content="Aljeri JOil Yaseeir System" />
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, maximum-scale=5, initial-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>

    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 
    <link rel="stylesheet" href="{{ asset('theme_files/css/style.css') }}">
    <!-- } --> 
    <link rel="stylesheet" href="{{ asset('theme_files/css/frontend.min.css') }}">

    <style>
        /* Menu Active States */
        .menu-item.active > .menu-link {
            background-color: rgba(0, 97, 242, 0.1) !important;
            color: #0061f2 !important;
        }
        
        .menu-item.menuActive > .menu-link {
            background-color: rgba(0, 97, 242, 0.1) !important;
            color: #0061f2 !important;
            border-radius: 5px;
        }

        .menu-item.active .sub-menu-container {
            display: block !important;
        }

        .headerMenuBg {
            background-color: rgba(0, 97, 242, 0.1) !important;
            color: #0061f2 !important;
            border-radius: 5px;
        }

        /* Enhance submenu appearance */
        .sub-menu-container {
            border-left: 2px solid rgba(0, 97, 242, 0.1);
            margin-left: 1rem;
            padding-left: 1rem;
        }

        /* Menu item hover effect */
        .menu-item .menu-link:hover {
            background-color: rgba(0, 97, 242, 0.05) !important;
            transform: translateX(5px);
            transition: all 0.3s ease;
        }

        /* Active menu icon color */
        .menu-item.active > .menu-link i,
        .menu-item.menuActive > .menu-link i {
            color: #0061f2 !important;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('theme_files/css/yasseir-custom-style.css') }}">
    <!-- @* other *@ -->
    <link rel="stylesheet" href="{{ asset('theme_files/css/vendor_bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_files/css/components/select2.min.css') }}">
    <!-- Include CSS files -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="" data-loader="4" data-animation-in="fadeIn" data-speed-in="1500" data-animation-out="fadeOut" data-speed-out="800" style="background-color:#FFFF">
{{-- Include header and menu --}}


{{-- real html --}}
<div   id="wrapper">
 

    <style type="text/css">


        .title-block {
            border-left: 7px solid #a2c943 !important;
        }

        .title-block h4 + span, .title-block .h4 + span {
            font-size: 1.10rem !important;
        }

    </style>

    <section  id="content" >
 
 
        <section id="content" style="margin-bottom: 0px;">
            <div class="content-wrap mb-5">
                @yield('content')
            </div>
        </section>



    </section>
</div>
{{-- real html --END --}}



    {{-- Main page content --}}

 

{{-- Global Loader Partial --}}

 
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Axios -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<script>
    // Set up Axios CSRF token
    axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    
    // Set up SweetAlert2 default settings
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    // Global error handler for Axios
    axios.interceptors.response.use(
        response => response,
        error => {
            if (error.response?.status === 419) { // CSRF token mismatch
                Toast.fire({
                    icon: 'error',
                    title: 'Session expired. Please refresh the page.'
                });
            }
            return Promise.reject(error);
        }
    );
</script>
 

@include('partials.loader')
@stack('scripts')
</body>
</html>
