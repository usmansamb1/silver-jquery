@extends('layouts.admin')

@section('title', 'Validation Review')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Validation Review</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Item Information</h4>
                            <p><strong>ID:</strong> {{ $item->id ?? 'N/A' }}</p>
                            <p><strong>Type:</strong> {{ $item->type ?? 'N/A' }}</p>
                            <p><strong>Status:</strong> {{ $item->status ?? 'Pending' }}</p>
                            <p><strong>Submitted At:</strong> {{ isset($item->created_at) ? $item->created_at->format('Y-m-d H:i') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h4>Submitter Information</h4>
                            <p><strong>Name:</strong> {{ $item->user->name ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $item->user->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row mt-3">
                        <div class="col-12">
                            <form action="{{ route('validation.approve', $item) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">Approve</button>
                            </form>
                            
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                Reject
                            </button>
                            
                            <a href="{{ route('validation.index') }}" class="btn btn-secondary">Back to List</a>
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
            <form action="{{ route('validation.reject', $item) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Validation</h5>
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