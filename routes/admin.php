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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');


Route::middleware(['prevent.history'])->as('admin.')->group(function () {

    Route::get('/', [App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::get('/login', [App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::class, 'create'])->name('login');

    Route::get('/register', [App\Http\Controllers\Admin\Auth\PasswordResetLinkController::class, 'create'])->name('register');

    Route::post('/register', [App\Http\Controllers\Admin\Auth\PasswordResetLinkController::class, 'store']);

    Route::post('/login', [App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::class, 'store']);

    Route::get('/forgot-password', [App\Http\Controllers\Admin\Auth\PasswordResetLinkController::class, 'create'])->name('password.request');

    Route::post('/forgot-password', [App\Http\Controllers\Admin\Auth\PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [App\Http\Controllers\Admin\Auth\NewPasswordController::class, 'create'])->name('password.reset');

    Route::post('/reset-password', [App\Http\Controllers\Admin\Auth\NewPasswordController::class, 'store'])->name('password.update');

    Route::post('/create-password', [App\Http\Controllers\Admin\Auth\CreatePasswordController::class, 'store'])->name('password.newcreate');

    Route::get('/password/create/{token}', [App\Http\Controllers\Admin\Auth\CreatePasswordController::class, 'create'])->name('password.create');

    Route::get('/verify-email', [App\Http\Controllers\Admin\Auth\EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');

    Route::get('/verify-email/{id}/{hash}', [App\Http\Controllers\Admin\Auth\VerifyEmailController::class, '__invoke'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
});

Route::middleware(['auth:admin', 'prevent.history', 'AdminAuth'])->as('admin.')->group(
    function () {

        Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])
            ->middleware('verified:admin.verification.notice')->name('dashboard');

        Route::get('/profile', [App\Http\Controllers\Admin\AdminUserController::class, 'profile'])
            ->middleware('verified:admin.verification.notice')->name('profile');

        Route::post('/updateprofile', [App\Http\Controllers\Admin\AdminUserController::class, 'updateprofile'])
            ->middleware('verified:admin.verification.notice')->name('updateprofile');

        Route::get('/change-password', [App\Http\Controllers\Admin\ChangePasswordController::class, 'index'])
            ->name('change-password.index');

        Route::post('/logout', [App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::get('/logout', [App\Http\Controllers\Admin\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::resources([
            'admin-users' => App\Http\Controllers\Admin\AdminUserController::class,
            'settings' => App\Http\Controllers\Admin\SettingController::class,
            'pages' => App\Http\Controllers\Admin\PagesController::class,
            'users' => App\Http\Controllers\Admin\UsersController::class,
            'posts' => App\Http\Controllers\Admin\PostController::class,
            'tags' => App\Http\Controllers\Admin\TagsController::class,
            'admin-roles' => App\Http\Controllers\Admin\RoleController::class,
            'hooks' => App\Http\Controllers\Admin\EmailHooksController::class,
            'email-preferences' => App\Http\Controllers\Admin\EmailPreferencesController::class,
            'email-templates' => App\Http\Controllers\Admin\EmailTemplatesController::class,
            'categories' => App\Http\Controllers\Admin\CategoriesController::class,
            'locations' => App\Http\Controllers\Admin\AreasController::class,
            'areas' => App\Http\Controllers\Admin\AreasController::class,
        ]);

        Route::get('/blocked', [App\Http\Controllers\Admin\UsersController::class, 'blockedUsers'])->name('users.blocks');
        Route::get('/reported', [App\Http\Controllers\Admin\UsersController::class, 'reportedUsers'])->name('users.reported');
        Route::get('/report-detail/{user}', [App\Http\Controllers\Admin\UsersController::class, 'reportDetail'])->name('users.reportDetail');

        Route::post('posts/image/{id}', [App\Http\Controllers\Admin\PostController::class, 'imageUpdate'])->name('posts.image');
        Route::post('posts/upload-image', [App\Http\Controllers\Admin\PostController::class, 'uploadImage'])->name('posts.uploadImage');
        Route::delete('posts/delete-attachment/{id}', [App\Http\Controllers\Admin\PostController::class, 'deleteAttachment'])->name('posts.deleteAttachment');
    }
);
