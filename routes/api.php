<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Clients\ClientController;
use Illuminate\Support\Facades\Route;

// Route::controller()->group(function () {});

Route::middleware('guest')->controller(AuthController::class)->group(function () {

    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');

});

Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {

    Route::post('/logout', 'logout')->name('logout');

});

Route::middleware('auth:sanctum')->controller(ClientController::class)->group(function () {

    Route::post('/create-client', 'createClient')->name('create-client');
    Route::get('/get-clients', 'getClients')->name('get-clients');
    Route::get('/get-client/{id}', 'getClient')->name('get-client');
    Route::post('/update-client/{id}', 'updateClient')->name('update-client');
    Route::get('/delete-client/{id}', 'deleteClient')->name('delete-client');
    Route::get('/delete-all-clients', 'deleteAllClients')->name('delete-all-clients');


});
