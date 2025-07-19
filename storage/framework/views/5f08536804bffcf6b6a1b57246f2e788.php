<?php $__env->startSection('title', __('Order Service')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <div id="form-errors"></div>
    <?php if(session('success')): ?>
        <div class="alert alert-success">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger">
            <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3 class="text-center mb-0"><?php echo e(__('Order Service Form')); ?></h3>
        </div>
        <div class="card-body">
            <form id="orderServiceForm" data-action="<?php echo e(route('services.booking.order.form.json')); ?>">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <!-- Left Column - Add New Service -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h4 class="mb-3"><?php echo e(__('Add New Service')); ?></h4>
                                
                                <div class="mb-4">
                                    <label class="form-label"><i class="fa fa-tag me-2"></i><?php echo e(__('Service Type')); ?></label>
                                    <div class="input-group mt-2">
                                        <select id="service_type" name="service_type" class="form-select <?php $__errorArgs = ['service_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                            <option value=""><?php echo e(__('Select Service Type')); ?></option>
                                            <option value="rfid_car"><?php echo e(__('RFID Chip for Cars')); ?></option>
                                            <option value="rfid_truck"><?php echo e(__('RFID Chip for Trucks')); ?></option> 
                                    </select>
                                    </div>
                                    <small class="text-danger service-type-error d-none"><?php echo e(__('Please select service type')); ?></small>
                                </div>
                                
                                <!-- Vehicle Selection Toggle -->
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="use_existing_vehicle" name="use_existing_vehicle">
                                        <label class="form-check-label fw-bold" for="use_existing_vehicle">
                                            <i class="fa fa-car-alt me-1"></i> <?php echo e(__('Use existing vehicle')); ?>

                                        </label>
                                        <div class="form-text"><?php echo e(__('Toggle to select from your saved vehicles')); ?></div>
                                    </div>
                                </div>
                                
                                <!-- Existing Vehicle Dropdown -->
                                <div id="existing_vehicle_section" class="mb-3 d-none">
                                    <label class="form-label"><i class="fa fa-car me-2"></i><?php echo e(__('Select Vehicle')); ?></label>
                                    <select id="vehicle_id" name="vehicle_id" class="form-select <?php $__errorArgs = ['vehicle_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value=""><?php echo e(__('-- Select a vehicle --')); ?></option>
                                        <?php $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($vehicle->id); ?>" 
                                                data-plate="<?php echo e($vehicle->plate_number); ?>"
                                                data-make="<?php echo e($vehicle->make); ?>"
                                                data-manufacturer="<?php echo e($vehicle->manufacturer); ?>"
                                                data-model="<?php echo e($vehicle->model); ?>"
                                                data-year="<?php echo e($vehicle->year); ?>">
                                                <?php echo e($vehicle->manufacturer); ?> <?php echo e($vehicle->model); ?> (<?php echo e($vehicle->plate_number); ?>)
                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <div class="form-text">
                                        <i class="fa fa-info-circle text-primary me-1"></i> 
                                        <?php echo e(__('Vehicle details will be automatically filled based on your selection')); ?>

                                    </div>
                                    <?php $__errorArgs = ['vehicle_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                
                                <!-- New Vehicle Section -->
                                <div id="new_vehicle_section">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fa fa-car me-2"></i><?php echo e(__('Plate No')); ?></label>
                                            <div class="input-group">
                                                <input type="text" id="plate_number" name="plate_number" class="form-control <?php $__errorArgs = ['plate_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('Plate Number')); ?>">
                                            </div>
                                            <div class="form-text"><?php echo e(__('Ex: 1234 ASD, 0012 RGF')); ?></div>
                                            <small class="text-danger plate-error d-none"><?php echo e(__('Please enter plate number')); ?></small>
                                        </div>
                                    
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fa fa-gas-pump me-2"></i><?php echo e(__('Fuel Type')); ?></label>
                                        <div class="input-group">
                                                <select id="service_id" name="service_id" class="form-select <?php $__errorArgs = ['service_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                                    <option value=""><?php echo e(__('Select Fuel Type')); ?></option>
                                                    <option value="rfid_80mm"><?php echo e(__('Unleaded 91')); ?></option>
                                                    <option value="rfid_120mm"><?php echo e(__('Premium 95')); ?></option>
                                                    <option value="oil_change"><?php echo e(__('Diesel')); ?></option>
                                                </select>
                                            </div>
                                            <small class="text-danger service-error d-none"><?php echo e(__('Please select fuel type')); ?></small>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fa fa-tag me-2"></i><?php echo e(__('Name On Card/RFID (in English)')); ?></label>
                                            <div class="input-group">
                                                <input type="text" id="vehicle_make" name="vehicle_make" class="form-control <?php $__errorArgs = ['vehicle_make'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('Name (in English)')); ?>">
                                            </div>
                                            <small class="text-danger make-error d-none"><?php echo e(__('Please enter name')); ?></small>
                                        </div>
                                    
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fa fa-car me-2"></i><?php echo e(__('Vehicle Make')); ?></label>
                                            <div class="input-group">
                                                <input type="text" id="vehicle_manufacturer" name="vehicle_manufacturer" class="form-control <?php $__errorArgs = ['vehicle_manufacturer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('Vehicle Manufacturer')); ?>">
                                            </div>
                                            <div class="form-text"><?php echo e(__('Ex: Toyota, BMW, Ford')); ?></div>
                                            <small class="text-danger manufacturer-error d-none"><?php echo e(__('Please enter vehicle make')); ?></small>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fa fa-calendar-alt me-2"></i><?php echo e(__('Vehicle Model')); ?></label>
                                            <div class="input-group">
                                                <input type="text" id="vehicle_model" name="vehicle_model" class="form-control <?php $__errorArgs = ['vehicle_model'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('Vehicle Model')); ?>">
                                            </div>
                                            <div class="form-text"><?php echo e(__('Ex: Camry, X5, Focus')); ?></div>
                                            <small class="text-danger model-error d-none"><?php echo e(__('Please enter model')); ?></small>
                                        </div>
                                    
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fa fa-calendar-alt me-2"></i><?php echo e(__('Vehicle Year')); ?></label>
                                            <div class="input-group">
                                                <input type="text" id="vehicle_year" name="vehicle_year" class="form-control <?php $__errorArgs = ['vehicle_year'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('Vehicle Year')); ?>">
                                            </div>
                                            <div class="form-text"><?php echo e(__('Ex: 2021, 2022')); ?></div>
                                            <small class="text-danger year-error d-none"><?php echo e(__('Please enter a valid year')); ?></small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label"><i class="fa fa-money-bill me-2"></i><?php echo e(__('Fueling Amount (in SAR)')); ?></label>
                                        <div class="input-group">
                                            <input type="text" id="refule_amount" name="refule_amount" class="form-control <?php $__errorArgs = ['refule_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="<?php echo e(__('Amount in SAR')); ?>">
                                        </div>
                                        <small class="text-danger refule-error d-none"><?php echo e(__('Please enter refule amount')); ?></small>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="save_vehicle" name="save_vehicle" value="1" checked>
                                        <label class="form-check-label" for="save_vehicle">
                                            <?php echo e(__('Save vehicle information for future bookings')); ?>

                                        </label>
                                    </div>
                                </div>
                                
                                <div class="text-end mt-3">
                                    <button type="button" id="addServiceBtn" class="btn btn-primary px-4">
                                        <i class="fa fa-plus"></i> <?php echo e(__('Save')); ?>

                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- List of Purchase Services -->
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-3"><?php echo e(__('List of purchase Services')); ?></h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th><?php echo e(__('Service')); ?></th>
                                                <th><?php echo e(__('Vehicle Details')); ?></th> 
                                                <th><?php echo e(__('Ref. amount')); ?></th>
                                                <th><?php echo e(__('Actions')); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="servicesList">
                                            <!-- Services will be added here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                                <div id="noServicesMessage" class="text-center py-3 <?php echo e(old('services') ? 'd-none' : ''); ?>">
                                    <p><?php echo e(__('No services added yet. Please add at least one service.')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column - Delivery Details and Summary -->
                    <div class="col-md-6">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h4 class="mb-3"><?php echo e(__('Delivery Details')); ?></h4>
                                <div class="mb-3">
                                    <label class="form-label"><?php echo e(__('Pickup Station')); ?> <span class="text-danger">*</span></label>
                                    <select id="pickup_location" name="pickup_location" class="form-select <?php $__errorArgs = ['pickup_location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                        <option value=""><?php echo e(__('Choose location')); ?></option>
                                        <option value="station1"><?php echo e(__('Station 1')); ?></option>
                                        <option value="station2"><?php echo e(__('Station 2')); ?></option>
                                        <option value="station3"><?php echo e(__('Station 3')); ?></option>
                                    </select>
                                    <small class="text-danger location-error d-none"><?php echo e(__('Please select a pickup location')); ?></small>
                                    <?php $__errorArgs = ['pickup_location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <h4 class="mb-3"><?php echo e(__('Summary')); ?></h4>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo e(__('Unit price')); ?></span>
                                    <span id="unitPrice"> 150.00</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo e(__('Quantity')); ?></span>
                                    <span id="quantity">0</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo e(__('Topup')); ?></span>
                                    <span id="topupAmount"><span class="icon-saudi_riyal"></span> 0.00</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo e(__('Subtotal')); ?></span>
                                    <span id="subtotalAmount"><span class="icon-saudi_riyal"></span> 0.00</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span><?php echo e(__('VAT (15%)')); ?></span>
                                    <span id="vatAmount"><span class="icon-saudi_riyal"></span> 0.00</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-4 pb-2 border-bottom fw-bold">
                                    <span><?php echo e(__('Total (including VAT)')); ?></span>
                                    <span id="totalAmount"><span class="icon-saudi_riyal"></span> 0.00</span>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_wallet" value="wallet" checked>
                                        <label class="form-check-label d-flex align-items-center" for="payment_wallet">
                                            <i class="fa fa-wallet me-2"></i> <?php echo e(__('Wallet')); ?>

                                            <span class="ms-2 badge bg-success"> <?php echo e($walletBalance); ?> <span class="icon-saudi_riyal"></span></span>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="payment_credit_card" value="credit_card">
                                        <label class="form-check-label d-flex align-items-center" for="payment_credit_card">
                                            <i class="fa fa-credit-card me-2"></i> <?php echo e(__('Credit Card')); ?>

                                        </label>
                                    </div>
                                    
                                    <?php $__errorArgs = ['payment_method'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                                
                                <button type="submit" id="placeOrderBtn" class="btn btn-primary w-100"><?php echo e(__('Place order')); ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            
            <!-- HyperPay Widget Container - MOVED OUTSIDE THE FORM -->
            <div id="credit-card-payment-container" class="d-none mt-4 col-md-6 offset-md-6" style="margin-top: -402px!important; padding-left: 11px;">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-3"><?php echo e(__('Secure Payment')); ?></h4>
                        
                        <!-- Card Brand Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold"><?php echo e(__('Select Card Type')); ?></label>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check card-brand-option">
                                        <input class="form-check-input" type="radio" name="card_brand" id="visa_mastercard" value="VISA MASTER" checked>
                                        <label class="form-check-label d-flex align-items-center" for="visa_mastercard">
                                            <div class="card-brand-icons me-3">
                                                <svg class="visa-icon" width="40" height="25" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="40" height="25" rx="3" fill="#1A1F71"/>
                                                    <path d="M16.5 8.5L14 16.5H11.5L14 8.5H16.5ZM22.5 8.5L20.5 13.5L19.5 9.5C19.2 8.5 18.5 8.5 18.5 8.5H15.5L15.6 8.8C16.2 9 16.5 9.5 16.5 9.5L18.5 16.5H21L25.5 8.5H22.5ZM28.5 8.5H26.5C26.2 8.5 26 8.7 26 9V16.5H28.5V8.5Z" fill="white"/>
                                                </svg>
                                                <svg class="mastercard-icon ms-2" width="40" height="25" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="40" height="25" rx="3" fill="#EB001B"/>
                                                    <rect x="20" width="20" height="25" rx="3" fill="#F79E1B"/>
                                                    <circle cx="17" cy="12.5" r="6" fill="#FF5F00"/>
                                                    <circle cx="23" cy="12.5" r="6" fill="#FF5F00"/>
                                                </svg>
                                            </div>
                                            <span class="card-brand-text"><?php echo e(__('Visa / MasterCard')); ?></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check card-brand-option">
                                        <input class="form-check-input" type="radio" name="card_brand" id="mada_card" value="MADA">
                                        <label class="form-check-label d-flex align-items-center" for="mada_card">
                                            <div class="card-brand-icons me-3">
                                                <svg class="mada-icon" width="40" height="25" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <rect width="40" height="25" rx="3" fill="#0066CC"/>
                                                    <text x="20" y="16" text-anchor="middle" fill="white" font-family="Arial, sans-serif" font-size="12" font-weight="bold">مدى</text>
                                                </svg>
                                            </div>
                                            <span class="card-brand-text"><?php echo e(__('MADA Card')); ?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Test Card Info (shown in test environment) -->
                        
                        
                        <!-- HyperPay Widget Container -->
                        <div id="hyperpay-widget" style="min-height: 300px;">
                            <div class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2 text-muted">Loading secure payment form...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- HyperPay will be loaded dynamically -->
<script>
    // Pass service prices to JavaScript
    window.servicePrices = <?php echo json_encode($servicePrices ?? [], 15, 512) ?>;
</script>
<script src="<?php echo e(asset('js/order-form.js')); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize the form - key configuration will be picked up by the module
        
        // Add payment method radio button toggle for credit card form
        const paymentWallet = document.getElementById('payment_wallet');
        const paymentCreditCard = document.getElementById('payment_credit_card');
        const creditCardPaymentContainer = document.getElementById('credit-card-payment-container');
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        
        function toggleCreditCardForm() {
            if (paymentCreditCard && paymentCreditCard.checked) {
                if (creditCardPaymentContainer) {
                    creditCardPaymentContainer.classList.remove('d-none');
                }
                // Hide place order button when credit card is selected - HyperPay will provide its own Pay Now button
                if (placeOrderBtn) {
                    placeOrderBtn.style.display = 'none';
                }
                
                // CRITICAL FIX: Don't load widget here - let order-form.js handle it
                // This prevents multiple loading and flickering
                // setTimeout(() => {
                //     // Only trigger widget loading if we have services added
                //     const serviceItems = document.querySelectorAll('.service-item');
                //     if (serviceItems.length > 0 && typeof window.loadHyperPayWidget === 'function') {
                //         console.log('🔄 Triggering HyperPay widget loading after payment method change...');
                //         window.loadHyperPayWidget();
                //     }
                // }, 100);

            } else {
                if (creditCardPaymentContainer) {
                    creditCardPaymentContainer.classList.add('d-none');
                }
                // Show place order button when wallet is selected
                if (placeOrderBtn) {
                    placeOrderBtn.style.display = 'block';
                    placeOrderBtn.innerHTML = '<i class="fa fa-wallet me-2"></i>Place Order (Wallet Payment)';
                }
                
                // Clear any existing HyperPay widget when switching to wallet
                const hyperpayWidget = document.getElementById('hyperpay-widget');
                if (hyperpayWidget) {
                    hyperpayWidget.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fa fa-wallet fa-2x text-muted"></i>
                            <p class="mt-2 text-muted">Wallet payment selected - click "Place Order" button below</p>
                        </div>
                    `;
                }
            }
        }
        
        if (paymentWallet) {
            paymentWallet.addEventListener('change', toggleCreditCardForm);
        }
        if (paymentCreditCard) {
            paymentCreditCard.addEventListener('change', toggleCreditCardForm);
        }
        
        // Initial toggle based on saved state
        toggleCreditCardForm();
        
        // Card brand selection enhancement
        document.querySelectorAll('.card-brand-option').forEach(option => {
            const radio = option.querySelector('input[type="radio"]');
            const label = option.querySelector('.form-check-label');
            
            // Make entire card clickable
            option.addEventListener('click', function(e) {
                if (e.target !== radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change', { bubbles: true }));
                }
            });
            
            // Update visual state when radio changes
            radio.addEventListener('change', function() {
                // Remove active class from all options
                document.querySelectorAll('.card-brand-option').forEach(opt => {
                    opt.classList.remove('active');
                });
                
                // Add active class to selected option
                if (this.checked) {
                    option.classList.add('active');
                }
            });
        });
        
        // Show test card info in test environment
        <?php if(config('services.hyperpay.mode') === 'test' || str_contains(config('services.hyperpay.base_url'), 'test') || config('app.env') === 'local'): ?>
            const testCardInfo = document.getElementById('test-card-info');
            if (testCardInfo) {
                testCardInfo.style.display = 'block';
            }
        <?php endif; ?>
        
        // Add copy functionality for test cards
        document.addEventListener('click', function(e) {
            if (e.target.closest('.test-card-copy')) {
                const cardElement = e.target.closest('.test-card-copy');
                const cardNumber = cardElement.dataset.card;
                
                if (navigator.clipboard) {
                    navigator.clipboard.writeText(cardNumber).then(() => {
                        // Show success toast
                        const toast = document.createElement('div');
                        toast.className = 'toast align-items-center text-white bg-success border-0';
                        toast.style.position = 'fixed';
                        toast.style.top = '20px';
                        toast.style.right = '20px';
                        toast.style.zIndex = '9999';
                        toast.innerHTML = `
                            <div class="d-flex">
                                <div class="toast-body">
                                    <i class="fas fa-check me-2"></i>
                                    Card number copied: ${cardNumber}
                                </div>
                            </div>
                        `;
                        
                        document.body.appendChild(toast);
                        
                        // Auto-remove toast after 3 seconds
                        setTimeout(() => {
                            if (toast.parentNode) {
                                toast.remove();
                            }
                        }, 3000);
                    });
                } else {
                    // Fallback for browsers without clipboard API
                    const textArea = document.createElement('textarea');
                    textArea.value = cardNumber;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    // Show success message
                    alert('Card number copied: ' + cardNumber);
                }
            }
        });
        
        // Note: HyperPay initialization is handled by order-form.js
    });
</script>

<style>
/* HyperPay Integration Styles */
#hyperpay-widget {
    min-height: 300px;
}

/* Override HyperPay default styles if needed */
.wpwl-form {
    color: #363d47;
}

.wpwl-button {
    background: #0061f2;
    border: none;
    padding: 10px 30px;
    font-size: 16px;
}

.wpwl-button:hover {
    background: #0051d2;
}

/* Card Brand Selection Styles */
.card-brand-option {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
    cursor: pointer;
    background: #fff;
}

.card-brand-option:hover {
    border-color: #0061f2;
    box-shadow: 0 2px 8px rgba(0, 97, 242, 0.15);
    transform: translateY(-1px);
}

.card-brand-option .form-check-input:checked + .form-check-label {
    color: #0061f2;
    font-weight: 600;
}

.card-brand-option .form-check-input:checked ~ .card-brand-option {
    border-color: #0061f2;
    background: rgba(0, 97, 242, 0.05);
}

.card-brand-option input[type="radio"]:checked + .form-check-label {
    color: #0061f2;
    font-weight: 600;
}

.card-brand-option input[type="radio"]:checked ~ .card-brand-option {
    border-color: #0061f2;
    background: rgba(0, 97, 242, 0.05);
}

/* Card Brand Icons */
.card-brand-icons {
    display: flex;
    align-items: center;
    gap: 8px;
}

.visa-icon, .mastercard-icon, .mada-icon {
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.card-brand-option:hover .visa-icon,
.card-brand-option:hover .mastercard-icon,
.card-brand-option:hover .mada-icon {
    transform: scale(1.05);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.card-brand-text {
    font-size: 14px;
    font-weight: 500;
    color: #363d47;
    transition: color 0.3s ease;
}

.card-brand-option:hover .card-brand-text {
    color: #0061f2;
}

/* Selected state styling */
.card-brand-option input[type="radio"]:checked + .form-check-label .card-brand-text {
    color: #0061f2;
    font-weight: 600;
}

.card-brand-option input[type="radio"]:checked + .form-check-label .card-brand-icons svg {
    filter: drop-shadow(0 2px 4px rgba(0, 97, 242, 0.3));
}

/* Active state styling */
.card-brand-option.active {
    border-color: #0061f2;
    background: rgba(0, 97, 242, 0.05);
    box-shadow: 0 2px 8px rgba(0, 97, 242, 0.15);
}

.card-brand-option.active .card-brand-text {
    color: #0061f2;
    font-weight: 600;
}

.card-brand-option.active .card-brand-icons svg {
    filter: drop-shadow(0 2px 4px rgba(0, 97, 242, 0.3));
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-brand-option {
        padding: 12px;
    }
    
    .card-brand-icons {
        gap: 6px;
    }
    
    .visa-icon, .mastercard-icon, .mada-icon {
        width: 35px;
        height: 22px;
    }
    
    .card-brand-text {
        font-size: 13px;
    }
}

/* Test Card Copy Styles */
.test-card-copy {
    cursor: pointer;
    transition: all 0.2s ease;
}

.test-card-copy:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.test-card-copy:hover .fas.fa-copy {
    color: #007bff !important;
}

.test-card-copy .card-body {
    position: relative;
}

.test-card-copy .fas.fa-copy {
    font-size: 12px;
    opacity: 0.7;
    transition: all 0.2s ease;
}

/* Toast Animation */
.toast {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
<?php $__env->stopPush(); ?>
 
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp81\htdocs\aljeri-joil-yaseer-o3mhigh\resources\views/services/booking/order-form.blade.php ENDPATH**/ ?>