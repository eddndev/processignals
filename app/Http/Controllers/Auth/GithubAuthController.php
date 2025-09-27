<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GithubAuthController extends Controller
{
    /**
     * Redirige al usuario a la página de autenticación de GitHub.
     */
    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    /**
     * Obtiene la información del usuario de GitHub y maneja el login/registro.
     */
    public function callback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();

            $user = User::updateOrCreate(
                [
                    'email' => $githubUser->getEmail(),
                ],
                [
                    'name' => $githubUser->getName() ?? $githubUser->getNickname(),
                    'github_id' => $githubUser->getId(),
                    'github_token' => $githubUser->token,
                    'github_refresh_token' => $githubUser->refreshToken,
                    'email_verified_at' => now(),
                    'password' => Hash::make(Str::random(24)),
                ]
            );

            Auth::login($user, remember: true);

            return redirect()->intended(route('dashboard', absolute: false));

        } catch (Exception $e) {
            Log::error('Error en la autenticación con GitHub', ['exception' => $e]);

            return redirect('/login')->with('error', 'No se pudo autenticar con GitHub. Por favor, inténtelo de nuevo.');
        }
    }
}
