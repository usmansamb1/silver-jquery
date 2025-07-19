<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login & Registration</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .form-icon {
            position: absolute;
            left: 10px;
            top: 50%;
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
                        <form method="POST" action="{{ route('api.register') }}">
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
                                    <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Enter your mobile number" required>
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
                            </div>
                            <!-- Company Fields -->
                            <div id="company_fields" style="display:none;">
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
                                    <input type="text" name="mobile" id="mobile_company" class="form-control" placeholder="Enter company mobile" required>
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
                            </div>
                            <button type="submit" class="btn btn-success btn-block mt-4">
                                <i class="fa fa-user-plus"></i> Register
                            </button>
                        </form>
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

<!-- Axios from CDN -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<!-- jQuery and Bootstrap JS (for UI toggling and modal) -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>

    let countdownValue = 52;
    let countdownInterval;
    let currentMobile = ''; // Global variable to store the user's mobile number
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
            // Enable personal fields and disable company fields on switch
            $('#personal_fields').find('input, select, textarea').removeAttr('disabled');
            $('#company_fields').find('input, select, textarea').attr('disabled', true);
        });
        $('#toLogin').click(function(e) {
            e.preventDefault();
            $('#registration_form').slideUp();
            $('#login_form').slideDown();
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
        });

        // Initially disable company fields
        $('#company_fields').find('input, select, textarea').attr('disabled', true);

        // Use Axios for login form submission instead of jQuery.ajax
        $('#loginForm').submit(function(e) {
            e.preventDefault();
            const mobile = $('#login_mobile').val();
            currentMobile = mobile // Save the mobile number
            axios.post('/api/login', { mobile: currentMobile, otp: '' })  // Assuming backend will send OTP on mobile login
                .then(response => {
                    // Show OTP modal on success
                    $('#otpModal').modal('show');
                })
                .catch(error => {
                    console.error(error);
                    alert('Login failed.');
                });
        });

        // Use Axios for OTP form submission
        $('#otpForm').submit(function(e) {
            e.preventDefault();
            const otp = $('#otp').val();
            axios.post('/api/login/verify', { mobile: currentMobile ,otp: otp }) // Your OTP verification endpoint
                .then(response => {
                    alert('OTP verified successfully.');
                    $('#otpModal').modal('hide');
                })
                .catch(error => {
                    console.error(error);
                    alert('OTP verification failed.');
                });
        });

        // Example usage of Axios for GET, PATCH, DELETE requests:
        // GET request
     /*   axios.get('/api/example')
            .then(response => console.log('GET:', response.data))
            .catch(error => console.error('GET error:', error));

        // PATCH request
        axios.patch('/api/example/1', { name: 'Updated Name' })
            .then(response => console.log('PATCH:', response.data))
            .catch(error => console.error('PATCH error:', error));

        // DELETE request
        axios.delete('/api/example/1')
            .then(response => console.log('DELETE:', response.data))
            .catch(error => console.error('DELETE error:', error));*/

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

        // Verify button click
        $('#verifyOtpBtn').on('click', function() {
            const digit1 = $('#otp_digit_1').val();
            const digit2 = $('#otp_digit_2').val();
            const digit3 = $('#otp_digit_3').val();
            const digit4 = $('#otp_digit_4').val();
            const fullOtp = digit1 + digit2 + digit3 + digit4;

            // Perform your OTP verification logic (AJAX or Axios request)
            // Example:
            axios.post('/api/login/verify', { mobile: currentMobile, otp: fullOtp })
              .then(response => {
                alert(response.user);
                console.log(response)
              })
              .catch(error => {
                // handle error
                  alert(error.response.data.message);
                  console.log("-----")
                  console.log(error)
              });

            alert('OTP entered: ' + fullOtp);
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
</script>
</body>
</html>
