<!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="author" content="Usman Developer at Aljeri">
    <meta name="description" content="Complete cross platform system">
    <meta name="robots" content="Aljeri FuelApp - JOIL order system" />
    <meta property="og:title" content="Aljeri FuelApp - JOIL System" />
    <meta property="og:description" content="Aljeri FuelApp - JOIL System" />
    <meta property="og:image" content="Aljeri FuelApp - JOIL System" />
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="width=device-width, maximum-scale=5, initial-scale=1, user-scalable=0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Rubik:wght@400;600&family=Lora:ital@0;1&display=swap" rel="stylesheet">

        {{-- @if (isRTL)
        {
            <link rel="stylesheet" href="./theme_files/css/style-rtl.css">
        }
        else
        { -->--}}
        <link rel="stylesheet" href="{{ asset('theme_files/css/style.css') }}">
        <!-- } -->

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

        <!-- <link rel="stylesheet" href="{{ asset('theme_files/css/post-3583.css') }}"> -->

        <title>FuelApp by JOIL </title>
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
        </style>
</head>
<!-- side-header -->
<body class="stretched  ltr  page-transition" data-loader="4" data-animation-in="fadeIn" data-speed-in="1500" data-animation-out="fadeOut" data-speed-out="800" style="background-color:#FFFF">
<div id="wrapper">


    <section id="content" >
        <section class="content-wrap" >
            <div class="container-fluid">
                <div class="row">
                    <div class=" col-sm-5 col-5 ">
                        <div class="row">
                            <div class="col-sm-12 mt-1"><div class="btn-group">
                                    <button type="button" class="btn   btn-sm dropdown-toggle  button button-border button-rounded button-fill fill-from-right button-blue" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span> <i class="bi-globe2"></i>   Language</span>
                                    </button>
                                    <div class="dropdown-menu" style="">
                                        <a class="dropdown-item" href="#"> <i class="ti ti-arabic-lang nocolor m-0"></i> Arabic</a>
                                        <a class="dropdown-item" href="#"><i class="ti ti-us-lang nocolor m-0"></i> English</a>
                                    </div>
                                </div> </div>
                            <!-- language buttons -->
                            <div class="col-sm-12 mt-2 text-center "> <img src="{{ asset('theme_files/imgs/yaseeir-smal-new-logo6.png') }}" class="img-responsive" ></div>
                            <!-- logo -->

                        </div>
                        <div class="row  mt-5">
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
                                                Login to your Account
                                            </div>
                                        </div>

                                        <div class="accordion-content" style="display: block;">
                                            <form id="loginForm">
                                                <div class="col-12 form-group mt-4 mb-4">

                                                    <div class="input-group w-80 mx-auto">
                                <span class="input-group-text">
                                    <!-- <i class="fa-solid fa-mobile-alt"></i>  -->
                                    <i class="ti ti-arabic nocolor m-0"></i> SA   +966
                                </span>
                                                        <input type="text" id="login_mobile" name="mobile" class="form-control " value="" placeholder="Enter Mobile.Ex: +966 5XXXXXXXX" required>
                                                    </div>
                                                    <!-- <input type="text" id="login-form-username" name="login-form-username" value="" class="form-control"> -->
                                                </div>


                                                <div class="col-12 form-group">
                                                    <div class="d-flex justify-content-between">
                                                        <button class="button button-small button-3d button-blue m-0 LoadingUi"   type="submit"
                                                                 > <i class="fa fa-sign-in-alt"></i> Login</button>
                                                        <!-- <a href="#">Forgot Password?</a>onclick="sendSms()"  -->
                                                    </div>
                                                </div>
                                                <!-- sms otp -->


                                                <!-- sms otp -->
                                        </form>

                                        </div>

                                        <div class="accordion-header mt-4 ">
                                            <div class="accordion-icon">
                                                <i class="accordion-closed bi-person"></i>
                                                <i class="accordion-open bi-check-circle-fill"></i>
                                            </div>
                                            <div class="accordion-title">
                                                New Signup? Register for an Account
                                            </div>
                                        </div>
                                        <div class="accordion-content text-dark" style="display: inline-block;">
                                            <form id="registerForm" name="registerForm" class="row mb-0">
                                                @csrf
                                                <div class="col-12 text-center form-group mt-4">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="registration_type" id="reg_personal" value="personal" checked>{{-- onclick="showPersonal()"--}}
                                                        <label class="form-check-label" for="inlineRadio1">Personal Account</label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="registration_type" id="reg_company" value="company" >{{--onclick="showCompany()"--}}
                                                        <label class="form-check-label" for="inlineRadio2">Company Account</label>
                                                    </div>
                                                </div>

                                                <!-- Private -->
                                                <!-- Semi Govt -->
                                                <!-- Govt -->
                                                <div class="col-sm-12" id="company_fields">

                                                    <div class="col-12 form-group mt-4 text-left " >
                                                        <input type="radio" class="btn-check" name="company_type" id="private" value="private" autocomplete="off" checked>
                                                        <label class="btn fcheck" for="option5">Private</label>

                                                        <input type="radio" class="btn-check" name="company_type" id="semiGovt" value="semi Govt." autocomplete="off">
                                                        <label class="btn fcheck" for="option6">Semi Govt</label>


                                                        <input type="radio" class="btn-check" name="company_type" id="govt" value="Govt" autocomplete="off">
                                                        <label class="btn fcheck" for="option8">Govt</label>
                                                    </div>

                                                    <div class="col-12 form-group  ">
                                                        <div class="input-group mb-1">
                                <span class="input-group-text" id="basic-addon1"><i class="fa-regular fa-building"></i>
                                </span>
                                                            <input type="text" name="company_name" id="company_name" class="form-control" placeholder=" Company Name " aria-label="Username" aria-describedby="basic-addon1">
                                                        </div>
                                                    </div>

                                                    <div class="col-12 form-group  ">
                                                        <div class="input-group mb-1">
                                <span class="input-group-text" id="basic-addon2"><i class="fa  fa-square-envelope"></i>
                                </span>
                                                            <input type="text" class="form-control" placeholder=" Email" name="email" id="email_company" aria-label="Username" aria-describedby="basic-addon1">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group  ">
                                                        <div class="input-group w-80 mx-auto">
                                <span class="input-group-text">
                                    <i class="ti ti-arabic nocolor m-0"></i> SA   +966
                                </span>
                                                            <input type="text"  name="mobile" id="mobile_company" class="form-control " value="" placeholder="Enter Mobile. Ex: +966 5XXXXXXXX">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group  ">
                                                        <div class="input-group mb-1">
                                <span class="input-group-text" id="basic-C-R"><i class="fa-regular fa-file-lines"></i>
                                </span>
                                                            <input type="text" class="form-control" name="cr_number" id="cr_number" placeholder=" C.R Number" aria-label="Username" aria-describedby="basic-addon1">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group  ">
                                                        <div class="input-group mb-1">
                                <span class="input-group-text" id="basic-VAT"><i class="fa-regular fa-file-lines"></i>
                                </span>
                                                            <input type="text" class="form-control" name="vat_number" id="vat_number" placeholder=" VAT Number" aria-label="Username" aria-describedby="basic-addon1">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group  ">
                                                        <div class="input-group mb-1">
                                <span class="input-group-text" id="basic-street"><i class="fa-solid fa-road"></i>
                                </span>
                                                            <input type="text" class="form-control" placeholder=" Street" aria-label="Username" aria-describedby="basic-addon1">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group  ">
                                                        <div class="input-group mb-1">
                                <span class="input-group-text" id="basic-Building">
                                    <i class="fa fa-building-columns"></i>
                                </span>
                                                            <input type="text" class="form-control" placeholder=" Building No" aria-label="Username"   name="building_number" id="building_number" aria-describedby="basic-addon1">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group  ">
                                                        <div class="input-group mb-1">
                                <span class="input-group-text" id="basic-city"><i class="fa fa-city"></i>
                                </span>
                                                            <input type="text"   name="city" id="city"  class="form-control" placeholder=" City " aria-label="Username" aria-describedby="basic-addon1">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group  ">
                                                        <div class="input-group mb-1">
                                <span class="input-group-text" id="basic-zipcode">
                                    <i class="fa-brands fa-periscope"></i>
                                </span>
                                                            <input type="text"  name="zip_code" id="zip_code"  class="form-control" placeholder=" Zip Code " aria-label="Username" aria-describedby="basic-addon1">
                                                        </div>
                                                    </div>

                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">Region</span>
                                                        </div>
                                                        <select class="form-control required valid"  name="company_region" id="company_region"    aria-invalid="false">
                                                            <option value="Central"> Central </option>
                                                            <option value="Eastern">Eastern</option>
                                                            <option value="Southern">Southern</option>
                                                            <option value="Northern">Northern</option>
                                                            <option value="Western">Western</option>
                                                            <option value="AlQassim"> Al Qassim</option>
                                                        </select>
                                                    </div>

                                                </div>
                                                <!-- user form from here. . -->


                                                <div class="col-12" id="personal_fields" >
                                                    <br>
                                                    <div class="col-12 form-group mt-0">
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="gender" id="male" value="male" checked>
                                                            <label class="form-check-label" for="inlineGdr2">Male</label>
                                                        </div>
                                                        <div class="form-check form-check-inline">
                                                            <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                                                            <label class="form-check-label" for="inlineGdr3">Female</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 form-group  ">
                                                        <div class="input-group mb-1">
                                <span class="input-group-text" id="basic-addon5"><i class="fa-regular fa-user"></i>
                                </span>
                                                            <input type="text" class="form-control" name="name" id="name" placeholder=" Full Name " aria-label="Username" aria-describedby="basic-addon1">
                                                        </div>
                                                    </div>



                                                    <div class="col-12 form-group">

                                                        <div class="input-group w-80 mx-auto">
                                <span class="input-group-text">
                                    <i class="ti ti-arabic nocolor m-0"></i> SA   +966
                                </span>
                                                            <input type="text"   class="form-control "  name="mobile" id="mobile" placeholder="Enter Mobile. Ex: +966 5XXXXXXXX">
                                                        </div>
                                                    </div>


                                                    <div class="input-group mb-1">
                                <span class="input-group-text" id="basic-addon22"><i class="fa  fa-square-envelope"></i>
                                </span>
                                                        <input type="text" class="form-control" name="email" id="email"  placeholder=" Email (optional)" aria-label="Username" aria-describedby="basic-addon1">
                                                    </div>
                                                    </div>

                                                <!-- user form from here. . END -->

                                                <div class="col-12 form-group mt-4">
                                                    <button type="submit" class="button  button-small  button-3d button-blue m-0 LoadingUi" id="register-form-submit" name="register-form-submit" value="register">Register Now</button>
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
                                        <br> Fuel
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-wrench"></i>
                    </span>
                                        <br> Maintenance Center

                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-utensils"></i>
                    </span>
                                        <br> Restaurants
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-store"></i>
                    </span>
                                        <br> Supermarket
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-car-side"></i>
                    </span>
                                        <br> Automotive Electrical <br>Maintenance

                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-oil-can"></i>
                    </span>
                                        <br> Oil Change

                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-mosque"></i>
                    </span>
                                        <br> Mosque
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-car-alt"></i>
                    </span>
                                        <br> Tire Sales and Repair
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-restroom"></i>
                    </span>
                                        <br> Public Toilets

                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-child"></i>
                    </span>
                                        <br> 	Kids Area
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-car-side"></i>
                    </span>
                                        <br> Car Wash
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-coffee"></i>
                    </span>
                                        <br> Coffee Kiosks

                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-money-check"></i>
                    </span>
                                        <br> ATM
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-hand-holding-medical"></i>
                    </span>
                                        <br> Pharmacy
                                    </li>

                                    <li class="list-service">
                    <span class="elementor-icon elementor-animation-">
                    <i aria-hidden="true" class="fas fa-car-alt"></i>
                    </span>
                                        <br> Free Services <br>( Water + air)
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
<!-- OTP Modal (Will not close if clicked outside) -->
<div class="modal fade" id="otpModal" tabindex="-1" role="dialog" aria-labelledby="otpModalLabel" aria-hidden="true"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content p-3">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="otpModalLabel">Verify Your OTP</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" id="closeOtpModal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-4">Enter the 4 digit code sent to your registered Mobile Number / Email</p>

                <!-- OTP Inputs -->
                <div class="d-flex justify-content-center mb-3">
                    <input type="text" class="otp-input mx-1" maxlength="1" id="otp_digit_1">
                    <input type="text" class="otp-input mx-1" maxlength="1" id="otp_digit_2">
                    <input type="text" class="otp-input mx-1" maxlength="1" id="otp_digit_3">
                    <input type="text" class="otp-input mx-1" maxlength="1" id="otp_digit_4">
                </div>

                <!-- Resend / Timer Message -->
                <div id="resendSection" class="mb-3">
                    <small class="text-danger" id="resendMessage">Wait <span id="countdown">52</span> seconds before resending!</small>
                </div>

                <p class="mb-2">
                    Did not receive a code?
                    <a href="#" id="resendOtpLink" class="text-primary">Resend</a>
                    OR
                    <a href="#" id="sendToEmailLink" class="text-primary">Send To Email</a>
                </p>

                <!-- Verify Button -->
                <button type="button" class="btn btn-success btn-block mt-3" id="verifyOtpBtn">Verify</button>
            </div>
        </div>
    </div>
</div>
{{------------------------------------------------------------------------------------------------------}}

    <script src="{{ asset('theme_files/js/jquery.js') }}"></script>
<script src="{{ asset('theme_files/js/plugins.min.js') }}"></script>
    <script src="{{ asset('theme_files/js/functions.bundle.js') }}"></script>

    <script src="{{ asset('theme_files/js/core.js') }}"></script>

{{--
    <script src="{{ asset('theme_files/js/components/select-boxes.js') }}"></script>
    <script src="{{ asset('theme_files/js/components/selectsplitter.js') }}"></script>
    <script src="{{ asset('theme_files/js/components/bs-select.js') }}"></script>
    <script src="{{ asset('theme_files/js/components/bs-datatable.js') }}"></script>

    <script src="{{ asset('theme_files/js/components/bs-filestyle.js') }}"></script>
    <script src="{{ asset('theme_files/js/components/select2.min.js') }}"></script>
    <script src="{{ asset('theme_files/js/components/bs-switches.js') }}"></script>
    <script src="{{ asset('theme_files/js/components/dataTables.checkboxes.min.js') }}"></script>--}}
<script src="{{ asset('theme_files/js/js-loading-overlay.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>




    <script>

        let countdownValue = 52;
        let countdownInterval;
        let currentMobile = ''; // Global variable to store the user's mobile number
        JsLoadingOverlay.show({
            "overlayBackgroundColor": "#DBD0D0",
            "overlayOpacity": 0.6,
            "spinnerIcon": "line-scale",
            "spinnerColor": "#E11919",
            "spinnerSize": "3x",
            "overlayIDName": "overlay",
            "spinnerIDName": "spinner",
            "offsetX": 0,
            "offsetY": 0,
            "containerID": null,
            "lockScroll": true,
            "overlayZIndex": 99998,
            "spinnerZIndex": 99999
        });
        jQuery(document).ready(function () {
            $('.LoadingUi').on('click', function (event) {
                //$('.modal').modal('hide');
                JsLoadingOverlay.show(JsLoadingOverlay);
            });

            // Hide the loading overlay.
            JsLoadingOverlay.hide();
            console
                .log("jquery is enabled");
            //showPersonal();

            // Initially disable company fields
            $('#company_fields').find('input, select, textarea').attr('disabled', true);

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
            });

            // $link.attr('disabled', 'disabled');
            // Auto-focus next OTP input
            $('.otp-input').on('input', function() {
                if ($(this).val().length === 1) {
                    $(this).next('.otp-input').focus();
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
            });


// Global variables to track the OTP context and data:
            // otpContext: "login" or "registration"
            // otpData: for login { mobile: '...' } or for registration { temp_token: '...' }
            let otpContext = '';
            let otpData = {};

            // When the login form is submitted:
            $('#loginForm').submit(function(e) {
                e.preventDefault();
                const mobile = $('#login_mobile').val();
                otpContext = 'login';
                otpData = { mobile: mobile };
console.log("your requested mobile is ==> "+ mobile);

                axios.post('/api/login', { mobile: mobile })
                    .then(response => {
                        JsLoadingOverlay.hide();
                        $('#otpModal').modal('show');
                    })
                    .catch(error => {
                        JsLoadingOverlay.hide();
                        console.error(error);
                        console.error(error.response.request.responseText);
                        alert('Login failed. :'+ error.response.request.responseText);
                    });
            });

            // When the registration form is submitted:
            $('#registerForm').submit(function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const data = {};
                formData.forEach((value, key) => { data[key] = value; });
                otpContext = 'registration';
                otpData = {}; // Will be updated with temp_token from response

                axios.post('/api/register/otp', data)
                    .then(response => {
                        console.log(response.data);
                        otpData.temp_token = response.data.temp_token;
                        $('#otpModal').modal('show');
                    })
                    .catch(error => {
                        //console.error(error);
                        console.error('Registration failed: ' + error.response.data.message);
                        alert('Registration failed: ' + error.response.data.message);
                        return false;
                    });
            });

            // Auto-focus to next OTP input when a digit is entered:
            $('.otp-input').on('input', function() {
                if ($(this).val().length === 1) {
                    $(this).next('.otp-input').focus();
                }
            });

            // When the Verify OTP button is clicked:
            $('#verifyOtpBtn').on('click', function() {
                const otp = $('#otp_digit_1').val() +
                    $('#otp_digit_2').val() +
                    $('#otp_digit_3').val() +
                    $('#otp_digit_4').val();

                if (otp.length !== 4) {
                    alert("Please enter a 4-digit OTP.");
                    return;
                }

                let payload = { otp: otp };
                let endpoint = '';

                if (otpContext === 'login') {
                    payload.mobile = otpData.mobile;
                    endpoint = '/api/verify-otp';
                } else if (otpContext === 'registration') {
                    payload.temp_token = otpData.temp_token;
                    endpoint = '/api/register/verify-otp';
                } else {
                    alert("OTP context is not set.");
                    return;
                }
                console.log("user requesting this page---" + endpoint);
                axios.post(endpoint, payload)
                    .then(response => {
                        alert('OTP verified successfully.');
                        $('#otpModal').modal('hide');
                        console.log("redirect to user home page" +response.data.message);
                        console.log(response.data);
                        if(otpContext === 'login'){
                            window.location.href = "{{ route('home') }}";
                            return false;
                        }else if(otpContext === 'registration'){
                            window.location.href = "{{ route('login') }}";
                        }else{
                            return;
                        }
                        //window.location.href = "{{ route('auth.index') }}";
                        return ;
                        // Optionally, redirect to the dashboard/home page here.
                    })
                    .catch(error => {
                        console.error(error);
                        console.error(error.response.data);

                        alert('OTP verification failed: ' + error.response.data.message);
                    });
            });
            // Resend button click
            $('#resendOtpLink').on('click', function(e) {
                e.preventDefault();
                // Call your resend OTP API
                // For now, just reset the countdown
                resetCountdown();
                alert('Resending OTP...');
            });

            // Send To Email button click
            $('#sendToEmailLink').on('click', function(e) {
                e.preventDefault();
                // Implement your send-to-email logic here
                alert('Sending code to email...');
            });
        });
        function sendSms() {
            enableDisableLink("smsLink","timer", 20000);
            $("#smsLink").click();

            return false;
        }


        function showForm(formid) {
            $("#"+formid).show('fast');
            return false;
        }
        function hideForm(formid) {
            $("#"+formid).hide('fast');
            return false;
        }
        function showPersonal() {
            showForm("personalFormid");
            hideForm("companyFormid");
            $('#personal_fields').find('input, select, textarea').removeAttr('disabled');
            $('#company_fields').find('input, select, textarea').attr('disabled', true);
            return false;
        }
        function showCompany() {
            hideForm("personalFormid");
            showForm("companyFormid");
            return false;
        }



        function enableDisableLink(linkId, timerId, delay) {
            let $link = $("#" + linkId);
            let $timer = $("#" + timerId);

            function startTimer() {
                let remainingTime = delay / 1000; // Convert ms to seconds

                // Countdown timer function
                let countdown = setInterval(function() {
                    remainingTime--;
                    $timer.text(`(${remainingTime}s)`);

                    if (remainingTime <= 0) {
                        clearInterval(countdown);
                        $timer.text(""); // Remove timer text
                        $link.removeAttr("disabled"); // Enable the link
                        $link.removeClass("disabled"); // Enable the link
                    }
                }, 1000);

                // Enable link after delay
                setTimeout(() => {
                    $link.removeAttr("disabled");
                    $link.removeClass("disabled");

                    // Disable again when clicked and restart the process
                    $link.one("click", function() {
                        $(this).attr('disabled', 'disabled');
                        $(this).addClass('disabled');

                        $timer.text("-");
                        setTimeout(startTimer, 1000); // Restart after 1 sec
                    });

                }, delay);
            }

            // Start the initial timer
            startTimer();
        }
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
                    $('#resendMessage').text('You can resend the code now!');
                }
            }, 1000);
        }

        function resetCountdown() {
            // Reset the countdown value to 52
            countdownValue = 52;
            $('#resendMessage').text('Wait 52 seconds before resending!');
            startCountdown();
        }
        // Call function to enable link after 2 minutes (120,000 ms)

    </script>
    <!-- custom scripts will be here.. -->
    </body>
    </html>
