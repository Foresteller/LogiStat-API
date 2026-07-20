<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(
            function (ValidationException $e, Request $request) {
                if ($request->is('api/*')) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'The given data was invalid',
                        'errors' => $e->errors(),
                    ], 422);
                }
            }
        );

        $exceptions->render(
            function (NotFoundHttpException $e, Request $request) {
                if ($request->is('api/*')) {
                    return new JsonResponse([
                        'success' => false,
                        'message' => 'Resource or endpoint not found',
                    ], 404);
                }
            }
        );

        $exceptions->render(
            function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $isDebug = config('app.debug');
                return new JsonResponse([
                    'success' => false,
                    'message' => $isDebug ? $e->getMessage()
                        : 'Internal Server Error',
                    'trace' => $isDebug ? collect($e->getTrace())->take(5) : null
                ], 500);
            }
        });
    })->create();



















