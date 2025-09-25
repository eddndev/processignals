<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Para registrar errores
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception; // Para capturar excepciones

class GoogleAuthController extends Controller
{
    /**
     * Redirige al usuario a la página de autenticación de Google.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtiene la información del usuario de Google y maneja el login/registro.
     */
    public function callback()
    {
        try {
            // 1. Obtiene el usuario de Google
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate(
                [
                    'email' => $googleUser->getEmail(), // Ahora se guardará
                ],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(24)),
                ]
            );

            // 3. Inicia sesión con el usuario encontrado o creado
            Auth::login($user, remember: true);

            // 4. Redirige al usuario a su dashboard
            return redirect()->intended(route('home', absolute: false));

        } catch (Exception $e) {
            // Si algo sale mal (ej. el usuario deniega el acceso),
            // se registra el error y se redirige al login con un mensaje.
            Log::error('Error en la autenticación con Google: ' . $e->getMessage());

            return redirect('/login')->with('error', 'No se pudo autenticar con Google. Por favor, inténtelo de nuevo.');
        }
    }
}