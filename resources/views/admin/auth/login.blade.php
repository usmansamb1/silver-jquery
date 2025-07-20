@extends('layouts.admin-auth')

@section('title', __('admin-dashboard.navigation.login'))

@section('content')
<div class="login-container">
    <div class="login-box animate__animated animate__fadeIn">
        <div class="text-center mb-4">
            <img src="{{ asset('theme_files/imgs/yaseeir-smal-new-logo5-trans.png') }}" alt="Yaseir Logo" class="logo-img animate__animated animate__pulse">
        </div>
        
        <h4 class="text-center mb-4">{{ __('admin-dashboard.navigation.login') }}</h4>
        
        <!-- Step 1: Email + Password Form -->
        <form id="loginForm" class="needs-validation animate__animated animate__fadeIn" novalidate>
            @csrf
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" 
                       required aria-label="Email Address" autocomplete="email">
                <label for="email">{{ __('admin-users.form.email') }}</label>
                <div class="invalid-feedback" data-error="email"></div>
            </div>

            <div class="form-floating mb-3 password-container">
                <input type="password" class="form-control" id="password" name="password" placeholder="Password" 
                       required aria-label="Password" autocomplete="current-password">
                <label for="password">{{ __('admin-users.form.password') }}</label>
                <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                    <i class="fas fa-eye"></i>
                </button>
                <div class="invalid-feedback" data-error="password"></div>
                <div class="password-strength mt-1 d-none">
                    <div class="progress" style="height: 5px;">
                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <small class="strength-text text-muted"></small>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember" name="remember" value="1">
                    <label class="form-check-label" for="remember">{{ __('admin-dashboard.auth.remember_me') }}</label>
                </div>
                {{-- <a href="javascript:void(0)" class="text-decoration-none small" onclick="showResetMessage()">Forgot password?</a> --}}
            </div>

            <button type="submit" class="btn btn-primary w-100 position-relative" id="loginBtn">
                <span class="btn-text">{{ __('admin-dashboard.navigation.login') }}</span>
                <span class="spinner-border spinner-border-sm d-none position-absolute" role="status"></span>
            </button>
        </form>

        <!-- Step 2: OTP Verification Form (Hidden by default) -->
        <form id="otpForm" class="needs-validation animate__animated d-none" novalidate>
            @csrf
            <div class="text-center mb-4">
                <i class="fas fa-envelope-open-text fa-3x text-primary mb-3"></i>
                <h5>{{ __('admin-dashboard.auth.enter_otp') }}</h5>
                <p class="text-muted">{{ __('admin-dashboard.auth.otp_sent_message') }}</p>
            </div>

            <div class="otp-input-container text-center mb-4">
                <div class="d-flex justify-content-center gap-2">
                    @for ($i = 1; $i <= 6; $i++)
                    <input type="text" class="form-control otp-input" maxlength="1" 
                           id="otp-{{$i}}" aria-label="OTP digit {{$i}}" inputmode="numeric">
                    @endfor
                </div>
                <input type="hidden" id="otp" name="otp">
                <div class="invalid-feedback mt-2" data-error="otp"></div>
            </div>

            <button type="submit" class="btn btn-primary w-100 position-relative" id="verifyBtn">
                <span class="btn-text">{{ __('admin-dashboard.auth.verify_otp') }}</span>
                <span class="spinner-border spinner-border-sm d-none position-absolute" role="status"></span>
            </button>

            <div class="text-center mt-3">
                <button type="button" class="btn btn-link" id="backToLogin">
                    <i class="fas fa-arrow-left"></i> {{ __('admin-dashboard.auth.back_to_login') }}
                </button>
            </div>
        </form>
        
        <!-- Session timeout notification -->
        <div class="toast-container position-fixed bottom-0 end-0 p-3">
            <div id="sessionTimeoutToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header">
                    <i class="fas fa-clock me-2 text-warning"></i>
                    <strong class="me-auto">{{ __('admin-dashboard.auth.session_alert') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
                <div class="toast-body">
                    {{ __('admin-dashboard.auth.session_timeout_message') }} <span id="timeout-countdown">5</span> {{ __('admin-dashboard.auth.minutes') }}.
                    <div class="mt-2 pt-2 border-top">
                        <button type="button" class="btn btn-primary btn-sm" id="extendSession">{{ __('admin-dashboard.auth.keep_logged_in') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast notifications container -->
<div class="toast-container position-fixed top-0 end-0 p-3" id="toastContainer"></div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<style>
:root {
    --primary-color: #0275d8;
    --primary-dark: #0056b3;
    --secondary-color: #6c757d;
    --success-color: #198754;
    --danger-color: #dc3545;
    --warning-color: #ffc107;
    --light-color: #f8f9fa;
    --dark-color: #212529;
}

body {
    background-color: #f5f7fa;
    background-image: 
        radial-gradient(circle at 100% 100%, rgba(2, 117, 216, 0.05) 0, rgba(2, 117, 216, 0) 20%),
        radial-gradient(circle at 0% 0%, rgba(2, 117, 216, 0.05) 0, rgba(2, 117, 216, 0) 20%);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 20px;
    min-width: 700px;
}

.login-box {
    background-color: white;
    border-radius: 12px;
    padding: 60px 50px;
    width: 100%;
    min-width: 600px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.login-box:hover {
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.logo-img {
    max-width: 220px;
    height: auto;
    margin-bottom: 10px;
}

h4 {
    color: var(--dark-color);
    font-weight: 600;
    font-size: 1.6rem;
}

.form-control {
    height: 60px;
    border-radius: 8px;
    border: 1px solid #ddd;
    transition: all 0.3s;
    padding-left: 15px;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(2, 117, 216, 0.15);
}

.form-floating > .form-control {
    height: 58px;
    line-height: 1.25;
}

.form-floating > label {
    padding: 0.75rem 0.75rem;
}

.btn-primary {
    background-color: var(--primary-color);
    height: 60px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s;
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-primary:active {
    transform: translateY(0);
    box-shadow: none;
}

.btn .spinner-border {
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.password-container {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--secondary-color);
    cursor: pointer;
    z-index: 5;
}

.password-toggle:hover {
    color: var(--primary-color);
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.invalid-feedback {
    display: none;
    color: var(--danger-color);
    margin-top: 0.25rem;
}

/* OTP input styling */
.otp-input {
    width: 45px;
    height: 50px;
    text-align: center;
    font-size: 1.2rem;
    font-weight: 600;
}

.otp-input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(2, 117, 216, 0.15);
}

/* Password strength indicator */
.password-strength .progress-bar {
    transition: width 0.3s;
}

.password-strength .progress-bar.weak {
    background-color: var(--danger-color);
}

.password-strength .progress-bar.medium {
    background-color: var(--warning-color);
}

.password-strength .progress-bar.strong {
    background-color: var(--success-color);
}

/* Toast styling */
.toast {
    backdrop-filter: blur(10px);
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* Responsive adjustments */
@media (max-width: 576px) {
    .login-box {
        padding: 30px;
        border-radius: 8px;
    }
    
    .form-control, .btn-primary {
        height: 60px; /* Maintain larger touch targets */
    }
    
    .otp-input {
        width: 40px;
        height: 55px;
    }
    
    h4 {
        font-size: 1.5rem;
    }
}

/* Accessibility focus states */
a:focus, button:focus, input:focus, .btn:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}
</style>
@endpush

@push('scripts')
<!-- SweetAlert2 for better notifications -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const otpForm = document.getElementById('otpForm');
    const backToLogin = document.getElementById('backToLogin');
    const passwordInput = document.getElementById('password');
    const passwordToggle = document.querySelector('.password-toggle');
    const rememberCheck = document.getElementById('remember');
    
    // Initialize toast instances
    const toastContainer = document.getElementById('toastContainer');
    const sessionTimeoutToast = new bootstrap.Toast(document.getElementById('sessionTimeoutToast'));
    
    // Function to show toast notifications
    function showToast(message, type = 'error') {
        const iconMap = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'warning': 'fa-exclamation-triangle',
            'info': 'fa-info-circle'
        };
        
        const bgClass = {
            'success': 'bg-success',
            'error': 'bg-danger',
            'warning': 'bg-warning',
            'info': 'bg-info'
        };
        
        const icon = iconMap[type] || iconMap.info;
        const bg = bgClass[type] || bgClass.info;
        
        const toastEl = document.createElement('div');
        toastEl.classList.add('toast', 'align-items-center', 'border-0', 'animate__animated', 'animate__fadeInRight');
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');
        
        toastEl.innerHTML = `
            <div class="d-flex">
                <div class="toast-body text-white ${bg} rounded-start">
                    <i class="fas ${icon} me-2"></i> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toastEl);
        const toast = new bootstrap.Toast(toastEl);
        toast.show();
        
        // Remove toast after it's hidden
        toastEl.addEventListener('hidden.bs.toast', function() {
            toastEl.remove();
        });
    }
    
    // Setup password toggle
    passwordToggle.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.querySelector('i').classList.toggle('fa-eye');
        this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    // Password strength meter
    const strengthMeter = document.querySelector('.password-strength');
    const strengthBar = strengthMeter.querySelector('.progress-bar');
    const strengthText = strengthMeter.querySelector('.strength-text');
    
    passwordInput.addEventListener('input', function() {
        if (this.value.length > 0) {
            strengthMeter.classList.remove('d-none');
            
            // Simple password strength calculation
            let strength = 0;
            if (this.value.length >= 8) strength += 25;
            if (this.value.match(/[A-Z]/)) strength += 25;
            if (this.value.match(/[0-9]/)) strength += 25;
            if (this.value.match(/[^A-Za-z0-9]/)) strength += 25;
            
            strengthBar.style.width = `${strength}%`;
            strengthBar.classList.remove('weak', 'medium', 'strong');
            
            if (strength <= 25) {
                strengthBar.classList.add('weak');
                strengthText.textContent = 'Weak';
            } else if (strength <= 75) {
                strengthBar.classList.add('medium');
                strengthText.textContent = 'Medium';
            } else {
                strengthBar.classList.add('strong');
                strengthText.textContent = 'Strong';
            }
        } else {
            strengthMeter.classList.add('d-none');
        }
    });
    
    // Session timeout simulation
    let sessionTimeoutId;
    function simulateSessionTimeout() {
        clearTimeout(sessionTimeoutId);
        sessionTimeoutId = setTimeout(() => {
            const countdown = document.getElementById('timeout-countdown');
            countdown.textContent = '5';
            sessionTimeoutToast.show();
            
            // Countdown timer
            let timeLeft = 5;
            const countdownInterval = setInterval(() => {
                timeLeft--;
                countdown.textContent = timeLeft;
                
                if (timeLeft <= 0) {
                    clearInterval(countdownInterval);
                    // Redirect to logout or session expiry page
                    // window.location.href = '/logout';
                }
            }, 60000); // 1 minute intervals
            
            // Extend session button
            document.getElementById('extendSession').addEventListener('click', function() {
                clearInterval(countdownInterval);
                sessionTimeoutToast.hide();
                simulateSessionTimeout();
                showToast('Your session has been extended.', 'success');
            });
        }, 25 * 60000); // 25 minutes
    }
    
    // OTP input enhancement
    const otpInputs = document.querySelectorAll('.otp-input');
    const otpHiddenInput = document.getElementById('otp');
    
    otpInputs.forEach((input, index) => {
        // Handle input changes and auto-focus next input
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            
            // Auto-focus next input
            if (this.value.length === 1 && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
            
            // Update hidden input with combined OTP
            updateOTPValue();
        });
        
        // Handle keyboard navigation
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                // Move to previous input on backspace if current is empty
                otpInputs[index - 1].focus();
            } else if (e.key === 'ArrowLeft' && index > 0) {
                otpInputs[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
            }
        });
    });
    
    function updateOTPValue() {
        let otp = '';
        otpInputs.forEach(input => {
            otp += input.value;
        });
        otpHiddenInput.value = otp;
    }
    
    // Handle login form submission
    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const loginBtn = document.getElementById('loginBtn');
        const btnText = loginBtn.querySelector('.btn-text');
        const spinner = loginBtn.querySelector('.spinner-border');
        
        try {
            // Reset previous errors
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.style.display = 'none';
                el.textContent = '';
            });
            
            loginBtn.disabled = true;
            btnText.classList.add('invisible');
            spinner.classList.remove('d-none');
            
            const response = await axios.post('{{ route("admin.login") }}', {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value,
                remember: rememberCheck.checked ? 1 : 0
            });
            
            if (response.data.show_otp_form) {
                // Animate form transition
                loginForm.classList.remove('animate__fadeIn');
                loginForm.classList.add('animate__fadeOut');
                
                setTimeout(() => {
                    loginForm.classList.add('d-none');
                    otpForm.classList.remove('d-none');
                    otpForm.classList.add('animate__fadeIn');
                    // Focus first OTP input
                    otpInputs[0].focus();
                }, 300);
                
                // Start session timeout simulation
                simulateSessionTimeout();
            }
        } catch (error) {
            console.error('Login error:', error);
            
            if (error.response?.data?.errors) {
                Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                    const feedback = document.querySelector(`[data-error="${field}"]`);
                    if (feedback) {
                        feedback.textContent = messages[0];
                        feedback.style.display = 'block';
                    }
                });
                showToast('Please correct the errors in the form.', 'error');
            } else {
                showToast(error.response?.data?.message || 'Login failed. Please try again.', 'error');
            }
        } finally {
            loginBtn.disabled = false;
            btnText.classList.remove('invisible');
            spinner.classList.add('d-none');
        }
    });
    
    // Handle OTP form submission
    otpForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Validate OTP is complete
        if (otpHiddenInput.value.length !== 6) {
            showToast('Please enter a complete 6-digit OTP.', 'warning');
            return;
        }
        
        const verifyBtn = document.getElementById('verifyBtn');
        const btnText = verifyBtn.querySelector('.btn-text');
        const spinner = verifyBtn.querySelector('.spinner-border');
        
        try {
            verifyBtn.disabled = true;
            btnText.classList.add('invisible');
            spinner.classList.remove('d-none');
            
            const response = await axios.post('{{ route("admin.login.verify") }}', {
                otp: otpHiddenInput.value
            });
            
            if (response.data.redirect) {
                showToast('Login successful! Redirecting...', 'success');
                setTimeout(() => {
                    window.location.href = response.data.redirect;
                }, 1000);
            }
        } catch (error) {
            console.error('OTP verification error:', error);
            
            if (error.response?.data?.errors) {
                Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                    const feedback = document.querySelector(`[data-error="${field}"]`);
                    if (feedback) {
                        feedback.textContent = messages[0];
                        feedback.style.display = 'block';
                    }
                });
            }
            
            // Clear OTP inputs on error
            otpInputs.forEach(input => input.value = '');
            otpHiddenInput.value = '';
            otpInputs[0].focus();
            
            showToast(error.response?.data?.message || 'OTP verification failed. Please try again.', 'error');
        } finally {
            verifyBtn.disabled = false;
            btnText.classList.remove('invisible');
            spinner.classList.add('d-none');
        }
    });
    
    // Handle back to login button
    backToLogin.addEventListener('click', function() {
        otpForm.classList.remove('animate__fadeIn');
        otpForm.classList.add('animate__fadeOut');
        
        setTimeout(() => {
            otpForm.classList.add('d-none');
            loginForm.classList.remove('d-none', 'animate__fadeOut');
            loginForm.classList.add('animate__fadeIn');
            
            // Clear OTP fields
            otpInputs.forEach(input => input.value = '');
            otpHiddenInput.value = '';
        }, 300);
    });
    
    // Add keyboard navigation support
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && document.activeElement.tagName !== 'BUTTON') {
            e.preventDefault();
            const form = document.activeElement.closest('form');
            if (form) {
                const submitBtn = form.querySelector('[type="submit"]');
                if (submitBtn) submitBtn.click();
            }
        }
    });
});

// Function to show reset password message
function showResetMessage() {
    showToast('Please contact an administrator to reset your password.', 'info');
}
</script>
@endpush 