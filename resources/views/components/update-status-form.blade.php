@props(['user', 'availableStatuses'])

<div class="card border-0 rounded-4 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-4 pb-2">
        <div class="d-flex align-items-center">
            <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                <i class="fas fa-user-shield text-primary"></i>
            </div>
            <h4 class="card-title mb-0">Update User Status</h4>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.update-status', $user->id) }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="">Select Status</option>
                    @foreach($availableStatuses as $value => $label)
                        <option value="{{ $value }}" {{ $user->status == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="comment" class="form-label">Comment (Optional)</label>
                <textarea name="comment" id="comment" class="form-control @error('comment') is-invalid @enderror" rows="3"></textarea>
                @error('comment')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="form-text">Please provide a reason for this status change.</div>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i> Update Status
                </button>
            </div>
        </form>
    </div>
</div> 