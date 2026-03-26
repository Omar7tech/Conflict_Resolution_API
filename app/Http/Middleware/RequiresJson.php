<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class RequiresJson
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->wantsJson()) {
            throw new NotAcceptableHttpException(
                'Please request with HTTP header: Accept: application/json'
            );
        }
        return $next($request);
    }
}
