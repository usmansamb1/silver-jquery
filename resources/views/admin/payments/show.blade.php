@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('admin-payments.payment_details') }}</h4>
                    <div>
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> {{ __('admin-payments.back_to_list') }}
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    <!-- Payment Details -->
                    <div class="mb-4">
                        <h5>{{ __('admin-payments.application_details') }}</h5>
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">{{ __('admin-payments.id') }}:</div>
                                    <div class="col-md-8">{{ $payment->id }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">{{ __('admin-payments.user') }}:</div>
                                    <div class="col-md-8">{{ $payment->user->name }} ({{ $payment->user->email }})</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">{{ __('admin-payments.amount') }}:</div>
                                    <div class="col-md-8">{{ number_format($payment->amount, 2) }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">{{ __('admin-payments.payment_type') }}:</div>
                                    <div class="col-md-8">{{ __('admin-payments.payment_types.' . $payment->payment_type, ['default' => ucfirst(str_replace('_', ' ', $payment->payment_type))]) }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">{{ __('admin-payments.status') }}:</div>
                                    <div class="col-md-8">
                                        <span class="badge bg-{{ $payment->status === 'pending' ? 'warning' : ($payment->status === 'approved' ? 'success' : 'danger') }}">
                                            {{ __('admin-payments.statuses.' . $payment->status, ['default' => ucfirst($payment->status)]) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-4 fw-bold">{{ __('admin-payments.created_date') }}:</div>
                                    <div class="col-md-8">{{ $payment->created_at->format('d M Y H:i:s') }}</div>
                                </div>
                                
                                @if($payment->file)
                                    <div class="row mb-2">
                                        <div class="col-md-4 fw-bold">{{ __('admin-payments.attachment') }}:</div>
                                        <div class="col-md-8">
                                            <a href="{{ Storage::url($payment->file) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-file-earmark"></i> {{ __('admin-payments.view_attachment') }}
                                            </a>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($payment->notes)
                                    <div class="row mb-2">
                                        <div class="col-md-4 fw-bold">{{ __('admin-payments.notes') }}:</div>
                                        <div class="col-md-8">{{ $payment->notes }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Approval Workflow -->
                    @if($approvalWorkflow)
                        <div class="mb-4">
                            <h5>{{ __('admin-payments.approval_policies') }}</h5>
                            <div class="card">
                                <div class="card-body">
                                    <!-- Approval workflow visualization -->
                                    <div class="approval-steps d-flex justify-content-between align-items-center mb-4">
                                        @php 
                                            $approvedSteps = $payment->approvalInstance->approvals->where('action', 'approved')->pluck('approval_step_id')->toArray();
                                            $rejectedSteps = $payment->approvalInstance->approvals->where('action', 'rejected')->pluck('approval_step_id')->toArray();
                                            $pendingStep = $currentApproval ? $currentApproval->approval_step_id : null;
                                        @endphp
                                        
                                        @foreach($approvalWorkflow->steps as $step)
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
                                                <small class="text-muted">{{ ucfirst($step->approver_type) }}</small>
                                            </div>
                                            
                                            @if(!$loop->last)
                                                <div class="step-connector flex-grow-1 mx-2 position-relative">
                                                    <hr class="m-0 position-absolute w-100 top-50" style="border-top: 2px dashed #ccc;">
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    
                                    <div class="d-flex justify-content-end mt-3">
                                        @if($payment->status === 'pending' && $currentApproval)
                                            <a href="{{ route('approvals.form', $payment->approvalInstance->id) }}" class="btn btn-primary">
                                                <i class="bi bi-check2-square"></i> {{ __('admin-payments.process_approval') }}
                                            </a>
                                        @endif
                                        
                                        <a href="{{ route('approvals.history', $payment->approvalInstance->id) }}" class="btn btn-info ms-2">
                                            <i class="bi bi-clock-history"></i> {{ __('admin-payments.view_history') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Approval History Timeline -->
                        <div>
                            <h5>{{ __('admin-payments.approvals_history') }}</h5>
                            <div class="timeline mt-3">
                                @if($approvalHistory->count() > 0)
                                    @foreach($approvalHistory as $approval)
                                        <div class="timeline-item p-3 mb-3 border-start border-4 border-{{ $approval->action === 'approved' ? 'success' : ($approval->action === 'rejected' ? 'danger' : ($approval->action === 'transferred' ? 'info' : 'warning')) }} position-relative">
                                            <div class="timeline-icon position-absolute" style="top: 0; left: -15px; width: 30px; height: 30px;">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-{{ $approval->action === 'approved' ? 'success' : ($approval->action === 'rejected' ? 'danger' : ($approval->action === 'transferred' ? 'info' : 'warning')) }}" style="width: 30px; height: 30px;">
                                                    <i class="bi bi-{{ $approval->action === 'approved' ? 'check-lg' : ($approval->action === 'rejected' ? 'x-lg' : ($approval->action === 'transferred' ? 'arrow-repeat' : 'hourglass')) }} text-white"></i>
                                                </div>
                                            </div>
                                            
                                            <div class="ps-3">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0">
                                                        {{ __('admin-payments.action_taken') }}: <span class="text-{{ $approval->action === 'approved' ? 'success' : ($approval->action === 'rejected' ? 'danger' : ($approval->action === 'transferred' ? 'info' : 'warning')) }}">{{ __('admin-payments.statuses.' . $approval->action, ['default' => ucfirst($approval->action)]) }}</span>
                                                    </h6>
                                                    <small class="text-muted">{{ $approval->created_at->format('d-M-Y H:i:s A') }}</small>
                                                </div>
                                                
                                                <p class="mb-2">
                                                    <strong>{{ __('admin-payments.user') }}:</strong> {{ $approval->user->name ?? __('admin-payments.pending') }}<br>
                                                    <strong>{{ __('admin-payments.step') }}:</strong> {{ $approval->step->name }}<br>
                                                    @if($approval->comments)
                                                        <strong>{{ __('admin-payments.comments') }}:</strong> {{ $approval->comments }}<br>
                                                    @endif
                                                    @if($approval->transferred_to)
                                                        <strong>{{ __('admin-payments.transferred_to') }}:</strong> {{ $approval->transferredTo->name ?? __('admin-payments.unknown') }}<br>
                                                    @endif
                                                </p>
                                                
                                                @if($approval->file_path)
                                                    <p class="mb-0">
                                                        <a href="{{ Storage::url($approval->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-file-earmark"></i> {{ __('admin-payments.view_attachment') }}
                                                        </a>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-info">
                                        {{ __('admin-payments.no_approval_history_found') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>{{ __('admin-payments.payment_actions') }}</h5>
                            </div>
                            
                            <div class="card">
                                <div class="card-body">
                                    @if($payment->status === 'pending')
                                        <div class="d-flex justify-content-center gap-3">
                                            <form action="{{ route('admin.payments.approve', $payment->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-check-circle"></i> {{ __('admin-payments.approve_payment') }}
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.payments.reject', $payment->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="bi bi-x-circle"></i> {{ __('admin-payments.reject_payment') }}
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="alert alert-{{ $payment->status === 'approved' ? 'success' : 'danger' }}">
                                            {{ __('admin-payments.this_payment_has_been') }} {{ __('admin-payments.statuses.' . $payment->status, ['default' => $payment->status]) }}.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
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
        padding-left: 15px;
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