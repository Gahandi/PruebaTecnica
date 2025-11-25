<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class VerificationController extends Controller
{
    /**
     * Enviar código de verificación por email (POST desde formulario)
     */
    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Usuario no encontrado.'])->withInput();
        }

        // Si ya está verificado, no enviar código
        if ($user->verified_at) {
            return back()->with('info', 'Este correo ya está verificado.');
        }

        // Generar código de 6 dígitos
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Invalidar códigos anteriores del mismo tipo
        UsersCode::where('user_id', $user->id)
            ->where('type', 'email_verification')
            ->where('used', false)
            ->update(['used' => true]);

        // Crear nuevo código
        UsersCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => 'email_verification',
            'used' => false,
            'expires_at' => Carbon::now()->addHours(24),
        ]);

        // Guardar código en el usuario (opcional, para referencia rápida)
        $user->verification_code = $code;
        $user->save();

        // Enviar correo con el código
        try {
            // Validar configuración de correo antes de intentar enviar
            $mailHost = config('mail.mailers.smtp.host');
            $mailPort = config('mail.mailers.smtp.port');
            
            if (empty($mailHost) || empty($mailPort)) {
                Log::warning('Configuración de correo incompleta al enviar código', [
                    'user_id' => $user->id,
                    'mail_host' => $mailHost,
                    'mail_port' => $mailPort
                ]);
                
            return back()->withErrors(['email' => 'El servicio de correo no está configurado. Por favor, contacta al administrador.'])->withInput();
            }

            Mail::send('emails.verification-code', [
                'user' => $user,
                'code' => $code,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Código de verificación de email')
                    ->replyTo(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info('Código de verificación enviado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return back()->with('success', 'Código de verificación enviado a tu correo electrónico. Revisa tu bandeja de entrada.');

        } catch (\Swift_TransportException $e) {
            // Error de conexión SMTP
            Log::error('Error de conexión SMTP al enviar código de verificación', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
            ]);

            return back()->withErrors(['email' => 'Error de conexión con el servidor de correo. El código se ha guardado, pero no se pudo enviar el correo. Por favor, contacta al administrador.'])->withInput();
        } catch (\Exception $e) {
            Log::error('Error al enviar código de verificación', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['email' => 'Error al enviar el código. El código se ha guardado. Por favor, intenta de nuevo o contacta al administrador.'])->withInput();
        }
    }

    /**
     * Verificar código de verificación
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['code' => 'Usuario no encontrado.'])->withInput();
        }

        // Buscar código válido
        $userCode = UsersCode::where('user_id', $user->id)
            ->where('code', $request->code)
            ->where('type', 'email_verification')
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$userCode) {
            return back()->withErrors(['code' => 'Código inválido o expirado. Por favor, verifica el código e intenta de nuevo.'])->withInput();
        }

        // Marcar código como usado
        $userCode->update(['used' => true]);

        // Verificar usuario
        $user->update([
            'verified_at' => Carbon::now(),
            'verified' => true,
            'verification_code' => null,
        ]);

        Log::info('Usuario verificado exitosamente', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        // Si el usuario está autenticado, redirigir según su rol
        if (auth()->check() && auth()->id() === $user->id) {
            if ($user->hasRole('admin') || $user->hasRole('staff')) {
                return redirect()->route('dashboard')->with('success', '¡Correo verificado exitosamente!');
            }
            return redirect()->intended('/')->with('success', '¡Correo verificado exitosamente!');
        }

        // Si no está autenticado, redirigir a login
        return redirect()->route('login')->with('success', '¡Correo verificado exitosamente! Por favor, inicia sesión.');
    }

    /**
     * Mostrar formulario de verificación
     */
    public function showVerificationForm()
    {
        // Si el usuario está autenticado, usar su email
        $email = auth()->check() ? auth()->user()->email : old('email');
        
        return view('auth.verify-email', compact('email'));
    }
}

