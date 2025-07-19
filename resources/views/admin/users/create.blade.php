@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('admin-users.create_user') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}" class="needs-validation" novalidate>
                        @csrf
                        
                        <!-- Hidden Fields -->
                        <input type="hidden" name="registration_type" value="personal">
                        <!-- <input type="hidden" name="company_type" value="private"> -->
                        
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">{{ __('admin-users.personal_information') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label text-gray-700">{{ __('admin-users.form.full_name') }}</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label text-gray-700">{{ __('admin-users.form.email') }}</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="password" class="form-label text-gray-700">{{ __('admin-users.form.password') }}</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                       id="password" name="password" required>
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone" class="form-label text-gray-700">{{ __('admin-users.form.phone') }}</label>
                                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone') }}" required>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="mobile" class="form-label text-gray-700">{{ __('admin-users.form.phone') }} <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">+966</span>
                                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                                                       id="mobile" name="mobile" value="{{ old('mobile') }}"
                                                       pattern="^(05\d{8}|5\d{8,9})$"
                                                       title="Mobile number must start with 05 or 5 followed by 8-9 digits"
                                                       placeholder="5XXXXXXXX"
                                                       required>
                                            </div>
                                            @error('mobile')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Format: 5XXXXXXXX (9 digits)</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="gender" class="form-label text-gray-700">{{ __('admin-users.form.gender') }}</label>
                                            <select class="form-select @error('gender') is-invalid @enderror" 
                                                    id="gender" name="gender" required>
                                                <option value="">{{ __('admin-users.form.select_gender') }}</option>
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('admin-users.form.male') }}</option>
                                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('admin-users.form.female') }}</option>
                                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>{{ __('admin-users.form.other') }}</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Company Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">{{ __('admin-users.company.company_details') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="company_name" class="form-label text-gray-700">{{ __('admin-users.company.company_name') }}</label>
                                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                                   id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="city" class="form-label text-gray-700">{{ __('City') }}</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                                   id="city" name="city" value="{{ old('city') }}" required>
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Roles -->
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">{{ __('admin-users.form.role') }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <label class="form-label text-gray-700">{{ __('admin-users.form.select_role') }}</label>
                                        <div class="role-checkboxes">
                                            @foreach($roles as $role)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="roles[]" value="{{ $role->name }}" 
                                                           id="role_{{ $role->name }}"
                                                           {{ (old('roles') && in_array($role->name, old('roles'))) ? 'checked' : '' }}>
                                                    <label class="form-check-label text-gray-700" for="role_{{ $role->name }}">
                                                        {{ ucfirst($role->name) }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                        @error('roles')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">{{ __('admin-system.actions.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('admin-users.create_user') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const password = document.getElementById('password');
    const icon = this.querySelector('i');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});

// Bootstrap form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()

// Handle customer role exclusive selection
document.querySelectorAll('input[name="roles[]"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const customerRole = document.querySelector('input[value="customer"]');
        const otherRoles = document.querySelectorAll('input[name="roles[]"]:not([value="customer"])');
        
        if (this.value === 'customer' && this.checked) {
            otherRoles.forEach(role => {
                role.checked = false;
                role.disabled = true;
            });
        } else if (this.value === 'customer' && !this.checked) {
            otherRoles.forEach(role => {
                role.disabled = false;
            });
        } else if (this.checked) {
            customerRole.checked = false;
            customerRole.disabled = true;
        } else if (!Array.from(otherRoles).some(role => role.checked)) {
            customerRole.disabled = false;
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.text-gray-700 {
    color: #4a5568 !important;
}
.role-checkboxes {
    max-height: 150px;
    overflow-y: auto;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
}
.form-check {
    margin-bottom: 8px;
}
.form-check:last-child {
    margin-bottom: 0;
}
.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e3e6f0;
}
.card-header h6 {
    color: #4e73df;
    font-weight: 600;
}
</style>
@endpush 