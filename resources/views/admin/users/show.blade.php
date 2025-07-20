@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">User Details</h1>
            <p class="mb-0 text-muted">{{ $user->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Back to Users
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary ml-2">
                <i class="fas fa-edit mr-1"></i> Edit
            </a>
        </div>
    </div>

    @include('partials.alerts')

    <div class="row">
        <!-- User Information -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Profile Information</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img class="img-profile rounded-circle mb-3" width="100" height="100"
                            src="{{ $user->profile_image ? Storage::url($user->profile_image) : asset('img/default-avatar.png') }}">
                        <h4 class="mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                        <div class="mt-2">
                            @foreach($roles as $role)
                                <span class="badge badge-info">{{ ucfirst($role) }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-top pt-3">
                        <div class="row mb-3">
                            <div class="col-6 text-muted">User ID</div>
                            <div class="col-6 text-right">{{ substr($user->id, 0, 8) }}...</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 text-muted">Phone</div>
                            <div class="col-6 text-right">{{ $user->phone ?? 'Not provided' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 text-muted">Gender</div>
                            <div class="col-6 text-right">{{ ucfirst($user->gender ?? 'Not specified') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 text-muted">Status</div>
                            <div class="col-6 text-right">
                                @if($user->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 text-muted">Registration</div>
                            <div class="col-6 text-right">{{ ucfirst($user->registration_type ?? 'Personal') }}</div>
                        </div>
                        @if($user->registration_type == 'company')
                        <div class="row mb-3">
                            <div class="col-6 text-muted">Company</div>
                            <div class="col-6 text-right">{{ $user->company_name }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 text-muted">Company Type</div>
                            <div class="col-6 text-right">{{ $user->company_type }}</div>
                        </div>
                        @endif
                        <div class="row mb-3">
                            <div class="col-6 text-muted">Created</div>
                            <div class="col-6 text-right">{{ $user->created_at->format('Y-m-d') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 text-muted">Last Login</div>
                            <div class="col-6 text-right">{{ $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i') : 'Never' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Contact Information</h6>
                </div>
                <div class="card-body">
                    @if($user->address || $user->city || $user->country)
                        <p class="mb-1"><strong>Address:</strong> {{ $user->address }}</p>
                        <p class="mb-1"><strong>City:</strong> {{ $user->city }}</p>
                        <p class="mb-0"><strong>Country:</strong> {{ $user->country }}</p>
                    @else
                        <p class="text-center text-muted mb-0">No address information provided</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Wallet and Transactions -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Wallet Information</h6>
                </div>
                <div class="card-body">
                    @if($wallet)
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-primary h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                    Current Balance</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    SAR {{ number_format($wallet->balance, 2) }}
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-wallet fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-success h-100 py-2">
                                    <div class="card-body">
                                        <div class="row no-gutters align-items-center">
                                            <div class="col mr-2">
                                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                    Wallet ID</div>
                                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                    {{ substr($wallet->id, 0, 12) }}...
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <i class="fas fa-id-card fa-2x text-gray-300"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="font-weight-bold">Recent Transactions</h5>
                            @if(!empty($transactions) && count($transactions) > 0)
                                <div class="table-responsive mt-3">
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Amount</th>
                                                <th>Description</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($transactions as $transaction)
                                                <tr>
                                                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                                    <td>{{ ucfirst($transaction->type) }}</td>
                                                    <td class="font-weight-bold {{ $transaction->type == 'debit' ? 'text-danger' : 'text-success' }}">
                                                        {{ $transaction->type == 'debit' ? '-' : '+' }}
                                                        SAR {{ number_format($transaction->amount, 2) }}
                                                    </td>
                                                    <td>{{ $transaction->description ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge badge-{{ $transaction->status == 'completed' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($transaction->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle mr-1"></i> No recent transactions found
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-wallet fa-4x text-muted mb-3"></i>
                            <h5>No Wallet Found</h5>
                            <p class="text-muted">This user does not have a wallet yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">System Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Last Updated:</strong> {{ $user->updated_at->format('Y-m-d H:i:s') }}</p>
                            <p><strong>Email Verified:</strong> 
                                @if($user->email_verified_at)
                                    <span class="text-success">Yes, on {{ $user->email_verified_at->format('Y-m-d') }}</span>
                                @else
                                    <span class="text-danger">No</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Account ID:</strong> {{ $user->id }}</p>
                            <p><strong>Created By:</strong> {{ $user->created_by ?? 'System' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 