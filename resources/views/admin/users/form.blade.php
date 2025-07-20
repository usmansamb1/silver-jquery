@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Users
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" 
                  method="POST" 
                  class="needs-validation" 
                  novalidate>
                @csrf
                @if(isset($user))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $user->name ?? '') }}" 
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $user->email ?? '') }}" 
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        {{ isset($user) ? 'New Password (leave blank to keep current)' : 'Password' }}
                    </label>
                    <input type="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           {{ !isset($user) ? 'required' : '' }}>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" 
                           class="form-control" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           {{ !isset($user) ? 'required' : '' }}>
                </div>

                <div class="mb-3">
                    <label class="form-label d-block">Roles</label>
                    @foreach($roles as $role)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('roles') is-invalid @enderror" 
                                   type="checkbox" 
                                   name="roles[]" 
                                   value="{{ $role->name }}" 
                                   id="role_{{ $role->id }}"
                                   {{ (isset($user) && $user->hasRole($role->name)) || (is_array(old('roles')) && in_array($role->name, old('roles'))) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role_{{ $role->id }}">
                                {{ ucfirst($role->name) }}
                            </label>
                        </div>
                    @endforeach
                    @error('roles')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        {{ isset($user) ? 'Update User' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Form validation
(function() {
    'use strict';
    var forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>
@endpush
@endsection 