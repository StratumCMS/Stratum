<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InjectCustomAssets
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            str_contains($response->headers->get('Content-Type'), 'text/html') && $response->getContent()
        ){
            $content = $response->getContent();

            $inject = <<<HTML
<link rel="stylesheet" href="/custom/custom.css">
<script src="/custom/custom.js" defer></script>
HTML;
            $content = str_ireplace('</head>', $inject . "\n</head>", $content);
            $response->setContent($content);
        }

        return $response;
    }
}
