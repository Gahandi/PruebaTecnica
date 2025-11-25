<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    /**
     * Mostrar formulario para solicitar reset de contraseña
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Enviar link de reset de contraseña
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'Debe ser un correo electrónico válido.',
            'email.exists' => 'No encontramos una cuenta con ese correo electrónico.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No encontramos una cuenta con ese correo electrónico.'])
                ->withInput();
        }

        // Generar token
        $token = Str::random(64);

        // Eliminar tokens anteriores para este email
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        // Guardar nuevo token
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Generar URL de reset
        $resetUrl = url('/reset-password/' . $token . '?email=' . urlencode($request->email));

        // Enviar email
        try {
            Mail::send('emails.reset-password-link', [
                'user' => $user,
                'resetUrl' => $resetUrl,
                'token' => $token,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Restablecer tu contraseña')
                    ->replyTo(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info('Link de reset de contraseña enviado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return back()->with('status', 'Te hemos enviado un enlace para restablecer tu contraseña. Por favor, revisa tu correo electrónico.');

        } catch (\Swift_TransportException $e) {
            Log::error('Error de conexión SMTP al enviar link de reset', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['email' => 'Hubo un problema al enviar el correo. Por favor, intenta más tarde.']);
        } catch (\Exception $e) {
            Log::error('Error al enviar link de reset de contraseña', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['email' => 'Hubo un problema al enviar el correo. Por favor, intenta más tarde.']);
        }
    }

    /**
     * Mostrar formulario para resetear contraseña
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->query('email');

        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'El enlace de restablecimiento no es válido.']);
        }

        // Verificar que el token existe y no ha expirado
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$passwordReset) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Este enlace de restablecimiento no es válido o ha expirado.']);
        }

        // Verificar que el token coincide
        if (!Hash::check($token, $passwordReset->token)) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Este enlace de restablecimiento no es válido.']);
        }

        // Verificar que no haya expirado (60 minutos)
        $createdAt = Carbon::parse($passwordReset->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();

            return redirect()->route('password.request')
                ->withErrors(['email' => 'Este enlace de restablecimiento ha expirado. Por favor, solicita uno nuevo.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Procesar el reset de contraseña
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'El correo electrónico es requerido.',
            'email.email' => 'Debe ser un correo electrónico válido.',
            'email.exists' => 'No encontramos una cuenta con ese correo electrónico.',
            'password.required' => 'La contraseña es requerida.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        // Verificar que el token existe y no ha expirado
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return back()->withErrors(['email' => 'Este enlace de restablecimiento no es válido o ha expirado.'])
                ->withInput();
        }

        // Verificar que el token coincide
        if (!Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors(['email' => 'Este enlace de restablecimiento no es válido.'])
                ->withInput();
        }

        // Verificar que no haya expirado (60 minutos)
        $createdAt = Carbon::parse($passwordReset->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return back()->withErrors(['email' => 'Este enlace de restablecimiento ha expirado. Por favor, solicita uno nuevo.'])
                ->withInput();
        }

        // Actualizar contraseña del usuario
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // Eliminar el token usado
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        Log::info('Contraseña restablecida exitosamente', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return redirect()->route('login')
            ->with('success', 'Tu contraseña ha sido restablecida exitosamente. Ahora puedes iniciar sesión.');
    }
}

