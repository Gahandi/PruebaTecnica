<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSpacesController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $spaces = $user->spaces()->with('events')->get();

        return view('user.spaces.index', compact('spaces'));
    }

    public function create()
    {
        $user = Auth::user();

        // Verificar si el usuario ya tiene un cajón
        if ($user->spaces()->count() > 0) {
            return redirect()->route('user.spaces.index')
                            ->with('error', 'Ya tienes un cajón de eventos. Solo puedes tener uno por usuario.');
        }

        return view('user.spaces.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Verificar si el usuario ya tiene un cajón
        if ($user->spaces()->count() > 0) {
            return redirect()->route('user.spaces.index')
                            ->with('error', 'Ya tienes un cajón de eventos. Solo puedes tener uno por usuario.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'subdomain' => 'required|string|max:50|unique:spaces,subdomain|alpha_dash',
        ]);

        $space = Space::create([
            'name' => $request->name,
            'logo' => 'logo.png',
            'description' => $request->description,
            'subdomain' => $request->subdomain,
            'openpay_id' => 'temp_' . time(), // Temporal
            'reference' => 'ref_' . time(), // Temporal
        ]);

        // Asociar el usuario al espacio como admin
        $user->spaces()->attach($space->id, [
            'role_space_id' => 1, // Asumir que 1 es admin
        ]);

        return redirect()->route('user.spaces.index')
                        ->with('success', 'Tu cajón de eventos ha sido creado exitosamente');
    }

    public function join()
    {
        $spaces = Space::whereDoesntHave('users', function($query) {
            $query->where('user_id', Auth::id());
        })->get();

        return view('user.spaces.join', compact('spaces'));
    }

    public function joinSpace(Request $request, Space $space)
    {
        $user = Auth::user();

        // Verificar que el usuario no esté ya en el espacio
        if ($user->spaces()->where('space_id', $space->id)->exists()) {
            return redirect()->back()->with('error', 'Ya estás en este espacio');
        }

        // Unir al usuario al espacio
        $user->spaces()->attach($space->id, [
            'role_space_id' => 2, // Asumir que 2 es viewer
        ]);

        return redirect()->route('user.spaces.index')
                        ->with('success', 'Te has unido al espacio exitosamente');
    }
}
