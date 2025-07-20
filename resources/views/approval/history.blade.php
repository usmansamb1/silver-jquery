@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Approvals History</h4>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>
                
                <div class="card-body">
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
                    
                    <!-- Approval Policies -->
                    <div class="mb-4">
                        <h5>Approval Policies</h5>
                        <div class="card">
                            <div class="card-body">
                                <!-- Approval workflow visualization -->
                                <div class="approval-steps d-flex justify-content-between align-items-center mb-4">
                                    @php 
                                        $approvedSteps = $instance->approvals->where('action', 'approved')->pluck('approval_step_id')->toArray();
                                        $rejectedSteps = $instance->approvals->where('action', 'rejected')->pluck('approval_step_id')->toArray();
                                        $pendingStep = $instance->currentApproval() ? $instance->currentApproval()->approval_step_id : null;
                                    @endphp
                                    
                                    @foreach($instance->workflow->steps as $step)
                                        <div class="approval-step text-center">
                                            @if(in_array($step->id, $approvedSteps))
                                                <div class="step-icon bg-success rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 50px; height: 50px;">
                                                    <i class="bi bi-check-lg text-white fs-4"></i>
                                                </div>
                                            @elseif(in_array($step->id, $rejectedSteps))
                                                <div class="step-icon bg-danger rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 50px; height: 50px;">
                                                    <i class="bi bi-x-lg text-white fs-4"></i>
                                                </div>
                                            @elseif($step->id === $pendingStep)
                                                <div class="step-icon bg-warning rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 50px; height: 50px;">
                                                    <i class="bi bi-pencil-fill text-white fs-4"></i>
                                                </div>
                                            @else
                                                <div class="step-icon bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 50px; height: 50px;">
                                                    <i class="bi bi-hourglass text-white fs-4"></i>
                                                </div>
                                            @endif
                                            
                                            <p class="mt-2 mb-0">{{ $step->name }}</p>
                                            @php
                                                $approverType = 'User';
                                                if ($step->approver_type === 'role') {
                                                    $approverType = 'Role';
                                                } elseif ($step->approver_type === 'department') {
                                                    $approverType = 'Department';
                                                }
                                            @endphp
                                            <small class="text-muted">{{ $approverType }}</small>
                                        </div>
                                        
                                        @if(!$loop->last)
                                            <div class="step-connector flex-grow-1 mx-2 position-relative">
                                                <hr class="m-0 position-absolute w-100 top-50" style="border-top: 2px dashed #ccc;">
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Approval History Timeline -->
                    <div>
                        <h5>Approvals History</h5>
                        <div class="timeline mt-3">
                            @if($instance->approvals->count() > 0)
                                @foreach($instance->approvals->sortByDesc('created_at') as $approval)
                                    <div class="timeline-item p-3 mb-3 border-start border-4 border-{{ $approval->action === 'approved' ? 'success' : ($approval->action === 'rejected' ? 'danger' : ($approval->action === 'transferred' ? 'info' : 'warning')) }} position-relative">
                                        <div class="timeline-icon position-absolute" style="top: 0; left: -15px; width: 30px; height: 30px;">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-{{ $approval->action === 'approved' ? 'success' : ($approval->action === 'rejected' ? 'danger' : ($approval->action === 'transferred' ? 'info' : 'warning')) }}" style="width: 30px; height: 30px;">
                                                <i class="bi bi-{{ $approval->action === 'approved' ? 'check-lg' : ($approval->action === 'rejected' ? 'x-lg' : ($approval->action === 'transferred' ? 'arrow-repeat' : 'hourglass')) }} text-white"></i>
                                            </div>
                                        </div>
                                        
                                        <div class="ps-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-0">
                                                    Action Taken: <span class="text-{{ $approval->action === 'approved' ? 'success' : ($approval->action === 'rejected' ? 'danger' : ($approval->action === 'transferred' ? 'info' : 'warning')) }}">{{ ucfirst($approval->action) }}</span>
                                                </h6>
                                                <small class="text-muted">{{ $approval->created_at->format('d-M-Y H:i:s A') }}</small>
                                            </div>
                                            
                                            <p class="mb-2">
                                                <strong>User:</strong> {{ $approval->user->name ?? 'Pending' }}<br>
                                                <strong>Step:</strong> {{ $approval->step->name }}<br>
                                                @if($approval->comments)
                                                    <strong>Description:</strong> {{ $approval->comments }}<br>
                                                @endif
                                                @if($approval->transferred_to)
                                                    <strong>Transferred to:</strong> {{ $approval->transferredTo->name ?? 'Unknown' }}<br>
                                                @endif
                                            </p>
                                            
                                            @if($approval->file_path)
                                                <p class="mb-0">
                                                    <a href="{{ Storage::url($approval->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-file-earmark"></i> View Attachment
                                                    </a>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="alert alert-info">
                                    No approval history found.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        width: 2px;
        background-color: #ccc;
    }
    
    .step-connector {
        height: 2px;
    }
</style>
@endpush 