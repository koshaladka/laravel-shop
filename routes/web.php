<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
//    logger()
//        ->channel('telegram')
//        ->info('test');

    return view('welcome');
});
