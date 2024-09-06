<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

//тест отправки уведомлений в Telegram
//Route::get('/telegram', function () {
//        logger()
//            ->channel('telegram')
//            ->info('test');
//});

Route::controller(SignInController::class)->group(function () {
    Route::get('/login', 'page')->name('login');
    Route::post('/login', 'handle')->name('signIn');
    Route::delete('/logout', 'logOut')->name('logOut');
});

Route::controller(SignUpController::class)->group(function () {
    Route::get('/sign-up', 'page')->name('signUp');
    Route::post('/sign-up', 'handle')->name('signUp.store');
});

Route::group(['middleware' => 'guest'], function () {

    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::get('/forgot-password', 'page')
            ->name('password.request');
        Route::post('/forgot-password', 'handle')
            ->name('password.email');
    });

    Route::controller(ResetPasswordController::class)->group(function () {
        Route::get('/reset-password/{token}', 'page')
            ->name('password.reset');
        Route::post('/reset-password', 'handle')
            ->name('password.update');
    });

});


Route::controller(SocialAuthController::class)->group(function () {
    Route::get('/auth/socialite/github', 'github')
        ->name('socialite.github');

    Route::get('/auth/socialite/github/callback', 'githubCallback')
        ->name('socialite.github.callback');
});




Route::get('/', HomeController::class)->name('home');





