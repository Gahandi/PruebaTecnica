<?php

namespace App\Http\Controllers;

use App\Models\Space;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\S3ImageManager;

class UserSpacesController extends Controller
{
    use S3ImageManager;

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
        if ($user->hasAdminSpace()) {
            return redirect()->route('user.spaces.index')
                ->with('error', 'Ya tienes un cajón de eventos. Solo puedes tener uno por usuario.');
        }
        return view('user.spaces.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Verificar si ya tiene un cajón
        if ($user->hasAdminSpace()) {
            return redirect()->route('user.spaces.index')
                ->withErrors(['error' => 'Ya tienes un cajón de eventos. Solo puedes tener uno por usuario.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'subdomain' => 'required|string|max:50|unique:spaces,subdomain|alpha_dash',
            'logo' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'banner' => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ], [
            // Mensajes para el nombre
            'name.required' => 'El nombre de tu cajón es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no puede tener más de 255 caracteres.',

            // Mensajes para la descripción
            'description.required' => 'La descripción es obligatoria.',
            'description.string' => 'La descripción debe ser un texto válido.',
            'description.max' => 'La descripción no puede tener más de 1000 caracteres.',

            // Mensajes para el subdominio
            'subdomain.required' => 'Debes escribir un nombre para generar la URL personalizada.',
            'subdomain.string' => 'La URL debe ser un texto válido.',
            'subdomain.max' => 'La URL no puede tener más de 50 caracteres.',
            'subdomain.unique' => 'Esta URL ya está en uso. Prueba con otro nombre.',
            'subdomain.alpha_dash' => 'La URL solo puede contener letras, números y guiones.',

            // Mensajes para el logo
            'logo.required' => 'El logo es obligatorio. Por favor sube una imagen.',
            'logo.image' => 'El logo debe ser una imagen válida.',
            'logo.mimes' => 'El logo debe ser de tipo: JPEG, PNG, JPG o WebP.',
            'logo.max' => 'El logo no puede pesar más de 2MB.',

            // Mensajes para el banner
            'banner.required' => 'El banner es obligatorio. Por favor sube una imagen.',
            'banner.image' => 'El banner debe ser una imagen válida.',
            'banner.mimes' => 'El banner debe ser de tipo: JPEG, PNG, JPG o WebP.',
            'banner.max' => 'El banner no puede pesar más de 4MB.',
        ]);

        // SUBIR LOGO
        $logoFile = $request->file('logo');
        $logoContent = file_get_contents($logoFile->getPathname());
        $logoUrl = $this->saveImages($logoContent, 'spaces/logos', uniqid('logo_'));

        // SUBIR BANNER
        $bannerFile = $request->file('banner');
        $bannerContent = file_get_contents($bannerFile->getPathname());
        $bannerUrl = $this->saveImages($bannerContent, 'spaces/banners', uniqid('banner_'));

        // CREAR SPACE
        $space = Space::create([
            'name' => $request->name,
            'description' => $request->description,
            'subdomain' => $request->subdomain,
            'logo' => $logoUrl,
            'banner' => $bannerUrl,
            'openpay_id' => 'temp_' . time(),
            'reference' => 'ref_' . time(),
        ]);

        // Asociar usuario como admin
        $user->spaces()->attach($space->id, [
            'role_space_id' => 1,
        ]);

        return redirect()->route('user.spaces.index')
            ->with('success', 'Tu cajón se creó correctamente con su logo y banner.');
    }

    public function join()
    {
        $spaces = Space::whereDoesntHave('users', function ($query) {
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
