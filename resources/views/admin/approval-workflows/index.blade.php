@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('admin-approvals.titles.approval_workflows') }}</h5>
                    <a href="{{ route('admin.approval-workflows.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('admin-approvals.workflow.create_workflow') }}
                    </a>
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
                                    <th>{{ __('admin-approvals.fields.id') }}</th>
                                    <th>{{ __('admin-approvals.fields.name') }}</th>
                                    <th>{{ __('admin-approvals.fields.description') }}</th>
                                    <th>{{ __('admin-approvals.fields.status') }}</th>
                                    <th>{{ __('admin-approvals.workflow.workflow_steps') }}</th>
                                    <th>{{ __('admin-approvals.fields.created_by') }}</th>
                                    <th>{{ __('admin-approvals.fields.created_at') }}</th>
                                    <th>{{ __('admin-approvals.fields.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($workflows as $workflow)
                                    <tr>
                                        <td>{{ $workflow->id }}</td>
                                        <td>{{ $workflow->name }}</td>
                                        <td>{{ Str::limit($workflow->description, 50) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $workflow->is_active ? 'success' : 'secondary' }}">
                                                {{ $workflow->is_active ? __('admin-approvals.status.active') : __('admin-approvals.status.inactive') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $workflow->steps->count() }} {{ __('admin-approvals.workflow.workflow_steps') }}</span>
                                            <button type="button" 
                                                    class="btn btn-sm btn-link view-steps" 
                                                    data-workflow-id="{{ $workflow->id }}" 
                                                    data-workflow-name="{{ $workflow->name }}">
                                                {{ __('admin-approvals.actions.view_steps') }}
                                            </button>
                                        </td>
                                        <td>{{ $workflow->creator->name }}</td>
                                        <td>{{ $workflow->created_at->format('Y-m-d H:i') }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('admin.approval-workflows.edit', $workflow) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-edit"></i> {{ __('admin-approvals.actions.edit') }}
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger delete-workflow"
                                                        data-workflow-id="{{ $workflow->id }}" 
                                                        data-workflow-name="{{ $workflow->name }}">
                                                    <i class="fas fa-trash"></i> {{ __('admin-approvals.actions.delete') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('admin-approvals.messages.no_workflows_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $workflows->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Steps Modal -->
<div class="modal fade" id="viewStepsModal" tabindex="-1" aria-labelledby="viewStepsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewStepsModalLabel">{{ __('admin-approvals.workflow.workflow_steps') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('admin-approvals.actions.close') }}"></button>
            </div>
            <div class="modal-body">
                <div id="steps-container">
                    <!-- Steps will be displayed here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin-approvals.actions.close') }}</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Workflow Modal -->
<div class="modal fade" id="deleteWorkflowModal" tabindex="-1" aria-labelledby="deleteWorkflowModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteWorkflowModalLabel">{{ __('admin-approvals.actions.delete_workflow') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('admin-approvals.actions.close') }}"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('admin-approvals.messages.confirm_delete_workflow') }} <strong id="workflow-name"></strong>?</p>
                <p class="text-danger">{{ __('admin-approvals.messages.delete_workflow_warning') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('admin-approvals.actions.cancel') }}</button>
                <form id="delete-workflow-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('admin-approvals.actions.delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // View steps
        const viewStepsButtons = document.querySelectorAll('.view-steps');
        const stepsContainer = document.getElementById('steps-container');
        const viewStepsModal = document.getElementById('viewStepsModal');
        
        viewStepsButtons.forEach(button => {
            button.addEventListener('click', function() {
                const workflowId = this.getAttribute('data-workflow-id');
                const workflowName = this.getAttribute('data-workflow-name');
                
                // Update modal title
                document.getElementById('viewStepsModalLabel').textContent = `Approval Steps for ${workflowName}`;
                
                // Find workflow in the data
                try {
                    const workflowsData = {!! json_encode($workflows->toArray()) !!};
                    const workflow = workflowsData.data ? workflowsData.data.find(w => w.id == workflowId) : null;
                    
                    if (workflow) {
                    // Clear previous content
                    stepsContainer.innerHTML = '';
                    
                    if (workflow.steps && workflow.steps.length === 0) {
                        stepsContainer.innerHTML = '<div class="alert alert-info">{{ __('admin-approvals.messages.no_steps_found') }}</div>';
                    } else {
                        // Create step list
                        const stepList = document.createElement('ol');
                        stepList.className = 'list-group list-group-numbered';
                        
                        workflow.steps.sort((a, b) => a.order - b.order)
                            .forEach(step => {
                                const listItem = document.createElement('li');
                                listItem.className = 'list-group-item d-flex justify-content-between align-items-center';
                                
                                const content = document.createElement('div');
                                content.className = 'ms-2 me-auto';
                                
                                const name = document.createElement('div');
                                name.className = 'fw-bold';
                                name.textContent = step.user.name;
                                
                                const details = document.createElement('small');
                                details.innerHTML = `
                                    {{ __('admin-approvals.fields.required') }}: ${step.is_required ? '{{ __('admin-approvals.common.yes') }}' : '{{ __('admin-approvals.common.no') }}'} | 
                                    {{ __('admin-approvals.fields.can_reject') }}: ${step.can_reject ? '{{ __('admin-approvals.common.yes') }}' : '{{ __('admin-approvals.common.no') }}'} | 
                                    {{ __('admin-approvals.fields.can_edit') }}: ${step.can_edit ? '{{ __('admin-approvals.common.yes') }}' : '{{ __('admin-approvals.common.no') }}'}
                                `;
                                
                                content.appendChild(name);
                                content.appendChild(details);
                                
                                const badge = document.createElement('span');
                                badge.className = `badge ${step.is_active ? 'bg-success' : 'bg-secondary'} rounded-pill`;
                                badge.textContent = step.is_active ? '{{ __('admin-approvals.status.active') }}' : '{{ __('admin-approvals.status.inactive') }}';
                                
                                listItem.appendChild(content);
                                listItem.appendChild(badge);
                                stepList.appendChild(listItem);
                            });
                        
                        stepsContainer.appendChild(stepList);
                    }
                    } else {
                        stepsContainer.innerHTML = '<div class="alert alert-danger">{{ __('admin-approvals.validation.workflow_not_found') }}</div>';
                    }
                } catch (error) {
                    console.error('Error processing workflow data:', error);
                    stepsContainer.innerHTML = '<div class="alert alert-danger">{{ __('admin-approvals.messages.error_loading_data') }}</div>';
                }
                
                // Show modal
                const modal = new bootstrap.Modal(viewStepsModal);
                modal.show();
            });
        });
        
        // Delete workflow
        const deleteButtons = document.querySelectorAll('.delete-workflow');
        const deleteModal = document.getElementById('deleteWorkflowModal');
        const deleteForm = document.getElementById('delete-workflow-form');
        const workflowNameElement = document.getElementById('workflow-name');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const workflowId = this.getAttribute('data-workflow-id');
                const workflowName = this.getAttribute('data-workflow-name');
                
                workflowNameElement.textContent = workflowName;
                deleteForm.action = `/admin/approval-workflows/${workflowId}`;
                
                const modal = new bootstrap.Modal(deleteModal);
                modal.show();
            });
        });
    });
</script>
@endpush 
                
 