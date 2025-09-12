<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view coupons')->only(['index', 'show']);
        $this->middleware('permission:create coupons')->only(['create', 'store']);
        $this->middleware('permission:edit coupons')->only(['edit', 'update']);
        $this->middleware('permission:delete coupons')->only(['destroy']);
    }

    /**
     * Muestra una lista de cupones.
     */
    public function index()
    {
        $coupons = Coupon::withCount('orders')->get();
        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Muestra los detalles de un cupón específico.
     */
    public function show(Coupon $coupon)
    {
        $coupon->load('orders.user');
        return view('admin.coupons.show', compact('coupon'));
    }

    /**
     * Muestra el formulario para crear un nuevo cupón.
     */
    public function create()
    {
        return view('admin.coupons.create');
    }

    /**
     * Guarda un nuevo cupón.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons|max:50',
            'discount_percentage' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date',
        ]);

        Coupon::create($request->all());

        return redirect()->route('admin.coupons.index')
                         ->with('success', 'Cupón creado correctamente.');
    }

    /**
     * Muestra el formulario para editar un cupón.
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
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
        ]);

        $coupon->update($request->all());

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
