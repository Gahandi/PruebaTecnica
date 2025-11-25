<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SpaceCouponController extends Controller
{
    /**
     * Obtener el espacio desde el subdomain
     */
    private function getSpace($subdomain)
    {
        $space = Space::where('subdomain', $subdomain)->first();
        
        if (!$space) {
            abort(404, 'Espacio no encontrado');
        }
        
        return $space;
    }

    /**
     * Verificar que el usuario es admin del espacio
     */
    private function checkAdminPermission($space)
    {
        if (!auth()->check() || !auth()->user()->isAdminOfSpace($space->id)) {
            abort(403, 'No tienes permisos para gestionar cupones en este espacio');
        }
    }

    /**
     * Muestra una lista de cupones del espacio.
     */
    public function index(Request $request, $subdomain)
    {
        $space = $this->getSpace($subdomain);
        $this->checkAdminPermission($space);

        $coupons = Coupon::where('spaces_id', $space->id)
            ->withCount('payments')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('spaces.coupons.index', compact('coupons', 'space'));
    }

    /**
     * Muestra el formulario para crear un nuevo cupón.
     */
    public function create(Request $request, $subdomain)
    {
        $space = $this->getSpace($subdomain);
        $this->checkAdminPermission($space);

        return view('spaces.coupons.create', compact('space'));
    }

    /**
     * Guarda un nuevo cupón.
     */
    public function store(Request $request, $subdomain)
    {
        $space = $this->getSpace($subdomain);
        $this->checkAdminPermission($space);

        $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($space) {
                    $exists = Coupon::where('code', $value)
                        ->where('spaces_id', $space->id)
                        ->exists();
                    if ($exists) {
                        $fail('El código de cupón ya existe para este espacio.');
                    }
                },
            ],
            'discount_percentage' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date|after:now',
        ]);

        Coupon::create([
            'code' => strtoupper($request->code),
            'discount_percentage' => $request->discount_percentage,
            'expires_at' => $request->expires_at,
            'spaces_id' => $space->id,
        ]);

        return redirect()->route('spaces.coupons.index', $subdomain)
                         ->with('success', 'Cupón creado correctamente.');
    }

    /**
     * Muestra los detalles de un cupón específico.
     */
    public function show(Request $request, $subdomain, Coupon $coupon)
    {
        $space = $this->getSpace($subdomain);
        $this->checkAdminPermission($space);

        // Verificar que el cupón pertenece al espacio
        if ($coupon->spaces_id !== $space->id) {
            abort(404, 'Cupón no encontrado');
        }

        $coupon->load('payments.order.user');

        return view('spaces.coupons.show', compact('coupon', 'space'));
    }

    /**
     * Muestra el formulario para editar un cupón.
     */
    public function edit(Request $request, $subdomain, Coupon $coupon)
    {
        $space = $this->getSpace($subdomain);
        $this->checkAdminPermission($space);

        // Verificar que el cupón pertenece al espacio
        if ($coupon->spaces_id !== $space->id) {
            abort(404, 'Cupón no encontrado');
        }

        return view('spaces.coupons.edit', compact('coupon', 'space'));
    }

    /**
     * Actualiza un cupón.
     */
    public function update(Request $request, $subdomain, Coupon $coupon)
    {
        $space = $this->getSpace($subdomain);
        $this->checkAdminPermission($space);

        // Verificar que el cupón pertenece al espacio
        if ($coupon->spaces_id !== $space->id) {
            abort(404, 'Cupón no encontrado');
        }

        $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                function ($attribute, $value, $fail) use ($space, $coupon) {
                    $exists = Coupon::where('code', $value)
                        ->where('spaces_id', $space->id)
                        ->where('id', '!=', $coupon->id)
                        ->exists();
                    if ($exists) {
                        $fail('El código de cupón ya existe para este espacio.');
                    }
                },
            ],
            'discount_percentage' => 'required|integer|min:1|max:100',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $coupon->update([
            'code' => strtoupper($request->code),
            'discount_percentage' => $request->discount_percentage,
            'expires_at' => $request->expires_at,
        ]);

        return redirect()->route('spaces.coupons.index', $subdomain)
                         ->with('success', 'Cupón actualizado correctamente.');
    }

    /**
     * Elimina un cupón.
     */
    public function destroy(Request $request, $subdomain, Coupon $coupon)
    {
        $space = $this->getSpace($subdomain);
        $this->checkAdminPermission($space);

        // Verificar que el cupón pertenece al espacio
        if ($coupon->spaces_id !== $space->id) {
            abort(404, 'Cupón no encontrado');
        }

        $coupon->delete();

        return redirect()->route('spaces.coupons.index', $subdomain)
                         ->with('success', 'Cupón eliminado correctamente.');
    }
}
