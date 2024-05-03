<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'battleship-ia',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('battleship-ia/*')) {
                return response()->json([
                    'message' => trans('errors.401')
                ], 401);
            }

            return $request->expectsJson();
        });

        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('battleship-ia/*')) {

                return response()->json([
                    'message' => trans('errors.403')
                ], 403);
            }

            return $request->expectsJson();
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('battleship-ia/*')) {
                return response()->json([
                    'message' => trans('errors.404'),
                ], 404);
            }

            return $request->expectsJson();
        });
    })->create();
