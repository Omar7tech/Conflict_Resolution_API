<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonResponse
{
    /**
     * Ensure the incoming request has JSON headers before delegating to the next handler.
     *
     * @param \Closure $next The next middleware/controller; expected signature: function(Request): Response.
     * @return Response The HTTP response produced by the next middleware/controller.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');
        $request->headers->set('Content-Type', 'application/json');
        return $next($request);
    }
}
