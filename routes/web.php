<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::prefix('config')->group(function () {
    Route::get('/phpinfo', function () {
        phpinfo();
        die;
    });

    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        return '<h1>Cache facade value cleared</h1>';
    });

    Route::get('/location', function () {
        echo __FILE__;
        echo "<pre>";print_r($_SERVER);
        return '<h1>google analytics</h1>';
    });

    Route::get('/run-migration', function () {
        Artisan::call('migrate');
        return '<h1>Migrations Completed</h1>';
    });

    Route::get('/run-migration-fresh', function () {
        Artisan::call('migrate:refresh --seed');
        return '<h1>Fresh Migrations Completed</h1>';
    });
    Route::get('/run-only-seed', function () {
        Artisan::call('db:seed');
        return '<h1>Seeding Completed</h1>';
    });
});
Route::get('/', function () {
    return view('home');
});

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified'
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });
