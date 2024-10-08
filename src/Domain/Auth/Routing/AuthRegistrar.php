<?php

namespace Domain\Auth\Routing;

use App\Contracts\RouteRegistrar;
use App\Http\Controllers\Auth\SignInController;
use Illuminate\Contracts\Routing\Registrar;
use Illuminate\Support\Facades\Route;

class AuthRegistrar implements RouteRegistrar
{
    public function map(Registrar $registrar): void
    {
        Route::middleware('web')->group(function () {
            Route::controller(SignInController::class)->group(function () {
                Route::get('/login', 'index')->name('login');
                Route::post('/login', 'signIn')->name('signIn');

                Route::get('/sign-up', 'signUp')->name('signUp');
                Route::post('/sign-up', 'store')->name('store');

                Route::delete('/logout', 'logOut')->name('logOut');

                Route::get('/forgot-password', 'forgot')
                    ->middleware('guest')
                    ->name('password.request');
                Route::post('/forgot-password', 'forgotPassword')
                    ->middleware('guest')
                    ->name('password.email');
                Route::get('/reset-password/{token}', 'reset')
                    ->middleware('guest')
                    ->name('password.reset');
                Route::post('/reset-password', 'resetPassword')
                    ->middleware('guest')
                    ->name('password.update');

                Route::get('/auth/socialite/github', 'github')
                    ->name('socialite.github');

                Route::get('/auth/socialite/github/callback', 'githubCallback')
                    ->name('socialite.github.callback');
            });
        });
    }

}
