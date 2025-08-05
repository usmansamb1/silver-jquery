<!DOCTYPE html>
<html dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="author" content="Usman Developer at Aljeri">
    <meta name="description" content="Complete cross platform system developed by Aljeri IT Development Department">
    <meta name="robots" content="Aljeri FuelApp - JOIL order system" />
    <meta property="og:title" content="Aljeri FuelApp - JOIL System" />
    <meta property="og:description" content="Aljeri FuelApp - JOIL System" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta property="og:image" content="Aljeri FuelApp - JOIL System" />
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, maximum-scale=5, initial-scale=1, user-scalable=0">
    {{-- <title>@yield('title', 'JOIL YASEEIR ONLINE ')</title> --}}
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Rubik:wght@400;600&family=Lora:ital@0;1&display=swap" rel="stylesheet">
    @if(app()->getLocale() == 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

        @if(app()->getLocale() == 'ar')
            <link rel="stylesheet" href="{{ asset('theme_files/css/style-rtl.css') }}">
        @else
            <link rel="stylesheet" href="{{ asset('theme_files/css/style.css') }}">
        @endif

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

        <link rel="stylesheet" href="{{ asset('theme_files/css/yasseir-custom-style.css') }}">
        <!-- @* other *@ -->
        <link rel="stylesheet" href="{{ asset('theme_files/css/vendor_bundle.min.css') }}">
        <link rel="stylesheet" href="{{ asset('theme_files/css/components/select2.min.css') }}">
        <script src="https://cdn.jsdelivr.net/npm/js-loading-overlay@1.2.0/dist/js-loading-overlay.min.js"></script>

        <!-- <link rel="stylesheet" href="{{ asset('theme_files/css/post-3583.css') }}"> -->


        <style>
            .disabled {
                pointer-events: none;
                opacity: 0.5;
                color: gray;
            }

            /* Adjust as needed for your style */
            .otp-input {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
                text-align: center;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            .close {
                outline: none;
            }
            
            /* Enhanced form styling */
            .input-group {
                margin-bottom: 15px;
                position: relative;
            }
            
            .input-group-text {
                background-color: #f8f9fa;
                border-color: #dee2e6;
                color: #6c757d;
            }
            
            .form-control {
                border-color: #dee2e6;
                border-radius: 0.25rem;
                padding: 0.575rem 0.75rem;
                transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            }
            
            .form-control:focus {
                border-color: #80bdff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            }
            
            .form-label {
                font-weight: 500;
                margin-bottom: 0.5rem;
                color: #495057;
            }
            
            /* Validation styling */
            .form-control.is-invalid {
                border-color: #dc3545;
                background-image: none;
            }
            
            .form-control.is-valid {
                border-color: #28a745;
                background-image: none;
            }
            
            .invalid-feedback {
                display: none;
                color: #dc3545;
                font-size: 0.875em;
                margin-top: 0.25rem;
            }
            
            .invalid-feedback.show {
                display: block;
            }
            
            .was-validated .form-control:invalid, 
            .form-control.is-invalid {
                border-color: #dc3545;
            }
            
            .was-validated .form-control:valid, 
            .form-control.is-valid {
                border-color: #28a745;
            }
            
            /* Input icon position */
            .input-icon {
                position: absolute;
                top: 12px;
                left: 12px;
                color: #6c757d;
                z-index: 10;
            }
            
            .input-with-icon {
                padding-left: 35px;
            }
            
            /* Toggle sections */
            .form-section {
                transition: all 0.3s ease;
            }
            
            /* Form section headers */
            .section-header {
                padding: 10px 15px;
                background-color: #f8f9fa;
                border-radius: 5px;
                margin-bottom: 20px;
                border-left: 5px solid #0061f2;
                cursor: pointer;
            }
            
            /* Help tooltips */
            .help-tooltip {
                color: #6c757d;
                margin-left: 5px;
                cursor: help;
            }
            
            /* Auth card styling */
            .auth-card {
                border: none;
                border-radius: 10px;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
                overflow: hidden;
            }
            
            .auth-card-header {
                background-color: #f8f9fa;
                padding: 1.5rem;
                border-bottom: 1px solid #e9ecef;
            }
            
            .auth-card-body {
                padding: 1.5rem;
            }
            
            /* Buttons */
            .btn-auth {
                padding: 0.5rem 1.5rem;
                border-radius: 30px;
                font-weight: 500;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                transition: all 0.3s ease;
            }
            
            .btn-auth:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            
            /* Form type toggle */
            .registration-type-toggle {
                display: flex;
                justify-content: center;
                margin-bottom: 1.5rem;
                gap: 1rem;
            }
            
            .registration-type-item {
                flex: 1;
                text-align: center;
                padding: 1rem;
                border: 1px solid #dee2e6;
                border-radius: 5px;
                cursor: pointer;
                transition: all 0.3s ease;
            }
            
            .registration-type-item.active {
                border-color: #0061f2;
                background-color: rgba(0, 97, 242, 0.05);
            }
            
            .registration-type-item:hover {
                border-color: #0061f2;
            }
            
            /* Saudi Flag Icon */
            .saudi-flag-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-right: 5px;
            }
            
            .saudi-flag-icon svg {
                width: 30px;
                /* height: 24px; */
                border-radius: 50%;
                overflow: hidden;
                box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            }
            
            /* OTP Input Styling */
            .swal2-otp-input {
                display: flex;
                justify-content: center;
                gap: 10px;
                margin: 20px 0;
            }
            
            .swal2-otp-input input {
                width: 50px;
                height: 50px;
                text-align: center;
                font-size: 24px;
                border: 1px solid #ced4da;
                border-radius: 8px;
                background-color: #f8f9fa;
                transition: all 0.2s ease;
            }
            
            .swal2-otp-input input:focus {
                border-color: #80bdff;
                box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
                outline: none;
            }
            
            /* Enhanced buttons */
            .enhanced-button {
                position: relative;
                overflow: hidden;
                transition: all 0.3s ease;
            }
            
            .enhanced-button:before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255,255,255,0.1);
                transform: translateX(-100%);
                transition: all 0.3s ease;
            }
            
            .enhanced-button:hover:before {
                transform: translateX(0);
            }

            .flag-selector .dropdown-toggle::after {
                display: none;
            }
            
            .flag-selector .btn {
                padding: 0.375rem 0.75rem;
                border-right: none;
                border-top-right-radius: 0;
                border-bottom-right-radius: 0;
            }
            
            .form-control.phone-input {
                border-left: none;
                border-top-left-radius: 0; 
                border-bottom-left-radius: 0;
            }
            
            .enhanced-button {
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
            }
            
            .enhanced-button:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            
            .enhanced-button:active {
                transform: translateY(0);
            }
            
            .enhanced-button::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 5px;
                height: 5px;
                background: rgba(255, 255, 255, 0.5);
                opacity: 0;
                border-radius: 100%;
                transform: scale(1, 1) translate(-50%);
                transform-origin: 50% 50%;
            }
            
            .enhanced-button:hover::after {
                animation: ripple 1s ease-out;
            }
            
            @keyframes ripple {
                0% {
                    transform: scale(0, 0);
                    opacity: 0.5;
                }
                100% {
                    transform: scale(20, 20);
                    opacity: 0;
                }
            }
            
            /* Saudi Flag Icon */
            .saudi-flag {
                width: 30px;
                /* height: 16px; */
                display: inline-block;
                vertical-align: middle;
                background-image: none;
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                border-radius: 2px;
            }
            
            /* SweetAlert2 OTP Input Styling */
            .swal2-otp-popup {
                width: 400px !important;
            }
            
            .swal2-otp-input {
                display: flex;
                justify-content: center;
                gap: 10px;
                margin: 20px 0;
            }
            
            .swal2-otp-digit {
                width: 50px;
                height: 50px;
                border: 1px solid #ddd;
                border-radius: 8px;
                text-align: center;
                font-size: 20px;
                font-weight: bold;
                background-color: #f9f9f9;
                transition: all 0.2s;
            }
            
            .swal2-otp-digit:focus {
                border-color: #006C35;
                box-shadow: 0 0 0 0.2rem rgba(0, 108, 53, 0.25);
                background-color: #fff;
                outline: none;
            }

            /* Hide the dropdown toggle arrow */
            .flag-selector.dropdown-toggle::after {
                display: none;
            }

            /* Flag selector styling */
            .flag-selector {
                padding: 3px 5px;
                background-color: #f8f9fa;
                border-color: #ced4da;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 5px;
            }

            /* Enhanced buttons */
            .btn {
                position: relative;
                transition: all 0.3s ease;
                overflow: hidden;
            }

            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .btn:active {
                transform: translateY(0);
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .btn::after {
                content: '';
                position: absolute;
                top: 50%;
                left: 50%;
                width: 0;
                height: 0;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 50%;
                transform: translate(-50%, -50%);
                opacity: 0;
                transition: width 0.4s ease, height 0.4s ease, opacity 0.4s ease;
            }

            .btn:active::after {
                width: 200%;
                height: 200%;
                opacity: 1;
                transition: 0s;
            }

            /* Phone input styling */
            .phone-input {
                height: 48px;
                font-size: 1rem;
            }

            /* SweetAlert2 OTP styling */
            .swal2-otp-popup {
                border-radius: 12px;
                padding: 20px;
            }

            .swal2-otp-input {
                display: flex;
                justify-content: center;
                gap: 8px;
                margin: 20px 0;
            }

            .swal2-otp-digit {
                width: 40px;
                height: 48px;
                border: 1px solid #ddd;
                border-radius: 8px;
                text-align: center;
                font-size: 18px;
                font-weight: bold;
                transition: all 0.2s ease;
            }

            .swal2-otp-digit:focus {
                border-color: #006C35;
                box-shadow: 0 0 0 3px rgba(0, 108, 53, 0.2);
                outline: none;
            }
            
            /* Flag Icons for Language Selection */
            .flag-icon {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 20px;
                height: 15px;
                border-radius: 2px;
                overflow: hidden;
                box-shadow: 0 1px 2px rgba(0,0,0,0.1);
                margin-right: 8px;
            }
            
            .flag-icon svg {
                width: 100%;
                height: 100%;
                display: block;
            }
            
            /* RTL support for flag icons */
            html[dir="rtl"] .flag-icon {
                margin-right: 0;
                margin-left: 8px;
            }
            
            /* Enhanced Arabic RTL Support */
            @if(app()->getLocale() == 'ar')
            html[dir="rtl"] {
                font-family: 'Noto Kufi Arabic', 'Arial Unicode MS', sans-serif !important;
                text-rendering: optimizeLegibility;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }
            
            html[dir="rtl"] body {
                font-family: 'Noto Kufi Arabic', 'Arial Unicode MS', sans-serif !important;
                direction: rtl;
                text-align: right;
            }
            
            html[dir="rtl"] .input-group-text {
                border-left: 1px solid #ced4da;
                border-right: none;
                border-radius: 0.375rem 0 0 0.375rem;
            }
            
            html[dir="rtl"] .form-control {
                text-align: right;
                border-right: 1px solid #ced4da;
                border-left: none;
                border-radius: 0 0.375rem 0.375rem 0;
            }
            
            html[dir="rtl"] .phone-input {
                border-left: 1px solid #ced4da;
                border-right: none;
                border-radius: 0 0.375rem 0.375rem 0;
                direction: ltr;
                text-align: left;
            }
            
            html[dir="rtl"] .flag-selector {
                border-right: 1px solid #ced4da;
                border-left: none;
                border-radius: 0.375rem 0 0 0.375rem;
            }
            
            html[dir="rtl"] .accordion-icon {
                margin-left: 0.75rem;
                margin-right: 0;
            }
            
            html[dir="rtl"] .form-check-input {
                margin-right: 0;
                margin-left: 0.5rem;
            }
            
            html[dir="rtl"] .form-check-label {
                padding-right: 0;
                padding-left: 1.25rem;
            }
            
            html[dir="rtl"] .help-tooltip {
                margin-right: 5px;
                margin-left: 0;
            }
            
            html[dir="rtl"] .me-2 {
                margin-right: 0 !important;
                margin-left: 0.5rem !important;
            }
            
            html[dir="rtl"] .me-1 {
                margin-right: 0 !important;
                margin-left: 0.25rem !important;
            }
            
            html[dir="rtl"] .ms-2 {
                margin-left: 0 !important;
                margin-right: 0.5rem !important;
            }
            
            html[dir="rtl"] .ms-1 {
                margin-left: 0 !important;
                margin-right: 0.25rem !important;
            }
            
            html[dir="rtl"] .btn-group .dropdown-menu {
                text-align: right;
            }
            
            html[dir="rtl"] .dropdown-item {
                text-align: right;
            }
            
            html[dir="rtl"] .text-center {
                text-align: center !important;
            }
            
            html[dir="rtl"] .text-start {
                text-align: right !important;
            }
            
            html[dir="rtl"] .text-end {
                text-align: left !important;
            }
            
            html[dir="rtl"] .d-flex.justify-content-center {
                flex-direction: row-reverse;
            }
            
            html[dir="rtl"] .form-check-inline {
                margin-left: 0;
                margin-right: 1rem;
            }
            
            html[dir="rtl"] .form-check-inline:last-child {
                margin-right: 0;
            }
            
            html[dir="rtl"] .invalid-feedback {
                text-align: right;
            }
            
            html[dir="rtl"] .section-header {
                text-align: right;
            }
            
            html[dir="rtl"] .accordion-title {
                text-align: right;
            }
            
            html[dir="rtl"] .card-text {
                text-align: right;
            }
            
            html[dir="rtl"] .form-label {
                text-align: right;
                display: block;
            }
            
            html[dir="rtl"] .list-service {
                text-align: center;
            }
            
            html[dir="rtl"] .card-body {
                text-align: right;
            }
            
            html[dir="rtl"] .accordion-content {
                text-align: right;
            }
            
            html[dir="rtl"] .d-flex.gap-3 {
                flex-direction: row-reverse;
            }
            
            html[dir="rtl"] .d-flex.justify-content-between {
                flex-direction: row-reverse;
            }
            
            /* Keep numbers and phone inputs in LTR */
            html[dir="rtl"] input[type="tel"],
            html[dir="rtl"] input[type="number"],
            html[dir="rtl"] .number,
            html[dir="rtl"] .phone-input {
                direction: ltr;
                text-align: left;
            }
            
            /* Additional RTL styling for better Arabic support */
            html[dir="rtl"] .container,
            html[dir="rtl"] .container-fluid {
                text-align: right;
            }
            
            html[dir="rtl"] .row {
                direction: rtl;
            }
            
            html[dir="rtl"] .col,
            html[dir="rtl"] .col-12,
            html[dir="rtl"] .col-md-6,
            html[dir="rtl"] .col-md-8,
            html[dir="rtl"] .col-lg-6 {
                text-align: right;
            }
            
            html[dir="rtl"] .card {
                text-align: right;
            }
            
            html[dir="rtl"] .card-header {
                text-align: right;
            }
            
            html[dir="rtl"] .form-group {
                text-align: right;
            }
            
            html[dir="rtl"] .btn {
                text-align: center;
            }
            
            html[dir="rtl"] .btn-block {
                text-align: center;
            }
            
            html[dir="rtl"] .alert {
                text-align: right;
            }
            
            html[dir="rtl"] .modal-content {
                text-align: right;
            }
            
            html[dir="rtl"] .modal-header {
                text-align: right;
                flex-direction: row-reverse;
            }
            
            html[dir="rtl"] .modal-body {
                text-align: right;
            }
            
            html[dir="rtl"] .modal-footer {
                text-align: left;
                flex-direction: row-reverse;
            }
            
            html[dir="rtl"] .nav-tabs {
                text-align: right;
            }
            
            html[dir="rtl"] .nav-link {
                text-align: center;
            }
            
            html[dir="rtl"] .tab-content {
                text-align: right;
            }
            
            html[dir="rtl"] .registration-type-toggle {
                flex-direction: row-reverse;
            }
            
            html[dir="rtl"] .registration-type-item {
                text-align: center;
            }
            
            html[dir="rtl"] .section-header {
                border-left: none;
                border-right: 5px solid #0061f2;
            }
            
            html[dir="rtl"] .input-with-icon {
                padding-left: 0.75rem;
                padding-right: 35px;
            }
            
            html[dir="rtl"] .input-icon {
                left: auto;
                right: 12px;
            }
            
            html[dir="rtl"] .language-switcher {
                text-align: center;
            }
            
            html[dir="rtl"] .language-switcher .btn {
                margin: 0 2px;
            }
            
            html[dir="rtl"] .accordion-title {
                text-align: right;
            }
            
            html[dir="rtl"] .accordion-content {
                text-align: right;
            }
            
            html[dir="rtl"] .accordion-icon {
                margin-left: 0.75rem;
                margin-right: 0;
            }
            
            html[dir="rtl"] .btn-group .dropdown-menu {
                right: 0;
                left: auto;
            }
            
            html[dir="rtl"] .dropdown-item {
                text-align: right;
                padding-right: 1rem;
                padding-left: 1rem;
            }
            
            html[dir="rtl"] .img-responsive {
                text-align: center;
            }
            @endif
        </style>
</head>
<!-- side-header -->
<body class="stretched {{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }} page-transition" data-loader="4" data-animation-in="fadeIn" data-speed-in="1500" data-animation-out="fadeOut" data-speed-out="800" style="background-color:#FFFF">
<div id="wrapper">


    <section id="content" >
        <section class="content-wrap" >
            <div class="container-fluid">
                <div class="row">
                    <div class=" col-sm-5 col-5 ">
                    <div class="row">
                        <div class="col-3 form-group mt-0">
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('admin.login') }}" target="_blank" class="button button-small button-3d button-blue m-0" type="button" id="employeeLoginButton">
                                    <i class="fa fa-user"></i> {{ __('Employee Login') }}
        </a>
                            </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-sm-12 mt-1">
                                
                            <div class="btn-group">
                                    <button type="button" class="btn btn-sm dropdown-toggle button button-border button-rounded button-fill fill-from-right button-blue" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span> <i class="bi-globe2"></i> {{ __('Language') }}</span>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('lang.change', 'ar') }}">
                                            <span class="flag-icon {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}">
                                                <svg width="20" height="15" viewBox="0 0 20 15" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="20" height="15" fill="#006c35"/>
                                                    <g fill="#fff">
                                                        <path d="M6.5,3.5c-0.4,0-0.8,0.3-0.8,0.8s0.3,0.8,0.8,0.8s0.8-0.3,0.8-0.8S6.9,3.5,6.5,3.5z M13.5,3.5c-0.4,0-0.8,0.3-0.8,0.8s0.3,0.8,0.8,0.8s0.8-0.3,0.8-0.8S13.9,3.5,13.5,3.5z"/>
                                                        <path d="M5.8,7.5c0.2,0.3,0.5,0.4,0.8,0.4c0.1,0,0.2,0,0.3,0c0.2-0.1,0.5-0.3,0.6-0.6c0.1-0.2,0.1-0.5,0-0.7c-0.1-0.2-0.3-0.5-0.6-0.6c-0.2-0.1-0.5-0.1-0.7,0c-0.2,0.1-0.5,0.3-0.6,0.6c-0.1,0.2-0.1,0.5,0,0.7C5.6,7.3,5.7,7.4,5.8,7.5z"/>
                                                        <path d="M14.2,7.5c0.2,0.3,0.5,0.4,0.8,0.4c0.1,0,0.2,0,0.3,0c0.2-0.1,0.5-0.3,0.6-0.6c0.1-0.2,0.1-0.5,0-0.7c-0.1-0.2-0.3-0.5-0.6-0.6c-0.2-0.1-0.5-0.1-0.7,0c-0.2,0.1-0.5,0.3-0.6,0.6c-0.1,0.2-0.1,0.5,0,0.7C14,7.3,14.1,7.4,14.2,7.5z"/>
                                                        <path d="M6,2.5h8c0.3,0,0.5,0.2,0.5,0.5v5c0,1.7-1.3,3-3,3h-3c-1.7,0-3-1.3-3-3v-5C5.5,2.7,5.7,2.5,6,2.5z M14,3.5h-8v4.5c0,1.1,0.9,2,2,2h4c1.1,0,2-0.9,2-2V3.5z"/>
                                                        <path d="M12.8,8.8l-0.5-0.2l-0.5,0.2c-0.1,0-0.1,0-0.1-0.1l0.1-0.6L11.5,7.8c0-0.1-0.1-0.1,0-0.2l0.5-0.2l0.1-0.6c0-0.1,0.1-0.1,0.2-0.1l0.5,0.2l0.5-0.2c0.1,0,0.1,0,0.1,0.1l0.1,0.6l0.5,0.2c0.1,0.1,0.1,0.1,0,0.2l-0.5,0.2l-0.1,0.6c0,0.1-0.1,0.1-0.2,0.1L12.8,8.8z"/>
                                                        <path d="M8.8,8.8l-0.5-0.2l-0.5,0.2c-0.1,0-0.1,0-0.1-0.1l0.1-0.6L7.5,7.8c0-0.1-0.1-0.1,0-0.2l0.5-0.2l0.1-0.6c0-0.1,0.1-0.1,0.2-0.1l0.5,0.2l0.5-0.2c0.1,0,0.1,0,0.1,0.1l0.1,0.6l0.5,0.2c0.1,0.1,0.1,0.1,0,0.2l-0.5,0.2l-0.1,0.6c0,0.1-0.1,0.1-0.2,0.1L8.8,8.8z"/>
                                                    </g>
                                                </svg>
                                            </span> {{ __('Arabic') }}
                                        </a>
                                        <a class="dropdown-item" href="{{ route('lang.change', 'en') }}">
                                            <span class="flag-icon {{ app()->getLocale() == 'ar' ? 'ms-2' : 'me-2' }}">
                                                <svg width="20" height="15" viewBox="0 0 20 15" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="20" height="15" fill="#012169"/>
                                                    <path d="M0,0 L20,15 M20,0 L0,15" stroke="#fff" stroke-width="3"/>
                                                    <path d="M0,0 L20,15 M20,0 L0,15" stroke="#C8102E" stroke-width="2"/>
                                                    <path d="M10,0 L10,15 M0,7.5 L20,7.5" stroke="#fff" stroke-width="5"/>
                                                    <path d="M10,0 L10,15 M0,7.5 L20,7.5" stroke="#C8102E" stroke-width="3"/>
                                                </svg>
                                            </span> {{ __('English') }}
                                        </a>
                                    </div>
                                </div> 
                                </div>
                            <!-- language buttons -->
                            <div class="col-sm-12 mt-3 text-center "> <img src="{{ asset('theme_files/imgs/yaseeir-smal-new-logo6.png') }}" class="img-responsive" style="max-width: 350px;" ></div>
                            <!-- logo -->
                        

                        </div>
                        <div class="row  mt-4">
                            <div class="col-1"></div>
                            <div class="card col-10">
                                <div class="card-body">
                                    <p class="card-text">
                                    <div class="accordion accordion-lg mx-auto mb-0" style="">

                                        <div class="accordion-header accordion-active">
                                            <div class="accordion-icon">
                                                <i class="accordion-closed fa-solid fa-lock"></i>
                                                <i class="accordion-open bi-unlock"></i>
                                            </div>
                                            <div class="accordion-title">
                                                {{ __('Login to your Account') }}
                                            </div>
                                        </div>

                                        <div class="accordion-content" style="display: block;">
                                            <form id="loginForm">
                                                <!-- Login Mobile Field -->
                                                @csrf
                                                <div class="col-12 form-group mt-4 mb-4" @if(app()->getLocale() == 'ar') style="direction: ltr;" @endif>
                                                    <label for="login_mobile" class="form-label">{{ __('Mobile Number') }}</label>
                                                    <div class="input-group w-80 mx-auto position-relative">
                                                        <span class="input-group-text flag-selector">
                                                            <img src="{{ asset('theme_files/imgs/saudi-flag-icon.png') }}" alt="Saudi Flag" class="saudi-flag">
                                                        </span>
                                                        <input type="tel" id="login_mobile" name="mobile" class="form-control phone-input" placeholder="05XXXXXXXX" required  @if(app()->getLocale() == 'ar') style="text-align:right; " @endif>
                                                    </div>
                                                </div>

                                                <div class="col-12 form-group">
                                                    <div class="d-flex justify-content-between">
                                                        <button class="button button-small button-3d button-blue m-0" type="submit">
                                                            <i class="fas fa-sign-in-alt me-2 {{ app()->getLocale() == 'ar' ? 'fa-rotate-180': '' }}"></i> {{ __('Login') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="accordion-header mt-4 ">
                                            <div class="accordion-icon">
                                                <i class="accordion-closed bi-person"></i>
                                                <i class="accordion-open bi-check-circle-fill"></i>
                                            </div>
                                            <div class="accordion-title">
                                                {{ __('New Signup?') }} {{ __('Register for an Account') }}
                                            </div>
                                        </div>
                                        <div class="accordion-content text-dark" style="display: inline-block;">
                                            <form id="registerForm" name="registerForm" class="row mb-0">
                                                @csrf
                                                <div class="col-12 text-center form-group mt-4">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="registration_type" id="reg_personal" value="personal" checked>{{-- onclick="showPersonal()"--}}
                                                        <label class="form-check-label" for="reg_personal">{{ __('Personal Account') }}</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="registration_type" id="reg_company" value="company" >{{--onclick="showCompany()"--}}
                                                        <label class="form-check-label" for="reg_company">{{ __('Company Account') }}</label>
                                                    </div>
                                                </div>

                                                <!-- Private -->
                                                <!-- Semi Govt -->
                                                <!-- Govt -->
                                                <div class="col-sm-12" id="company_fields">
                                                    <h5 class="section-header mb-3">
                                                        <i class="fas fa-building me-2"></i> {{ __('Company Account Details') }}
                                                    </h5>

                                                    <!-- Company Type -->
                                                    <div class="form-group mb-3">
                                                        <label class="form-label">{{ __('Company Type') }}</label>
                                                        <div class="d-flex gap-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="company_type" id="private" value="private" checked>
                                                                <label class="form-check-label" for="private">{{ __('Private') }}</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="company_type" id="semiGovt" value="semi Govt.">
                                                                <label class="form-check-label" for="semiGovt">{{ __('Semi Govt') }}</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="company_type" id="govt" value="Govt">
                                                                <label class="form-check-label" for="govt">{{ __('Govt') }}</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Company Name -->
                                                    <div class="col-12 form-group">
                                                        <label for="company_name" class="form-label">{{ __('Company Name') }}</label>
                                                        <div class="input-group mb-3 position-relative">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-building"></i>
                                                            </span>
                                                            <input type="text" name="company_name" id="company_name" class="form-control" 
                                                                placeholder="{{ __('Enter company name') }}" required>
                                                            <div class="invalid-feedback">{{ __('Company name is required') }}</div>
                                                        </div>
                                                    </div>

                                                    <!-- Company Email -->
                                                    <div class="col-12 form-group">
                                                        <label for="email_company" class="form-label">{{ __('Company Email') }}</label>
                                                        <div class="input-group mb-3 position-relative">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-envelope"></i>
                                                            </span>
                                                            <input type="email" class="form-control" name="email" id="email_company" 
                                                                placeholder="{{ __('company@example.com') }}" required>
                                                            <div class="invalid-feedback">{{ __('Please enter a valid email address') }}</div>
                                                        </div>
                                                    </div>

                                                    <!-- Company Registration Mobile Field -->
                                                    <div class="col-12 form-group">
                                                        <label for="mobile_company" class="form-label">{{ __('Mobile Number') }}</label>
                                                        <div class="input-group mb-3 position-relative">
                                                            <span class="input-group-text flag-selector">
                                                                <img src="{{ asset('theme_files/imgs/saudi-flag-icon.png') }}" alt="Saudi Flag" class="saudi-flag">
                                                            </span>
                                                            <input type="tel" name="mobile" id="mobile_company" class="form-control phone-input" 
                                                                placeholder="{{ __('05XXXXXXXX') }}" required pattern="^5[0-9]{8}$">
                                                            <div class="invalid-feedback">{{ __('Mobile must start with 5 and be exactly 9 digits') }}</div>
                                                        </div>
                                                    </div>

                                                    <!-- CR Number -->
                                                    <div class="col-12 form-group">
                                                        <label for="cr_number" class="form-label">
                                                            {{ __('Commercial Registration Number') }}
                                                            <i class="fas fa-info-circle help-tooltip" data-bs-toggle="tooltip" 
                                                               title="{{ __('Must be exactly 10 digits and start with 1 or 2') }}"></i>
                                                        </label>
                                                        <div class="input-group mb-3 position-relative">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-file-contract"></i>
                                                            </span>
                                                            <input type="text" class="form-control" name="cr_number" id="cr_number" 
                                                                placeholder="{{ __('1XXXXXXXXX or 2XXXXXXXXX') }}" required 
                                                                pattern="^[12][0-9]{9}$">
                                                            <div class="invalid-feedback">{{ __('CR Number must be exactly 10 digits and start with 1 or 2') }}</div>
                                                        </div>
                                                    </div>

                                                    <!-- VAT Number -->
                                                    <div class="col-12 form-group">
                                                        <label for="vat_number" class="form-label">
                                                            {{ __('VAT Number') }}
                                                            <i class="fas fa-info-circle help-tooltip" data-bs-toggle="tooltip" 
                                                               title="{{ __('Must be exactly 15 digits, starting and ending with 3') }}"></i>
                                                        </label>
                                                        <div class="input-group mb-3 position-relative">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-receipt"></i>
                                                            </span>
                                                            <input type="text" class="form-control" name="vat_number" id="vat_number" 
                                                                placeholder="{{ __('3XXXXXXXXXXXXX3') }}" required pattern="^3[0-9]{13}3$">
                                                            <div class="invalid-feedback">{{ __('VAT Number must be exactly 15 digits, starting and ending with 3') }}</div>
                                                        </div>
                                                    </div>

                                                    <!-- Street -->
                                                    <div class="col-12 form-group">
                                                        <label for="street" class="form-label">{{ __('Street') }}</label>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-road"></i>
                                                            </span>
                                                            <input type="text" class="form-control" name="street" id="street" 
                                                                placeholder="{{ __('Enter street name') }}">
                                                        </div>
                                                    </div>

                                                    <!-- Building Number -->
                                                    <div class="col-12 form-group">
                                                        <label for="building_number" class="form-label">{{ __('Building Number') }}</label>
                                                        <div class="input-group mb-3 position-relative">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-building"></i>
                                                            </span>
                                                            <input type="text" class="form-control" name="building_number" id="building_number" 
                                                                placeholder="{{ __('Enter building number or name') }}">
                                                            <div class="invalid-feedback">{{ __('Please enter a valid building identifier') }}</div>
                                                        </div>
                                                    </div>

                                                    <!-- City -->
                                                    <div class="col-12 form-group">
                                                        <label for="city" class="form-label">{{ __('City') }}</label>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-city"></i>
                                                            </span>
                                                            <input type="text" name="city" id="city" class="form-control" 
                                                                placeholder="{{ __('Enter city name') }}">
                                                        </div>
                                                    </div>

                                                    <!-- Zip Code -->
                                                    <div class="col-12 form-group">
                                                        <label for="zip_code" class="form-label">{{ __('Zip Code') }}</label>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-map-pin"></i>
                                                            </span>
                                                            <input type="text" name="zip_code" id="zip_code" class="form-control" 
                                                                placeholder="{{ __('Enter zip code') }}">
                                                        </div>
                                                    </div>

                                                    <!-- Region -->
                                                    <div class="col-12 form-group">
                                                        <label for="company_region" class="form-label">{{ __('Region') }}</label>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-map-marker-alt"></i>
                                                            </span>
                                                            <select class="form-control" name="company_region" id="company_region" required>
                                                                <option value="Central">{{ __('Central') }}</option>
                                                                <option value="Eastern">{{ __('Eastern') }}</option>
                                                                <option value="Southern">{{ __('Southern') }}</option>
                                                                <option value="Northern">{{ __('Northern') }}</option>
                                                                <option value="Western">{{ __('Western') }}</option>
                                                                <option value="AlQassim">{{ __('Al Qassim') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- user form from here. . -->


                                                <div class="col-12" id="personal_fields">
                                                    <h5 class="section-header mb-3">
                                                        <i class="fas fa-user-circle me-2"></i> {{ __('Personal Account Details') }}
                                                    </h5>
                                                    
                                                    <div class="col-12 form-group mt-3">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="gender" id="male" value="male" checked>
                                                            <label class="form-check-label" for="male">{{ __('Male') }}</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                                                            <label class="form-check-label" for="female">{{ __('Female') }}</label>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Full Name -->
                                                    <div class="col-12 form-group">
                                                        <label for="name" class="form-label">{{ __('Full Name') }}</label>
                                                        <div class="input-group mb-3 position-relative">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-user"></i>
                                                            </span>
                                                            <input type="text" class="form-control" name="name" id="name" 
                                                                placeholder="{{ __('Enter your full name') }}" minlength="3" maxlength="50" required>
                                                            <div class="invalid-feedback">{{ __('Name must be 3-50 characters') }}</div>
                                                        </div>
                                                    </div>

                                                    <!-- Mobile -->
                                                    <div class="col-12 form-group">
                                                        <label for="mobile" class="form-label">{{ __('Mobile Number') }}</label>
                                                        <div class="input-group mb-3 position-relative">
                                                            <span class="input-group-text flag-selector">
                                                                <img src="{{ asset('theme_files/imgs/saudi-flag-icon.png') }}" alt="Saudi Flag" class="saudi-flag">
                                                            </span>
                                                            <input type="tel" class="form-control phone-input" name="mobile" id="mobile" 
                                                                placeholder="{{ __('05XXXXXXXX') }}" required pattern="^5[0-9]{8}$">
                                                            <div class="invalid-feedback">{{ __('Mobile must start with 5 and be exactly 9 digits') }}</div>
                                                        </div>
                                                    </div>

                                                    <!-- Email (optional) -->
                                                    <div class="col-12 form-group">
                                                        <label for="email" class="form-label">{{ __('Email (optional)') }}</label>
                                                        <div class="input-group mb-3 position-relative">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-envelope"></i>
                                                            </span>
                                                            <input type="email" class="form-control" name="email" id="email" 
                                                                placeholder="{{ __('your@email.com') }}">
                                                            <div class="invalid-feedback">{{ __('Please enter a valid email address') }}</div>
                                                        </div>
                                                    </div>

                                                    <!-- Region -->
                                                    <div class="col-12 form-group">
                                                        <label for="region" class="form-label">{{ __('Region') }}</label>
                                                        <div class="input-group mb-3">
                                                            <span class="input-group-text">
                                                                <i class="fas fa-map-marker-alt"></i>
                                                            </span>
                                                            <select class="form-control" name="region" id="region" required>
                                                                <option value="Central">{{ __('Central') }}</option>
                                                                <option value="Eastern">{{ __('Eastern') }}</option>
                                                                <option value="Southern">{{ __('Southern') }}</option>
                                                                <option value="Northern">{{ __('Northern') }}</option>
                                                                <option value="Western">{{ __('Western') }}</option>
                                                                <option value="AlQassim">{{ __('Al Qassim') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- user form from here. . END -->

                                                <!-- Terms and Conditions Checkbox -->
                                                <div class="col-12 form-group mt-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="terms_agree" name="terms_agree" required>
                                                        <label class="form-check-label" for="terms_agree">
                                                            {{ __('I agree to the') }} <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">{{ __('Terms and Conditions') }}</a>
                                                        </label>
                                                        <div class="invalid-feedback">
                                                            {{ __('You must agree to the Terms and Conditions') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-12 form-group mt-4">
                                                    <button type="submit" class="button button-small button-3d button-blue m-0 w-100" 
                                                            id="register-form-submit" name="register-form-submit" value="register" disabled>
                                                        <i class="fas fa-user-plus me-2"></i> {{ __('Create Account') }}
                                                    </button>
                                                    <div class="progress mt-2 d-none" id="form-progress">
                                                        <div class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                    </div>

                                    </p>
                                </div>


                            </div>
                            <div class="col-1"></div>
                        </div>
                        <p> &nbsp;</p>
                        <p> &nbsp;</p>
                        <p> &nbsp;</p>   <p> &nbsp;</p>
                        <p> &nbsp;</p>
                    </div>
                    <div class=" col-sm-7 col-7 login-rollout-bg" style="position: relative; min-height: 1200px;">

                        <div class="login-bg" style="position: absolute;">
                        </div>
                        <div class=" col-12 service-list-parent">
                            <marquee behavior="scroll" direction="up" scrollamount="10" onmouseout="start()" onmouseover="stop()" >


                                <ul class=" ">

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-gas-pump"></i>
                    </span>
                                        <br> {{ __('Fuel') }}
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-wrench"></i>
                    </span>
                                        <br> {{ __('Maintenance Center') }}

                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-utensils"></i>
                    </span>
                                        <br> {{ __('Restaurants') }}
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-store"></i>
                    </span>
                                        <br> {{ __('Supermarket') }}
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-car-side"></i>
                    </span>
                                        <br> {{ __('Automotive Electrical') }} <br>{{ __('Maintenance') }}

                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-oil-can"></i>
                    </span>
                                        <br> {{ __('Oil Change') }}

                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-mosque"></i>
                    </span>
                                        <br> {{ __('Mosque') }}
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-car-alt"></i>
                    </span>
                                        <br> {{ __('Tire Sales and Repair') }}
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-restroom"></i>
                    </span>
                                        <br> {{ __('Public Toilets') }}

                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-child"></i>
                    </span>
                                        <br> {{ __('Kids Area') }}
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-car-side"></i>
                    </span>
                                        <br> {{ __('Car Wash') }}
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-coffee"></i>
                    </span>
                                        <br> {{ __('Coffee Kiosks') }}

                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-money-check"></i>
                    </span>
                                        <br> {{ __('ATM') }}
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-hand-holding-medical"></i>
                    </span>
                                        <br> {{ __('Pharmacy') }}
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-car-alt"></i>
                    </span>
                                        <br> {{ __('Free Services') }} <br>( {{ __('Water + air') }} )
                                    </li>


                                </ul>
                            </marquee>
                            <p> &nbsp;</p>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </section>
</div>

<!-- Combined OTP Modal (Place this in your Blade template, e.g. resources/views/auth/index.blade.php) -->
<!-- Updated Fancy OTP Modal Popup -->


<!-- OTP Modal Popup -->
<!-- OTP Modal (Will not close if clicked outside) -->
<!-- Updated Fancy OTP Modal Popup -->
{{--<div class="modal fade" id="otpModal" tabindex="-1" role="dialog" aria-labelledby="otpModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">--}}
<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true"
     data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border: none; border-radius: 15px; overflow: hidden;">
            <!-- Modal Header with Example Image -->
            <div class="modal-header p-0 border-0">
                {{--                <img src="https://via.placeholder.com/600x200?text=OTP+Verification" alt="OTP Verification" class="img-fluid w-100">--}}
                <button type="button" class="close m-1" data-dismiss="modal" aria-label="Close" id="closeOtpModal">
                    <span aria-hidden="true">X</span>
                </button>
            </div>
            <!-- Modal Body -->
            <div class="modal-body text-center p-4">
                <h4 class="mb-3">{{ __('Verify Your OTP') }}</h4>
                <p class="mb-4">{{ __('Enter the 4-digit code sent to your registered mobile.') }}</p>
                <!-- OTP Input Fields -->
                <div class="d-flex justify-content-center mb-3">
                    <input type="text" class="otp-input mx-1" maxlength="1" id="otp_digit_1" style="width: 60px; height: 60px; font-size: 24px; text-align: center;">
                    <input type="text" class="otp-input mx-1" maxlength="1" id="otp_digit_2" style="width: 60px; height: 60px; font-size: 24px; text-align: center;">
                    <input type="text" class="otp-input mx-1" maxlength="1" id="otp_digit_3" style="width: 60px; height: 60px; font-size: 24px; text-align: center;">
                    <input type="text" class="otp-input mx-1" maxlength="1" id="otp_digit_4" style="width: 60px; height: 60px; font-size: 24px; text-align: center;">
                </div>
                <!-- Resend / Timer Message -->
                <div id="resendSection" class="mb-3">
                    <small class="text-danger" id="resendMessage">{{ __('Wait') }} <span id="countdown">52</span> {{ __('seconds before resending!') }}</small>
                </div>
                <p class="mb-2">
                    {{ __('Didn\'t receive a code?') }}
                    <a href="#" id="resendOtpLink" class="text-primary">{{ __('Resend') }}</a>
                    {{ __('OR') }}
                    <a href="#" id="sendToEmailLink" class="text-primary">{{ __('Send To Email') }}</a>
                </p>
                <!-- Verify Button -->
                <button type="button" class="btn btn-success btn-block mt-3 w-100 LoadingUi" id="verifyOtpBtn">{{ __('Verify OTP') }}</button>
            </div>
        </div>
    </div>
</div>
{{------------------------------------------------------------------------------------------------------}}

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">{{ __('Terms and Conditions') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>{{ __('Joil Yaseeir Service Agreement') }}</h5>
                <p class="mb-3">{{ __('Last Updated') }}: {{ date('F d, Y') }}</p>
                
                <h6>{{ __('Acceptance of Terms') }}</h6>
                <embed src="{{ asset('images/contract-bbb.pdf') }}" type="application/pdf" width="100%" height="500px" style="overflow: scroll;" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" id="agreeTerms">{{ __('I Agree') }}</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
            </div>
        </div>
    </div>
</div>
{{------------------------------------------------------------------------------------------------------}}

    <script src="{{ asset('theme_files/js/jquery.js') }}"></script>
<script src="{{ asset('theme_files/js/plugins.min.js') }}"></script>
    <script src="{{ asset('theme_files/js/functions.bundle.js') }}"></script>

    <script src="{{ asset('theme_files/js/core.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/additional-methods.min.js"></script>

<script src="{{ asset('theme_files/js/js-loading-overlay.min.js') }}"></script>
 
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>




    <script>
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); //

        let countdownValue = 52;
        let countdownInterval;
        let currentMobile = ''; // Global variable to store the user's mobile number
        let otpVerified = false; // New flag to track OTP verification status
        
        // Initialize SweetAlert2 Toast configuration
        const Toast = Swal.mixin({
            toast: true,
            @if(app()->getLocale() == 'ar')
            position: 'top-start',
            @else
            position: 'top-end',
            @endif
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        
        // Configure loading overlay default settings
        JsLoadingOverlay.setOptions({
            overlayBackgroundColor: 'rgba(0, 0, 0, 0.5)',
            overlayOpacity: 0.7,
            spinnerIcon: 'ball-spin',
            spinnerColor: '#006C35',
            spinnerSize: '3x',
            overlayIDName: 'overlay',
            spinnerIDName: 'spinner',
        });
        
        jQuery(document).ready(function () {
            // Initialize tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
            
            // Replace mobile icons with Saudi flag
            $('.input-group-text i.fas.fa-mobile-alt').removeClass('fa-mobile-alt').addClass('fa-flag');
            
            // Replace plain flag with Saudi flag SVG
            $('.input-group-text:contains("SA")').each(function() {
                // Create Saudi flag SVG
                const saudiFlag = `
                <span class="saudi-flag-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 42 28">
                        <rect width="42" height="28" fill="#006c35"/>
                        <g fill="#fff">
                            <path d="M13,7.2c-0.7,0-1.3,0.6-1.3,1.3s0.6,1.3,1.3,1.3s1.3-0.6,1.3-1.3S13.7,7.2,13,7.2z M29,7.2c-0.7,0-1.3,0.6-1.3,1.3 s0.6,1.3,1.3,1.3s1.3-0.6,1.3-1.3S29.7,7.2,29,7.2z"/>
                            <path d="M11.8,14.5c0.4,0.5,1,0.8,1.7,0.8c0.2,0,0.5,0,0.7-0.1c0.5-0.2,1-0.6,1.2-1.2c0.2-0.5,0.2-1,0-1.5 c-0.2-0.5-0.6-1-1.2-1.2c-0.5-0.2-1-0.2-1.5,0c-0.5,0.2-1,0.6-1.2,1.2c-0.2,0.5-0.2,1,0,1.5C11.6,14.1,11.7,14.3,11.8,14.5z"/>
                            <path d="M30.2,14.5c0.4,0.5,1,0.8,1.7,0.8c0.2,0,0.5,0,0.7-0.1c0.5-0.2,1-0.6,1.2-1.2c0.2-0.5,0.2-1,0-1.5 c-0.2-0.5-0.6-1-1.2-1.2c-0.5-0.2-1-0.2-1.5,0c-0.5,0.2-1,0.6-1.2,1.2c-0.2,0.5-0.2,1,0,1.5C30,14.1,30.1,14.3,30.2,14.5z"/>
                            <path d="M12,5.1h18c0.6,0,1,0.4,1,1v10c0,3.3-2.7,6-6,6h-8c-3.3,0-6-2.7-6-6v-10C11,5.6,11.4,5.1,12,5.1z M30,7.2h-18v9 c0,2.2,1.8,4,4,4h10c2.2,0,4-1.8,4-4V7.2z"/>
                            <path d="M26.8,17.3l-1.1-0.5l-1.1,0.5c-0.1,0-0.2,0-0.2-0.1l0.2-1.2L24,15.4c-0.1-0.1-0.1-0.2,0-0.3l1.1-0.5l0.2-1.2 c0-0.1,0.1-0.2,0.3-0.1l1.1,0.5l1.1-0.5c0.1,0,0.2,0,0.2,0.1l0.2,1.2l1.1,0.5c0.1,0.1,0.1,0.2,0,0.3l-1.1,0.5l-0.2,1.2 c0,0.1-0.1,0.2-0.3,0.1L26.8,17.3z"/>
                            <path d="M18.8,17.3l-1.1-0.5l-1.1,0.5c-0.1,0-0.2,0-0.2-0.1l0.2-1.2L16,15.4c-0.1-0.1-0.1-0.2,0-0.3l1.1-0.5l0.2-1.2 c0-0.1,0.1-0.2,0.3-0.1l1.1,0.5l1.1-0.5c0.1,0,0.2,0,0.2,0.1l0.2,1.2l1.1,0.5c0.1,0.1,0.1,0.2,0,0.3l-1.1,0.5l-0.2,1.2 c0,0.1-0.1,0.2-0.3,0.1L18.8,17.3z"/>
                        </g>
                    </svg>
                </span>
                SA`;
                
                // Replace content with Saudi flag
                $(this).html(saudiFlag);
            });
            
            $('.LoadingUi').on('click', function (event) {
                JsLoadingOverlay.show(JsLoadingOverlay);
            });

            // Hide the loading overlay.
            JsLoadingOverlay.hide();
            
            // Initially disable company fields
            $('#company_fields').slideUp(function(){
                $(this).find('input, select, textarea').attr('disabled', true);
            });

            // Toggle between personal and company fields based on registration type radio buttons
            $('input[name="registration_type"]').change(function(){
                if ($(this).val() === 'company') {
                    $('#personal_fields').slideUp(function(){
                        $(this).find('input, select, textarea').attr('disabled', true);
                    });
                    $('#company_fields').slideDown(function(){
                        $(this).find('input, select, textarea').removeAttr('disabled');
                    });
                } else {
                    $('#company_fields').slideUp(function(){
                        $(this).find('input, select, textarea').attr('disabled', true);
                    });
                    $('#personal_fields').slideDown(function(){
                        $(this).find('input, select, textarea').removeAttr('disabled');
                    });
                }
                validateForm(); // Re-validate the form when switching
            });
            
            // Initialize form validation
            $.validator.setDefaults({
                errorElement: 'div',
                errorClass: 'invalid-feedback',
                highlight: function(element) {
                    $(element).addClass('is-invalid').removeClass('is-valid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid').addClass('is-valid');
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.input-group'));
                },
                focusInvalid: false, // Prevent automatic focusing on the first invalid element
                focusCleanup: true,   // Remove error classes on field focus
                onkeyup: false,       // Disable validation on keyup (no real-time validation)
                onfocusout: true      // Enable validation when user leaves the field
            });
            
            // Login form validation
            $('#loginForm').validate({
                focusInvalid: false, // Disable focus jumping
                rules: {
                    mobile: {
                        required: true,
                        pattern: /^05[0-9]{8}$/
                    }
                },
                                        messages: {
                            mobile: {
                                required: "{{ __('Mobile number is required') }}",
                                pattern: "{{ __('Mobile must start with 05 and be exactly 10 digits') }}"
                            }
                        },
                submitHandler: function(form) {
                    const mobile = $('#login_mobile').val();
                    otpContext = 'login';
                    otpData = { mobile: mobile };
                    
                    // Show loading
                    JsLoadingOverlay.show();
                    
                    axios.post('/api/login', { mobile: mobile })
                        .then(response => {
                            JsLoadingOverlay.hide();
                            Toast.fire({
                                icon: 'success',
                                title: '{{ __('OTP sent successfully!') }}'
                            });
                            $('#otpModal').modal('show');
                        })
                        .catch(error => {
                            JsLoadingOverlay.hide();
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('Login Failed') }}',
                                text: error.response?.data?.message || '{{ __('An error occurred during login') }}',
                                confirmButtonText: '{{ __('Try Again') }}'
                            });
                        });
                }
            });
            
            // Registration form validation
            $('#registerForm').validate({
                focusInvalid: false, // Disable focus jumping
                rules: {
                    // Personal account rules
                    name: {
                        required: function() {
                            return $('input[name="registration_type"]:checked').val() === 'personal';
                        },
                        minlength: 3,
                        maxlength: 50
                    },
                    mobile: {
                        required: true,
                        pattern: /^05[0-9]{8}$/
                    },
                    email: {
                        email: true,
                        required: function() {
                            return $('input[name="registration_type"]:checked').val() === 'company';
                        }
                    },
                    
                    // Company account rules
                    company_name: {
                        required: function() {
                            return $('input[name="registration_type"]:checked').val() === 'company';
                        }
                    },
                    cr_number: {
                        required: function() {
                            return $('input[name="registration_type"]:checked').val() === 'company';
                        },
                        pattern: /^[12][0-9]{9}$/
                    },
                    vat_number: {
                        required: function() {
                            return $('input[name="registration_type"]:checked').val() === 'company';
                        },
                        pattern: /^3[0-9]{13}3$/
                    },
                    
                    // Terms & Conditions
                    terms_agree: {
                        required: true
                    }
                },
                                        messages: {
                            name: {
                                required: "{{ __('Full name is required') }}",
                                minlength: "{{ __('Name must be at least 3 characters') }}",
                                maxlength: "{{ __('Name must not exceed 50 characters') }}"
                            },
                            mobile: {
                                required: "{{ __('Mobile number is required') }}",
                                pattern: "{{ __('Mobile must start with 05 and be exactly 10 digits') }}"
                            },
                            email: {
                                required: "{{ __('Email is required for company accounts') }}",
                                email: "{{ __('Please enter a valid email address') }}"
                            },
                            company_name: {
                                required: "{{ __('Company name is required') }}"
                            },
                            cr_number: {
                                required: "{{ __('CR number is required') }}",
                                pattern: "{{ __('CR Number must be exactly 10 digits and start with 1 or 2') }}"
                            },
                            vat_number: {
                                required: "{{ __('VAT number is required') }}",
                                pattern: "{{ __('VAT Number must be exactly 15 digits, starting and ending with 3') }}"
                            },
                            terms_agree: {
                                required: "{{ __('You must agree to the Terms and Conditions') }}"
                            }
                        },
                invalidHandler: function(event, validator) {
                    // Don't focus on invalid fields, just show a notification
                    if (validator.errorList.length > 0) {
                        // Display errors without focusing
                        Toast.fire({
                            icon: 'error',
                            title: '{{ __('Please fix the highlighted errors') }}'
                        });
                    }
                },
                submitHandler: function(form) {
                    // Check if OTP has been verified and T&C are checked
                    if (!otpVerified) {
                        // If OTP is not verified, trigger OTP sending
                        const formData = new FormData(form);
                        const data = {};
                        formData.forEach((value, key) => { data[key] = value; });
                        
                        // Only send OTP if T&C are checked
                        if (!$('#terms_agree').is(':checked')) {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('Terms and Conditions') }}',
                                text: '{{ __('You must agree to the Terms and Conditions to continue') }}'
                            });
                            return;
                        }
                        
                        otpContext = 'registration';
                        otpData = {};
                        
                        // Show loading and progress
                        JsLoadingOverlay.show();
                        $('#form-progress').removeClass('d-none');
                        
                        // Simulate progress
                        let progress = 0;
                        const progressInterval = setInterval(() => {
                            progress += 10;
                            if (progress <= 90) {
                                $('#form-progress .progress-bar').css('width', progress + '%');
                            }
                        }, 300);
                        
                        axios.post('/api/register/otp', data)
                            .then(response => {
                                clearInterval(progressInterval);
                                $('#form-progress .progress-bar').css('width', '100%');
                                
                                setTimeout(() => {
                                    JsLoadingOverlay.hide();
                                    $('#form-progress').addClass('d-none');
                                    
                                    otpData.temp_token = response.data.temp_token;
                                    
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('OTP Sent') }}',
                                        text: '{{ __('We\'ve sent you an OTP. Please verify your account.') }}',
                                        confirmButtonText: '{{ __('Verify Now') }}'
                                    }).then(() => {
                                        $('#otpModal').modal('show');
                                    });
                                }, 500);
                            })
                            .catch(error => {
                                clearInterval(progressInterval);
                                JsLoadingOverlay.hide();
                                $('#form-progress').addClass('d-none');
                                
                                let errorMessage = '{{ __('An error occurred during registration') }}';
                                if (error.response && error.response.data) {
                                    if (error.response.data.errors) {
                                        // Format validation errors
                                        const errorMessages = [];
                                        for (const field in error.response.data.errors) {
                                            errorMessages.push(error.response.data.errors[field].join('<br>'));
                                        }
                                        errorMessage = errorMessages.join('<br>');
                                    } else if (error.response.data.message) {
                                        errorMessage = error.response.data.message;
                                    }
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Registration Failed',
                                    html: errorMessage,
                                    confirmButtonText: 'Try Again'
                                });
                            });
                    } else {
                        // If OTP is verified, proceed with registration
                        // Submit final registration with verified OTP status
                        const formData = new FormData(form);
                        const data = {};
                        formData.forEach((value, key) => { data[key] = value; });
                        data.temp_token = otpData.temp_token;
                        data.otp_verified = true;
                        
                        // Show loading
                        JsLoadingOverlay.show();
                        
                        // Complete registration
                        axios.post('/api/register', data)
                            .then(response => {
                                JsLoadingOverlay.hide();
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Registration Successful',
                                    text: 'Your account has been created successfully!',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href = "{{ route('login') }}";
                                });
                            })
                            .catch(error => {
                                JsLoadingOverlay.hide();
                                
                                let errorMessage = 'An error occurred during registration';
                                if (error.response && error.response.data) {
                                    if (error.response.data.errors) {
                                        // Format validation errors
                                        const errorMessages = [];
                                        for (const field in error.response.data.errors) {
                                            errorMessages.push(error.response.data.errors[field].join('<br>'));
                                        }
                                        errorMessage = errorMessages.join('<br>');
                                    } else if (error.response.data.message) {
                                        errorMessage = error.response.data.message;
                                    }
                                }
                                
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Registration Failed',
                                    html: errorMessage,
                                    confirmButtonText: 'Try Again'
                                });
                            });
                    }
                }
            });
            
            // Handle agreeing to terms from the modal
            $('#agreeTerms').on('click', function() {
                $('#terms_agree').prop('checked', true).trigger('change');
                validateForm();
            });
            
            // Enable/disable submit button based on form validity without changing focus
            function validateForm() {
                // Store current focused element
                var currentFocus = document.activeElement;
                
                // Validate form without changing focus
                var isValid = $('#registerForm').valid();
                
                // Update button state based on validation
                $('#register-form-submit').prop('disabled', !isValid);
                
                // Make sure focus stays where it was
                if (currentFocus && currentFocus.id) {
                    setTimeout(function() {
                        $('#' + currentFocus.id).focus();
                    }, 10);
                }
            }
            
            // Form validation on blur (when user leaves the field) - much better UX
            $('#registerForm input, #registerForm select').on('blur', function(e) {
                // Only validate the current field when user leaves it
                $(this).valid();
                validateForm();
            });
            
            // For checkboxes and radio buttons, validate on change
            $('#registerForm input[type="checkbox"], #registerForm input[type="radio"]').on('change', function(e) {
                validateForm();
            });
            
            // IMPORTANT: ONLY handle OTP inputs in the OTP modal
            // All other inputs should have NO auto-focus behavior
            
            // Completely remove all auto-focus behaviors first
            $('.otp-input').off('input');
            $('.otp-input').off('keydown');
            $('#registerForm input').off('input.autoFocus');
            $('#registerForm input').off('keydown.autoFocus');
            $('#registerForm select').off('input.autoFocus');
            $('#registerForm select').off('keydown.autoFocus');
            
            // Disable default browser behaviors that might cause focus jumping
            $('#registerForm input, #registerForm select').on('keydown', function(e) {
                // Prevent tab index navigation from triggering any auto-focus scripts
                if (e.key === 'Tab') {
                    // Let the default tab behavior work, but prevent any other scripts from running
                    e.stopPropagation();
                }
            });
            
            // Only add the auto-focus behavior back to OTP modal inputs AFTER isolating them
            $('#otpModal .otp-input').on('input.otpOnly', function() {
                // Only run for OTP modal inputs
                if ($(this).closest('#otpModal').length) {
                    if ($(this).val().length === 1) {
                        $(this).next('#otpModal .otp-input').focus();
                    }
                }
            });
            
            // Only add backspace behavior to OTP modal inputs
            $('#otpModal .otp-input').on('keydown.otpOnly', function(e) {
                // Only run for OTP modal inputs
                if ($(this).closest('#otpModal').length) {
                    // If backspace is pressed and the field is empty
                    if (e.key === 'Backspace' && $(this).val() === '') {
                        // Focus the previous input
                        $(this).prev('#otpModal .otp-input').focus();
                    }
                }
            });

            // Close modal button
            $('#closeOtpModal').on('click', function() {
                // If you want to allow manual close, remove "data-backdrop='static'"
                // or handle logic here (like resetting fields).
                $('#otpModal').modal('hide');
            });

            // Start or restart countdown when modal is shown
            $('#otpModal').on('shown.bs.modal', function () {
                startCountdown();
                // Focus the first OTP input
                $('#otp_digit_1').focus();
            });

            // When the Verify OTP button is clicked:
            $('#verifyOtpBtn').on('click', function() {
                const otp = $('#otp_digit_1').val() +
                    $('#otp_digit_2').val() +
                    $('#otp_digit_3').val() +
                    $('#otp_digit_4').val();

                if (otp.length !== 4) {
                    Toast.fire({
                        icon: 'warning',
                        title: '{{ __('Please enter a 4-digit OTP') }}'
                    });
                    return;
                }
               
                let payload = { otp: otp };
                let endpoint = '';

                if (otpContext === 'login') {
                    JsLoadingOverlay.show();
                    payload.mobile = otpData.mobile;
                    endpoint = '/api/verify-otp';
                } else if (otpContext === 'registration') {
                    JsLoadingOverlay.show();
                    payload.temp_token = otpData.temp_token;
                    endpoint = '/api/register/verify-otp';
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: 'OTP context is not set'
                    });
                    return;
                }
                
                // Show loading
                //JsLoadingOverlay.show();
                
                axios.post(endpoint, payload)
                    .then(response => {
                        JsLoadingOverlay.hide();
                        $('#otpModal').modal('hide');
                        
                        if (otpContext === 'registration') {
                            // Mark OTP as verified and update button text
                            otpVerified = true;
                            $('#register-form-submit').html('<i class="fas fa-check-circle me-2"></i> Complete Registration');
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'OTP Verified',
                                text: 'Your OTP has been verified. Click "Complete Registration" to finish.',
                                confirmButtonText: 'OK'
                            });
                             window.location.href = "{{ route('home') }}";
                             return null
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'Verification Successful',
                                text: response.data?.message || 'Your account has been verified successfully!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                JsLoadingOverlay.hide();
                                if(otpContext === 'login'){
                                    window.location.href = "{{ route('home') }}";
                                          return null
                                }
                            });
                        }
                    })
                    .catch(error => {
                        JsLoadingOverlay.hide();
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Verification Failed',
                            text: error.response?.data?.message || 'OTP verification failed. Please try again.',
                            confirmButtonText: 'Try Again'
                        });
                        JsLoadingOverlay.hide();
                    });
                    
            });
            
            // Resend button click
            $('#resendOtpLink').on('click', function(e) {
                e.preventDefault();
                
                if (countdownValue > 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: `Please wait ${countdownValue} seconds before requesting a new code`
                    });
                    return;
                }
                
                // Reset and start countdown
                resetCountdown();
                
                let endpoint = '';
                let payload = {};
                
                if (otpContext === 'login') {
                    endpoint = '/api/login';
                    payload = { mobile: otpData.mobile };
                } else if (otpContext === 'registration') {
                    endpoint = '/api/register/resend-otp';
                    payload = { temp_token: otpData.temp_token };
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: 'OTP context is not set'
                    });
                    return;
                }
                
                // Show loading
                JsLoadingOverlay.show();
                
                axios.post(endpoint, payload)
                    .then(response => {
                        JsLoadingOverlay.hide();
                        Toast.fire({
                            icon: 'success',
                            title: '{{ __('OTP resent successfully') }}'
                        });
                    })
                    .catch(error => {
                        JsLoadingOverlay.hide();
                        Toast.fire({
                            icon: 'error',
                            title: error.response?.data?.message || '{{ __('Failed to resend OTP') }}'
                        });
                    });
            });
            
            // Send To Email button click
            $('#sendToEmailLink').on('click', function(e) {
                e.preventDefault();
                
                if (countdownValue > 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: `Please wait ${countdownValue} seconds before requesting a new code`
                    });
                    return;
                }
                
                // Only available for login context
                if (otpContext !== 'login') {
                    Toast.fire({
                        icon: 'error',
                        title: 'Email OTP is only available for login'
                    });
                    return;
                }
                
                // Reset and start countdown
                resetCountdown();
                
                // Show loading
                JsLoadingOverlay.show();
                
                axios.post('/api/login/send-email-otp', { mobile: otpData.mobile })
                    .then(response => {
                        JsLoadingOverlay.hide();
                        Toast.fire({
                            icon: 'success',
                            title: 'OTP sent to your email successfully'
                        });
                    })
                    .catch(error => {
                        JsLoadingOverlay.hide();
                        let errorMessage = error.response?.data?.message || 'Failed to send OTP to email';
                        if (errorMessage === 'User does not have an email address') {
                            errorMessage = 'No email address found for this account';
                        }
                        Toast.fire({
                            icon: 'error',
                            title: errorMessage
                        });
                    });
            });
        });

        function startCountdown() {
            // Reset input fields
            $('.otp-input').val('');
            $('#countdown').text(countdownValue);
            $('#resendMessage').show();

            // Clear any existing interval
            clearInterval(countdownInterval);

            countdownInterval = setInterval(() => {
                countdownValue--;
                $('#countdown').text(countdownValue);

                if (countdownValue <= 0) {
                    clearInterval(countdownInterval);
                    $('#resendMessage').text('{{ __('You can resend the code now!') }}');
                }
            }, 1000);
        }

        function resetCountdown() {
            // Reset the countdown value to 52
            countdownValue = 52;
            $('#resendMessage').text('{{ __('Wait 52 seconds before resending!') }}');
            startCountdown();
        }
    </script>

    </body>
    </html>

    {{-- window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content'); --}}
