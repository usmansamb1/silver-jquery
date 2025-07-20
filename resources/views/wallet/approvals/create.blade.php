@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create Wallet Approval Request</h5>
                    <a href="{{ route('wallet.approvals.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
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

                    <form action="{{ route('wallet.approvals.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="workflow_id" class="form-label">Approval Workflow <span class="text-danger">*</span></label>
                                    <select class="form-select @error('workflow_id') is-invalid @enderror" 
                                           id="workflow_id" name="workflow_id" required>
                                        <option value="">-- Select Workflow --</option>
                                        @foreach($workflows as $workflow)
                                            <option value="{{ $workflow->id }}" {{ old('workflow_id') == $workflow->id ? 'selected' : '' }}>
                                                {{ $workflow->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('workflow_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0.01" 
                                               class="form-control @error('amount') is-invalid @enderror" 
                                               id="amount" name="amount" value="{{ old('amount') }}" required>
                                        <span class="input-group-text">SAR</span>
                                    </div>
                                    @error('amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="vendor_name" class="form-label">Vendor Name</label>
                                    <input type="text" class="form-control @error('metadata.vendor_name') is-invalid @enderror" 
                                           id="vendor_name" name="metadata[vendor_name]" value="{{ old('metadata.vendor_name') }}">
                                    @error('metadata.vendor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="vendor_type" class="form-label">Vendor Type</label>
                                    <select class="form-select @error('metadata.vendor_type') is-invalid @enderror" 
                                           id="vendor_type" name="metadata[vendor_type]">
                                        <option value="">-- Select Type --</option>
                                        <option value="supplier" {{ old('metadata.vendor_type') == 'supplier' ? 'selected' : '' }}>Supplier</option>
                                        <option value="contractor" {{ old('metadata.vendor_type') == 'contractor' ? 'selected' : '' }}>Contractor</option>
                                        <option value="service" {{ old('metadata.vendor_type') == 'service' ? 'selected' : '' }}>Service Provider</option>
                                        <option value="other" {{ old('metadata.vendor_type') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('metadata.vendor_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="contract_reference" class="form-label">Contract Reference</label>
                                    <input type="text" class="form-control @error('metadata.contract_reference') is-invalid @enderror" 
                                           id="contract_reference" name="metadata[contract_reference]" value="{{ old('metadata.contract_reference') }}">
                                    @error('metadata.contract_reference')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Supporting Documents</label>
                                    <input type="file" class="form-control @error('attachments') is-invalid @enderror" 
                                           id="attachments" name="attachments[]" multiple>
                                    <div class="form-text">Upload supporting documents (PDF, JPG, PNG). Max 5MB per file.</div>
                                    @error('attachments')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 