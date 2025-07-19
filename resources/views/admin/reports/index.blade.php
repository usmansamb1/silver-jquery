@extends('layouts.admin')

@section('title', 'Reports')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Reports</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">User Reports</h5>
                                    <p class="card-text">View and export user-related reports.</p>
                                    <a href="{{ route('reports.users') }}" class="btn btn-primary">View User Reports</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Payment Reports</h5>
                                    <p class="card-text">View and export payment-related reports.</p>
                                    <a href="{{ route('reports.payments') }}" class="btn btn-primary">View Payment Reports</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Export Reports</h5>
                                    <p class="card-text">Export system data in various formats.</p>
                                    <div class="btn-group">
                                        <a href="{{ route('reports.export', 'users') }}" class="btn btn-success">Export Users</a>
                                        <a href="{{ route('reports.export', 'payments') }}" class="btn btn-success">Export Payments</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 