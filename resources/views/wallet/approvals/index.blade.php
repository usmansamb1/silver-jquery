@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>{{ __('Wallet Approval Requests') }}</h1>
        <a href="{{ route('wallet.approvals.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> {{ __('New Request') }}
        </a>
    </div>

    @include('partials.alerts')

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('Reference') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        <tr>
                            <td>{{ $request->reference_no }}</td>
                            <td>{{ number_format($request->amount, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $request->status_color }}">
                                    {{ $request->status }}
                                </span>
                            </td>
                            <td>{{ $request->created_at->format('d M Y H:i') }}</td>
                            <td>
                                <a href="{{ route('wallet.approvals.show', $request) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> {{ __('View') }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">{{ __('No approval requests found.') }}</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection 