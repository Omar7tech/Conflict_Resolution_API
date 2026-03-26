<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

class RequiresJson
{
    /**
     * Require that the incoming request accepts JSON; otherwise abort the request.
     *
     * @param Request $request The incoming HTTP request.
     * @param Closure(Request): Response $next The next middleware or controller to handle the request.
     * @return Response The response produced by the next request handler.
     * @throws NotAcceptableHttpException If the request does not indicate it accepts JSON.
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
