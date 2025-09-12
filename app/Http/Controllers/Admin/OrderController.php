<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Event;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Muestra una lista de todas las órdenes.
     * Un administrador no crea ni edita órdenes, solo las visualiza o cancela.
     */
    public function index(Request $request)
    {
        // Carga las relaciones para evitar N+1 queries en la vista
        $orders = Order::with(['user', 'event'])->latest()->get();
        
        // Obtener todos los eventos para el filtro
        $events = Event::orderBy('name')->get();
        
        return view('admin.orders.index', compact('orders', 'events'));
    }

    /**
     * Muestra los detalles de una orden específica.
     */
    public function show(Order $order)
    {
        // Carga todas las relaciones de la orden para mostrar detalles completos.
        $order->load(['user', 'event', 'coupon', 'items.ticketType', 'tickets']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Elimina (o cancela) una orden.
     */
    public function destroy(Order $order)
    {
        // Aquí podrías añadir lógica adicional, como anular los boletos.
        $order->delete();

        return redirect()->route('orders.index')
                         ->with('success', 'Orden cancelada correctamente.');
    }
}
