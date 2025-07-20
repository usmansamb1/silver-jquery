@extends('layouts.admin')

@section('title', 'User Activation Review')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Activation Review</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>User Information</h4>
                            <p><strong>Name:</strong> {{ $user->name }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <p><strong>Registered:</strong> {{ $user->created_at->format('Y-m-d H:i') }}</p>
                            <p><strong>Phone:</strong> {{ $user->phone }}</p>
                            <p><strong>Mobile:</strong> {{ $user->mobile }}</p>
                        </div>
                        <div class="col-md-6">
                            <h4>Company Information</h4>
                            <p><strong>Company Name:</strong> {{ $user->company_name }}</p>
                            <p><strong>Company Type:</strong> {{ $user->company_type }}</p>
                            <p><strong>City:</strong> {{ $user->city }}</p>
                            <p><strong>Registration Type:</strong> {{ $user->registration_type }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-12">
                            <form action="{{ route('activation.approve', $user) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">Approve Activation</button>
                            </form>
                            
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                Reject Activation
                            </button>
                            
                            <a href="{{ route('activation.index') }}" class="btn btn-secondary">Back to List</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('activation.reject', $user) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject User Activation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reason">Rejection Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 