@extends('layouts.app')

@section('title', 'Edit Profile')

@push('styles')
{{-- Cropper.js CSS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" integrity="sha512-UtLOu9C7NuThQhuXXrGwx9Jb/z9zPQJctuAgNUBK3Z6kkSYT9wJ+2+dh6klS+TDBCV9kNPBbAxbVD+vCcfGPaA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .avatar-upload {
        position: relative;
        max-width: 150px;
        margin: 0 auto;
    }
    .avatar-preview {
        width: 150px;
        height: 150px;
        position: relative;
        border-radius: 50%;
        overflow: hidden;
        cursor: pointer;
    }
    .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .avatar-edit {
        position: absolute;
        right: 0;
        bottom: 0;
        cursor: pointer;
    }
    .avatar-remove {
        position: absolute;
        left: 0;
        bottom: 0;
        cursor: pointer;
    }
    /* Cropper Modal */
    #cropper-modal .modal-lg {
        max-width: 800px;
    }
    #cropper-modal .modal-body {
        min-height: 400px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    #cropper-image-container {
        width: 100%;
        overflow: hidden;
        min-height: 300px;
    }
    #cropper-image {
        display: block;
        max-width: 100%;
        height: auto;
        opacity: 1 !important;
        visibility: visible !important;
    }
    /* Override potential Cropper inline styles if necessary */
    .cropper-container {
        width: 100% !important;
        height: auto !important;
        min-height: 300px;
        background: #f7f7f7;
    }
    .cropper-wrap-box, .cropper-canvas, .cropper-canvas img {
        width: 100% !important;
        height: auto !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    /* Ensure the crop box itself is visible if image loads but box doesn't */
    .cropper-crop-box {
        display: block !important;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">Edit Profile</h4>

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profile-form">
                        @csrf
                        @method('PUT')
                        
                        {{-- Hidden input for cropped image data --}}
                        <input type="hidden" name="cropped_avatar" id="cropped_avatar">

                        <div class="text-center mb-4">
                            <div class="avatar-upload">
                                <div class="avatar-preview">
                                    <img src="{{ $user->avatar ? Storage::url($user->avatar) : asset('images/default-avatar.png') }}"
                                         id="avatar-preview-img"
                                         alt="Profile Picture">
                                </div>
                                <div class="avatar-edit">
                                    <label for="avatar" class="btn btn-sm btn-primary mb-0">
                                        <i class="fas fa-camera"></i>
                                    </label>
                                </div>
                                @if($user->avatar)
                                <div class="avatar-remove">
                                    <button type="button" class="btn btn-sm btn-danger" id="remove-avatar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @endif
                            </div>
                            <input type="file" id="avatar" name="avatar" class="d-none" accept="image/jpeg,image/png,image/jpg">
                            @error('avatar')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                            @error('cropped_avatar')
                                <div class="text-danger mt-2">{{ $message }}</div> 
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="text" class="form-control" id="mobile"
                                       value="{{ $user->mobile }}" disabled>
                                <small class="text-muted">Mobile number cannot be changed</small>
                            </div>
                        </div>

                        @if($user->registration_type === 'personal')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select @error('gender') is-invalid @enderror"
                                            id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="region" class="form-label">Region</label>
                                    <input type="text" class="form-control @error('region') is-invalid @enderror"
                                           id="region" name="region" value="{{ old('region', $user->region) }}" required>
                                    @error('region')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="company_type" class="form-label">Company Type</label>
                                    <select class="form-select @error('company_type') is-invalid @enderror"
                                            id="company_type" name="company_type" required>
                                        <option value="">Select Type</option>
                                        <option value="private" {{ old('company_type', $user->company_type) === 'private' ? 'selected' : '' }}>Private</option>
                                        <option value="semi Govt." {{ old('company_type', $user->company_type) === 'semi Govt.' ? 'selected' : '' }}>Semi Government</option>
                                        <option value="Govt" {{ old('company_type', $user->company_type) === 'Govt' ? 'selected' : '' }}>Government</option>
                                    </select>
                                    @error('company_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="company_name" class="form-label">Company Name</label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                           id="company_name" name="company_name"
                                           value="{{ old('company_name', $user->company_name) }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="cr_number" class="form-label">CR Number</label>
                                    <input type="text" class="form-control @error('cr_number') is-invalid @enderror"
                                           id="cr_number" name="cr_number"
                                           value="{{ old('cr_number', $user->cr_number) }}" required>
                                    @error('cr_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="vat_number" class="form-label">VAT Number</label>
                                    <input type="text" class="form-control @error('vat_number') is-invalid @enderror"
                                           id="vat_number" name="vat_number"
                                           value="{{ old('vat_number', $user->vat_number) }}" required>
                                    @error('vat_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                           id="city" name="city"
                                           value="{{ old('city', $user->city) }}" required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="company_region" class="form-label">Region</label>
                                    <input type="text" class="form-control @error('company_region') is-invalid @enderror"
                                           id="company_region" name="company_region"
                                           value="{{ old('company_region', $user->company_region) }}" required>
                                    @error('company_region')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="building_number" class="form-label">Building Number</label>
                                    <input type="text" class="form-control @error('building_number') is-invalid @enderror"
                                           id="building_number" name="building_number"
                                           value="{{ old('building_number', $user->building_number) }}">
                                    @error('building_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="zip_code" class="form-label">ZIP Code</label>
                                    <input type="text" class="form-control @error('zip_code') is-invalid @enderror"
                                           id="zip_code" name="zip_code"
                                           value="{{ old('zip_code', $user->zip_code) }}" required>
                                    @error('zip_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone"
                                           value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        @if($requiresPassword)
                            <div class="mb-3">
                                <label for="password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                <small class="text-muted">Please enter your current password to save changes</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cropper Modal -->
<div class="modal fade" id="cropper-modal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropperModalLabel">Crop Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body"  >
                <div id="cropper-image-container">
                    <img id="cropper-image" src="" alt="Crop Target">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="crop-button">Crop</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
{{-- jQuery (ensure it's loaded first) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
{{-- Bootstrap Bundle (includes Popper) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
{{-- Cropper.js (Load this *before* the custom script below) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js" integrity="sha512-JyCZjCOZoyeQZSd5+YEAcFgz2fowJ1F1hyJOXgtKu4llIa0KneLcidn5bwfutiehUTiOuK87A986BZJMko0eWQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- Your custom script that uses Cropper --}}
<script>
    $(document).ready(function() {
        let cropper;
        const imageInput = document.getElementById('avatar');
        const preview = document.getElementById('avatar-preview-img');
        const modalElement = document.getElementById('cropper-modal');
        const cropTargetImage = document.getElementById('cropper-image');
        const cropButton = document.getElementById('crop-button');
        const croppedAvatarInput = document.getElementById('cropped_avatar');
        let modalInstance = null;
        let currentFile = null;

        if (modalElement) {
             modalInstance = new bootstrap.Modal(modalElement);

             modalElement.addEventListener('shown.bs.modal', function () {
                 console.log('Modal shown.');
                 if (currentFile && cropTargetImage && cropTargetImage.src) {
                     setTimeout(() => {
                        if (cropper) {
                            console.log('Destroying previous Cropper instance.');
                            cropper.destroy();
                        }
                        console.log('Initializing Cropper...');
                        try {
                            cropper = new Cropper(cropTargetImage, {
                                aspectRatio: 1,
                                viewMode: 1,
                                autoCropArea: 0.85,
                                responsive: true,
                                background: false,
                                checkOrientation: false,
                                movable: true,
                                zoomable: true,
                                rotatable: true,
                                scalable: true,
                                ready: function () {
                                    console.log('Cropper is ready!');
                                }
                            });
                            console.log('Cropper initialized successfully (after timeout).');
                        } catch (error) {
                            console.error('Error initializing Cropper:', error);
                        }
                     }, 100);
                 } else {
                     console.warn('Cropper initialization skipped.');
                 }
             });

             modalElement.addEventListener('hide.bs.modal', function () {
                 console.log('Modal hiding. Destroying Cropper if exists.');
                 if (cropper) {
                     cropper.destroy();
                     cropper = null;
                 }
                 if (croppedAvatarInput && !croppedAvatarInput.value && imageInput) {
                     imageInput.value = '';
                 }
                 currentFile = null;
             });
        }

        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                console.log('File input changed.');
                const files = e.target.files;
                if (files && files.length > 0) {
                    const file = files[0];
                    console.log('File selected:', file.name, file.type, file.size);
                    
                    // Basic type check
                    if (!file.type.startsWith('image/')){
                        console.error('Selected file is not an image.');
                        alert('Please select an image file (jpg, png, gif).'); // User feedback
                        imageInput.value = ''; // Clear input
                        return; 
                    }
                    
                    const reader = new FileReader();

                    reader.onload = function(event) {
                        console.log('FileReader onload triggered.');
                        if (!cropTargetImage || !modalInstance) {
                            console.error('Cannot load image to modal: Target or modal instance missing.');
                            return;
                        } 
                        currentFile = event.target.result;
                        console.log('Base64 data loaded (first 50 chars): ', currentFile.substring(0, 50) + '...');
                        try {
                            cropTargetImage.src = currentFile; // Set image source
                            console.log('Set cropTargetImage.src successfully.');
                            // Verify src right after setting
                            console.log('cropTargetImage.src after set:', cropTargetImage.src.substring(0, 50) + '...');
                            modalInstance.show(); // Show the modal
                            console.log('Modal show() called.');
                        } catch (error) {
                            console.error('Error setting image src or showing modal:', error);
                            alert('Could not display the selected image for cropping.');
                        }
                    };
                    
                    reader.onerror = function(event) {
                        console.error("FileReader error: ", event.target.error);
                        alert('Error reading the selected file.');
                    };
                    
                    reader.readAsDataURL(file);
                    console.log('FileReader readAsDataURL called.');
                } else {
                    console.log('No file selected or files array empty.');
                }
            });
        }

        if (cropButton) {
            cropButton.addEventListener('click', function() {
                if (cropper) {
                    const canvas = cropper.getCroppedCanvas({
                        width: 250,
                        height: 250,
                        imageSmoothingEnabled: true,
                        imageSmoothingQuality: 'high',
                    });

                    if(preview) preview.src = canvas.toDataURL();
                    if(croppedAvatarInput) croppedAvatarInput.value = canvas.toDataURL('image/png');

                    if(modalInstance) modalInstance.hide();
                }
            });
        }

        const removeBtn = document.getElementById('remove-avatar');
        if (removeBtn) {
             $('#remove-avatar').on('click', function() {
                if (confirm('Are you sure you want to remove your avatar?')) {
                    $.ajax({
                        url: '{{ route("profile.remove-avatar") }}',
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if(preview) preview.src = '{{ asset("images/default-avatar.png") }}';
                            if(croppedAvatarInput) croppedAvatarInput.value = '';
                            if(imageInput) imageInput.value = '';
                            $('.avatar-remove').remove();
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Removed!', 'Your avatar has been removed.', 'success');
                            } else {
                                alert('Avatar removed successfully.');
                            }
                        },
                        error: function(xhr) {
                            console.error('Error removing avatar:', xhr.responseText);
                            if (typeof Swal !== 'undefined') {
                                Swal.fire('Error', 'Failed to remove avatar.', 'error');
                            } else {
                                alert('Failed to remove avatar.');
                            }
                        }
                    });
                }
            });
        }
    });
</script>
@endpush 