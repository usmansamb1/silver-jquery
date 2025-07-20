@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ __('My Pending Approvals') }}</h4>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> {{ __('Back to Dashboard') }}
                    </a>
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
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Details') }}</th>
                                    <th>{{ __('Workflow') }}</th>
                                    <th>{{ __('Step') }}</th>
                                    <th>{{ __('Created') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingApprovals as $approvalItem)
                                    <tr>
                                        <td>{{ substr($approvalItem['instance']->id, 0, 8) }}</td>
                                        <td>{{ class_basename($approvalItem['instance']->approvable_type) }}</td>
                                        <td>
                                            @if($approvalItem['instance']->approvable_type === 'App\\Models\\Payment')
                                                {{ __('Amount') }}: {{ number_format($approvalItem['instance']->approvable->amount, 2) }}<br>
                                                {{ __('Type') }}: {{ ucfirst(str_replace('_', ' ', $approvalItem['instance']->approvable->payment_type)) }}
                                            @else
                                                {{ $approvalItem['instance']->approvable_id }}
                                            @endif
                                        </td>
                                        <td>{{ $approvalItem['instance']->workflow->name }}</td>
                                        <td>{{ $approvalItem['step']->name }}</td>
                                        <td>{{ $approvalItem['instance']->created_at->format('d M Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('approvals.form', $approvalItem['instance']->id) }}" class="btn btn-primary btn-sm">
                                                    <i class="bi bi-check2-square"></i> {{ __('Review') }}
                                                </a>
                                                <a href="{{ route('approvals.history', $approvalItem['instance']->id) }}" class="btn btn-info btn-sm">
                                                    <i class="bi bi-clock-history"></i> {{ __('History') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="alert alert-info mb-0">
                                                {{ __('No pending approvals found that require your attention.') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 