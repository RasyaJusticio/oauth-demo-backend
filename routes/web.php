<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'auth'], function () {
    Route::group(['prefix' => 'google'], function () {
        Route::get('redirect', [AuthController::class, 'googleRedirect']);
        Route::get('callback', [AuthController::class, 'googleCallback']);
    });
});
