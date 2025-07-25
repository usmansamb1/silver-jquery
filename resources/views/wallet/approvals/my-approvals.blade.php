@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Pending Approvals</h5>
                    <div>
                        <a href="{{ route('wallet.approvals.index') }}" class="btn btn-info">
                            <i class="fas fa-list"></i> All Requests
                        </a>
                        <a href="{{ route('wallet.approvals.history') }}" class="btn btn-secondary">
                            <i class="fas fa-history"></i> History
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Reference</th>
                                    <th>Workflow</th>
                                    <th>Requester</th>
                                    <th>Amount</th>
                                    <th>Current Step</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $request)
                                    @php
                                        $currentStep = $request->getCurrentStep();
                                    @endphp
                                    <tr>
                                        <td>{{ $request->reference_no }}</td>
                                        <td>{{ $request->workflow->name }}</td>
                                        <td>{{ $request->user->name }}</td>
                                        <td>{{ number_format($request->amount, 2) }} {{ $request->currency }}</td>
                                        <td>
                                            @if($currentStep)
                                                <span class="badge bg-warning">
                                                    Awaiting Your Approval
                                                </span>
                                            @else
                                                <span class="badge" style="background-color: {{ $request->status->color }}">
                                                    {{ $request->status->name }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('wallet.approvals.show', $request) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No pending approvals found that require your attention.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
