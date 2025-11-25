<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UsersCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Si el usuario no está verificado, redirigir a verificación
            if (!$user->verified_at) {
                return redirect()->route('verify.email')
                    ->with('warning', 'Por favor, verifica tu correo electrónico para continuar.');
            }
            
            // Redirigir a la URL previa o al dashboard si es admin, o a eventos si es usuario normal
            if ($user->hasRole('admin') || $user->hasRole('staff')) {
                return redirect()->intended(route('dashboard'));
            }
            
            return redirect()->intended('/');
        }

        throw ValidationException::withMessages([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ]);

        $user = User::create([
            'name' => $request->name,
            'last_name' => $request->last_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'viewer',
            'verified' => false,
            'verified_at' => null,
        ]);

        // Enviar código de verificación
        $this->sendVerificationCodeToUser($user);

        Auth::login($user);

        return redirect()->route('verify.email')->with('success', '¡Cuenta creada exitosamente! Por favor, verifica tu correo electrónico.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('events.public');
    }

    /**
     * Enviar código de verificación a un usuario
     */
    private function sendVerificationCodeToUser(User $user)
    {
        // Generar código de 6 dígitos
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Invalidar códigos anteriores del mismo tipo
        UsersCode::where('user_id', $user->id)
            ->where('type', 'email_verification')
            ->where('used', false)
            ->update(['used' => true]);

        // Crear nuevo código (siempre se guarda, incluso si falla el correo)
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

        // Intentar enviar correo con el código
        try {
            // Validar configuración de correo antes de intentar enviar
            $mailHost = config('mail.mailers.smtp.host');
            $mailPort = config('mail.mailers.smtp.port');
            
            if (empty($mailHost) || empty($mailPort)) {
                Log::warning('Configuración de correo incompleta', [
                    'user_id' => $user->id,
                    'mail_host' => $mailHost,
                    'mail_port' => $mailPort
                ]);
                // No lanzar excepción, solo loguear
                return;
            }

            Mail::send('emails.verification-code', [
                'user' => $user,
                'code' => $code,
            ], function ($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Código de verificación de email')
                    // El from se toma de config/mail.php (debe ser del dominio SMTP)
                    // replyTo permite que las respuestas vayan a otro correo si es necesario
                    ->replyTo(config('mail.from.address'), config('mail.from.name'));
            });

            Log::info('Código de verificación enviado exitosamente', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

        } catch (\Swift_TransportException $e) {
            // Error de conexión SMTP
            Log::error('Error de conexión SMTP al enviar código de verificación', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'suggestion' => 'Verifica la configuración SMTP en .env (MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD). Para Zoho usa puerto 587 (TLS) o 465 (SSL).'
            ]);
            // No lanzar excepción para no interrumpir el registro
        } catch (\Exception $e) {
            Log::error('Error al enviar código de verificación', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            // No lanzar excepción para no interrumpir el registro
        }
    }
}
