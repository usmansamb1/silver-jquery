@extends('layouts.app')
<style>
    .carddark{
        color:rgb(33, 34, 34) !important;
    }
</style>
@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('admin-dashboard.dashboard.title') }}</h1>
        <div class="d-flex">
            <a href="{{ route('admin.users.index') }}" class="btn btn-primary me-2">
                <i class="fas fa-users"></i> {{ __('admin-dashboard.navigation.users') }}
            </a>
            @role('admin')
            <a href="{{ route('wallet.pending-payments') }}" class="btn btn-warning">
                <i class="fas fa-clock"></i> {{ __('admin-dashboard.financial_summary.pending_payments') }}
            </a>
            @endrole
            @role('finance|activation|validation|it')
            <a href="{{ route('wallet.approvals.my-approvals') }}" class="btn btn-info">
                <i class="fas fa-clock"></i> {{ __('admin-dashboard.financial_summary.pending_payments') }}
            </a>
            @endrole
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ __('admin-dashboard.statistics.active_users') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeUsersCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ __('admin-dashboard.user_summary.blocked_users') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $inactiveUsersCount }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-slash fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <a href="{{ route('wallet.approvals.my-approvals') }}" class="text-decoration-none">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    {{ __('admin-dashboard.financial_summary.pending_payments') }}</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingWalletRequestsCount }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-wallet fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('admin-dashboard.quick_actions.title') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-block">
                                <i class="fas fa-user-plus"></i> {{ __('admin-dashboard.quick_actions.add_user') }}
                            </a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('admin.wallet-requests.index') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-money-bill-wave"></i> {{ __('admin-dashboard.panels.financial_overview') }}
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <a href="{{ route('wallet.pending-payments') }}" class="btn btn-info btn-block">
                                <i class="fas fa-money-check"></i> {{ __('admin-dashboard.quick_actions.process_payment') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('admin-dashboard.dashboard.system_status') }}</h6>
                </div>
                <div class="card-body carddark">
                    <div class="mb-2">
                        <strong>{{ __('admin-dashboard.dashboard.last_login') }}:</strong> {{ auth()->user()->last_login_at ?? __('admin-dashboard.recent_activities.no_activities') }}
                    </div>
                    <div class="mb-2">
                        <strong>{{ __('admin-dashboard.widgets.server_status') }}:</strong> {{ now()->format('Y-m-d H:i:s') }}
                    </div>
                    <div>
                        <strong>{{ __('admin-users.form.role') }}:</strong>
                        @foreach(auth()->user()->roles as $role)
                            <span class="badge bg-primary">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-primary {
    border-left: .25rem solid #4e73df!important;
}
.border-left-warning {
    border-left: .25rem solid #f6c23e!important;
}
.border-left-info {
    border-left: .25rem solid #36b9cc!important;
}
.btn-block {
    display: block;
    width: 100%;
}
</style>
@endpush 
