<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Space;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Muestra una lista de cupones.
     */
    public function index()
    {
        $coupons = Coupon::with('space')
            ->withCount('payments')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Muestra los detalles de un cupón específico.
     */
    public function show(Coupon $coupon)
    {
        $coupon->load(['payments.order.user', 'space']);
        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Muestra el formulario para crear un nuevo cupón.
     */
    public function create()
    {
        $spaces = Space::all();
        return view('admin.coupons.create', compact('spaces'));
    }

    /**
     * Guarda un nuevo cupón.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'discount_percentage' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date|after:now',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'min_order_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'spaces_id' => 'nullable|exists:spaces,id',
        ]);

        Coupon::create([
            'code' => strtoupper($request->code),
            'discount_percentage' => $request->discount_percentage,
            'expires_at' => $request->expires_at,
            'max_uses' => $request->max_uses,
            'max_uses_per_user' => $request->max_uses_per_user,
            'min_order_amount' => $request->min_order_amount,
            'is_active' => $request->has('is_active'),
            'spaces_id' => $request->spaces_id,
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Cupón creado correctamente.');
    }

    /**
     * Muestra el formulario para editar un cupón.
     */
    public function edit(Coupon $coupon)
    {
        $spaces = Space::all();
        return view('admin.coupons.edit', compact('coupon', 'spaces'));
    }

    /**
     * Actualiza un cupón.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'discount_percentage' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date',
            'max_uses' => 'nullable|integer|min:1',
            'max_uses_per_user' => 'nullable|integer|min:1',
            'min_order_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'spaces_id' => 'nullable|exists:spaces,id',
        ]);

        $coupon->update([
            'code' => strtoupper($request->code),
            'discount_percentage' => $request->discount_percentage,
            'expires_at' => $request->expires_at,
            'max_uses' => $request->max_uses,
            'max_uses_per_user' => $request->max_uses_per_user,
            'min_order_amount' => $request->min_order_amount,
            'is_active' => $request->has('is_active'),
            'spaces_id' => $request->spaces_id,
        ]);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Cupón actualizado correctamente.');
    }

    /**
     * Elimina un cupón.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Cupón eliminado correctamente.');
    }
}
