@extends('layouts.app')

@section('title', 'Send Test Email')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">Send Test Email</div>
        <div class="card-body">
          @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
          @endif
          @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
          @endif
          <form method="POST" action="{{ route('admin.test.email') }}">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label">Recipient Email</label>
              <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" placeholder="user@example.com" required>
              @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-envelope me-1"></i> Send Test Email
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 