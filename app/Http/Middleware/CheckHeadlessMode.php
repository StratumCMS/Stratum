<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckHeadlessMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (env('CMS_MODE') === 'headless') {
            if (! $request->is('admin*') && ! $request->is('api*') && ! $request->is('graphql*')) {
                abort(404);
            }
        }

        return $next($request);
    }
}
