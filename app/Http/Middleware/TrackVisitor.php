<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        $alreadyVisitedToday = Visit::where('ip', $ip)
            ->whereDate('visited_at', Carbon::today())
            ->exists();

        if (! $alreadyVisitedToday) {
            Visit::create([
                'ip' => $ip,
                'visited_at' => now(),
            ]);
        }

        return $next($request);
    }
}
