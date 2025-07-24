<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthenticatedUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use PragmaRX\Google2FA\Google2FA;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);
    
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Les informations de connexion sont invalides.'],
            ]);
        }
    
        $user = Auth::user();

        if($user->two_factor_enabled && !$request->has('code')) {
            return response()->json([
                'message' => '2FA est activé.',
            ], 401);
        }

        if (!$user || ($user->two_factor_enabled &&!$user->google2fa_secret)) {
            throw ValidationException::withMessages([
                'email' => ['Utilisateur ou 2FA non configuré.'],
            ]);
        }

        $code = $request->input('code');

        if($code) {
            try {
                $secret = Crypt::decrypt($user->google2fa_secret);
            } catch (\Exception $e) {
                throw ValidationException::withMessages([
                    'code' => ['Erreur de déchiffrement de la clé 2FA.'],
                ]);
            }
    
            $google2fa = new Google2FA();
    
            if (!$google2fa->verifyKey($secret, $code)) {
                throw ValidationException::withMessages([
                    'code' => ['Code incorrect. Veuillez réessayer.'],
                ]);
            }
        }

        $token = $user->createToken('auth_token')->plainTextToken;
    
        return (new AuthenticatedUser($user))
            ->additional([
                'access_token' => $token,
                'token_type'   => 'Bearer'
            ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Déconnexion réussie.',
        ]);
    }

    public function verify(Request $request)
    {
        return new AuthenticatedUser($request->user());
    }
}
