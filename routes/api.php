<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\ServiceController;
use App\Http\Controllers\API\HealthController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/register/otp', [AuthController::class, 'registerOtp'])
    ->middleware(['throttle:60,1']) // Temporarily simplified middleware
    ->name('api.register.otp');
Route::post('/register/verify-otp', [AuthController::class, 'verifyRegistrationOtp'])
    ->middleware(['throttle:60,1']) // Temporarily simplified middleware
    ->name('api.register.verify-otp');
// API endpoints for registration and login
Route::post('/register', [AuthController::class, 'register'])
    ->middleware(['throttle:60,1']) // Temporarily simplified middleware
    ->name('api.register');
//Route::post('/login', [AuthController::class, 'login'])->name('api.login');

// API endpoint for wallet recharge
Route::post('/wallet/recharge', [WalletController::class, 'recharge'])
    ->middleware('throttle:60,1') // Temporarily simplified middleware
    ->name('api.wallet.recharge');

// Service API endpoints
Route::get('/services', [ServiceController::class, 'getServices'])->name('api.services');
Route::post('/service/order', [ServiceController::class, 'orderService'])
    ->middleware('throttle:60,1') // Temporarily simplified middleware
    ->name('api.service.order');
Route::post('/service/booking', [ServiceController::class, 'bookService'])
    ->middleware('throttle:60,1') // Temporarily simplified middleware
    ->name('api.service.booking');
Route::post('/service/saved-cards', [ServiceController::class, 'getSavedCards'])->name('api.service.saved-cards');
Route::post('/service/booking-history', [ServiceController::class, 'getBookingHistory'])->name('api.service.booking-history');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware(['throttle:60,1']) // Temporarily simplified middleware
    ->name('api.login');
Route::post('/login/send-email-otp', [AuthController::class, 'sendOtpEmail'])
    ->middleware(['throttle:60,1']) // Temporarily simplified middleware
    ->name('api.login.send-email-otp');
//Route::post('/login/verify', [AuthController::class, 'verifyOtp']) // use this route from web.php ('/verify-otp', [AuthController::class, 'verifyOtp'])->name('verifyOtp');
//   // ->middleware(['api', \Illuminate\Session\Middleware\StartSession::class])
//    ->name('api.login.verify');

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

// Health check endpoint
Route::get('/health', [HealthController::class, 'check']);
