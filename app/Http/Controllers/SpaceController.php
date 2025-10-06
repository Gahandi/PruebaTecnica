<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\Event;
use Illuminate\Http\Request;

class SpaceController extends Controller
{
    public function show(Request $request, $subdomain)
    {
        // Buscar el espacio por subdomain
        $space = Space::where('subdomain', $subdomain)->first();
        
        if (!$space) {
            abort(404, 'Espacio no encontrado');
        }
        
        return view('spaces.profile', compact('space'));
    }
    
    public function edit(Request $request, $subdomain)
    {
        $space = Space::where('subdomain', $subdomain)->first();
        
        if (!$space) {
            abort(404, 'Espacio no encontrado');
        }
        
        // Verificar que el usuario autenticado pertenece al espacio
        if (!auth()->user() || !auth()->user()->spaces->contains($space->id)) {
            abort(403, 'No tienes permisos para editar este espacio');
        }
        
        return view('spaces.edit-profile', compact('space'));
    }
    
    public function update(Request $request, $subdomain)
    {
        $space = Space::where('subdomain', $subdomain)->first();
        
        if (!$space) {
            abort(404, 'Espacio no encontrado');
        }
        
        // Verificar que el usuario autenticado pertenece al espacio
        if (!auth()->user() || !auth()->user()->spaces->contains($space->id)) {
            abort(403, 'No tienes permisos para editar este espacio');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'about' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'color_primary' => 'nullable|string|max:7',
            'color_secondary' => 'nullable|string|max:7',
            'location' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:20',
        ]);
        
        // Actualizar campos básicos
        $space->update([
            'name' => $request->name,
            'description' => $request->description,
            'about' => $request->about,
            'color_primary' => $request->color_primary,
            'color_secondary' => $request->color_secondary,
            'location' => $request->location,
            'website' => $request->website,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
        ]);
        
        // Manejar upload de logo
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('spaces/logos', 'public');
            $space->update(['logo' => $logoPath]);
        }
        
        // Manejar upload de banner
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('spaces/banners', 'public');
            $space->update(['banner' => $bannerPath]);
        }
        
        return redirect()->route('spaces.profile', $space->subdomain)
                        ->with('success', 'Perfil del cajón actualizado exitosamente');
    }
}
