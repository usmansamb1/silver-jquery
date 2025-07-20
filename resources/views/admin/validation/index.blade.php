@extends('layouts.admin')

@section('title', __('admin-approvals.titles.approval_management'))

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('admin-approvals.titles.approval_management') }}</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ __('admin-dashboard.tables.id') }}</th>
                                <th>{{ __('admin-approvals.workflow.workflow_type') }}</th>
                                <th>{{ __('admin-approvals.fields.requester') }}</th>
                                <th>{{ __('admin-approvals.status.pending') }}</th>
                                <th>{{ __('admin-dashboard.tables.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $item)
                            <tr>
                                <td>{{ $item->id ?? '' }}</td>
                                <td>{{ $item->type ?? '' }}</td>
                                <td>{{ $item->user->name ?? '' }}</td>
                                <td>{{ $item->status ?? __('admin-approvals.status.pending') }}</td>
                                <td>
                                    <a href="{{ route('validation.show', $item) }}" class="btn btn-sm btn-info">{{ __('admin-approvals.actions.view_details') }}</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">{{ __('admin-approvals.messages.no_pending_approvals') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 