<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-icon {
            position: absolute;
            left: 10px;
            top: 70%;
            transform: translateY(-50%);
            color: #aaa;
        }
        .input-with-icon {
            position: relative;
        }
        .input-with-icon input {
            padding-left: 2.5rem;
        }
        .toggle-radio label {
            cursor: pointer;
            padding: 0.5rem 1rem;
            border: 1px solid #007bff;
            border-radius: 0.25rem;
            margin-right: 0.5rem;
        }
        .toggle-radio input[type="radio"] {
            display: none;
        }
        .toggle-radio input[type="radio"]:checked + label {
            background-color: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <!-- Toggle Buttons for Login / Registration -->
                    <div class="text-center mb-4">
                        <button id="showLogin" class="btn btn-primary">
                            <i class="fa fa-sign-in-alt"></i> Login
                        </button>
                        <button id="showRegister" class="btn btn-secondary">
                            <i class="fa fa-user-plus"></i> Register
                        </button>
                    </div>
                    <!-- Login Form (visible by default) -->
                    <div id="login_form">
                        <h3 class="text-center mb-3">Login</h3>
                        <form id="loginForm">
                            <div class="form-group input-with-icon">
                                <label for="login_mobile"><i class="fa fa-phone form-icon"></i> Mobile</label>
                                <input type="text" id="login_mobile" name="mobile" class="form-control" placeholder="Enter your mobile number" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fa fa-sign-in-alt"></i> Login
                            </button>
                        </form>
                        <div class="text-center mt-3">
                            <small>Don't have an account? <a href="#" id="toRegister">Register here</a></small>
                        </div>
                    </div>
                    <!-- Registration Form (hidden by default) -->
                    <div id="registration_form" style="display:none;">
                        <h3 class="text-center mb-3">Register</h3>

                        @csrf
                        <!-- Registration Type as Toggle Radio Buttons -->
                            <div class="form-group toggle-radio">
                                <label>Registration Type</label><br>
                                <input type="radio" name="registration_type" id="reg_personal" value="personal" checked>
                                <label for="reg_personal"><i class="fa fa-user"></i> Personal</label>
                                <input type="radio" name="registration_type" id="reg_company" value="company">
                                <label for="reg_company"><i class="fa fa-building"></i> Company</label>
                            </div>
                            <!-- Personal Fields -->
                            <div id="personal_fields">
                                <form method="POST" id="personal" action="{{ route('api.register') }}">
                                <div class="form-group input-with-icon">
                                    <label for="name"><i class="fa fa-user form-icon"></i> Name*</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter your name">
                                </div>
                                <div class="form-group input-with-icon">
                                    <label for="email"><i class="fa fa-envelope form-icon"></i> Email</label>
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Enter your email">
                                </div>
                                <div class="form-group input-with-icon">
                                    <label for="mobile"><i class="fa fa-phone form-icon"></i> Mobile*</label>
                                    <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter your mobile number"  >
                                </div>
                                <div class="form-group input-with-icon">
                                    <label for="region"><i class="fa fa-map-marker-alt form-icon"></i> Region</label>
                                    <input type="text" name="region" id="region" class="form-control" placeholder="Enter your region">
                                </div>
                                <!-- Gender Option -->
                                <div class="form-group">
                                    <label>Gender</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="male" value="male">
                                        <label class="form-check-label" for="male"><i class="fa fa-mars"></i> Male</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="female" value="female">
                                        <label class="form-check-label" for="female"><i class="fa fa-venus"></i> Female</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="gender" id="other" value="other">
                                        <label class="form-check-label" for="other"><i class="fa fa-transgender-alt"></i> Other</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success btn-block mt-4">
                                    <i class="fa fa-user-plus"></i> Register
                                </button></form>
                            </div>
                            <!-- Company Fields -->
                            <div id="company_fields" style="display:none;">
                                <form method="POST" id="company" action="{{ route('api.register') }}">
                                <div class="form-group">
                                    <label>Company Type</label><br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="company_type" id="private" value="private">
                                        <label class="form-check-label" for="private"><i class="fa fa-user"></i> Private</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="company_type" id="semiGovt" value="semi Govt.">
                                        <label class="form-check-label" for="semiGovt"><i class="fa fa-building"></i> Semi Govt.</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="company_type" id="govt" value="Govt">
                                        <label class="form-check-label" for="govt"><i class="fa fa-landmark"></i> Govt</label>
                                    </div>
                                </div>
                                <div class="form-group input-with-icon">
                                    <label for="company_name"><i class="fa fa-building form-icon"></i> Company Name*</label>
                                    <input type="text" name="company_name" id="company_name" class="form-control" placeholder="Enter your company name">
                                </div>
                                <div class="form-group input-with-icon">
                                    <label for="email_company"><i class="fa fa-envelope form-icon"></i> Email*</label>
                                    <input type="email" name="email" id="email_company" class="form-control" placeholder="Enter company email">
                                </div>
                                <div class="form-group input-with-icon">
                                    <label for="mobile_company"><i class="fa fa-phone form-icon"></i> Mobile*</label>
                                    <input type="text" name="compmobile" id="mobile_company" class="form-control" placeholder="Enter company mobile"  >
                                </div>
                                <div class="form-group input-with-icon">
                                    <label for="cr_number"><i class="fa fa-id-card form-icon"></i> CR Number*</label>
                                    <input type="text" name="cr_number" id="cr_number" class="form-control" placeholder="Enter CR Number">
                                </div>
                                <div class="form-group input-with-icon">
                                    <label for="vat_number"><i class="fa fa-receipt form-icon"></i> VAT Number*</label>
                                    <input type="text" name="vat_number" id="vat_number" class="form-control" placeholder="Enter VAT Number">
                                </div>
                                <div class="form-group input-with-icon">
                                    <label for="city"><i class="fa fa-city form-icon"></i> City</label>
                                    <input type="text" name="city" id="city" class="form-control" placeholder="Enter City">
                                </div>
                                <div class="form-group input-with-icon">
                                    <label for="building_number"><i class="fa fa-building form-icon"></i> Building Number</label>
                                    <input type="text" name="building_number" id="building_number" class="form-control" placeholder="Enter Building Number">
                                </div>
                                <div class="form-group input-with-icon">
                                    <label for="zip_code"><i class="fa fa-mail-bulk form-icon"></i> Zip Code</label>
                                    <input type="text" name="zip_code" id="zip_code" class="form-control" placeholder="Enter Zip Code">
                                </div>
                                <div class="form-group input-with-icon">
                                    <label for="company_region"><i class="fa fa-map-marker-alt form-icon"></i> Region</label>
                                    <input type="text" name="company_region" id="company_region" class="form-control" placeholder="Enter Region">
                                </div>
                                    <button type="submit" class="btn btn-success btn-block mt-4">
                                        <i class="fa fa-user-plus"></i> Register
                                    </button>
                                </form>
                            </div>



                        <div class="text-center mt-3">
                            <small>Already have an account? <a href="#" id="toLogin">Login here</a></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- OTP Modal (modal will not close if clicked outside) -->
<div class="modal fade" id="otpModal" tabindex="-1" role="dialog" aria-labelledby="otpModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="otpModalLabel">Enter OTP</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="otpForm">
                    <div class="form-group input-with-icon">
                        <label for="otp"><i class="fa fa-key form-icon"></i> OTP</label>
                        <input type="text" id="otp" name="otp" class="form-control" placeholder="Enter OTP" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Submit OTP</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Toggle between login and registration forms
        $('#showLogin').click(function() {
            $('#registration_form').slideUp();
            $('#login_form').slideDown();
        });
        $('#showRegister, #toRegister').click(function(e) {
            e.preventDefault();
            $('#login_form').slideUp();
            $('#registration_form').slideDown();
        });
        $('#toLogin').click(function(e) {
            e.preventDefault();
            $('#registration_form').slideUp();
            $('#login_form').slideDown();
        });

        // Toggle between personal and company fields based on registration type radio buttons
        $('input[name="registration_type"]').change(function(){
            if ($(this).val() === 'company') {
                $('#personal_fields').slideUp();
                $('#company_fields').slideDown();
            } else {
                $('#company_fields').slideUp();
                $('#personal_fields').slideDown();
            }
        });

        // Handle login form submission: show OTP modal
        $('#loginForm').submit(function(e) {
            e.preventDefault();
            // Process mobile login via AJAX here if needed.
            $('#otpModal').modal('show');
        });

        // Handle OTP form submission
        $('#otpForm').submit(function(e) {
            e.preventDefault();
            // Process OTP verification via AJAX here if needed.
            alert('OTP submitted: ' + $('#otp').val());
            $('#otpModal').modal('hide');
        });
    });
</script>
</body>
</html>
