@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Business Process Management</h4>
                    <a href="{{ route('approval-workflows.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus"></i> Create New Workflow
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
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Model Type</th>
                                    <th>Steps</th>
                                    <th>Status</th>
                                    <th>Notifications</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($workflows as $workflow)
                                    <tr>
                                        <td>{{ $workflow->name }}</td>
                                        <td>{{ Str::limit($workflow->description, 50) }}</td>
                                        <td>{{ class_basename($workflow->model_type) ?? 'Generic' }}</td>
                                        <td>{{ $workflow->steps->count() }}</td>
                                        <td>
                                            <span class="badge bg-{{ $workflow->is_active ? 'success' : 'danger' }}">
                                                {{ $workflow->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $workflow->notify_by_email ? 'primary' : 'secondary' }}">Email</span>
                                            <span class="badge bg-{{ $workflow->notify_by_sms ? 'primary' : 'secondary' }}">SMS</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('approval-workflows.show', $workflow->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('approval-workflows.edit', $workflow->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('approval-workflows.destroy', $workflow->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this workflow?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No workflows found</td>
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