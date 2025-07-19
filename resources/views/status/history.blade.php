@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Status History</h5>
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>{{ ucfirst(str_replace('_', ' ', $modelType)) }} Details</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <tbody>
                                        <tr>
                                            <th style="width: 30%">ID:</th>
                                            <td>{{ $model->id }}</td>
                                        </tr>
                                        @if(property_exists($model, 'reference_number') || isset($model->reference_number))
                                            <tr>
                                                <th>Reference:</th>
                                                <td>{{ $model->reference_number }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>Current Status:</th>
                                            <td>
                                                <span class="badge bg-{{ $model->status == 'approved' ? 'success' : ($model->status == 'rejected' ? 'danger' : ($model->status == 'pending' ? 'warning' : 'secondary')) }}">
                                                    {{ ucfirst($model->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @if(property_exists($model, 'created_at') || isset($model->created_at))
                                            <tr>
                                                <th>Created:</th>
                                                <td>{{ $model->created_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                        @endif
                                        @if(property_exists($model, 'updated_at') || isset($model->updated_at))
                                            <tr>
                                                <th>Last Updated:</th>
                                                <td>{{ $model->updated_at->format('Y-m-d H:i:s') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h6>Status Change History</h6>
                    
                    @if(count($history) > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Changed From</th>
                                        <th>Changed To</th>
                                        <th>Changed By</th>
                                        <th>Comment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($history as $entry)
                                        <tr>
                                            <td>{{ $entry['changed_at'] }}</td>
                                            <td>
                                                @if($entry['previous_status'])
                                                    <span class="badge bg-{{ $entry['previous_status'] == 'approved' ? 'success' : ($entry['previous_status'] == 'rejected' ? 'danger' : ($entry['previous_status'] == 'pending' ? 'warning' : 'secondary')) }}">
                                                        {{ ucfirst($entry['previous_status']) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $entry['new_status'] == 'approved' ? 'success' : ($entry['new_status'] == 'rejected' ? 'danger' : ($entry['new_status'] == 'pending' ? 'warning' : 'secondary')) }}">
                                                    {{ ucfirst($entry['new_status']) }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($entry['user'])
                                                    {{ $entry['user']['name'] }}
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>
                                            <td>{{ $entry['comment'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No status change history found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 