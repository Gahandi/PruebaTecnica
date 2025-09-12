<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\CouponController;

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

// API pública v1 con rate limiting y seguridad
Route::prefix('v1')->middleware(['api.security', 'throttle:60,1'])->group(function () {
    // Eventos públicos
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}', [EventController::class, 'show']);
    
    // Órdenes (checkout) con rate limiting más estricto
    Route::middleware(['throttle:10,1'])->group(function () {
        Route::post('/orders', [OrderController::class, 'store']);
    });
    
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    
    // Tickets
    Route::get('/tickets/{id}', [TicketController::class, 'show']);
    Route::get('/tickets/{id}/validate', [TicketController::class, 'validateTicket']);
    
    // Coupons
    Route::post('/coupons/validate', [CouponController::class, 'validateCoupon']);
});

// Rutas protegidas por autenticación
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
