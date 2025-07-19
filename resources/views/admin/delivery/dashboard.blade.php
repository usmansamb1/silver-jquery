@extends('layouts.app')

@section('title', __('admin-dashboard.panels.rfid_management'))

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-dashboard.panels.rfid_management') }}</h1>
        <div>
            <button id="batch-sync-rfids-btn" class="btn btn-info mr-2">
                <i class="fas fa-sync-alt"></i> {{ __('admin-dashboard.quick_actions.manage_rfid') }}
            </button>
            <button id="sync-balances-btn" class="btn btn-success">
                <i class="fas fa-coins"></i> {{ __('admin-dashboard.financial_summary.wallet_balance') }}
            </button>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Pending Delivery Card -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('admin-dashboard.statistics.pending_orders') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivered Card -->
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ __('admin-dashboard.statistics.completed_orders') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $deliveredCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Bookings Filter and Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ __('admin-dashboard.statistics.service_bookings') }}</h6>
        </div>
        <div class="card-body">
            <form id="filter-form" class="mb-4">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="booking_id">{{ __('admin-dashboard.tables.id') }}</label>
                        <input type="text" class="form-control" id="booking_id" name="booking_id" placeholder="Enter booking ID">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="customer_id">{{ __('admin-dashboard.navigation.customers') }}</label>
                        <input type="text" class="form-control" id="customer_id" name="customer_id" placeholder="Enter customer ID">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="mobile">{{ __('admin-users.form.phone') }}</label>
                        <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter mobile number">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="delivery_status">{{ __('admin-dashboard.status.pending') }}</label>
                        <select class="form-control" id="delivery_status" name="delivery_status">
                            <option value="">{{ __('admin-dashboard.filters.all') }}</option>
                            <option value="pending">{{ __('admin-dashboard.status.pending') }}</option>
                            <option value="delivered">{{ __('admin-dashboard.status.completed') }}</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <button type="submit" class="btn btn-primary">{{ __('admin-dashboard.filters.apply_filters') }}</button>
                        <button type="reset" class="btn btn-secondary" id="reset-filters">{{ __('admin-dashboard.filters.reset_filters') }}</button>
                    </div>
                </div>
            </form>

            <div id="services-table-container">
                @include('admin.delivery.services-table')
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle filter form submission
        $('#filter-form').on('submit', function(e) {
            e.preventDefault();
            let formData = $(this).serialize();
            
            $.ajax({
                url: "{{ route('admin.delivery.services') }}",
                type: "GET",
                data: formData,
                success: function(response) {
                    $('#services-table-container').html(response);
                },
                error: function(xhr) {
                    console.error('Error loading services:', xhr);
                }
            });
        });
        
        // Reset filters
        $('#reset-filters').on('click', function(e) {
            e.preventDefault();
            $('#filter-form')[0].reset();
            $('#filter-form').trigger('submit');
        });
        
        // Handle RFID update button clicks
        $(document).on('click', '.update-rfid-btn', function() {
            let bookingId = $(this).data('booking-id');
            
            Swal.fire({
                title: 'Update RFID',
                html: '<input type="text" id="rfid_number" class="swal2-input" placeholder="Enter RFID number">',
                showCancelButton: true,
                confirmButtonText: 'Update',
                focusConfirm: false,
                preConfirm: () => {
                    const rfidNumber = document.getElementById('rfid_number').value;
                    if (!rfidNumber) {
                        Swal.showValidationMessage('Please enter RFID number');
                        return false;
                    }
                    return rfidNumber;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    updateRFID(bookingId, result.value);
                }
            });
        });
        
        // Handle sync vehicle RFID button clicks
        $(document).on('click', '.sync-vehicle-rfid-btn', function() {
            let bookingId = $(this).data('booking-id');
            let rfidNumber = $(this).data('rfid');
            let vehicleId = $(this).data('vehicle-id');
            
            Swal.fire({
                title: 'Sync Vehicle RFID',
                text: `Are you sure you want to sync RFID ${rfidNumber} to the vehicle?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, sync it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    syncVehicleRFID(bookingId, rfidNumber, vehicleId);
                }
            });
        });
        
        // Handle batch sync RFID button click
        $('#batch-sync-rfids-btn').on('click', function() {
            Swal.fire({
                title: 'Batch Sync RFIDs',
                text: 'This will update all vehicles with missing RFID data. Continue?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, sync all',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    batchSyncRFIDs();
                }
            });
        });
        
        // Handle sync RFID balances button click
        $('#sync-balances-btn').on('click', function() {
            Swal.fire({
                title: 'Sync RFID Balances',
                text: 'This will update all vehicle RFID balances from booking refueling amounts. Continue?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, sync balances',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    syncRfidBalances();
                }
            });
        });
        
        function updateRFID(bookingId, rfidNumber) {
            $.ajax({
                url: "{{ url('admin/delivery/services') }}/" + bookingId + "/update-rfid",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    rfid_number: rfidNumber
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message
                        });
                        
                        // Refresh the table
                        $('#filter-form').trigger('submit');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while updating RFID';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            });
        }
        
        function batchSyncRFIDs() {
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we sync all RFID data',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: "{{ route('admin.delivery.batch-sync-vehicle-rfids') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message
                        });
                        
                        // Refresh the table
                        $('#filter-form').trigger('submit');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred during batch sync';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            });
        }
        
        function syncVehicleRFID(bookingId, rfidNumber, vehicleId) {
            $.ajax({
                url: "{{ route('admin.delivery.sync-vehicle-rfid') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    booking_id: bookingId,
                    rfid_number: rfidNumber,
                    vehicle_id: vehicleId
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message
                        });
                        
                        // Refresh the table
                        $('#filter-form').trigger('submit');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while syncing vehicle RFID';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            });
        }
        
        function syncRfidBalances() {
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we sync all RFID balances',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: "{{ route('admin.delivery.sync-vehicle-balances') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message
                        });
                        
                        // Refresh the table
                        $('#filter-form').trigger('submit');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred during balance sync';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            });
        }
    });
</script>
@endpush 