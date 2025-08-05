@extends('layouts.app')

@section('title', 'SMS Management')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">SMS Service Management</h4>
      </div>
      <div class="card-body">
        
        <!-- Configuration Status -->
        <div class="row mb-4">
          <div class="col-md-6">
            <div class="card border-left-primary">
              <div class="card-body">
                <h5 class="card-title">Configuration Status</h5>
                @if($configTest['success'])
                  <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ $configTest['message'] }}
                  </div>
                @else
                  <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ $configTest['message'] }}
                  </div>
                @endif
                <button type="button" class="btn btn-primary btn-sm" id="testConfigBtn">
                  <i class="fas fa-sync me-1"></i> Test Configuration
                </button>
              </div>
            </div>
          </div>
          
          <div class="col-md-6">
            <div class="card border-left-info">
              <div class="card-body">
                <h5 class="card-title">SMS Statistics</h5>
                <div class="row">
                  <div class="col-6">
                    <div class="text-center">
                      <h3 class="text-success">{{ $statistics['total_sent'] }}</h3>
                      <p class="text-muted mb-0">Sent</p>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="text-center">
                      <h3 class="text-danger">{{ $statistics['total_failed'] }}</h3>
                      <p class="text-muted mb-0">Failed</p>
                    </div>
                  </div>
                </div>
                <div class="mt-2">
                  <small class="text-muted">
                    Last sent: {{ $statistics['last_sent'] ? \Carbon\Carbon::parse($statistics['last_sent'])->diffForHumans() : 'Never' }}
                  </small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Test SMS Form -->
        <div class="card">
          <div class="card-header">
            <h5 class="card-title mb-0">Send Test SMS</h5>
          </div>
          <div class="card-body">
            <form id="testSmsForm">
              @csrf
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="mobile" class="form-label">Mobile Number</label>
                    <input type="text" class="form-control" id="mobile" name="mobile" 
                           placeholder="966501234567" required>
                    <div class="form-text">Enter mobile number with country code (966)</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="3" 
                              placeholder="Test SMS from FuelApp" required></textarea>
                    <div class="form-text">Maximum 160 characters</div>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-success" id="sendTestBtn">
                <i class="fas fa-paper-plane me-1"></i> Send Test SMS
              </button>
            </form>
          </div>
        </div>

        <!-- SMS Provider Information -->
        <div class="card mt-4">
          <div class="card-header">
            <h5 class="card-title mb-0">SMS Service Information</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-borderless">
                <tr>
                  <td><strong>SMS Provider:</strong></td>
                  <td>ConnectSaudi</td>
                </tr>
                <tr>
                  <td><strong>API URL:</strong></td>
                  <td>{{ config('sms.url') }}</td>
                </tr>
                <tr>
                  <td><strong>Sender ID:</strong></td>
                  <td>{{ config('sms.sender_id') }}</td>
                </tr>
                <tr>
                  <td><strong>Country Code:</strong></td>
                  <td>{{ config('sms.country_code') }}</td>
                </tr>
                <tr>
                  <td><strong>Priority:</strong></td>
                  <td>{{ config('sms.priority') }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Test Configuration Button
    $('#testConfigBtn').click(function() {
        const btn = $(this);
        const originalText = btn.html();
        
        btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Testing...').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("admin.sms.test-config") }}',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Configuration Valid',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Configuration Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to test configuration'
                });
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Send Test SMS Form
    $('#testSmsForm').submit(function(e) {
        e.preventDefault();
        
        const btn = $('#sendTestBtn');
        const originalText = btn.html();
        
        btn.html('<i class="fas fa-spinner fa-spin me-1"></i> Sending...').prop('disabled', true);
        
        $.ajax({
            url: '{{ route("admin.sms.send-test") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'SMS Sent Successfully!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                    
                    // Refresh statistics
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'SMS Sending Failed',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Failed to send SMS';
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            },
            complete: function() {
                btn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Character counter for message
    $('#message').on('input', function() {
        const maxLength = 160;
        const currentLength = $(this).val().length;
        const remaining = maxLength - currentLength;
        
        let feedbackClass = 'text-muted';
        if (remaining < 20) {
            feedbackClass = 'text-warning';
        }
        if (remaining < 0) {
            feedbackClass = 'text-danger';
        }
        
        $(this).next('.form-text').html(`Maximum 160 characters (${remaining} remaining)`)
                                 .removeClass('text-muted text-warning text-danger')
                                 .addClass(feedbackClass);
    });
});
</script>
@endpush 