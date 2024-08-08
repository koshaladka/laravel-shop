<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {

//        for Sentry
//        Integration::handles($exceptions);

//        $exceptions->reportable(function (Throwable $e) {
//            if (app()->bound('sentry')) {
//                app('sentry')->captureException($e);
//            }
//        });

        $exceptions->renderable(function (NotFoundHttpException $e) {
            return response()
                ->view('welcome');
                //->json([]);
        });
    })->create();
