<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceJsonErrors
{
    /**
     * Ensure exceptions are returned as JSON error responses while forwarding successful requests.
     *
     * Forwards the incoming request to the next middleware/handler. If a Throwable is thrown,
     * returns a JSON response with keys `error` (exception message) and `type` (exception class),
     * using the exception's `getStatusCode()` when available or `500` otherwise.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request.
     * @param Closure(Request): (Response) $next The next middleware or request handler.
     * @return \Illuminate\Http\Response The downstream response, or a JSON error response as described above.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'type' => get_class($e),
            ], method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500);
        }
    }
}
