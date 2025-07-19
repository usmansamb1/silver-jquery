

<?php $__env->startSection('title', __('Profile')); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Profile Header -->
            <div class="card border-0 rounded-4 shadow-sm mb-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="bg-primary bg-gradient text-white py-5 px-4 position-relative" style="min-height: 180px;">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="position-relative">
                                    <img src="<?php echo e($user->avatar ? Storage::url($user->avatar) : asset('images/default-avatar.png')); ?>"
                                         class="rounded-circle border border-4 border-white shadow"
                                         style="width: 150px; height: 150px; object-fit: cover;"
                                         alt="Profile Picture">
                                </div>
                            </div>
                            <div class="col ps-md-4">
                                <h2 class="display-6 fw-bold mb-1"><?php echo e($user->name ?? $user->company_name); ?></h2>
                                <div class="d-flex align-items-center mb-2">
                                    <span class="badge bg-light text-primary fs-6 me-2">
                                        <?php echo e($user->roles->pluck('name')->implode(', ')); ?>

                                    </span>
                                    <?php if($user->formatted_customer_no): ?>
                                    <span class="badge bg-light text-primary fs-6">
                                        Customer #<?php echo e($user->formatted_customer_no); ?>

                                    </span>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-0">
                                    <i class="fas fa-envelope me-2"></i> <?php echo e($user->email); ?>

                                    <span class="ms-3">
                                        <i class="fas fa-phone me-2"></i> <?php echo e($user->mobile); ?>

                                    </span>
                                </p>
                            </div>
                            <div class="col-auto">
                                <a href="<?php echo e(route('profile.edit')); ?>" class="btn btn-light">
                                    <i class="fas fa-edit"></i> <?php echo e(__('Edit Profile')); ?>

                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Personal Information Card -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 rounded-4 shadow-sm">
                        <div class="card-header bg-white border-0 pt-4 pb-2">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <h4 class="card-title mb-0"><?php echo e(__('Personal Information')); ?></h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('Mobile')); ?>:</span>
                                    <span class="fw-medium"><?php echo e($user->mobile); ?></span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('Email')); ?>:</span>
                                    <span class="fw-medium"><?php echo e($user->email); ?></span>
                                </li>
                                <?php if($user->registration_type === 'personal'): ?>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('Gender')); ?>:</span>
                                    <span class="fw-medium"><?php echo e(ucfirst($user->gender ?? __('Not specified'))); ?></span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('Region')); ?>:</span>
                                    <span class="fw-medium"><?php echo e($user->region); ?></span>
                                </li>
                                <?php endif; ?>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('Customer No')); ?>:</span>
                                    <span class="fw-medium"><?php echo e($user->formatted_customer_no ?? 'N/A'); ?></span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('Status')); ?>:</span>
                                    <span class="fw-medium d-flex align-items-center">
                                        <?php echo $statusBadge; ?>

                                        <a href="<?php echo e(route('profile.status-history')); ?>" class="ms-2 small">
                                            <i class="fas fa-history"></i> <?php echo e(__('View History')); ?>

                                        </a>
                                    </span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('Terms & Conditions')); ?>:</span>
                                    <span class="fw-medium">
                                        <?php if($user->terms_accepted_at): ?>
                                            <span class="text-success d-flex align-items-center">
                                                <i class="fas fa-check-circle me-1"></i>
                                                <?php echo e(__('Accepted')); ?> <?php echo e($user->terms_accepted_at->format('M j, Y g:i A')); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-danger d-flex align-items-center">
                                                <i class="fas fa-times-circle me-1"></i>
                                                <?php echo e(__('Not Accepted')); ?>

                                            </span>
                                        <?php endif; ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Company Details Card -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100 border-0 rounded-4 shadow-sm">
                        <div class="card-header bg-white border-0 pt-4 pb-2">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <?php if(!$user->hasRole('customer')): ?>
                                    <i class="fas fa-clock text-primary"></i>
                                    <?php else: ?>
                                    <i class="fas fa-building text-primary"></i>
                                    <?php endif; ?>
                                </div>
                                <h4 class="card-title mb-0"><?php echo e(__('Company Details')); ?></h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if($user->registration_type === 'company'): ?>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('Company')); ?>:</span>
                                    <span class="fw-medium"><?php echo e($user->company_name); ?></span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('Type')); ?>:</span>
                                    <span class="fw-medium"><?php echo e($user->company_type); ?></span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('CR Number')); ?>:</span>
                                    <span class="fw-medium"><?php echo e($user->cr_number); ?></span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('VAT Number')); ?>:</span>
                                    <span class="fw-medium"><?php echo e($user->vat_number); ?></span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('Phone')); ?>:</span>
                                    <span class="fw-medium"><?php echo e($user->phone ?? __('Not provided')); ?></span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0 border-bottom">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('City')); ?>:</span>
                                    <span class="fw-medium"><?php echo e($user->city); ?></span>
                                </li>
                                <li class="list-group-item px-0 py-3 d-flex border-0">
                                    <span class="text-muted me-2 w-25"><?php echo e(__('Region')); ?>:</span>
                                    <span class="fw-medium"><?php echo e($user->company_region); ?></span>
                                </li>
                            </ul>
                            <?php else: ?>
                                <?php if(!$user->hasRole('customer')): ?>
                                <div class="col-md-12">
                                    <div class="card border-0   mb-3">
                                        <div class="card-body text-center py-4">
                                             
                                            <h5><?php echo e(__('Last Login')); ?></h5>
                                            <h3 class="text-primary">
                                                <?php
                                                    // Direct DB query for last login with random param to avoid browser caching
                                                    $lastLogin = DB::table('users')
                                                        ->where('id', $user->id)
                                                        ->value('last_login_at');
                                                    
                                                    $formattedLastLogin = 'N/A';
                                                    if (!empty($lastLogin)) {
                                                        try {
                                                            // Don't use diffForHumans() for last login since it changes on refresh
                                                            // Instead use a fixed format that won't change unless the actual value changes
                                                            $carbonDate = \Carbon\Carbon::parse($lastLogin);
                                                            $formattedLastLogin = $carbonDate->format('M j, Y g:i A');
                                                        } catch (\Exception $e) {
                                                            $formattedLastLogin = 'N/A';
                                                        }
                                                    }
                                                ?>
                                                <?php echo e($formattedLastLogin); ?>

                                            </h3>
                                            <a href="<?php echo e(route('user.logs.index')); ?>" class="btn btn-sm btn-primary mt-2">
                                                <?php echo e(__('View Activity')); ?>

                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                            <div class="text-center py-5">
                                <div class="icon-box bg-light rounded-circle p-4 mx-auto mb-3" style="width: fit-content;">
                                    <i class="fas fa-info-circle text-muted fa-2x"></i>
                                </div>
                                <h5 class="text-muted"><?php echo e(__('No Company Information')); ?></h5>
                                <p class="text-muted mb-0"><?php echo e(__('This is a personal account without company details.')); ?></p>
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Account Activity Card -->
                <div class="col-12 mb-4">
                    <div class="card border-0 rounded-4 shadow-sm">
                        <div class="card-header bg-white border-0 pt-4 pb-2">
                            <div class="d-flex align-items-center">
                                <div class="icon-box bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="fas fa-chart-bar text-primary"></i>
                                </div>
                                <h4 class="card-title mb-0"><?php echo e(__('Account Activity')); ?></h4>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php if($user->hasRole('customer')): ?>
                                <div class="col-md-4">
                                    <div class="card border-0 bg-light mb-3">
                                        <div class="card-body text-center py-4">
                                            <div class="icon-box bg-white rounded-circle p-3 mx-auto mb-3" style="width: fit-content;">
                                                <i class="fas fa-wallet text-primary"></i>
                                            </div>
                                            <h5><?php echo e(__('Wallet Balance')); ?></h5>
                                            <h3 class="text-primary">
                                                <?php if(isset($user->wallet)): ?>
                                                    SAR <?php echo e(number_format($user->wallet->balance, 2)); ?>

                                                <?php else: ?>
                                                    SAR 0.00
                                                <?php endif; ?>
                                            </h3>
                                            <a href="<?php echo e(route('wallet.index')); ?>" class="btn btn-sm btn-primary mt-2">
                                                <?php echo e(__('Manage Wallet')); ?>

                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if($user->hasRole('customer')): ?>
                                <div class="<?php echo e($user->hasRole('customer') ? 'col-md-4' : 'col-md-6'); ?>">
                                    <div class="card border-0 bg-light mb-3">
                                        <div class="card-body text-center py-4">
                                            <div class="icon-box bg-white rounded-circle p-3 mx-auto mb-3" style="width: fit-content;">
                                                <i class="fas fa-calendar-check text-primary"></i>
                                            </div>
                                            <h5><?php echo e(__('Service Bookings')); ?></h5>
                                            <h3 class="text-primary">
                                                <?php
                                                    // Direct DB query for service bookings
                                                    $bookingsCount = \App\Models\ServiceBooking::where('user_id', $user->id)->count();
                                                    $ordersCount = 0;
                                                    if (class_exists('\App\Models\ServiceOrder')) {
                                                        $ordersCount = \App\Models\ServiceOrder::where('user_id', $user->id)->count();
                                                    }
                                                    $totalCount = $bookingsCount + $ordersCount;
                                                ?>
                                                <?php echo e($totalCount); ?>

                                            </h3>
                                            <a href="<?php echo e(route('services.booking.history')); ?>" class="btn btn-sm btn-primary mt-2">
                                                <?php echo e(__('View History')); ?>

                                            </a>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="<?php echo e($user->hasRole('customer') ? 'col-md-4' : 'col-md-6'); ?>">
                                    <div class="card border-0 bg-light mb-3">
                                        <div class="card-body text-center py-4">
                                            <div class="icon-box bg-white rounded-circle p-3 mx-auto mb-3" style="width: fit-content;">
                                                <i class="fas fa-clock text-primary"></i>
                                            </div>
                                            <h5><?php echo e(__('Last Login')); ?></h5>
                                            <h3 class="text-primary">
                                                <?php
                                                    // Direct DB query for last login with random param to avoid browser caching
                                                    $lastLogin = DB::table('users')
                                                        ->where('id', $user->id)
                                                        ->value('last_login_at');
                                                    
                                                    $formattedLastLogin = 'N/A';
                                                    if (!empty($lastLogin)) {
                                                        try {
                                                            // Don't use diffForHumans() for last login since it changes on refresh
                                                            // Instead use a fixed format that won't change unless the actual value changes
                                                            $carbonDate = \Carbon\Carbon::parse($lastLogin);
                                                            $formattedLastLogin = $carbonDate->format('M j, Y g:i A');
                                                        } catch (\Exception $e) {
                                                            $formattedLastLogin = 'N/A';
                                                        }
                                                    }
                                                ?>
                                                <?php echo e($formattedLastLogin); ?>

                                            </h3>
                                            <a href="<?php echo e(route('user.logs.index')); ?>" class="btn btn-sm btn-primary mt-2">
                                                <?php echo e(__('View Activity')); ?>

                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .icon-box {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .icon-box i {
        font-size: 1.5rem;
    }
    
    .fw-medium {
        font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .icon-box {
            width: 40px;
            height: 40px;
        }
        
        .icon-box i {
            font-size: 1.2rem;
        }
    }
</style>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp81\htdocs\aljeri-joil-yaseer-o3mhigh\resources\views/profile/show.blade.php ENDPATH**/ ?>