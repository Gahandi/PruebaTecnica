<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    use \App\Traits\S3ImageManager;

    public function show()
    {
        $user = Auth::user();

        // Get spaces the user follows (role_space_id = 3 = viewer = follower)
        $spacesFollowing = \App\Models\Space::whereHas('users', function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->where('role_space_id', 3)
                ->whereNull('spaces_users.deleted_at');
        })->withCount([
                    'users' => function ($q) {
                        $q->where('role_space_id', 3)->whereNull('spaces_users.deleted_at');
                    }
                ])->get();

        return view('users.profile', compact('user', 'spacesFollowing'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validar solo información personal (sin campos de contraseña)
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'image' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->name = $request->name;
        $user->last_name = $request->last_name;
        $user->phone = $request->phone;
        $user->email = $request->email;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image && \Storage::disk('public')->exists($user->image)) {
                \Storage::disk('public')->delete($user->image);
            }

            // Detectar extensión
            $imageFile = $request->file('image');
            $fileContents = file_get_contents($imageFile->getPathname());

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $fileContents);
            finfo_close($finfo);

            $extensions = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];

            $extension = $extensions[$mimeType] ?? 'jpg';

            // Generar nombre
            $fileName = $user->id . '.' . $extension;

            // Ruta relativa EXACTA como en UserController
            $relativePath = env('S3_ENVIRONMENT') . '/users/' . $fileName;

            // Subir a S3
            $this->saveImages(
                $fileContents,
                'users',
                $user->id
            );

            // Guardar SOLO la ruta relativa
            $user->image = $relativePath;
        }

        $user->save();

        return back()->with('success', 'Información personal actualizada correctamente.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Verificar contraseña actual
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        // Actualizar contraseña
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }
}
