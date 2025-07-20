<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ApprovalWorkflowController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\TestRolesController;
use App\Http\Controllers\WalletApprovalController;
use App\Http\Controllers\ServiceBookingController;
use App\Models\WalletApprovalRequest;
use App\Http\Controllers\ApprovalHistoryController;
use App\Http\Controllers\EmailTestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Customer\MapMarksController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\RfidController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HyperpayController;
use App\Http\Controllers\LocalizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', [AuthController::class, 'index'])->name('auth.login');
// Language switching routes
Route::get('/lang/{locale}', [LocalizationController::class, 'changeLanguage'])->name('lang.change');
Route::get('/api/current-language', [LocalizationController::class, 'getCurrentLanguage'])->name('lang.current');
Route::get('/api/supported-languages', [LocalizationController::class, 'getSupportedLanguages'])->name('lang.supported');

// Translation management routes
Route::get('/api/translations/{locale?}', [LocalizationController::class, 'getTranslationKeys'])->name('translations.keys');
Route::get('/api/translations/module/{module}/{locale?}', [LocalizationController::class, 'getModuleTranslations'])->name('translations.module');
Route::post('/api/translations/clear-cache', [LocalizationController::class, 'clearTranslationCache'])->name('translations.clear-cache');
Route::post('/api/translations/refresh', [LocalizationController::class, 'refreshTranslations'])->name('translations.refresh');
Route::get('/api/translations/stats', [LocalizationController::class, 'getTranslationStats'])->name('translations.stats');

// Translation test interface routes
Route::get('/translation-test', [\App\Http\Controllers\TranslationTestController::class, 'index'])->name('translation-test.index');
Route::post('/translation-test/test', [\App\Http\Controllers\TranslationTestController::class, 'test'])->name('translation-test.test');
Route::post('/translation-test/clear-cache', [\App\Http\Controllers\TranslationTestController::class, 'clearCache'])->name('translation-test.clear-cache');
Route::post('/translation-test/refresh', [\App\Http\Controllers\TranslationTestController::class, 'refreshTranslations'])->name('translation-test.refresh');
Route::get('/translation-test/keys', [\App\Http\Controllers\TranslationTestController::class, 'getTranslationKeys'])->name('translation-test.keys');


Route::get('/auth', [AuthController::class, 'index'])->name('auth.index');
Route::get('/login', [AuthController::class, 'index'])->name('login');

// Protected home page for authenticated users.
Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware(['auth', 'role.redirect']);
Route::get('/user-test', [\App\Http\Controllers\HomeController::class, 'testu'])->name('testu')->middleware('auth');


// OTP verification endpoint (placed in the web group to ensure session support).
Route::post('/api/verify-otp', [AuthController::class, 'verifyOtp'])->name('web.verifyOtp');

// Logout route.
Route::post('/logout', [\App\Http\Controllers\HomeController::class, 'logout'])->name('logout');


// Test routes for user roles (development only)
if (app()->environment('local', 'development')) {
    Route::get('/test-roles', [TestRolesController::class, 'index'])->name('test.roles');
    Route::get('/test-roles/login-as/{userId}', [TestRolesController::class, 'loginAs'])->name('test.login-as');
    Route::get('/test-roles/current-user', [TestRolesController::class, 'currentUser'])->name('test.current-user');
    Route::get('/test-roles/fix-roles', [TestRolesController::class, 'fixRoles'])->name('test.fix-roles');
    
    // Test Hyperpay form AJAX functionality
    Route::get('/test-hyperpay-form', function() {
        return view('test-hyperpay-form');
    })->name('test.hyperpay-form')->middleware('auth');
    
    // Test HyperPay widget integration
    Route::get('/test-hyperpay-widget', function() {
        return view('test-hyperpay-widget');
    })->name('test.hyperpay-widget')->middleware('auth');
    
    // Minimal HyperPay test
    Route::get('/test-hyperpay-minimal', function() {
        return view('test-hyperpay-minimal');
    })->name('test.hyperpay-minimal')->middleware('auth');
    
    // Direct HyperPay test
    Route::get('/test-hyperpay-direct', function() {
        return view('test-hyperpay-direct');
    })->name('test.hyperpay-direct')->middleware('auth');
    
    // Test error pages
    Route::get('/test-error/403', function() {
        abort(403, 'Test 403 Error');
    })->name('test.error.403');
    
    Route::get('/test-error/404', function() {
        abort(404, 'Test 404 Error');
    })->name('test.error.404');
    
    Route::get('/test-error/500', function() {
        abort(500, 'Test 500 Error');
    })->name('test.error.500');
}


Route::group(['middleware' => ['auth', 'customer.only']], function(){
    Route::get('/wallet/topup', [\App\Http\Controllers\WalletController::class, 'topup'])->name('wallet.topup');
    Route::post('/wallet/topup', [\App\Http\Controllers\WalletController::class, 'storeTopup'])->name('wallet.storeTopup');
    Route::post('/wallet/bank-payment', [\App\Http\Controllers\WalletController::class, 'processBankPayment'])->name('wallet.bankPayment');
    Route::get('/wallet/history', [\App\Http\Controllers\WalletController ::class, 'history'])->name('wallet.history'); // Optional
    Route::get('/wallet/approval/{request}', [\App\Http\Controllers\WalletController::class, 'approvalDetails'])->name('wallet.approval.details');
    Route::post('/wallet/approval/{approvalRequest}/approve', [\App\Http\Controllers\WalletController::class, 'approvePaymentStep'])->name('wallet.approval.approve');
    Route::post('/wallet/approval/{approvalRequest}/reject', [\App\Http\Controllers\WalletController::class, 'rejectApprovalRequest'])->name('wallet.approval.reject');
});

Route::get('/wallet/payment-confirmation', function () {
    // Only show this if user chose "credit_card" or "mada" in the topup form
    $amount = session('topup_amount', 0);
    return view('wallet.payment-confirmation',compact('amount'));
})->name('wallet.paymentConfirmation')->middleware(['auth', 'customer.only']);

Route::post('/wallet/payment-process', [\App\Http\Controllers\WalletController::class, 'paymentProcess'])
    ->name('wallet.paymentProcess')
    ->middleware(['auth', 'customer.only']);


// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/test-csp', function () {
    $headers = response('Testing CSP...')->headers->all();
    return response()->json($headers);
});

// Wallet routes for authenticated users
Route::middleware(['auth', 'customer.only'])->group(function () {
    // Basic wallet functionality
    Route::prefix('wallet')->name('wallet.')->group(function () {
        Route::get('/', [WalletController::class, 'index'])->name('index');
        Route::get('/history', [WalletController::class, 'history'])->name('history');
        Route::get('/transactions', [WalletController::class, 'transactions'])->name('transactions');
        Route::get('/topup', [WalletController::class, 'topup'])->name('topup');
        Route::post('/topup', [WalletController::class, 'storeTopup'])->name('store-topup');
        Route::post('/bank-payment', [WalletController::class, 'processBankPayment'])->name('bank-payment');
        Route::get('/pending-payments', [WalletController::class, 'pendingPayments'])->name('pending-payments');
        
        // Payment confirmation and processing
        Route::get('/payment-confirmation', function () {
            $amount = session('topup_amount', 0);
            return view('wallet.payment-confirmation', compact('amount'));
        })->name('payment-confirmation');
        Route::post('/payment-process', [WalletController::class, 'paymentProcess'])->name('payment-process');

        // Hyperpay routes
        Route::post('/hyperpay/checkout', [WalletController::class, 'hyperpayCheckout'])->name('hyperpay.checkout');
        Route::post('/hyperpay/get-form', [WalletController::class, 'getHyperpayForm'])->name('hyperpay.get-form');
        Route::post('/hyperpay/redirect', [WalletController::class, 'redirectToHyperpay'])->name('hyperpay.redirect');
        Route::post('/hyperpay/validate-session', [WalletController::class, 'validateCheckoutSession'])->name('hyperpay.validate-session');
        Route::post('/hyperpay/pre-create-sessions', [WalletController::class, 'preCreateSessions'])->name('hyperpay.pre-create-sessions');
        Route::post('/hyperpay/get-pooled-session', [WalletController::class, 'getPooledSession'])->name('hyperpay.get-pooled-session');
        Route::get('/hyperpay/status', [WalletController::class, 'hyperpayStatus'])->name('hyperpay.status');
        
        // Test page for Hyperpay integration
        Route::get('/test-hyperpay', function() {
            return view('wallet.test-hyperpay');
        })->name('test-hyperpay');
        
        // Payment error logging
        Route::post('/log-payment-error', [WalletController::class, 'logPaymentError'])->name('log-payment-error');
    });

    // Stripe routes removed - using Hyperpay only


    
    
// Service Booking Routes
Route::prefix('services/booking')->name('services.booking.')->middleware('auth')->group(function () {
    Route::get('/', [ServiceBookingController::class, 'index'])->name('index');
    // old service form not used (may be used in future)
    Route::get('/create', [ServiceBookingController::class, 'create'])->name('create');
    Route::post('/store', [ServiceBookingController::class, 'store'])->name('store');
    Route::get('/history', [ServiceBookingController::class, 'history'])->name('history');
    // Order form routes

    // actively used new service form  , new service order form
    Route::get('/order/form', [ServiceBookingController::class, 'orderForm'])->name('order.form');
    Route::post('/order/process', [ServiceBookingController::class, 'processOrder'])->name('order.process');
    Route::post('/order/form', [ServiceBookingController::class, 'processOrderJson'])->name('order.form.json');
    
    // HyperPay integration routes
    Route::post('/hyperpay/get-form', [ServiceBookingController::class, 'getHyperpayForm'])->name('hyperpay.get-form');
    Route::post('/hyperpay/checkout-pre-order', [ServiceBookingController::class, 'getHyperpayForm'])->name('hyperpay.checkout-pre-order');
    Route::get('/hyperpay/status', [ServiceBookingController::class, 'hyperpayStatus'])->name('hyperpay.status');
    Route::post('/hyperpay/status', [ServiceBookingController::class, 'hyperpayStatus'])->name('hyperpay.status.post');
    
    // These specific routes must come before the wildcard route /{booking}
    Route::get('/saved-cards', [ServiceBookingController::class, 'savedCards'])->name('saved-cards');
    Route::post('/cards/{card}/set-default', [ServiceBookingController::class, 'setDefaultCard'])->name('cards.set-default');
    Route::delete('/cards/{card}/delete', [ServiceBookingController::class, 'deleteCard'])->name('cards.delete');
    
    // Status update route
    Route::post('/{booking}/status', [ServiceBookingController::class, 'updateStatus'])->name('update-status');
    
    // Wildcard route should be last
    Route::get('/{booking}', [ServiceBookingController::class, 'show'])->name('show');
    Route::post('/{booking}/cancel', [ServiceBookingController::class, 'cancel'])->name('cancel');
});

// Vehicle Management Routes
Route::resource('vehicles', VehicleController::class)->middleware('auth');

// RFID Management Routes
Route::prefix('rfid')->name('rfid.')->middleware('auth')->group(function () {
    Route::get('/', [RfidController::class, 'index'])->name('index');
    
    // RFID Transfer
    Route::get('/transfer', [RfidController::class, 'transferForm'])->name('transfer');
    Route::post('/transfer', [RfidController::class, 'initiateTransfer'])->name('initiate-transfer');
    Route::get('/transfer/{transfer}/verify', [RfidController::class, 'verifyTransferForm'])->name('verify-transfer');
    Route::post('/transfer/{transfer}/verify', [RfidController::class, 'verifyTransfer'])->name('verify-transfer.submit');
    Route::post('/transfer/{transfer}/cancel', [RfidController::class, 'cancelTransfer'])->name('cancel-transfer');
    
    // RFID Recharge
    Route::get('/recharge', [RfidController::class, 'rechargeForm'])->name('recharge');
    Route::post('/recharge', [RfidController::class, 'processRecharge'])->name('process-recharge');
    
    // RFID HyperPay Routes
    Route::post('/hyperpay/get-form', [RfidController::class, 'getHyperpayForm'])->name('hyperpay.get-form');
    Route::get('/hyperpay/status', [RfidController::class, 'hyperpayStatus'])->name('hyperpay.status');
    Route::post('/hyperpay/validate-session', [RfidController::class, 'validateCheckoutSession'])->name('hyperpay.validate-session');
    
    // Transaction History
    Route::get('/transactions', [RfidController::class, 'transactionHistory'])->name('transactions');
});
    
});

    Route::middleware(['auth', 'role:admin|finance|validation|activation|it|customer'])->group(function () {
// Profile routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.update-avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.remove-avatar');
    Route::get('/profile/status-history', [ProfileController::class, 'statusHistory'])->name('profile.status-history');

    // User Activity Logs Routes
    Route::prefix('my-activity')->name('user.logs.')->group(function () {
        Route::get('/', [App\Http\Controllers\UserLogController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\UserLogController::class, 'show'])->name('show');
    });

        // Consolidated / Active Wallet Approval Routes  
        Route::prefix('wallet/approvals')->name('wallet.approvals.')->middleware(['auth', 'role:admin|finance|validation|activation|it'])->group(function () {
            Route::get('/', [WalletApprovalController::class, 'index'])->name('index');
            Route::get('/create', [WalletApprovalController::class, 'create'])->name('create');
            Route::post('/', [WalletApprovalController::class, 'store'])->name('store');
            Route::get('/my-approvals', [WalletApprovalController::class, 'myApprovals'])->name('my-approvals');
            Route::get('/history', [WalletApprovalController::class, 'history'])->name('history');
            Route::get('/{request}', [WalletApprovalController::class, 'show'])->name('show');
            Route::post('/{request}/approve', [WalletApprovalController::class, 'approve'])->name('approve');
            Route::post('/{request}/reject', [WalletApprovalController::class, 'reject'])->name('reject');
            Route::get('/{request}/download-attachment/{action}', [WalletApprovalController::class, 'downloadAttachment'])->name('download-attachment');
        });
});

// Status History Routes
Route::prefix('status-history')->name('status.history.')->middleware('auth')->group(function () {
    Route::get('/{modelType}/{modelId}', [App\Http\Controllers\StatusHistoryController::class, 'show'])->name('show');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|finance|validation|activation|it', 'role.redirect'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // User Management Routes
    Route::resource('users', UserController::class);
    
    // Payment Management Routes
    Route::resource('payments', PaymentController::class);
    Route::post('payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    
    // SMS Management Routes
    Route::prefix('sms')->name('sms.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SmsController::class, 'index'])->name('index');
        Route::post('/test-config', [App\Http\Controllers\Admin\SmsController::class, 'testConfig'])->name('test-config');
        Route::post('/send-test', [App\Http\Controllers\Admin\SmsController::class, 'sendTest'])->name('send-test');
        Route::get('/statistics', [App\Http\Controllers\Admin\SmsController::class, 'statistics'])->name('statistics');
        Route::get('/config-status', [App\Http\Controllers\Admin\SmsController::class, 'configStatus'])->name('config-status');
    });
    
    // Admin Approval Workflow management Routes
    Route::resource('approval-workflows', ApprovalWorkflowController::class);
    Route::post('approval-workflows/{workflow}/update-steps', [ApprovalWorkflowController::class, 'updateStepOrder'])
        ->name('approval-workflows.update-steps');
        
    // Admin Wallet Approval/ Routes
    Route::prefix('wallet-approvals')->name('wallet-approvals.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\WalletApprovalController::class, 'index'])->name('index');
        Route::get('/{id}', [App\Http\Controllers\Admin\WalletApprovalController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [App\Http\Controllers\Admin\WalletApprovalController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [App\Http\Controllers\Admin\WalletApprovalController::class, 'reject'])->name('reject');
    });
    
    // Admin Wallet Request Management Routes
    Route::prefix('wallet-requests')->name('wallet-requests.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\WalletRequestController::class, 'index'])->name('index');
        Route::get('/reset-filters', [App\Http\Controllers\Admin\WalletRequestController::class, 'resetFilters'])->name('reset-filters');
        Route::get('/{id}', [App\Http\Controllers\Admin\WalletRequestController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [App\Http\Controllers\Admin\WalletRequestController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [App\Http\Controllers\Admin\WalletRequestController::class, 'reject'])->name('reject');
    });
    
    // System Logs Routes
    Route::get('logs', [App\Http\Controllers\Admin\LogController::class, 'index'])->name('logs.index');
    Route::get('logs/{log}', [App\Http\Controllers\Admin\LogController::class, 'show'])->name('logs.show');
});

// Delivery Agent Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'delivery', 'role.redirect'])->group(function () {
    Route::get('deliverydashboard', [App\Http\Controllers\Admin\DeliveryDashboardController::class, 'index'])->name('delivery.dashboard');
    Route::get('delivery/services', [App\Http\Controllers\Admin\DeliveryDashboardController::class, 'getServices'])->name('delivery.services');
    Route::post('delivery/services/{booking}/update-rfid', [App\Http\Controllers\Admin\DeliveryDashboardController::class, 'updateRfid'])->name('delivery.update-rfid');
    Route::post('delivery/services/sync-vehicle-rfid', [App\Http\Controllers\Admin\DeliveryDashboardController::class, 'syncVehicleRfid'])->name('delivery.sync-vehicle-rfid');
    Route::post('delivery/services/batch-sync-vehicle-rfids', [App\Http\Controllers\Admin\DeliveryDashboardController::class, 'batchSyncVehicleRfids'])->name('delivery.batch-sync-vehicle-rfids');
    Route::post('delivery/services/sync-vehicle-balances', [App\Http\Controllers\Admin\DeliveryDashboardController::class, 'syncVehicleRfidBalances'])->name('delivery.sync-vehicle-balances');
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [App\Http\Controllers\Admin\Auth\LoginController::class, 'login']);
        Route::post('login/verify', [App\Http\Controllers\Admin\Auth\LoginController::class, 'verifyOTP'])->name('login.verify');
    });

    Route::middleware(['auth', 'check.admin.role'])->group(function () {
        Route::post('logout', [App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('logout');
    });
});

// Internal users' common approval history
Route::middleware(['auth','role:finance|validation|activation|audit|it|admin'])->group(function() {
    Route::get(
        '/wallet-approvals/history',
        [\App\Http\Controllers\ApprovalHistoryController::class, 'index']
    )->name('wallet-approvals.history');
});

// Email testing route for admins
Route::middleware(['auth','role:admin'])->match(['get','post'], '/admin/test-email', [\App\Http\Controllers\EmailTestController::class,'test'])
    ->name('admin.test.email');

// User routes
// Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
//     // Activity Logs
//     Route::get('/logs', [\App\Http\Controllers\User\LogsController::class, 'index'])->name('logs.index');
//     Route::get('/logs/{id}', [\App\Http\Controllers\User\LogsController::class, 'show'])->name('logs.show');
// });

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin|super-admin'])->group(function () {
    // ... existing admin routes
    
    // User management
    Route::post('/users/{user}/update-status', [ProfileController::class, 'updateStatus'])->name('users.update-status');
});

// Add map-marks route for customers
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/map-marks', [MapMarksController::class, 'index'])->name('map-marks.index');
    Route::get('/maps-list', [MapMarksController::class, 'mapsList'])->name('maps-list');
    Route::get('/enhanced-maps-list', [MapMarksController::class, 'enhancedMapsList'])->name('enhanced-maps-list');
    Route::get('/test-kml', [MapMarksController::class, 'testKmlFetch'])->name('test-kml');
    Route::get('/all-map-locations', [MapMarksController::class, 'allMapLocationsList'])->name('all-map-locations');
});
Route::get('/check-map-marks', [MapMarksController::class, 'checkMapMarks'])->name('check-map-marks');
Route::post('/nearest-station', [MapMarksController::class, 'getNearestStation'])->name('nearest-station');
Route::get('/map-view', [MapMarksController::class, 'mapView'])->name('map-view');

// Map locations from database
Route::get('/sync-map-locations', [MapMarksController::class, 'syncLocationsToDatabase'])->name('sync-map-locations');
Route::get('/database-maps-list', [MapMarksController::class, 'databaseMapLocationsList'])->name('database-maps-list');
Route::get('/map-data-list', [MapMarksController::class, 'syncKmlAndShowList'])
->name('maps.sync.list'); // Add auth/admin middleware as needed

// KML Map Routes
Route::get('/admin/map/test-kml-parsing', [App\Http\Controllers\Customer\MapMarksController::class, 'testKmlParsing'])->middleware(['auth']);
Route::get('/admin/map/sync-test-kml', [App\Http\Controllers\Customer\MapMarksController::class, 'syncTestKml'])->middleware(['auth']);
Route::get('/admin/map/test-kml-parsing/{filename?}', [App\Http\Controllers\Customer\MapMarksController::class, 'testKmlParsing'])->middleware(['auth']);
Route::get('/admin/map/test-sync-command/{filename?}', function($filename = 'test_locations.kml') {
    Artisan::call('kml:sync', [
        'filename' => $filename,
        '--test' => false
    ]);
    return response()->json([
        'status' => 'Success',
        'output' => Artisan::output()
    ]);
})->middleware(['auth']);

Route::post('/hyperpay/checkout', [HyperpayController::class, 'createCheckout'])->name('hyperpay.checkout');
    Route::post('/services/booking/hyperpay/get-form', [ServiceBookingController::class, 'getHyperpayForm'])->name('services.booking.hyperpay.get-form');
Route::get('/services/booking/hyperpay/status', [ServiceBookingController::class, 'hyperpayStatus'])->name('services.booking.hyperpay.status');
Route::post('/services/booking/hyperpay/status', [ServiceBookingController::class, 'hyperpayStatus'])->name('services.booking.hyperpay.status.post');

// DEBUG ROUTES - HyperPay Configuration & Testing (Remove after fixing issues)
Route::middleware('auth')->group(function () {
    Route::get('/debug-hyperpay-config', function() {
        $user = auth()->user();
        
        return response()->json([
            'config_status' => [
                'base_url' => config('services.hyperpay.base_url'),
                'has_access_token' => !empty(config('services.hyperpay.access_token')),
                'access_token_preview' => substr(config('services.hyperpay.access_token'), 0, 20) . '...',
                'entity_id_credit' => config('services.hyperpay.entity_id_credit'),
                'entity_id_mada' => config('services.hyperpay.entity_id_mada'),
                'currency' => config('services.hyperpay.currency'),
                'mode' => config('services.hyperpay.mode')
            ],
            'user_status' => [
                'id' => $user->id,
                'email' => $user->email,
                'email_valid' => filter_var($user->email, FILTER_VALIDATE_EMAIL),
                'has_email' => !empty($user->email)
            ],
            'test_params' => [
                'amount' => '100.00',
                'brand_credit' => 'credit_card',
                'brand_mada' => 'mada_card',
                'order_id' => null
            ]
        ]);
    })->name('debug.hyperpay.config');

    Route::post('/debug-hyperpay-api', function(Request $request) {
        $user = auth()->user();
        
        // Test HyperPay API directly
        $amount = $request->input('amount', '100.00');
        $brand = $request->input('brand', 'credit_card');
        
        $entityId = $brand === 'mada_card' 
            ? config('services.hyperpay.entity_id_mada')
            : config('services.hyperpay.entity_id_credit');
        
        $requestData = [
            'entityId' => $entityId,
            'amount' => number_format(floatval($amount), 2, '.', ''),
            'currency' => 'SAR',
            'paymentType' => 'DB',
            'merchantTransactionId' => 'DEBUG-' . time() . '-' . $user->id,
            'customer.email' => $user->email,
            'testMode' => 'EXTERNAL'
        ];
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.hyperpay.access_token'),
            ])->asForm()->post(config('services.hyperpay.base_url') . 'v1/checkouts', $requestData);
            
            return response()->json([
                'request_data' => $requestData,
                'response_status' => $response->status(),
                'response_body' => $response->json(),
                'success' => $response->successful()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'request_data' => $requestData
            ], 500);
        }
    })->name('debug.hyperpay.api');
    
    Route::get('/test-hyperpay-debug', function() {
        return view('test-hyperpay-debug');
    })->name('test.hyperpay.debug');
});