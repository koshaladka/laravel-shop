<?php

namespace App\Providers;

use Carbon\CarbonInterval;
use http\Env\Request;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(!app()->isProduction());

//      оба метода есть в предыдущем
//        Model::preventLazyLoading(!app()->isProduction());
//        Model::preventSilentlyDiscardingAttributes(!app()->isProduction());

        RateLimiter::for('web', function (Request $request) {
            return Limit::perMinute(1)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response('Take it easy', Response::HTTP_TOO_MANY_REQUESTS, $headers);
                });
        });
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        if(app()->isProduction()) {
            DB::listen(function ($query) {
                if($query->time > 100) {
                    logger()
                        ->channel('telegram')
                        ->debug('query longer than 1s ' . $query->sql, $query->bindings);
                }
            });

            DB::whenQueryingForLongerThan( CarbonInterval::second(5), function (Connection $connection, QueryExecuted $event) {
                logger()
                    ->channel('telegram')
                    ->debug('whenQueryingForLongerThan: ' . $connection->totalQueryDuration());
            });
        }

        // request cycle
        app(Kernel::class)->whenRequestLifecycleIsLongerThan(
            CarbonInterval::second(4),
            function () {
                logger()
                    ->channel('telegram')
                    ->debug('whenRequestLifecycleIsLongerThan: ' . request()->url());
            }
        );
    }
}
