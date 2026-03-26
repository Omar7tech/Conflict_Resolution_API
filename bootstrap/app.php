<?php

use App\Exceptions\ConflictDetectedException;
use App\Http\Middleware\ForceJsonResponse;
use App\Http\Middleware\RequiresJson;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append([
            ForceJsonResponse::class,
            RequiresJson::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            return $request->expectsJson();
        });

        $exceptions->render(function (ConflictDetectedException $e, Request $request) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'current_version' => $e->getCurrentVersion(),
                'your_version' => $e->getYourVersion(),
                'details' => $e->getDiff(),
            ], 409);
        });
    })->create();
