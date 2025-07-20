@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">{{ $request->workflow->name ?? 'OFF SHELF' }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        @if(auth()->user()->hasRole(['admin', 'finance', 'audit', 'it', 'contractor', 'activation', 'validation']))
                            <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                        @else
                            <a href="{{ route('home') }}">Dashboard</a>
                        @endif
                    </li>
                    <li class="breadcrumb-item active">{{ $request->reference_no }}</li>
                </ol>
            </nav>

            <a href="{{ route('wallet.approvals.index') }}" class="btn btn-outline-primary mb-3">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>

            <!-- Application Details Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Application Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Application ID #</div>
                                <div class="col-md-8">{{ $request->id }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Vendor Name</div>
                                <div class="col-md-8">{{ $request->metadata['vendor_name'] ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Vendor Type</div>
                                <div class="col-md-8">{{ $request->metadata['vendor_type'] ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Contract Reference No</div>
                                <div class="col-md-8">{{ $request->metadata['contract_reference'] ?? 'N/A' }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Description</div>
                                <div class="col-md-8">{{ $request->description }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Created Date</div>
                                <div class="col-md-8">{{ $request->created_at->format('d-F-Y H:i:s A') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Status</div>
                                <div class="col-md-8">
                                    <span class="badge" style="background-color: {{ $request->status->color }}">
                                        {{ $request->status->name }}
                                    </span>
                                    <br>
                                    <small>Under Approval {{ auth()->user()->name }}</small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">Start Date</div>
                                <div class="col-md-8">{{ $request->metadata['start_date'] ?? $request->created_at->format('d-F-Y') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">End Date</div>
                                <div class="col-md-8">{{ $request->metadata['end_date'] ?? $request->created_at->addDays(30)->format('d-F-Y') }}</div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4 fw-bold">System Calc. Activity Period</div>
                                <div class="col-md-8">{{ $request->metadata['system_activity_period'] ?? '1.00' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Shelf Details Section -->
                    @if(isset($request->metadata['shelf_details']) && count($request->metadata['shelf_details']) > 0)
                    <div class="mt-4">
                        <h6 class="fw-bold">Shelf Details :-</h6>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Brand</th>
                                        <th>Store</th>
                                        <th>Shelf Type</th>
                                        <th>Size</th>
                                        <th>Remarks</th>
                                        <th>Duration</th>
                                        <th>Calculate Period</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                        <th>Discount</th>
                                        <th>Sub total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($request->metadata['shelf_details'] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item['brand'] ?? 'PANACEA' }}</td>
                                        <td>{{ $item['store'] ?? '' }}</td>
                                        <td>{{ $item['shelf_type'] ?? '' }}</td>
                                        <td>{{ $item['size'] ?? '' }}</td>
                                        <td>{{ $item['remarks'] ?? '' }}</td>
                                        <td>{{ $item['duration'] ?? '' }}</td>
                                        <td>{{ $item['calculate_period'] ?? '0' }}</td>
                                        <td>{{ $item['quantity'] ?? '1' }}</td>
                                        <td>{{ number_format($item['price'] ?? 0, 2) }}</td>
                                        <td>{{ $item['discount'] ?? '0' }}%</td>
                                        <td>{{ number_format($item['subtotal'] ?? 0, 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="10" class="text-end fw-bold">Grand Total</td>
                                        <td colspan="2" class="fw-bold">
                                            SAR {{ number_format($request->amount, 2) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Approval Policies Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Approval Policies :-</h5>
                </div>
                <div class="card-body">
                    <div class="approval-flow d-flex justify-content-between align-items-center">
                        @foreach($request->workflow->steps as $index => $step)
                        <div class="approval-step text-center">
                            @php
                                $stepAction = $step->getLastActionForRequest($request);
                                $status = $stepAction ? $stepAction->action : 'pending';
                                $icon = $status == 'approve' ? 'check' : ($status == 'reject' ? 'times' : 'hourglass');
                                $color = $status == 'approve' ? 'success' : ($status == 'reject' ? 'danger' : 'warning');
                                $isCurrentStep = $request->getCurrentStep() && $request->getCurrentStep()->id == $step->id;
                            @endphp
                            
                            <div class="approval-icon mb-2">
                                <span class="rounded-circle d-inline-flex justify-content-center align-items-center p-3 
                                            bg-{{ $color }} text-white" style="width: 50px; height: 50px;">
                                    <i class="fas fa-{{ $icon }}"></i>
                                </span>
                            </div>
                            <div class="approval-user">
                                {{ $step->user->name }}
                            </div>
                            
                            @if($isCurrentStep && $request->canBeApprovedBy(auth()->user()))
                            <button type="button" class="btn btn-sm btn-primary mt-2" 
                                    data-bs-toggle="modal" data-bs-target="#approvalModal">
                                Take Action
                            </button>
                            @endif
                        </div>
                        
                        @if(!$loop->last)
                        <div class="approval-connector flex-grow-1 mx-2">
                            <hr style="border-top: 2px dashed #ccc;">
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Approvals History Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Approvals History :</h5>
                </div>
                <div class="card-body">
                    <div class="approval-history">
                        @forelse($request->actions as $action)
                        <div class="history-item mb-4">
                            <div class="d-flex">
                                <div class="history-timeline">
                                    <div class="history-date bg-light p-2 text-center border rounded">
                                        <strong>{{ $action->user->name }}</strong><br>
                                        {{ $action->created_at->format('d-F-Y') }}<br>
                                        {{ $action->created_at->format('h:i:s A') }}
                                    </div>
                                </div>
                                <div class="history-content border rounded p-3 ms-3 flex-grow-1">
                                    <h6 class="d-flex justify-content-between">
                                        <span>Action Taken : <span class="text-{{ $action->action == 'approve' ? 'success' : 'danger' }}">
                                            {{ ucfirst($action->action) }}
                                        </span></span>
                                    </h6>
                                    <div class="mt-2">
                                        <p><strong>Full name:</strong> {{ $action->metadata['full_name'] ?? $action->user->name }}</p>
                                        <p><strong>Reason:</strong> {{ $action->comment }}</p>
                                        <p><strong>Description:</strong> {{ $action->metadata['description'] ?? 'N/A' }}</p>
                                        <p><strong>Submitted by:</strong> {{ $action->user->name }}</p>
                                        <p><strong>Date:</strong> {{ $action->created_at->format('d-F-Y h:i:s A') }}</p>
                                    </div>
                                    
                                    @if($action->attachment)
                                    <div class="mt-2">
                                        <a href="{{ route('wallet.approvals.download-attachment', [$request->id, $action->id]) }}" 
                                           class="btn btn-sm btn-secondary">
                                            <i class="fas fa-download"></i> Download Attachment
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="alert alert-info">
                            No action history available yet.
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="approvalModalLabel">Approval Form</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name">
                </div>
                
                <div class="mb-3">
                    <label for="reason" class="form-label">Reason</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                
                <div class="mb-3">
                    <label for="attachment" class="form-label">Attachment</label>
                    <input type="file" class="form-control" id="attachment" name="attachment">
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <form action="{{ route('wallet.approvals.reject', $request) }}" method="POST" id="rejectForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="full_name" id="reject_full_name">
                    <input type="hidden" name="comment" id="reject_reason">
                    <input type="hidden" name="description" id="reject_description">
                    <button type="button" class="btn btn-danger" id="rejectBtn">Reject</button>
                </form>
                
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <form action="{{ route('wallet.approvals.approve', $request) }}" method="POST" id="approveForm" class="d-inline" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="full_name" id="approve_full_name">
                        <input type="hidden" name="comment" id="approve_reason">
                        <input type="hidden" name="description" id="approve_description">
                        <button type="button" class="btn btn-success" id="approveBtn">Approve</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fullNameInput = document.getElementById('full_name');
        const reasonInput = document.getElementById('reason');
        const descriptionInput = document.getElementById('description');
        const attachmentInput = document.getElementById('attachment');
        
        const approveBtn = document.getElementById('approveBtn');
        const rejectBtn = document.getElementById('rejectBtn');
        
        const approveForm = document.getElementById('approveForm');
        const rejectForm = document.getElementById('rejectForm');
        
        // Handle form submission for approve
        approveBtn.addEventListener('click', function() {
            document.getElementById('approve_full_name').value = fullNameInput.value;
            document.getElementById('approve_reason').value = reasonInput.value;
            document.getElementById('approve_description').value = descriptionInput.value;
            
            // Handle file attachment
            if (attachmentInput.files.length > 0) {
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = 'attachment';
                fileInput.files = attachmentInput.files;
                approveForm.appendChild(fileInput);
            }
            
            approveForm.submit();
        });
        
        // Handle form submission for reject
        rejectBtn.addEventListener('click', function() {
            document.getElementById('reject_full_name').value = fullNameInput.value;
            document.getElementById('reject_reason').value = reasonInput.value;
            document.getElementById('reject_description').value = descriptionInput.value;
            
            // Handle file attachment
            if (attachmentInput.files.length > 0) {
                const fileInput = document.createElement('input');
                fileInput.type = 'file';
                fileInput.name = 'attachment';
                fileInput.files = attachmentInput.files;
                rejectForm.appendChild(fileInput);
            }
            
            rejectForm.submit();
        });
    });
</script>
@endpush 