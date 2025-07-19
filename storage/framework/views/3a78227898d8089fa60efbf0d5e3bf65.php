

<?php $__env->startSection('title', __('Activity Log Details')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary"><?php echo e(__('Activity Log Details')); ?></h5>
                    <a href="<?php echo e(route('user.logs.index')); ?>" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left"></i> <?php echo e(__('Back to Logs')); ?>

                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th style="width: 150px;"><?php echo e(__('Date & Time')); ?></th>
                                        <td><?php echo e($log->created_at->format('Y-m-d H:i:s')); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(__('Activity Type')); ?></th>
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
                                            <?php elseif(Str::contains($log->event, 'payment')): ?>
                                                <span class="badge bg-<?php echo e(Str::contains($log->event, 'failed') ? 'danger' : (Str::contains($log->event, 'success') ? 'success' : 'warning')); ?>">
                                                    <?php echo e(__('Payment')); ?> <?php echo e(Str::contains($log->event, 'failed') ? __('Failed') : (Str::contains($log->event, 'success') ? __('Successful') : __('Processing'))); ?>

                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?php echo e(ucfirst(str_replace('_', ' ', $log->event))); ?></span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(__('Description')); ?></th>
                                        <td><?php echo e($log->description); ?></td>
                                    </tr>
                                    <tr>
                                        <th><?php echo e(__('IP Address')); ?></th>
                                        <td><?php echo e($log->ip_address); ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <?php if($log->subject_id): ?>
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="m-0 font-weight-bold"><?php echo e(__('Related Item')); ?></h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong><?php echo e(__('Type')); ?>:</strong> 
                                        <?php if(Str::contains($log->subject_type, 'Vehicle')): ?>
                                            <span class="badge bg-primary"><?php echo e(__('Vehicle')); ?></span>
                                        <?php elseif(Str::contains($log->subject_type, 'RfidTransfer')): ?>
                                            <span class="badge bg-info"><?php echo e(__('RFID Transfer')); ?></span>
                                        <?php elseif(Str::contains($log->subject_type, 'RfidTransaction')): ?>
                                            <span class="badge bg-success"><?php echo e(__('RFID Transaction')); ?></span>
                                        <?php elseif(Str::contains($log->subject_type, 'ServiceBooking')): ?>
                                            <span class="badge bg-warning"><?php echo e(__('Service Booking')); ?></span>
                                        <?php else: ?>
                                            <?php echo e(class_basename($log->subject_type)); ?>

                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <strong><?php echo e(__('ID')); ?>:</strong> <?php echo e($log->subject_id); ?>

                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if(!empty($log->properties)): ?>
                    <div class="mb-4">
                        <div class="card">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold"><?php echo e(__('Detailed Information')); ?></h6>
                                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#rawJsonCollapse" aria-expanded="false">
                                    <i class="fas fa-code"></i> <?php echo e(__('View Raw Data')); ?>

                                </button>
                            </div>
                            <div class="card-body">
                                <?php if($log->event == 'vehicle_created'): ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong><?php echo e(__('Plate Number')); ?>:</strong> <?php echo e($log->properties['plate_number'] ?? 'N/A'); ?></p>
                                            <p><strong><?php echo e(__('Make')); ?>:</strong> <?php echo e($log->properties['make'] ?? 'N/A'); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong><?php echo e(__('Model')); ?>:</strong> <?php echo e($log->properties['model'] ?? 'N/A'); ?></p>
                                            <p><strong><?php echo e(__('Year')); ?>:</strong> <?php echo e($log->properties['year'] ?? 'N/A'); ?></p>
                                        </div>
                                    </div>
                                <?php elseif($log->event == 'vehicle_updated'): ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6><?php echo e(__('Previous Information')); ?></h6>
                                            <p><strong><?php echo e(__('Plate Number')); ?>:</strong> <?php echo e($log->properties['old_values']['plate_number'] ?? 'N/A'); ?></p>
                                            <p><strong><?php echo e(__('Make')); ?>:</strong> <?php echo e($log->properties['old_values']['make'] ?? 'N/A'); ?></p>
                                            <p><strong><?php echo e(__('Model')); ?>:</strong> <?php echo e($log->properties['old_values']['model'] ?? 'N/A'); ?></p>
                                            <p><strong><?php echo e(__('Year')); ?>:</strong> <?php echo e($log->properties['old_values']['year'] ?? 'N/A'); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h6><?php echo e(__('Updated Information')); ?></h6>
                                            <p><strong><?php echo e(__('Plate Number')); ?>:</strong> <?php echo e($log->properties['new_values']['plate_number'] ?? 'N/A'); ?></p>
                                            <p><strong><?php echo e(__('Make')); ?>:</strong> <?php echo e($log->properties['new_values']['make'] ?? 'N/A'); ?></p>
                                            <p><strong><?php echo e(__('Model')); ?>:</strong> <?php echo e($log->properties['new_values']['model'] ?? 'N/A'); ?></p>
                                            <p><strong><?php echo e(__('Year')); ?>:</strong> <?php echo e($log->properties['new_values']['year'] ?? 'N/A'); ?></p>
                                        </div>
                                    </div>
                                <?php elseif($log->event == 'rfid_recharge'): ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong><?php echo e(__('Vehicle')); ?>:</strong> <?php echo e($log->properties['plate_number'] ?? 'N/A'); ?></p>
                                            <p><strong><?php echo e(__('Amount')); ?>:</strong> SAR <?php echo e(number_format($log->properties['amount'] ?? 0, 2)); ?></p>
                                            <p><strong><?php echo e(__('Payment Method')); ?>:</strong> <?php echo e(ucfirst($log->properties['payment_method'] ?? 'N/A')); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong><?php echo e(__('Previous Balance')); ?>:</strong> SAR <?php echo e(number_format($log->properties['previous_balance'] ?? 0, 2)); ?></p>
                                            <p><strong><?php echo e(__('New Balance')); ?>:</strong> SAR <?php echo e(number_format($log->properties['new_balance'] ?? 0, 2)); ?></p>
                                        </div>
                                    </div>
                                <?php elseif(Str::contains($log->event, 'rfid_transfer')): ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong><?php echo e(__('Source Vehicle')); ?>:</strong> <?php echo e($log->properties['source_vehicle']['plate_number'] ?? ($log->properties['source_vehicle'] ?? 'N/A')); ?></p>
                                            <p><strong><?php echo e(__('RFID Number')); ?>:</strong> <?php echo e($log->properties['rfid_number'] ?? 'N/A'); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong><?php echo e(__('Target Vehicle')); ?>:</strong> <?php echo e($log->properties['target_vehicle']['plate_number'] ?? ($log->properties['target_vehicle'] ?? 'N/A')); ?></p>
                                            <p><strong><?php echo e(__('Status')); ?>:</strong> 
                                                <?php if($log->event == 'rfid_transfer_initiated'): ?>
                                                    <span class="badge bg-info"><?php echo e(__('Initiated')); ?></span>
                                                <?php elseif($log->event == 'rfid_transfer_completed'): ?>
                                                    <span class="badge bg-success"><?php echo e(__('Completed')); ?></span>
                                                <?php elseif($log->event == 'rfid_transfer_cancelled'): ?>
                                                    <span class="badge bg-danger"><?php echo e(__('Cancelled')); ?></span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php elseif($log->event == 'wallet_recharge'): ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong><?php echo e(__('Amount')); ?>:</strong> SAR <?php echo e(number_format($log->properties['amount'] ?? 0, 2)); ?></p>
                                            <p><strong><?php echo e(__('Payment Method')); ?>:</strong> <?php echo e(ucfirst(str_replace('_', ' ', $log->properties['payment_method'] ?? 'N/A'))); ?></p>
                                            <?php if(isset($log->properties['card_brand'])): ?>
                                                <p><strong><?php echo e(__('Card Type')); ?>:</strong> 
                                                    <span class="badge 
                                                        <?php if($log->properties['card_brand'] === 'VISA'): ?> bg-primary
                                                        <?php elseif($log->properties['card_brand'] === 'MASTERCARD'): ?> bg-danger
                                                        <?php elseif($log->properties['card_brand'] === 'MADA'): ?> bg-success
                                                        <?php else: ?> bg-secondary
                                                        <?php endif; ?>">
                                                        <?php echo e($log->properties['card_brand']); ?>

                                                        <?php if($log->properties['card_brand'] === 'MADA'): ?>
                                                            (مدى)
                                                        <?php endif; ?>
                                                    </span>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php if(isset($log->properties['gateway'])): ?>
                                                <p><strong><?php echo e(__('Gateway')); ?>:</strong> <?php echo e(ucfirst($log->properties['gateway'] ?? 'N/A')); ?></p>
                                            <?php endif; ?>
                                            <?php if(isset($log->properties['transaction_id'])): ?>
                                                <p><strong><?php echo e(__('Transaction ID')); ?>:</strong> <?php echo e($log->properties['transaction_id'] ?? 'N/A'); ?></p>
                                            <?php endif; ?>
                                            <?php if(isset($log->properties['payment_id'])): ?>
                                                <p><strong><?php echo e(__('Payment Reference')); ?>:</strong> <?php echo e($log->properties['payment_id'] ?? 'N/A'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php elseif($log->event == 'service_booking'): ?>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong><?php echo e(__('Service Type')); ?>:</strong> <?php echo e($log->properties['service_type'] ?? 'N/A'); ?></p>
                                            <p><strong><?php echo e(__('Vehicle')); ?>:</strong> <?php echo e($log->properties['vehicle_make'] ?? 'N/A'); ?> <?php echo e($log->properties['vehicle_model'] ?? ''); ?></p>
                                            <p><strong><?php echo e(__('Plate Number')); ?>:</strong> <?php echo e($log->properties['plate_number'] ?? 'N/A'); ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong><?php echo e(__('Payment Method')); ?>:</strong> <?php echo e(ucfirst($log->properties['payment_method'] ?? 'N/A')); ?></p>
                                            <p><strong><?php echo e(__('Payment Status')); ?>:</strong> <?php echo e(ucfirst($log->properties['payment_status'] ?? 'N/A')); ?></p>
                                            <p><strong><?php echo e(__('Amount')); ?>:</strong> SAR <?php echo e(number_format($log->properties['amount'] ?? 0, 2)); ?></p>
                                            <p><strong><?php echo e(__('Reference')); ?>:</strong> <?php echo e($log->properties['reference_number'] ?? 'N/A'); ?></p>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    
                                    <div class="json-data-display">
                                        <?php
                                            $properties = is_string($log->properties) ? json_decode($log->properties, true) : $log->properties;
                                        ?>
                                        
                                        <?php if(isset($properties['payment_data'])): ?>
                                            
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h6 class="text-primary mb-3">
                                                        <i class="fas fa-credit-card me-2"></i><?php echo e(__('Payment Information')); ?>

                                                    </h6>
                                                    <div class="row">
                                                        <?php if(isset($properties['payment_data']['amount'])): ?>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="card border-left-primary h-100">
                                                                    <div class="card-body">
                                                                        <div class="row no-gutters align-items-center">
                                                                            <div class="col mr-2">
                                                                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1"><?php echo e(__('Amount')); ?></div>
                                                                                <div class="h5 mb-0 font-weight-bold text-gray-800">SAR <?php echo e(number_format($properties['payment_data']['amount'], 2)); ?></div>
                                                                            </div>
                                                                            <div class="col-auto">
                                                                                <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <?php if(isset($properties['payment_data']['id'])): ?>
                                                            <div class="col-md-6 mb-3">
                                                                <div class="card border-left-info h-100">
                                                                    <div class="card-body">
                                                                        <div class="row no-gutters align-items-center">
                                                                            <div class="col mr-2">
                                                                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1"><?php echo e(__('Payment ID')); ?></div>
                                                                                <div class="h6 mb-0 font-weight-bold text-gray-800"><?php echo e($properties['payment_data']['id']); ?></div>
                                                                            </div>
                                                                            <div class="col-auto">
                                                                                <i class="fas fa-hashtag fa-2x text-gray-300"></i>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            
                                            <?php if(isset($properties['payment_data']['card'])): ?>
                                                <div class="row mb-4">
                                                    <div class="col-12">
                                                        <h6 class="text-success mb-3">
                                                            <i class="fas fa-credit-card me-2"></i><?php echo e(__('Card Details')); ?>

                                                        </h6>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <table class="table table-sm">
                                                                    <tbody>
                                                                        <?php if(isset($properties['payment_data']['card']['type'])): ?>
                                                                            <tr>
                                                                                <th style="width: 40%;"><?php echo e(__('Card Type')); ?>:</th>
                                                                                <td>
                                                                                    <span class="badge bg-<?php echo e($properties['payment_data']['card']['type'] === 'DEBIT' ? 'success' : 'primary'); ?>">
                                                                                        <?php echo e($properties['payment_data']['card']['type']); ?>

                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                        <?php if(isset($properties['payment_data']['card']['level'])): ?>
                                                                            <tr>
                                                                                <th><?php echo e(__('Card Level')); ?>:</th>
                                                                                <td><?php echo e($properties['payment_data']['card']['level']); ?></td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                        <?php if(isset($properties['payment_data']['card']['holder'])): ?>
                                                                            <tr>
                                                                                <th><?php echo e(__('Card Holder')); ?>:</th>
                                                                                <td><?php echo e($properties['payment_data']['card']['holder']); ?></td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                        <?php if(isset($properties['payment_data']['card']['last4Digits'])): ?>
                                                                            <tr>
                                                                                <th><?php echo e(__('Last 4 Digits')); ?>:</th>
                                                                                <td>**** **** **** <?php echo e($properties['payment_data']['card']['last4Digits']); ?></td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <table class="table table-sm">
                                                                    <tbody>
                                                                        <?php if(isset($properties['payment_data']['card']['issuer']['bank'])): ?>
                                                                            <tr>
                                                                                <th style="width: 40%;"><?php echo e(__('Bank')); ?>:</th>
                                                                                <td><?php echo e($properties['payment_data']['card']['issuer']['bank']); ?></td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                        <?php if(isset($properties['payment_data']['card']['country'])): ?>
                                                                            <tr>
                                                                                <th><?php echo e(__('Country')); ?>:</th>
                                                                                <td><?php echo e($properties['payment_data']['card']['country']); ?></td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                        <?php if(isset($properties['payment_data']['card']['expiryMonth']) && isset($properties['payment_data']['card']['expiryYear'])): ?>
                                                                            <tr>
                                                                                <th><?php echo e(__('Expiry Date')); ?>:</th>
                                                                                <td><?php echo e($properties['payment_data']['card']['expiryMonth']); ?>/<?php echo e($properties['payment_data']['card']['expiryYear']); ?></td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                        <?php if(isset($properties['payment_data']['card']['regulatedFlag'])): ?>
                                                                            <tr>
                                                                                <th><?php echo e(__('Regulated')); ?>:</th>
                                                                                <td>
                                                                                    <span class="badge bg-<?php echo e($properties['payment_data']['card']['regulatedFlag'] ? 'success' : 'warning'); ?>">
                                                                                        <?php echo e($properties['payment_data']['card']['regulatedFlag'] ? __('Yes') : __('No')); ?>

                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            
                                            <?php if(isset($properties['payment_data']['risk'])): ?>
                                                <div class="row mb-4">
                                                    <div class="col-12">
                                                        <h6 class="text-warning mb-3">
                                                            <i class="fas fa-shield-alt me-2"></i><?php echo e(__('Risk Assessment')); ?>

                                                        </h6>
                                                        <div class="row">
                                                            <?php if(isset($properties['payment_data']['risk']['score'])): ?>
                                                                <div class="col-md-6">
                                                                    <div class="card border-left-warning">
                                                                        <div class="card-body">
                                                                            <div class="row no-gutters align-items-center">
                                                                                <div class="col mr-2">
                                                                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1"><?php echo e(__('Risk Score')); ?></div>
                                                                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($properties['payment_data']['risk']['score']); ?></div>
                                                                                </div>
                                                                                <div class="col-auto">
                                                                                    <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                            
                                            
                                            <?php if(isset($properties['payment_data']['result'])): ?>
                                                <div class="row mb-4">
                                                    <div class="col-12">
                                                        <h6 class="text-<?php echo e(isset($properties['payment_data']['result']['code']) && $properties['payment_data']['result']['code'] === '000.100.110' ? 'success' : 'danger'); ?> mb-3">
                                                            <i class="fas fa-check-circle me-2"></i><?php echo e(__('Transaction Result')); ?>

                                                        </h6>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <table class="table table-sm">
                                                                    <tbody>
                                                                        <?php if(isset($properties['payment_data']['result']['code'])): ?>
                                                                            <tr>
                                                                                <th style="width: 40%;"><?php echo e(__('Result Code')); ?>:</th>
                                                                                <td>
                                                                                    <span class="badge bg-<?php echo e($properties['payment_data']['result']['code'] === '000.100.110' ? 'success' : 'danger'); ?>">
                                                                                        <?php echo e($properties['payment_data']['result']['code']); ?>

                                                                                    </span>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                        <?php if(isset($properties['payment_data']['result']['description'])): ?>
                                                                            <tr>
                                                                                <th><?php echo e(__('Description')); ?>:</th>
                                                                                <td><?php echo e($properties['payment_data']['result']['description']); ?></td>
                                                                            </tr>
                                                                        <?php endif; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        
                                        <?php if(isset($properties['ip']) || isset($properties['url']) || isset($properties['route']) || isset($properties['method'])): ?>
                                            <div class="row mb-4">
                                                <div class="col-12">
                                                    <h6 class="text-info mb-3">
                                                        <i class="fas fa-info-circle me-2"></i><?php echo e(__('General Information')); ?>

                                                    </h6>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <table class="table table-sm">
                                                                <tbody>
                                                                    <?php if(isset($properties['ip'])): ?>
                                                                        <tr>
                                                                            <th style="width: 40%;"><?php echo e(__('IP Address')); ?>:</th>
                                                                            <td><code><?php echo e($properties['ip']); ?></code></td>
                                                                        </tr>
                                                                    <?php endif; ?>
                                                                    <?php if(isset($properties['method'])): ?>
                                                                        <tr>
                                                                            <th><?php echo e(__('HTTP Method')); ?>:</th>
                                                                            <td>
                                                                                <span class="badge bg-<?php echo e($properties['method'] === 'POST' ? 'success' : ($properties['method'] === 'GET' ? 'primary' : 'secondary')); ?>">
                                                                                    <?php echo e($properties['method']); ?>

                                                                                </span>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <table class="table table-sm">
                                                                <tbody>
                                                                    <?php if(isset($properties['route'])): ?>
                                                                        <tr>
                                                                            <th style="width: 40%;"><?php echo e(__('Route')); ?>:</th>
                                                                            <td><code><?php echo e($properties['route']); ?></code></td>
                                                                        </tr>
                                                                    <?php endif; ?>
                                                                    <?php if(isset($properties['user_agent'])): ?>
                                                                        <tr>
                                                                            <th><?php echo e(__('User Agent')); ?>:</th>
                                                                            <td><small class="text-muted"><?php echo e(Str::limit($properties['user_agent'], 50)); ?></small></td>
                                                                        </tr>
                                                                    <?php endif; ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        
                                        <?php
                                            $excludedKeys = ['payment_data', 'ip', 'url', 'route', 'method', 'user_agent'];
                                            $additionalData = array_diff_key($properties, array_flip($excludedKeys));
                                        ?>
                                        
                                        <?php if(!empty($additionalData)): ?>
                                            <div class="row">
                                                <div class="col-12">
                                                    <h6 class="text-secondary mb-3">
                                                        <i class="fas fa-database me-2"></i><?php echo e(__('Additional Data')); ?>

                                                    </h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th style="width: 30%;"><?php echo e(__('Field')); ?></th>
                                                                    <th><?php echo e(__('Value')); ?></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php $__currentLoopData = $additionalData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                    <tr>
                                                                        <th><?php echo e(ucfirst(str_replace('_', ' ', $key))); ?></th>
                                                                        <td>
                                                                            <?php if($key === 'subject' && is_array($value)): ?>
                                                                                
                                                                                <div class="user-profile-data">
                                                                                    <div class="row">
                                                                                        <?php if(isset($value['name']) || isset($value['email']) || isset($value['mobile'])): ?>
                                                                                            <div class="col-md-6 mb-3">
                                                                                                <h6 class="text-primary mb-2">
                                                                                                    <i class="fas fa-user me-2"></i><?php echo e(__('Personal Information')); ?>

                                                                                                </h6>
                                                                                                <table class="table table-sm table-borderless">
                                                                                                    <tbody>
                                                                                                        <?php if(isset($value['name'])): ?>
                                                                                                            <tr>
                                                                                                                <th style="width: 40%;"><?php echo e(__('Name')); ?>:</th>
                                                                                                                <td><?php echo e($value['name'] ?? __('Not provided')); ?></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['email'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('Email')); ?>:</th>
                                                                                                                <td><a href="mailto:<?php echo e($value['email']); ?>"><?php echo e($value['email']); ?></a></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['mobile'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('Mobile')); ?>:</th>
                                                                                                                <td><?php echo e($value['mobile']); ?></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['phone'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('Phone')); ?>:</th>
                                                                                                                <td><?php echo e($value['phone'] ?? __('Not provided')); ?></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['gender'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('Gender')); ?>:</th>
                                                                                                                <td><?php echo e(ucfirst($value['gender'] ?? __('Not specified'))); ?></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        <?php endif; ?>
                                                                                        
                                                                                        <?php if(isset($value['company_name']) || isset($value['company_type']) || isset($value['cr_number'])): ?>
                                                                                            <div class="col-md-6 mb-3">
                                                                                                <h6 class="text-success mb-2">
                                                                                                    <i class="fas fa-building me-2"></i><?php echo e(__('Company Information')); ?>

                                                                                                </h6>
                                                                                                <table class="table table-sm table-borderless">
                                                                                                    <tbody>
                                                                                                        <?php if(isset($value['company_name'])): ?>
                                                                                                            <tr>
                                                                                                                <th style="width: 40%;"><?php echo e(__('Company Name')); ?>:</th>
                                                                                                                <td><?php echo e($value['company_name']); ?></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['company_type'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('Company Type')); ?>:</th>
                                                                                                                <td>
                                                                                                                    <span class="badge bg-<?php echo e($value['company_type'] === 'private' ? 'primary' : 'info'); ?>">
                                                                                                                        <?php echo e(ucfirst($value['company_type'])); ?>

                                                                                                                    </span>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['cr_number'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('CR Number')); ?>:</th>
                                                                                                                <td><code><?php echo e($value['cr_number']); ?></code></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['vat_number'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('VAT Number')); ?>:</th>
                                                                                                                <td><code><?php echo e($value['vat_number']); ?></code></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['customer_no'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('Customer No')); ?>:</th>
                                                                                                                <td><code><?php echo e($value['customer_no']); ?></code></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        <?php endif; ?>
                                                                                    </div>
                                                                                    
                                                                                    <?php if(isset($value['city']) || isset($value['region']) || isset($value['zip_code'])): ?>
                                                                                        <div class="row">
                                                                                            <div class="col-md-6 mb-3">
                                                                                                <h6 class="text-info mb-2">
                                                                                                    <i class="fas fa-map-marker-alt me-2"></i><?php echo e(__('Location Information')); ?>

                                                                                                </h6>
                                                                                                <table class="table table-sm table-borderless">
                                                                                                    <tbody>
                                                                                                        <?php if(isset($value['city'])): ?>
                                                                                                            <tr>
                                                                                                                <th style="width: 40%;"><?php echo e(__('City')); ?>:</th>
                                                                                                                <td><?php echo e($value['city']); ?></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['region'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('Region')); ?>:</th>
                                                                                                                <td><?php echo e($value['region'] ?? __('Not specified')); ?></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['zip_code'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('ZIP Code')); ?>:</th>
                                                                                                                <td><?php echo e($value['zip_code']); ?></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['building_number'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('Building Number')); ?>:</th>
                                                                                                                <td><?php echo e($value['building_number']); ?></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                            
                                                                                            <div class="col-md-6 mb-3">
                                                                                                <h6 class="text-warning mb-2">
                                                                                                    <i class="fas fa-cog me-2"></i><?php echo e(__('Account Information')); ?>

                                                                                                </h6>
                                                                                                <table class="table table-sm table-borderless">
                                                                                                    <tbody>
                                                                                                        <?php if(isset($value['status'])): ?>
                                                                                                            <tr>
                                                                                                                <th style="width: 40%;"><?php echo e(__('Status')); ?>:</th>
                                                                                                                <td>
                                                                                                                    <span class="badge bg-<?php echo e($value['status'] === 'active' ? 'success' : 'danger'); ?>">
                                                                                                                        <?php echo e(ucfirst($value['status'])); ?>

                                                                                                                    </span>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['locale'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('Language')); ?>:</th>
                                                                                                                <td>
                                                                                                                    <span class="badge bg-<?php echo e($value['locale'] === 'ar' ? 'primary' : 'secondary'); ?>">
                                                                                                                        <?php echo e($value['locale'] === 'ar' ? 'العربية' : 'English'); ?>

                                                                                                                    </span>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['registration_type'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('Registration Type')); ?>:</th>
                                                                                                                <td><?php echo e(ucfirst($value['registration_type'])); ?></td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                        <?php if(isset($value['is_active'])): ?>
                                                                                                            <tr>
                                                                                                                <th><?php echo e(__('Active')); ?>:</th>
                                                                                                                <td>
                                                                                                                    <span class="badge bg-<?php echo e($value['is_active'] ? 'success' : 'danger'); ?>">
                                                                                                                        <?php echo e($value['is_active'] ? __('Yes') : __('No')); ?>

                                                                                                                    </span>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                        <?php endif; ?>
                                                                                                    </tbody>
                                                                                                </table>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php endif; ?>
                                                                                    
                                                                                    <?php if(isset($value['created_at']) || isset($value['updated_at']) || isset($value['last_login_at'])): ?>
                                                                                        <div class="row">
                                                                                            <div class="col-12">
                                                                                                <h6 class="text-secondary mb-2">
                                                                                                    <i class="fas fa-clock me-2"></i><?php echo e(__('Timestamps')); ?>

                                                                                                </h6>
                                                                                                <div class="row">
                                                                                                    <?php if(isset($value['created_at'])): ?>
                                                                                                        <div class="col-md-4">
                                                                                                            <small class="text-muted"><?php echo e(__('Created')); ?>:</small><br>
                                                                                                            <strong><?php echo e(\Carbon\Carbon::parse($value['created_at'])->format('Y-m-d H:i:s')); ?></strong>
                                                                                                        </div>
                                                                                                    <?php endif; ?>
                                                                                                    <?php if(isset($value['updated_at'])): ?>
                                                                                                        <div class="col-md-4">
                                                                                                            <small class="text-muted"><?php echo e(__('Updated')); ?>:</small><br>
                                                                                                            <strong><?php echo e(\Carbon\Carbon::parse($value['updated_at'])->format('Y-m-d H:i:s')); ?></strong>
                                                                                                        </div>
                                                                                                    <?php endif; ?>
                                                                                                    <?php if(isset($value['last_login_at'])): ?>
                                                                                                        <div class="col-md-4">
                                                                                                            <small class="text-muted"><?php echo e(__('Last Login')); ?>:</small><br>
                                                                                                            <strong><?php echo e(\Carbon\Carbon::parse($value['last_login_at'])->format('Y-m-d H:i:s')); ?></strong>
                                                                                                        </div>
                                                                                                    <?php endif; ?>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            <?php elseif($key === 'changes' && is_array($value)): ?>
                                                                                
                                                                                <div class="changes-data">
                                                                                    <h6 class="text-info mb-2">
                                                                                        <i class="fas fa-edit me-2"></i><?php echo e(__('Modified Fields')); ?>

                                                                                    </h6>
                                                                                    <div class="row">
                                                                                        <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                            <div class="col-md-6 mb-2">
                                                                                                <span class="badge bg-warning"><?php echo e(ucfirst(str_replace('_', ' ', $change))); ?></span>
                                                                                            </div>
                                                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                    </div>
                                                                                </div>
                                                                            <?php elseif($key === 'new_values' && is_array($value)): ?>
                                                                                
                                                                                <div class="new-values-data">
                                                                                    <h6 class="text-success mb-2">
                                                                                        <i class="fas fa-plus-circle me-2"></i><?php echo e(__('New Values')); ?>

                                                                                    </h6>
                                                                                    <table class="table table-sm table-borderless">
                                                                                        <tbody>
                                                                                            <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $newValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                <tr>
                                                                                                    <th style="width: 40%;"><?php echo e(ucfirst(str_replace('_', ' ', $field))); ?>:</th>
                                                                                                    <td>
                                                                                                        <?php if($field === 'locale'): ?>
                                                                                                            <span class="badge bg-<?php echo e($newValue === 'ar' ? 'primary' : 'secondary'); ?>">
                                                                                                                <?php echo e($newValue === 'ar' ? 'العربية' : 'English'); ?>

                                                                                                            </span>
                                                                                                        <?php elseif(is_bool($newValue)): ?>
                                                                                                            <span class="badge bg-<?php echo e($newValue ? 'success' : 'danger'); ?>"><?php echo e($newValue ? __('Yes') : __('No')); ?></span>
                                                                                                        <?php else: ?>
                                                                                                            <?php echo e($newValue); ?>

                                                                                                        <?php endif; ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            <?php elseif($key === 'old_values' && is_array($value)): ?>
                                                                                
                                                                                <div class="old-values-data">
                                                                                    <h6 class="text-danger mb-2">
                                                                                        <i class="fas fa-minus-circle me-2"></i><?php echo e(__('Previous Values')); ?>

                                                                                    </h6>
                                                                                    <table class="table table-sm table-borderless">
                                                                                        <tbody>
                                                                                            <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field => $oldValue): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                <tr>
                                                                                                    <th style="width: 40%;"><?php echo e(ucfirst(str_replace('_', ' ', $field))); ?>:</th>
                                                                                                    <td>
                                                                                                        <?php if($field === 'locale'): ?>
                                                                                                            <span class="badge bg-<?php echo e($oldValue === 'ar' ? 'primary' : 'secondary'); ?>">
                                                                                                                <?php echo e($oldValue === 'ar' ? 'العربية' : 'English'); ?>

                                                                                                            </span>
                                                                                                        <?php elseif(is_bool($oldValue)): ?>
                                                                                                            <span class="badge bg-<?php echo e($oldValue ? 'success' : 'danger'); ?>"><?php echo e($oldValue ? __('Yes') : __('No')); ?></span>
                                                                                                        <?php else: ?>
                                                                                                            <?php echo e($oldValue); ?>

                                                                                                        <?php endif; ?>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            <?php elseif(is_array($value)): ?>
                                                                                
                                                                                <div class="array-data">
                                                                                    <?php if(count($value) <= 5): ?>
                                                                                        <div class="row">
                                                                                            <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                <div class="col-md-6 mb-1">
                                                                                                    <span class="badge bg-secondary"><?php echo e($item); ?></span>
                                                                                                </div>
                                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                        </div>
                                                                                    <?php else: ?>
                                                                                        <div class="row">
                                                                                            <?php $__currentLoopData = array_slice($value, 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                                                <div class="col-md-6 mb-1">
                                                                                                    <span class="badge bg-secondary"><?php echo e($item); ?></span>
                                                                                                </div>
                                                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                                                            <div class="col-12">
                                                                                                <small class="text-muted"><?php echo e(__('And')); ?> <?php echo e(count($value) - 5); ?> <?php echo e(__('more items')); ?></small>
                                                                                            </div>
                                                                                        </div>
                                                                                    <?php endif; ?>
                                                                                </div>
                                                                            <?php elseif(is_bool($value)): ?>
                                                                                <span class="badge bg-<?php echo e($value ? 'success' : 'danger'); ?>"><?php echo e($value ? __('Yes') : __('No')); ?></span>
                                                                            <?php elseif(is_numeric($value) && strpos($key, 'amount') !== false): ?>
                                                                                SAR <?php echo e(number_format($value, 2)); ?>

                                                                            <?php elseif($key === 'type'): ?>
                                                                                <span class="badge bg-info"><?php echo e(ucfirst($value)); ?></span>
                                                                            <?php elseif($key === 'browser'): ?>
                                                                                <span class="badge bg-primary"><?php echo e($value); ?></span>
                                                                            <?php elseif($key === 'device_type'): ?>
                                                                                <span class="badge bg-secondary"><?php echo e(ucfirst($value)); ?></span>
                                                                            <?php else: ?>
                                                                                <?php echo e($value); ?>

                                                                            <?php endif; ?>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    
                                    <div class="collapse mt-3" id="rawJsonCollapse">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <h6 class="m-0 font-weight-bold text-muted"><?php echo e(__('Raw JSON Data')); ?></h6>
                                            </div>
                                            <div class="card-body">
                                                <pre class="mb-0"><code><?php echo e(json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></code></pre>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.border-left-danger {
    border-left: 0.25rem solid #e74a3b !important;
}
.text-xs {
    font-size: 0.7rem;
}
.json-data-display .card {
    transition: all 0.3s ease;
}
.json-data-display .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp81\htdocs\aljeri-joil-yaseer-o3mhigh\resources\views/user/logs/show.blade.php ENDPATH**/ ?>