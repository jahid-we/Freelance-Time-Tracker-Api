<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Authentication\AuthController;


Route::middleware('guest')->controller(AuthController::class)->group(function () {

        Route::post('/register', 'register')->name('register');
        Route::post('/login', 'login')->name('login');

    });

Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {

        Route::post('/logout', 'logout')->name('logout');

    });

    Route::controller()->group(function (){});

