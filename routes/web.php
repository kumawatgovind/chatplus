<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PayoutController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\CCAvenueController;

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
    // https://chatplus.co.in/config/clear-cache
    // http://127.0.0.1:8000/config/clear-cache
    Route::get('/clear-cache', function () {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        return '<h1>Cache facade value cleared</h1>';
    });

    Route::get('/location', function () {
        echo __FILE__;
        echo "<pre>";
        print_r($_SERVER);
        return '<h1>google analytics</h1>';
    });

    Route::get('/storage-link', function () {
        Artisan::call('storage:link');
        return '<h1>Storage linked</h1>';
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

Route::post('/stripe-webhook', [PaymentController::class, 'stripeWebhook']);
Route::get('/check-route', [PaymentController::class, 'checkRoute']);
Route::post('/webhook/get-payout-record', [PayoutController::class, 'payoutWebhook']);
Route::get('/check-payout', [PayoutController::class, 'checkRoute']);
Route::get('/page/{slug}', [PageController::class, 'getPage'])->name('frontend.page');
Route::get('/clear-data', [CommonController::class, 'clearData'])->name('frontend.clear-data');
Route::get('ccAvenueRequest/{id}', [CCAvenueController::class, 'ccAvenueRequestHandler'])->name('ccAvenueRequest');
Route::post('ccAvenueResponseSuccess', [CCAvenueController::class, 'ccAvenueResponseHandler'])
    ->name('ccAvenueResponseSuccess');
Route::post('ccAvenueResponseCancel', [CCAvenueController::class, 'ccAvenueResponseHandler'])
    ->name('ccAvenueResponseCancel');

// Route::middleware([
//     'auth:sanctum',
//     config('jetstream.auth_session'),
//     'verified'
// ])->group(function () {
//     Route::get('/dashboard', function () {
//         return view('dashboard');
//     })->name('dashboard');
// });
