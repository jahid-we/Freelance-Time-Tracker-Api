<?php

use App\Http\Controllers\Authentication\AuthController;
use App\Http\Controllers\Clients\ClientController;
use App\Http\Controllers\Projects\ProjectController;
use App\Http\Controllers\TimeLogs\TimeLogController;
use Illuminate\Support\Facades\Route;

// ==================================================
// API Route Definitions for Freelance Time Tracker
// Uses Laravel Sanctum for API token authentication
// ==================================================


// =====================================================
// =============== Authentication Routes ===============
// =====================================================
Route::middleware('guest')->controller(AuthController::class)->group(function () {

    Route::post('/register', 'register')->name('register');
    Route::post('/login', 'login')->name('login');

});


// =====================================================
// =============== Protected Auth Routes ===============
// =====================================================
Route::middleware('auth:sanctum')->controller(AuthController::class)->group(function () {

    Route::post('/logout', 'logout')->name('logout');

});


// =====================================================
// =============== Client Routes =======================
// =====================================================
Route::middleware('auth:sanctum')->controller(ClientController::class)->group(function () {

    Route::post('/create-client', 'createClient')->name('create-client');
    Route::get('/get-clients', 'getClients')->name('get-clients');
    Route::get('/get-client/{id}', 'getClient')->name('get-client');
    Route::post('/update-client/{id}', 'updateClient')->name('update-client');
    Route::delete('/delete-client/{id}', 'deleteClient')->name('delete-client');
    Route::delete('/delete-all-clients', 'deleteAllClients')->name('delete-all-clients');

});


// =====================================================
// =============== Project Routes ======================
// =====================================================
Route::middleware('auth:sanctum')->controller(ProjectController::class)->group(function () {

    Route::post('/create-project', 'createProject')->name('create-project');
    Route::get('/get-all-projects', 'getAllProjects')->name('get-all-projects');
    Route::get('/get-project/{id}', 'getProject')->name('get-project');
    Route::post('/update-project/{id}', 'updateProject')->name('update-project');
    Route::delete('/delete-project/{id}', 'deleteProject')->name('delete-project');
    Route::delete('/delete-all-projects', 'deleteAllProjects')->name('delete-all-projects');
    Route::get('/get-projects-by-client/{clientId}', 'getProjectsByClient')->name('get-projects-by-client');

});


// =====================================================
// =============== Time Log Related Routes =============
// =====================================================
Route::middleware('auth:sanctum')->controller(TimeLogController::class)->group(function () {

    Route::post('/start-timelog/{projectId}', 'start')->name('start-timelog');
    Route::post('/end-timelog/{projectId}', 'end')->name('end-timelog');

    Route::post('/manual-entry/{projectId}', 'manualEntry')->name('manual-entry');

    Route::get('/get-timelogs', 'getTimeLogs')->name('get-timelogs');
    Route::get('/get-timelog/{id}', 'getTimeLogById')->name('get-timelog');
    Route::post('/update-timelog/{id}', 'update')->name('update-timelog');
    Route::delete('/delete-timelog/{id}', 'delete')->name('delete-timelog');
    Route::delete('/delete-all-timelogs', 'deleteAll')->name('delete-all-timelogs');

    // Report Route (search or filter time logs)
    Route::get('/report', 'search')->name('timelog.search');

    // Export PDF Route (generates a downloadable report)
    Route::get('/export-pdf', 'exportTimeLogs')->name('export-pdf');

});
