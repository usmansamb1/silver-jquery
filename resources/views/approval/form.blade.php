@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4>Approval Form</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <!-- Application Details -->
                    <div class="mb-4">
                        <h5>Application Details</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">ID:</div>
                                    <div class="col-md-8">{{ $instance->approvable->id }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Type:</div>
                                    <div class="col-md-8">{{ class_basename($instance->approvable_type) }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Submitted by:</div>
                                    <div class="col-md-8">{{ $instance->initiator->name ?? 'Unknown' }}</div>
                                </div>
                                
                                @if ($instance->approvable_type === 'App\\Models\\Payment')
                                    <div class="row mb-2">
                                        <div class="col-md-4 fw-bold">Amount:</div>
                                        <div class="col-md-8">{{ number_format($instance->approvable->amount, 2) }}</div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-md-4 fw-bold">Payment Type:</div>
                                        <div class="col-md-8">{{ ucfirst(str_replace('_', ' ', $instance->approvable->payment_type)) }}</div>
                                    </div>
                                @endif
                                
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">Status:</div>
                                    <div class="col-md-8">
                                        <span class="badge bg-{{ $instance->status === 'pending' ? 'warning' : ($instance->status === 'approved' ? 'success' : 'danger') }}">
                                            {{ ucfirst($instance->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Approval Form -->
                    <form action="{{ route('approvals.process', $instance->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" value="{{ auth()->user()->name }}" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="comments" class="form-label">Description / Comments</label>
                            <textarea class="form-control" id="comments" name="comments" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="file" class="form-label">Attachment (Optional)</label>
                            <input class="form-control" type="file" id="file" name="file">
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" name="action" value="rejected" class="btn btn-danger">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                            
                            <button type="submit" name="action" value="transferred" class="btn btn-info mx-2">
                                <i class="bi bi-arrow-repeat"></i> Transfer To
                            </button>
                            
                            <button type="submit" name="action" value="approved" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Approve
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Close
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 