<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{

    public function handle($request, Closure $next){
        if (file_exists(storage_path('installed'))) {
            if ($request->is('install*')) {
                return redirect('/');
            }
        } else {
            if (!$request->is('install*')) {
                return redirect('/install');
            }
        }

        return $next($request);
    }
}
