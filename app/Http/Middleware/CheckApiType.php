<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApiType
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $api_type = config('cms.api_type');

        if ($request->is('api/*') && $api_type !== 'rest'){
            abort(404);
        }

        if ($request->is('graphql') && $api_type !== 'graph'){
            abort(404);
        }

        return $next($request);
    }
}
