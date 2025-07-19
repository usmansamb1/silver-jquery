<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ config('app.direction') }}">
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

    <!-- Google Fonts for Arabic -->
    @if(app()->getLocale() == 'ar')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @endif

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@emran-alhaddad/saudi-riyal-font/index.css">

    <link rel="stylesheet" href="{{ asset('theme_files/css/style.css') }}">
    <!-- } -->
    <link rel="stylesheet" href="{{ asset('theme_files/css/animate.css') }}" type="text/css" />
    <link rel="stylesheet" href="{{ asset('theme_files/css/magnific-popup.css') }}" type="text/css">

    <link rel="stylesheet" href="{{ asset('theme_files/css/font-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_files/css/components/bs-select.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_files/css/components/bs-switches.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_files/css/components/radio-checkbox.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_files/css/components/ion.rangeslider.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_files/css/components/bs-datatable.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_files/css/components/bs-filestyle.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_files/css/swiper.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_files/css/components/select-boxes.css') }}">

    <!-- Custom Styles  -->
    <!-- <link rel="stylesheet" href="{{ asset('theme_files/css/style-services.min.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('theme_files/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_files/css/frontend.min.css') }}">
    
    @if(app()->getLocale() == 'ar')
        <link rel="stylesheet" href="{{ asset('theme_files/css/style-rtl.css') }}">
    @endif

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
        
        @if(app()->getLocale() == 'ar')
        /* Arabic Font Styling with Noto Kufi Arabic */
        body,
        h1, h2, h3, h4, h5, h6,
        p, span, div, a, button,
        .form-control, .form-label, .form-text,
        .btn, .card, .table,
        .menu-link, .navbar-brand,
        .modal-content, .dropdown-menu,
        .alert, .badge, .tooltip-inner,
        .popover-body, .breadcrumb,
        input, textarea, select {
            font-family: 'Noto Kufi Arabic', 'Arial Unicode MS', sans-serif !important;
            font-weight: 400;
            line-height: 1.6;
        }
        
        /* Specific weight adjustments for better readability */
        h1, h2, h3, h4, h5, h6,
        .fw-bold, .font-weight-bold,
        .btn, .navbar-brand {
            font-weight: 500 !important;
        }
        
        .fw-bolder, .font-weight-bolder {
            font-weight: 600 !important;
        }
        
        /* Improve Arabic text rendering */
        body[dir="rtl"] {
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Arabic number formatting */
        .number, .amount, .currency {
            font-feature-settings: "tnum" 1;
        }
        @endif
    </style>

    <link rel="stylesheet" href="{{ asset('theme_files/css/yasseir-custom-style.css') }}">
    <!-- @* other *@ -->
    <link rel="stylesheet" href="{{ asset('theme_files/css/vendor_bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('theme_files/css/components/select2.min.css') }}">
    <!-- Include CSS files -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body class="stretched side-header is-expanded-menu page-transition" data-loader="4" data-animation-in="fadeIn" data-speed-in="1500" data-animation-out="fadeOut" data-speed-out="800" style="background-color:#FFFF" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
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

        @include('partials.menu')


        @include('partials.header')

        <section id="content" style="margin-bottom: 0px;">
            <div class="content-wrap mb-5">
                @yield('content')
            </div>
        </section>



    </section>
</div>
{{-- real html --END --}}



    {{-- Main page content --}}



{{-- Include footer --}}
@include('partials.footer')

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
                    title: '{{ __('Session expired. Please refresh the page.') }}'
                });
            }
            return Promise.reject(error);
        }
    );

    // Make Laravel translations available to JavaScript
    window.translations = {
        success: '{{ __('Success!') }}',
        error: '{{ __('Error!') }}',
        ok: '{{ __('OK') }}',
        cancel: '{{ __('Cancel') }}',
        loading: '{{ __('Loading...') }}',
        processing: '{{ __('Processing...') }}',
        info: '{{ __('Info') }}',
        service_order_success: '{{ __('Service order created successfully!') }}',
        unexpected_response: '{{ __('Unexpected response format. Please try again.') }}',
        processing_error: '{{ __('An error occurred while processing your request.') }}',
        processing_error_later: '{{ __('An error occurred while processing your request. Please try again later.') }}',
        please_add_service: '{{ __('Please add at least one service') }}',
        select_credit_card_payment: '{{ __('Select credit card payment to load payment form') }}',
        loading_secure_payment: '{{ __('Loading secure payment form...') }}',
        failed_to_initialize_payment: '{{ __('Failed to initialize payment') }}',
        failed_to_load_payment: '{{ __('Failed to load payment form. Please try again.') }}',
        please_complete_fields: '{{ __('Please complete all required fields correctly') }}',
        pickup_location_required: '{{ __('Pickup location is required.') }}',
        no_services_found: '{{ __('No services found. Please add at least one service to your order.') }}',
        order_created: '{{ __('Order created! Please complete your payment below.') }}',
        please_select_service_type: '{{ __('Please select a service type') }}',
        please_select_fuel_type: '{{ __('Please select a fuel type') }}',
        please_enter_valid_plate_number: '{{ __('Please enter a valid plate number') }}',
        please_enter_name_on_card_rfid: '{{ __('Please enter the name on card/RFID') }}',
        please_enter_vehicle_make: '{{ __('Please enter the vehicle make') }}',
        please_enter_vehicle_model: '{{ __('Please enter the vehicle model') }}',
        please_enter_valid_year: '{{ __('Please enter a valid year between 1900 and') }}',
        please_enter_valid_refueling_amount: '{{ __('Please enter a valid refueling amount greater than 0') }}',
        please_select_vehicle: '{{ __('Please select a vehicle from the dropdown') }}',
        please_enter_plate_number: '{{ __('Please enter a plate number') }}',
        please_enter_valid_amount: '{{ __('Please enter a valid amount') }}',
        prepaid: '{{ __('Prepaid') }}',
        service_ready_for_editing: '{{ __('Service ready for editing') }}',
        remove_service: '{{ __('Remove service?') }}',
        are_you_sure_remove_service: '{{ __('Are you sure you want to remove this service?') }}',
        yes_remove_it: '{{ __('Yes, remove it') }}',
        service_removed: '{{ __('Service removed from list') }}',
        service_added: '{{ __('Service added to List of purchase Services') }}'
    };
</script>

<!-- Common scripts -->
@include('partials.scripts')

@include('partials.loader')
@stack('scripts')
</body>
</html>
