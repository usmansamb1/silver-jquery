@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Approval History</h5>
                    <div>
                        <a href="{{ route('wallet.approvals.index') }}" class="btn btn-info">
                            <i class="fas fa-list"></i> All Requests
                        </a>
                        <a href="{{ route('wallet.approvals.my-approvals') }}" class="btn btn-primary">
                            <i class="fas fa-tasks"></i> My Approvals
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
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Last Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($requests as $request)
                                    <tr>
                                        <td>{{ $request->reference_no }}</td>
                                        <td>{{ $request->workflow->name ?? 'N/A' }}</td>
                                        <td>{{ number_format($request->amount, 2) }} {{ $request->currency }}</td>
                                        <td>
                                            <span class="badge" style="background-color: {{ $request->status->color }}">
                                                {{ $request->status->name }}
                                            </span>
                                        </td>
                                        <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $request->updated_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <a href="{{ route('wallet.approvals.show', $request) }}" 
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No approval history found.</td>
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
