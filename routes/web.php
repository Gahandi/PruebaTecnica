<?php

use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\TicketTypeController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CheckinController;
use App\Http\Controllers\Staff\CheckinController as StaffCheckinController;
use App\Http\Controllers\AdminCheckinRedirectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\EventController as PublicEventController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserSpacesController;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\SpaceEventController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

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
        Route::get('/', [SpaceController::class, 'show'])->name('spaces.profile');
        Route::get('/edit', [SpaceController::class, 'edit'])->name('spaces.edit');
        Route::put('/update', [SpaceController::class, 'update'])->name('spaces.update-profile');
        Route::post('/update-profile', [SpaceController::class, 'updateProfile'])->name('spaces.update-profile');
        Route::get('/events/create', [SpaceEventController::class, 'create'])
            ->name('spaces.events.create')
            ->middleware('space.member');
        Route::post('/events', [SpaceEventController::class, 'store'])
            ->name('spaces.events.store')
            ->middleware('space.member');
        Route::get('/{event:slug}', [SpaceEventController::class, 'show']);

        // Rutas del carrito para subdominios
        Route::post('/cart/add', [App\Http\Controllers\Public\EventController::class, 'addToCart'])->name('subdomain.cart.add');
        Route::get('/cart', [App\Http\Controllers\CheckoutController::class, 'cart'])->name('subdomain.cart');

        // Rutas de checkout para subdominio
    });

Route::get('/', [HomeController::class, 'index'])->name('home');

// Ruta para refrescar token CSRF
Route::get('/refresh-csrf', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Ruta de prueba para verificar carrito
Route::get('/debug-cart', function () {
    $cart = session()->get('cart', []);
    return response()->json([
        'cart' => $cart,
        'count' => count($cart),
        'user_id' => auth()->id(),
    ]);
})->middleware('auth');

// Ruta de prueba para verificar redirección
Route::get('/test-redirect', function () {
    if (!auth()->check()) {
        return 'No autenticado';
    }

    $user = auth()->user();
    return response()->json([
        'email' => $user->email,
        'roles' => $user->roles->pluck('name')->toArray(),
        'has_staff' => $user->hasRole('staff'),
        'has_admin' => $user->hasRole('admin'),
        'path' => request()->path(),
    ]);
})->middleware('auth');

// Check-in route for QR scanning (no auth required)
Route::get('/checkin/{ticket}', [OrderController::class, 'checkinTicket'])->name('tickets.checkin');

// Rutas de autenticación
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('redirect.after.login');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas públicas
Route::get('/events', [PublicEventController::class, 'index'])->name('events.public');
Route::get('/events/{event}', [PublicEventController::class, 'show'])->name('events.show');

// Rutas del carrito (sin autenticación)
Route::middleware(['cart.context'])->group(function () {
    Route::post('/cart/add', [PublicEventController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [App\Http\Controllers\CheckoutController::class, 'cart'])->name('cart');
    Route::get('/cart/count', [App\Http\Controllers\CheckoutController::class, 'getCartCount'])->name('cart.count');
});

// Rutas protegidas por roles
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', function () {
        $eventsCount = \App\Models\Event::count();
        $ordersCount = \App\Models\Order::count();
        $usersCount = \App\Models\User::count();
        $totalRevenue = \App\Models\Order::where('status', 'completed')->sum('total');

        return view('admin.index', compact('eventsCount', 'ordersCount', 'usersCount', 'totalRevenue'));
    })->name('admin.index');
    Route::resource('admin/events', EventController::class)->names('admin.events');
    Route::resource('admin/ticket-types', TicketTypeController::class)->names('admin.ticket-types');
    Route::resource('admin/coupons', CouponController::class)->names('admin.coupons');
    Route::resource('admin/orders', AdminOrderController::class)->names('admin.orders');
});

// Ruta de prueba sin middleware de roles
Route::middleware(['auth'])->group(function () {
    Route::get('/staff/checkins', [StaffCheckinController::class, 'index'])->name('staff.checkins.index');
    Route::post('/staff/checkins', [StaffCheckinController::class, 'store'])->name('staff.checkins.store');
    Route::get('/staff/checkins/create', [StaffCheckinController::class, 'create'])->name('staff.checkins.create');
    Route::get('/staff/checkins/{checkin}', [StaffCheckinController::class, 'show'])->name('staff.checkins.show');
    Route::put('/staff/checkins/{checkin}', [StaffCheckinController::class, 'update'])->name('staff.checkins.update');
    Route::delete('/staff/checkins/{checkin}', [StaffCheckinController::class, 'destroy'])->name('staff.checkins.destroy');
    Route::get('/staff/checkins/{checkin}/edit', [StaffCheckinController::class, 'edit'])->name('staff.checkins.edit');
});

// Ruta especial para redirección de staff a admin/checkins - DEBE IR ANTES de las rutas resource
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/checkins', [AdminCheckinRedirectController::class, 'index'])->name('admin.checkins.redirect');
    Route::get('/admin/checkins/{checkin}', [AdminCheckinRedirectController::class, 'show']);
    Route::get('/admin/checkins/create', [AdminCheckinRedirectController::class, 'create']);
});

Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::resource('admin/checkins', CheckinController::class)->names('admin.checkins');
});

// Rutas para todos los usuarios autenticados
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/tickets/{ticket}', [OrderController::class, 'showTicket'])->name('tickets.show');
    Route::get('/tickets/{ticket}/pdf', [OrderController::class, 'downloadPdf'])->name('tickets.pdf');
    Route::get('/my-tickets', [OrderController::class, 'myTickets'])->name('tickets.my');

    // Rutas de checkout
    Route::prefix('checkout')
        ->name('checkout.')
        ->middleware(['cart.context'])
        ->group(function () {
            Route::get('/cart', [App\Http\Controllers\CheckoutController::class, 'cart'])->name('cart');
            Route::post('/add-to-cart', [App\Http\Controllers\CheckoutController::class, 'addToCart'])->name('add-to-cart');
            Route::post('/update-cart', [App\Http\Controllers\CheckoutController::class, 'updateCart'])->name('update-cart');
            Route::delete('/remove-from-cart/{ticketType}', [App\Http\Controllers\CheckoutController::class, 'removeFromCart'])->name('remove-from-cart');
            Route::get('/checkout', [App\Http\Controllers\CheckoutController::class, 'checkout'])->name('checkout');
            Route::post('/process-payment', [App\Http\Controllers\CheckoutController::class, 'processPayment'])->name('process-payment');
            Route::post('/apply-coupon', [App\Http\Controllers\CheckoutController::class, 'applyCoupon'])->name('apply-coupon');
            Route::delete('/remove-coupon', [App\Http\Controllers\CheckoutController::class, 'removeCoupon'])->name('remove-coupon');
            Route::get('/success/{order}', [App\Http\Controllers\CheckoutController::class, 'success'])->name('success');
            Route::get('/order/{order}', [App\Http\Controllers\CheckoutController::class, 'showOrder'])->name('order');
        });

    // Ruta de prueba para debug
    Route::post('/test-payment', function (\Illuminate\Http\Request $request) {
        \Log::info('=== TEST PAYMENT ROUTE CALLED ===');
        \Log::info('Request data:', $request->all());
        return response()->json(['status' => 'success', 'message' => 'Test route working']);
    })->name('test.payment');

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
