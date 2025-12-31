<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvitationController extends Controller
{
    public function accept($token)
    {
        $invitation = \App\Models\SpaceInvitation::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$invitation) {
            return redirect()->route('home')->with('error', 'La invitación es inválida o ha expirado.');
        }

        // Si el usuario está logueado
        if (\Illuminate\Support\Facades\Auth::check()) {
            $user = \Illuminate\Support\Facades\Auth::user();

            // Verificar si el email coincide
            if ($user->email !== $invitation->email) {
                // Opción: Cerrar sesión y pedir registro, o mostrar error
                // Mostremos error por seguridad
                return redirect()->route('home')->with('error', 'Esta invitación fue enviada a ' . $invitation->email . ' y no coincide con tu cuenta actual.');
            }

            // Agregar al espacio
            $this->addUserToSpace($user, $invitation);

            // Borrar invitación
            $invitation->delete();

            // Redirigir al espacio
            $space = $invitation->space;
            $appUrl = config('app.url');
            $scheme = parse_url($appUrl, PHP_URL_SCHEME);
            $host = parse_url($appUrl, PHP_URL_HOST);
            $redirectUrl = "{$scheme}://{$space->subdomain}.{$host}";

            return redirect($redirectUrl)->with('success', '¡Te has unido al espacio correctamente!');
        }

        // Si no está logueado, redirigir al registro con token e email
        return redirect()->route('register', [
            'invitation_token' => $token,
            'email' => $invitation->email
        ]);
    }

    protected function addUserToSpace($user, $invitation)
    {
        // Verificar si ya existe (por si acaso)
        $existing = \App\Models\SpacesUser::where('space_id', $invitation->space_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->update(['role_space_id' => $invitation->role_space_id]);
        } else {
            \App\Models\SpacesUser::create([
                'space_id' => $invitation->space_id,
                'user_id' => $user->id,
                'role_space_id' => $invitation->role_space_id
            ]);
        }
    }
}
