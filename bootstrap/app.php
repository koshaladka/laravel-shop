<?php

use App\Contracts\RouteRegistrar;
use App\Routing\AppRegistrar;
use Domain\Auth\Routing\AuthRegistrar;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


//$registrars = [
//    AppRegistrar::class,
//    AuthRegistrar::class,
//];
//function mapRoutes(Registrar $router, array $registrars): void
//{
//    foreach ($registrars as $registrar) {
//        if(! class_exists($registrar) || ! is_subclass_of($registrar, RouteRegistrar::class))                                                                                    {
//            throw new RuntimeException(sprintf(
//                'Cannot map routes \'%s\', it is not a valid route class.',
//                $registrar
//            ));
//        }
//
//        (new $registrar)->map($router);
//    }
//}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
//        then: function (Registrar $router ) use ($registrars) {
//            mapRoutes($router, $registrars);
//        }
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
                ->view('index');
                //->json([]);
        });
    })->create();


