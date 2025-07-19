@extends('layouts.app')

@section('content')
<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow">
        <div class="card-body text-center">
          <h2 class="mb-4">Payment Status</h2>
          @php
            $code = $result['result']['code'] ?? null;
            $desc = $result['result']['description'] ?? 'Unknown';
            $success = isset($code) && str_starts_with($code, '000.');
          @endphp
          @if($success)
            <div class="alert alert-success">
              <i class="fas fa-check-circle fa-2x mb-2"></i>
              <h4 class="alert-heading">Payment Successful!</h4>
              <p>{{ $desc }}</p>
            </div>
          @else
            <div class="alert alert-danger">
              <i class="fas fa-times-circle fa-2x mb-2"></i>
              <h4 class="alert-heading">Payment Failed</h4>
              <p>{{ $desc }}</p>
            </div>
          @endif
          <hr>
          <p><strong>Result Code:</strong> {{ $code ?? 'N/A' }}</p>
          <a href="{{ route('wallet.topup') }}" class="btn btn-primary mt-3">Back to Top Up</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection 