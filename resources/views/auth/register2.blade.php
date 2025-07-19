@extends('layouts.app')

@section('title','Sign In or Register')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-CjgsKhwNQnJkY/+pQpGUhO1rjLr99ZFllrHcE2K1O0tQwhuL8bV6C7P6mOPGkD1EwXKcA2aH7uM3lzzawWc5AQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow-lg rounded-4" id="authCard">
                <div class="card-body p-5">
                    <!-- Login -->
                    <div id="loginSection">
                        <h1 class="h3 mb-4 text-center">Welcome Back</h1>
                        <div id="loginStatus" class="alert d-none" role="alert"></div>
                        <form id="loginForm" novalidate>
                            @csrf
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                <input type="tel" name="mobile" class="form-control" placeholder="Mobile" required>
                            </div>
                            <div class="mb-3 input-group">
                                <span class="input-group-text"><i class="fa-solid fa-key"></i></span>
                                <input type="text" name="otp" class="form-control" placeholder="OTP" required>
                            </div>
                            <div class="d-grid mb-3">
                                <button class="btn btn-primary">Login</button>
                            </div>
                            <p class="text-center small mb-0">New here? <a href="#" id="showRegister">Create an account</a></p>
                        </form>
                    </div>

                    <!-- Register -->
                    <div id="registerSection" style="display:none;">
                        <h1 class="h3 mb-4 text-center">Create Account</h1>
                        <div id="formStatus" class="alert d-none" role="alert"></div>
                        <form id="registerForm" class="needs-validation" novalidate>
                        @csrf

                        <!-- Account Type -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Register as</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="register_type" id="typeIndividual" value="personal" checked>
                                    <label class="btn btn-outline-primary" for="typeIndividual"><i class="fa-solid fa-user me-1"></i>Individual</label>

                                    <input type="radio" class="btn-check" name="register_type" id="typeCompany" value="company">
                                    <label class="btn btn-outline-primary" for="typeCompany"><i class="fa-solid fa-building me-1"></i>Company</label>
                                </div>
                            </div>

                            <!-- Individual Fields -->
                            <div id="individualFields">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="fullName">Full Name<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                                            <input type="text" id="fullName" class="form-control" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="indEmail">Email<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                            <input type="email" id="indEmail" class="form-control" name="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="gender">Gender<span class="text-danger">*</span></label>
                                        <select class="form-select" id="gender" name="gender" required>
                                            <option value="">Choose...</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="indMobile">Mobile<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                            <input type="tel" id="indMobile" class="form-control" name="mobile" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="indRegion">Region</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-location-dot"></i></span>
                                            <input type="text" id="indRegion" class="form-control" name="region">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Company Fields -->
                            <div id="companyFields" class="d-none">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Company Type</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="company_type" id="companyPrivate" value="private" required>
                                        <label class="form-check-label" for="companyPrivate">Private</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="company_type" id="companySemiGovt" value="semi_govt">
                                        <label class="form-check-label" for="companySemiGovt">Semi Govt.</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="company_type" id="companyGovt" value="govt">
                                        <label class="form-check-label" for="companyGovt">Govt</label>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label" for="companyName">Company Name<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-building"></i></span>
                                            <input type="text" id="companyName" class="form-control" name="company_name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="compEmail">Email<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                                            <input type="email" id="compEmail" class="form-control" name="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="compMobile">Mobile<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                                            <input type="tel" id="compMobile" class="form-control" name="mobile" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="crNumber">CR Number<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-id-card"></i></span>
                                            <input type="text" id="crNumber" class="form-control" name="cr_number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="vatNumber">VAT Number<span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-receipt"></i></span>
                                            <input type="text" id="vatNumber" class="form-control" name="vat_number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="city">City</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-city"></i></span>
                                            <input type="text" id="city" class="form-control" name="city">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="buildingNumber">Building Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-house"></i></span>
                                            <input type="text" id="buildingNumber" class="form-control" name="building_number">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="zipCode">Zip Code</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-envelope-open-text"></i></span>
                                            <input type="text" id="zipCode" class="form-control" name="zip_code">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="compRegion">Region</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fa-solid fa-location-dot"></i></span>
                                            <input type="text" id="compRegion" class="form-control" name="region">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-success btn-lg">Create Account</button>
                            </div>
                            <p class="text-center small mt-3 mb-0">Already have an account? <a href="#" id="showLogin">Sign in</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts') 
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
    $(function () {
        const $registerSection  = $('#registerSection');
        const $loginSection     = $('#loginSection');
        const $companyFields    = $('#companyFields');
        const $individualFields = $('#individualFields');
        const $formStatus       = $('#formStatus');
        const $loginStatus      = $('#loginStatus');

        // toggle between login and register
        $('#showRegister').on('click', function (e) {
            e.preventDefault();
            $loginSection.slideUp(300, () => $registerSection.slideDown());
        });
        $('#showLogin').on('click', function (e) {
            e.preventDefault();
            $registerSection.slideUp(300, () => $loginSection.slideDown());
        });

        // toggle individual/company fields
        function toggleForms() {
            const isCompany = $('#typeCompany').is(':checked');
            $companyFields.toggleClass('d-none', !isCompany).find(':input').prop('disabled', !isCompany);
            $individualFields.toggleClass('d-none', isCompany).find(':input').prop('disabled', isCompany);
        }
        toggleForms();
        $('input[name="register_type"]').on('change', toggleForms);

        // Login submit
        $('#loginForm').on('submit', function (e) {
            e.preventDefault();
            const url  = '{{ url('/api/v1/auth/verify') }}';
            const data = $(this).serialize();

            axios.post(url, data, {
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': $('input[name="_token"]').val()
                }
            })
                .then(() => {
                    $loginStatus.removeClass('d-none alert-danger').addClass('alert-success')
                        .text('Login successful! Redirecting...');
                    setTimeout(() => window.location.href = '{{ url('/dashboard') }}', 1000);
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Invalid credentials';
                    $loginStatus.removeClass('d-none alert-success').addClass('alert-danger').text(msg);
                });
        });

        // Register submit
        $('#registerForm').on('submit', function (e) {
            e.preventDefault();
            const $form = $(this);

            if (this.checkValidity() === false) {
                $form.addClass('was-validated');
                return;
            }

            const url  = '{{ url('/api/v1/auth/register') }}';
            const data = $form.serialize();

            axios.post(url, data, {
                headers: {
                    'X-CSRF-TOKEN': $('input[name="_token"]').val(),
                    'Content-Type': 'application/x-www-form-urlencoded'
                }
            })
                .then(() => {
                    $formStatus.removeClass('d-none alert-danger').addClass('alert-success')
                        .text('Registration successful! Check your SMS / Email to verify.');
                    setTimeout(() => {
                        $registerSection.slideUp(300, () => $loginSection.slideDown());
                    }, 1500);
                })
                .catch(err => {
                    const msg = err.response?.data?.message || 'Something went wrong';
                    $formStatus.removeClass('d-none alert-success').addClass('alert-danger').text(msg);
                });
        });
    });
    </script>
@endpush
