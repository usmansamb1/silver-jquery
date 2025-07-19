

<?php $__env->startSection('title', __('My Activity Logs')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary"><?php echo e(__('My Activity History')); ?></h5>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <form action="<?php echo e(route('user.logs.index')); ?>" method="get" class="row g-3">
                            <div class="col-md-4">
                                <label for="event" class="form-label"><?php echo e(__('Activity Type')); ?></label>
                                <select name="event" id="event" class="form-select">
                                    <option value=""><?php echo e(__('All Activities')); ?></option>
                                    <?php $__currentLoopData = $eventTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $eventType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($eventType); ?>" <?php echo e(request()->event == $eventType ? 'selected' : ''); ?>>
                                            <?php if($eventType == 'login'): ?>
                                                <?php echo e(__('Login Activity')); ?>

                                            <?php elseif($eventType == 'wallet_recharge'): ?>
                                                <?php echo e(__('Wallet Recharge')); ?>

                                            <?php elseif($eventType == 'service_booking'): ?>
                                                <?php echo e(__('Service Booking')); ?>

                                            <?php elseif($eventType == 'profile_update'): ?>
                                                <?php echo e(__('Profile Update')); ?>

                                            <?php elseif($eventType == 'vehicle_created'): ?>
                                                <?php echo e(__('Vehicle Added')); ?>

                                            <?php elseif($eventType == 'vehicle_updated'): ?>
                                                <?php echo e(__('Vehicle Updated')); ?>

                                            <?php elseif($eventType == 'vehicle_deleted'): ?>
                                                <?php echo e(__('Vehicle Deleted')); ?>

                                            <?php elseif($eventType == 'rfid_transfer_initiated'): ?>
                                                <?php echo e(__('RFID Transfer Initiated')); ?>

                                            <?php elseif($eventType == 'rfid_transfer_completed'): ?>
                                                <?php echo e(__('RFID Transfer Completed')); ?>

                                            <?php elseif($eventType == 'rfid_transfer_cancelled'): ?>
                                                <?php echo e(__('RFID Transfer Cancelled')); ?>

                                            <?php elseif($eventType == 'rfid_recharge'): ?>
                                                <?php echo e(__('RFID Recharged')); ?>

                                            <?php else: ?>
                                                <?php echo e(ucfirst(str_replace('_', ' ', $eventType))); ?>

                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label for="date_from" class="form-label"><?php echo e(__('From Date')); ?></label>
                                <input type="date" name="date_from" id="date_from" class="form-control" value="<?php echo e(request()->date_from); ?>">
                            </div>
                            
                            <div class="col-md-3">
                                <label for="date_to" class="form-label"><?php echo e(__('To Date')); ?></label>
                                <input type="date" name="date_to" id="date_to" class="form-control" value="<?php echo e(request()->date_to); ?>">
                            </div>
                            
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter"></i> <?php echo e(__('Filter')); ?>

                                </button>
                                <a href="<?php echo e(route('user.logs.index')); ?>" class="btn btn-secondary">
                                    <i class="fas fa-sync"></i> <?php echo e(__('Reset')); ?>

                                </a>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Activity Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th><?php echo e(__('Date & Time')); ?></th>
                                    <th><?php echo e(__('Activity')); ?></th>
                                    <th><?php echo e(__('Description')); ?></th>
                                    <th><?php echo e(__('Details')); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></td>
                                    <td>
                                        <?php if($log->event == 'login'): ?>
                                            <span class="badge bg-primary"><?php echo e(__('Login Activity')); ?></span>
                                        <?php elseif($log->event == 'wallet_recharge'): ?>
                                            <span class="badge bg-success"><?php echo e(__('Wallet Recharge')); ?></span>
                                        <?php elseif($log->event == 'service_booking'): ?>
                                            <span class="badge bg-info"><?php echo e(__('Service Booking')); ?></span>
                                        <?php elseif($log->event == 'profile_update'): ?>
                                            <span class="badge bg-warning"><?php echo e(__('Profile Update')); ?></span>
                                        <?php elseif($log->event == 'vehicle_created'): ?>
                                            <span class="badge bg-success"><?php echo e(__('Vehicle Added')); ?></span>
                                        <?php elseif($log->event == 'vehicle_updated'): ?>
                                            <span class="badge bg-warning"><?php echo e(__('Vehicle Updated')); ?></span>
                                        <?php elseif($log->event == 'vehicle_deleted'): ?>
                                            <span class="badge bg-danger"><?php echo e(__('Vehicle Deleted')); ?></span>
                                        <?php elseif($log->event == 'rfid_transfer_initiated'): ?>
                                            <span class="badge bg-info"><?php echo e(__('RFID Transfer Initiated')); ?></span>
                                        <?php elseif($log->event == 'rfid_transfer_completed'): ?>
                                            <span class="badge bg-success"><?php echo e(__('RFID Transfer Completed')); ?></span>
                                        <?php elseif($log->event == 'rfid_transfer_cancelled'): ?>
                                            <span class="badge bg-danger"><?php echo e(__('RFID Transfer Cancelled')); ?></span>
                                        <?php elseif($log->event == 'rfid_recharge'): ?>
                                            <span class="badge bg-success"><?php echo e(__('RFID Recharged')); ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary"><?php echo e(ucfirst(str_replace('_', ' ', $log->event))); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php echo e($log->description); ?>

                                        <?php if($log->event == 'wallet_recharge' && isset($log->properties['card_brand'])): ?>
                                            <br><small class="text-muted">
                                                <i class="fas fa-credit-card"></i> 
                                                <?php echo e($log->properties['card_brand']); ?>

                                                <?php if($log->properties['card_brand'] === 'MADA'): ?>
                                                    (مدى)
                                                <?php endif; ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('user.logs.show', $log->id)); ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> <?php echo e(__('View Details')); ?>

                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="text-center"><?php echo e(__('No activity logs found')); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        <?php echo e($logs->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    $(document).ready(function() {
        // Initialize any JavaScript functionality here if needed
    });
</script>
<?php $__env->stopPush(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp81\htdocs\aljeri-joil-yaseer-o3mhigh\resources\views/user/logs/index.blade.php ENDPATH**/ ?>