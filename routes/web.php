<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\EventController as PublicEventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserSpacesController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\SpaceEventController;
use App\Http\Controllers\SpaceCouponController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ScannerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

// Rutas con subdomain (deben ir primero para tener prioridad)
Route::domain('{subdomain}.' . config('app.url'))
    ->middleware(['subdomain.session', 'cart.context'])
    ->group(function () {
        Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner.index')->middleware(['auth', 'email.verified']);
        Route::get('/', [SpaceController::class, 'show'])->name('spaces.profile');
        Route::get('/edit', [SpaceController::class, 'edit'])->name('spaces.edit')->middleware(['auth', 'email.verified']);
        Route::put('/update', [SpaceController::class, 'update'])->name('spaces.update')->middleware(['auth', 'email.verified']);
        Route::post('/update-profile', [SpaceController::class, 'updateProfile'])->name('spaces.update-profile')->middleware(['auth', 'email.verified']);
        Route::get('/events/create', [SpaceEventController::class, 'create'])
            ->name('spaces.events.create')
            ->middleware(['auth', 'email.verified', 'space.member']);
        Route::post('/events', [SpaceEventController::class, 'store'])
            ->name('spaces.events.store')
            ->middleware(['auth', 'email.verified', 'space.member']);

        Route::get('eventos/{event:slug}/editar', [SpaceEventController::class, 'edit'])
            ->name('spaces.events.edit')
            ->middleware(['auth', 'email.verified', 'space.member']); // Asumiendo que solo los miembros pueden editar

        Route::put('eventos/{event:slug}', [SpaceEventController::class, 'update'])
            ->name('spaces.events.update')
            ->middleware(['auth', 'email.verified', 'space.member']);
        Route::get('/{event:slug}', [SpaceEventController::class, 'show']);
        // Mostrar eventos por categoría
        Route::get('/categories/{id}', [SpaceEventController::class, 'showEvents'])
            ->name('categories.events');
        
        // Rutas de cupones por espacio (solo para admins del espacio)
        Route::prefix('coupons')
            ->name('spaces.coupons.')
            ->middleware(['auth', 'email.verified', 'space.member'])
            ->group(function () {
                Route::get('/', [SpaceCouponController::class, 'index'])->name('index');
                Route::get('/create', [SpaceCouponController::class, 'create'])->name('create');
                Route::post('/', [SpaceCouponController::class, 'store'])->name('store');
                Route::get('/{coupon}', [SpaceCouponController::class, 'show'])->name('show');
                Route::get('/{coupon}/edit', [SpaceCouponController::class, 'edit'])->name('edit');
                Route::put('/{coupon}', [SpaceCouponController::class, 'update'])->name('update');
                Route::delete('/{coupon}', [SpaceCouponController::class, 'destroy'])->name('destroy');
            });
        // Rutas de checkout para subdominio
    });

Route::get('/', [HomeController::class, 'index'])->name('home');

// Ruta para refrescar token CSRF
Route::get('/refresh-csrf', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});


// Check-in route for QR scanning (no auth required)
Route::get('/checkin/{ticket}', [OrderController::class, 'checkinTicket'])->name('tickets.checkin');

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('redirect.after.login');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Rutas de restablecimiento de contraseña
    Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->name('password.update');
});

// Rutas de verificación de email
Route::prefix('verify')->name('verify.')->group(function () {
    Route::get('/email', [\App\Http\Controllers\Auth\VerificationController::class, 'showVerificationForm'])->name('email');
    Route::post('/send-code', [\App\Http\Controllers\Auth\VerificationController::class, 'sendVerificationCode'])->name('send-code');
    Route::post('/code', [\App\Http\Controllers\Auth\VerificationController::class, 'verifyCode'])->name('code');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas públicas
Route::get('/events', [PublicEventController::class, 'index'])->name('events.public');
Route::get('/events/{event}', [PublicEventController::class, 'show'])->name('events.show');


// Rutas del carrito (sin autenticación)
Route::middleware(['cart.context', \App\Http\Middleware\HandleCorsForCart::class])->group(function () {
    Route::options('/cart/{any}', function() { return response('', 200); })->where('any', '.*');
    Route::get('/cart/csrf-token', function(Request $request) {
        $origin = $request->headers->get('Origin');
        $response = response()->json(['token' => csrf_token()]);

        if ($origin) {
            $response->header('Access-Control-Allow-Origin', $origin)
                     ->header('Access-Control-Allow-Credentials', 'true')
                     ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
                     ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');
        }

        return $response;
    })->name('cart.csrf-token');
    Route::post('/cart/add', [PublicEventController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/sync', [App\Http\Controllers\CheckoutController::class, 'syncCart'])->name('cart.sync');
    Route::get('/cart', [App\Http\Controllers\CheckoutController::class, 'cart'])->name('cart');
    Route::get('/cart/count', [App\Http\Controllers\CheckoutController::class, 'getCartCount'])->name('cart.count');
    Route::get('/cart/dropdown', [CheckoutController::class, 'getCartDropdown'])->name('cart.dropdown');
});

// Rutas protegidas por roles
Route::middleware(['auth', 'email.verified', 'role:admin,staff'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'email.verified', 'role:admin'])->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.index');
});

// Ruta de prueba sin middleware de roles
Route::middleware(['auth', 'email.verified'])->group(function () {
    Route::get('/staff/checkins', [StaffCheckinController::class, 'index'])->name('staff.checkins.index');
    Route::post('/staff/checkins', [StaffCheckinController::class, 'store'])->name('staff.checkins.store');
    Route::get('/staff/checkins/create', [StaffCheckinController::class, 'create'])->name('staff.checkins.create');
    Route::get('/staff/checkins/{checkin}', [StaffCheckinController::class, 'show'])->name('staff.checkins.show');
    Route::put('/staff/checkins/{checkin}', [StaffCheckinController::class, 'update'])->name('staff.checkins.update');
    Route::delete('/staff/checkins/{checkin}', [StaffCheckinController::class, 'destroy'])->name('staff.checkins.destroy');
    Route::get('/staff/checkins/{checkin}/edit', [StaffCheckinController::class, 'edit'])->name('staff.checkins.edit');
});


// Rutas de checkout
Route::prefix('checkout')
    ->name('checkout.')
    ->middleware(['cart.context'])
    ->group(function () {
        // Rutas públicas del carrito (no requieren verificación)
        Route::get('/cart', [App\Http\Controllers\CheckoutController::class, 'cart'])->name('cart');
        Route::post('/add-to-cart', [App\Http\Controllers\CheckoutController::class, 'addToCart'])->name('add-to-cart');
        Route::post('/update-cart', [App\Http\Controllers\CheckoutController::class, 'updateCart'])->name('update-cart');
        Route::delete('/remove-from-cart/{key}', [CheckoutController::class, 'removeFromCart'])->name('remove-from-cart');
        Route::post('/apply-coupon', [App\Http\Controllers\CheckoutController::class, 'applyCoupon'])->name('apply-coupon');
        Route::delete('/remove-coupon', [App\Http\Controllers\CheckoutController::class, 'removeCoupon'])->name('remove-coupon');
        Route::post('/quick-login-register', [App\Http\Controllers\CheckoutController::class, 'quickLoginOrRegister'])->name('quick-login-register');
        Route::get('/callback', [App\Http\Controllers\CheckoutController::class, 'handlePaymentCallback'])->name('callback');
        
        // Rutas de pago que requieren autenticación y verificación de email
        Route::middleware(['auth', 'email.verified'])->group(function () {
            Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'checkout'])->name('checkout');
            Route::post('/process-payment', [App\Http\Controllers\CheckoutController::class, 'processPayment'])->name('process-payment');
            Route::get('/success/{order}', [App\Http\Controllers\CheckoutController::class, 'success'])->name('success');
            Route::get('/order/{order}', [App\Http\Controllers\CheckoutController::class, 'showOrder'])->name('order');
        });
    });

// Rutas para todos los usuarios autenticados
Route::middleware(['auth', 'email.verified'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/tickets/{ticket}', [OrderController::class, 'showTicket'])->name('tickets.show');
    Route::get('/tickets/{ticket}/pdf', [OrderController::class, 'downloadPdf'])->name('tickets.pdf');
    Route::get('/my-tickets', [OrderController::class, 'myTickets'])->name('tickets.my');

    // Ruta de prueba para debug
    Route::post('/test-payment', function (\Illuminate\Http\Request $request) {
        \Log::info('=== TEST PAYMENT ROUTE CALLED ===');
        \Log::info('Request data:', $request->all());
        return response()->json(['status' => 'success', 'message' => 'Test route working']);
    })->name('test.payment');

    // Ruta para el callback de Openpay (3D Secure)

    // Rutas de espacios del usuario
    Route::prefix('spaces')
        ->name('user.spaces.')
        ->group(function () {
            Route::get('/', [UserSpacesController::class, 'index'])->name('index');
            Route::get('/create', [UserSpacesController::class, 'create'])->name('create');
            Route::post('/', [UserSpacesController::class, 'store'])->name('store');
            Route::get('/join', [UserSpacesController::class, 'join'])->name('join');
            Route::post('/join/{space}', [UserSpacesController::class, 'joinSpace'])->name('join.space');
        });
});

