@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit User</h5>
                    <span class="badge bg-light text-dark">ID: {{ $user->formatted_customer_no }}</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')
                        
                        <!-- Hidden Fields -->
                        <input type="hidden" name="registration_type" value="personal">
                        <input type="hidden" name="company_type" value="private">
                        
                        <div class="row">
                            <!-- Personal Information -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Personal Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label text-gray-700">Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label text-gray-700">Email</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone" class="form-label text-gray-700">Phone</label>
                                            <input type="text" class="form-control" 
                                                   value="{{ $user->phone }}" disabled>
                                            <small class="text-muted">Phone number cannot be changed</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="mobile" class="form-label text-gray-700">Mobile <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">+966</span>
                                                <input type="text" class="form-control @error('mobile') is-invalid @enderror" 
                                                       id="mobile" name="mobile" value="{{ old('mobile', $user->mobile) }}"
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
                                            <label for="gender" class="form-label text-gray-700">Gender</label>
                                            <select class="form-select @error('gender') is-invalid @enderror" 
                                                    id="gender" name="gender" required>
                                                <option value="">Select Gender</option>
                                                <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
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
                                        <h6 class="mb-0">Company Information</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="company_name" class="form-label text-gray-700">Company Name</label>
                                            <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                                   id="company_name" name="company_name" value="{{ old('company_name', $user->company_name) }}" required>
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label for="city" class="form-label text-gray-700">City</label>
                                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                                   id="city" name="city" value="{{ old('city', $user->city) }}" required>
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Roles and Status -->
                            <div class="col-12">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0">Roles & Status</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label text-gray-700">Roles</label>
                                                <div class="role-checkboxes">
                                                    @foreach($roles as $role)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="roles[]" value="{{ $role->name }}" 
                                                                   id="role_{{ $role->name }}"
                                                                   {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                                                                   {{ $role->name === 'admin' && $user->hasRole('admin') ? 'disabled' : '' }}>
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

                                            <div class="col-md-6">
                                                <label for="is_active" class="form-label text-gray-700">Status</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="is_active" name="is_active" value="1"
                                                           {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                                           {{ $user->hasRole('admin') ? 'disabled' : '' }}>
                                                    <label class="form-check-label text-gray-700" for="is_active">
                                                        Active
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            @if(!$user->hasRole('admin') && !$user->hasRole('customer'))
                            <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                <i class="fas fa-trash"></i> Delete User
                            </button>
                            @else
                            <div></div>
                            @endif
                            <div>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update User</button>
                            </div>
                        </div>
                    </form>

                    @if(!$user->hasRole('admin') && !$user->hasRole('customer'))
                    <form id="delete-form" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

function confirmDelete() {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}

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
.form-switch .form-check-input {
    width: 3em;
    height: 1.5em;
    margin-top: 0;
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