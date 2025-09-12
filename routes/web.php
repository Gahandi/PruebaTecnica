<?php

use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\TicketTypeController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CheckinController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\EventController as PublicEventController;
use App\Http\Controllers\OrderController;
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

Route::get('/', function () {
    return redirect()->route('events.public');
});

// Ruta para refrescar token CSRF
Route::get('/refresh-csrf', function() {
    return response()->json(['csrf_token' => csrf_token()]);
});

// Ruta de prueba para verificar carrito
Route::get('/debug-cart', function() {
    $cart = session()->get('cart', []);
    return response()->json([
        'cart' => $cart,
        'count' => count($cart),
        'user_id' => auth()->id()
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
Route::post('/cart/add', [PublicEventController::class, 'addToCart'])->name('cart.add');

// Rutas protegidas por roles
Route::middleware(['auth', 'role:admin,staff'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

Route::middleware(['auth', 'role:admin', 'handle.403'])->group(function () {
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

Route::middleware(['auth', 'role:admin,staff', 'handle.403'])->group(function () {
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
    Route::prefix('checkout')->name('checkout.')->group(function () {
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
    Route::post('/test-payment', function(\Illuminate\Http\Request $request) {
        \Log::info('=== TEST PAYMENT ROUTE CALLED ===');
        \Log::info('Request data:', $request->all());
        return response()->json(['status' => 'success', 'message' => 'Test route working']);
    })->name('test.payment');
});