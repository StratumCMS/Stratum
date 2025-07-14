<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\User;
use PragmaRX\Google2FAQRCode\Google2FA;

class TwoFactorChallengeController extends Controller
{
    public function show()
    {
        return view('auth.2fa');
    }

    public function store(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $user = $request->user();

        if (!$user || !$user->google2fa_secret) {
            return redirect()->route('login')->withErrors(['otp' => 'Utilisateur ou 2FA non configurée.']);
        }

        try {
            $secret = Crypt::decrypt($user->google2fa_secret);
        } catch (\Exception $e) {
            return back()->withErrors(['otp' => 'Erreur de déchiffrement de la clé 2FA.']);
        }

        $google2fa = new Google2FA();

        if (!$google2fa->verifyKey($secret, $request->input('otp'))) {
            return back()->withErrors(['otp' => 'Code incorrect. Veuillez réessayer.']);
        }

        $request->session()->put('2fa_verified', true);

        return redirect()->intended(route('profile'));
    }
}
