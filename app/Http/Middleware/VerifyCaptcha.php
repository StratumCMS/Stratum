<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class VerifyCaptcha
{
    public function handle(Request $request, Closure $next)
    {
        if (!setting('captcha.enabled')) {
            return $next($request);
        }

        $type = setting('captcha.type');
        $secret = setting('captcha.secret_key');

        $token = match ($type) {
            'recaptcha' => $request->input('g-recaptcha-response'),
            'hcaptcha' => $request->input('h-captcha-response'),
            'turnstile' => $request->input('cf-turnstile-response'),
            default => null,
        };

        if (!$token) {
            return back()->withErrors(['captcha' => 'CAPTCHA requis.']);
        }

        $verifyUrl = match ($type) {
            'recaptcha' => 'https://www.google.com/recaptcha/api/siteverify',
            'hcaptcha' => 'https://hcaptcha.com/siteverify',
            'turnstile' => 'https://challenges.cloudflare.com/turnstile/v0/siteverify',
        };

        $response = Http::asForm()->post($verifyUrl, [
            'secret' => $secret,
            'response' => $token,
            'remoteip' => $request->ip(),
        ]);

        if (!$response->json('success')) {
            return back()->withErrors(['captcha' => 'Échec de la vérification CAPTCHA.']);
        }

        return $next($request);
    }
}
