<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Ensure2FAIsVerified
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->two_factor_enabled) {
            return $next($request);
        }

        if ($request->session()->get('2fa_verified') === true) {
            return $next($request);
        }

        if (
            $request->routeIs('2fa.challenge') ||
            $request->routeIs('2fa.verify.challenge') ||
            $request->routeIs('logout')
        ) {
            return $next($request);
        }

        return redirect()->route('2fa.challenge');
    }
}
