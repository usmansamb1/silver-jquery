@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('admin-approvals.workflow.edit_workflow') }}</h5>
                    <a href="{{ route('admin.approval-workflows.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('admin-approvals.actions.back_to_list') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('admin.approval-workflows.update', $workflow) }}" method="POST" id="workflow-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="mb-3">{{ __('admin-approvals.workflow.workflow_details') }}</h6>
                                <div class="mb-3">
                                    <label for="name" class="form-label">{{ __('admin-approvals.workflow.workflow_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $workflow->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{ __('admin-approvals.fields.description') }}</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description', $workflow->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" 
                                           id="is_active" name="is_active" value="1" 
                                           {{ old('is_active', $workflow->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        {{ __('admin-approvals.status.active') }}
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" 
                                           id="notify_by_email" name="notify_by_email" value="1" 
                                           {{ old('notify_by_email', $workflow->notify_by_email) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_by_email">
                                        {{ __('admin-approvals.settings.email_notifications') }}
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" 
                                           id="notify_by_sms" name="notify_by_sms" value="1" 
                                           {{ old('notify_by_sms', $workflow->notify_by_sms) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="notify_by_sms">
                                        {{ __('admin-approvals.settings.sms_notifications') }}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="mb-3">{{ __('admin-approvals.workflow.workflow_steps') }} <span class="text-danger">*</span></h6>
                                <p class="text-muted small">{{ __('admin-approvals.workflow.workflow_steps_help') }}</p>
                                
                                <div class="mb-3">
                                    <select class="form-select" id="user-select">
                                        <option value="">-- {{ __('admin-approvals.actions.select_user') }} --</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" data-name="{{ $user->name }}">
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-primary" id="add-approver">
                                            <i class="fas fa-plus"></i> {{ __('admin-approvals.actions.add_approver') }}
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <div class="alert alert-info d-none" id="no-approvers-message">
                                        {{ __('admin-approvals.messages.no_approvers_added') }}
                                    </div>
                                    <ul class="list-group" id="approvers-list">
                                        <!-- Existing approvers will be loaded via JavaScript -->
                                    </ul>
                                </div>
                                
                                @error('approvers')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('admin-approvals.workflow.update_workflow') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const userSelect = document.getElementById('user-select');
        const addApproverBtn = document.getElementById('add-approver');
        const approversList = document.getElementById('approvers-list');
        const noApproversMessage = document.getElementById('no-approvers-message');
        const workflowForm = document.getElementById('workflow-form');
        
        // Initialize with existing steps
        const existingSteps = @json($workflow->steps);
        
        // Load existing steps
        try {
            if (existingSteps && existingSteps.length > 0) {
                existingSteps.sort((a, b) => a.order - b.order).forEach(step => {
                    addApproverToList(step.user_id, step.user.name);
                });
            }
        } catch (error) {
            console.error('Error loading existing steps:', error);
        }
        
        // Initialize Sortable.js
        const sortable = new Sortable(approversList, {
            animation: 150,
            handle: '.handle',
            onEnd: updateInputOrder
        });
        
        // Show/hide no approvers message
        function updateNoApproversMessage() {
            if (approversList.children.length === 0) {
                noApproversMessage.classList.remove('d-none');
            } else {
                noApproversMessage.classList.add('d-none');
            }
            updateInputOrder();
        }
        
        // Update hidden input order
        function updateInputOrder() {
            // Remove all existing hidden inputs
            document.querySelectorAll('input[name^="approvers["]').forEach(input => input.remove());
            
            // Create new hidden inputs based on current order
            Array.from(approversList.children).forEach((item, index) => {
                const userId = item.getAttribute('data-user-id');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `approvers[${index}]`;
                input.value = userId;
                workflowForm.appendChild(input);
            });
        }
        
        // Add approver to list function
        function addApproverToList(userId, userName) {
            // Create list item
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.setAttribute('data-user-id', userId);
            
            // Create the main content
            const content = document.createElement('div');
            content.className = 'd-flex align-items-center';
            
            // Create drag handle
            const handle = document.createElement('span');
            handle.className = 'handle me-2';
            handle.innerHTML = '<i class="fas fa-grip-vertical text-muted"></i>';
            
            // Create the user info
            const userInfo = document.createElement('div');
            userInfo.innerHTML = `
                <strong>${userName}</strong> 
                <small class="text-muted">({{ __('admin-approvals.fields.step') }} ${approversList.children.length + 1})</small>
            `;
            
            // Create remove button
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn btn-sm btn-danger';
            removeBtn.innerHTML = '<i class="fas fa-times"></i> {{ __('admin-approvals.actions.remove') }}';
            removeBtn.addEventListener('click', function() {
                li.remove();
                updateNoApproversMessage();
            });
            
            // Assemble everything
            content.appendChild(handle);
            content.appendChild(userInfo);
            li.appendChild(content);
            li.appendChild(removeBtn);
            
            // Add to the list
            approversList.appendChild(li);
            
            // Update UI and form
            updateNoApproversMessage();
        }
        
        // Add approver button click handler
        addApproverBtn.addEventListener('click', function() {
            try {
                const userId = userSelect.value;
                if (!userId) {
                    alert('{{ __('admin-approvals.validation.select_user_required') }}');
                    return;
                }
            
            // Check if this user is already in the list
            if (document.querySelector(`li[data-user-id="${userId}"]`)) {
                alert('{{ __('admin-approvals.validation.user_already_added') }}');
                return;
            }
            
            const userName = userSelect.options[userSelect.selectedIndex].getAttribute('data-name');
            addApproverToList(userId, userName);
            
                // Reset select
                userSelect.value = '';
            } catch (error) {
                console.error('Error adding approver:', error);
                alert('{{ __('admin-approvals.messages.error_adding_approver') }}');
            }
        });
        
        // Form submit validation
        workflowForm.addEventListener('submit', function(e) {
            try {
                if (approversList.children.length === 0) {
                    e.preventDefault();
                    alert('{{ __('admin-approvals.validation.at_least_one_approver') }}');
                }
            } catch (error) {
                console.error('Error during form validation:', error);
                e.preventDefault();
                alert('{{ __('admin-approvals.messages.error_validating_form') }}');
            }
        });
        
        // Initial update
        updateNoApproversMessage();
    });
</script>
@endpush 