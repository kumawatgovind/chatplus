<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\CreatePasswordController;
use App\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ChangePasswordController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\EmailHooksController;
use App\Http\Controllers\Admin\EmailPreferencesController;
use App\Http\Controllers\Admin\EmailTemplatesController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\PayoutController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\KycController;
use App\Http\Controllers\Admin\PersonalDataController;
use App\Http\Controllers\Admin\ServiceProfileController;
use App\Http\Controllers\Admin\ServiceProductController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\StatesController;
use App\Http\Controllers\Admin\CitiesController;
use App\Http\Controllers\Admin\LocalitiesController;
use App\Http\Controllers\Admin\MarketingsController;


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

    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

    Route::get('/register', [PasswordResetLinkController::class, 'create'])->name('register');

    Route::post('/register', [PasswordResetLinkController::class, 'store']);

    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');

    Route::post('/create-password', [CreatePasswordController::class, 'store'])->name('password.newcreate');

    Route::get('/password/create/{token}', [CreatePasswordController::class, 'create'])->name('password.create');

    Route::get('/verify-email', [EmailVerificationPromptController::class, '__invoke'])->name('verification.notice');

    Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
});

Route::middleware(['auth:admin', 'prevent.history', 'AdminAuth'])->as('admin.')->group(
    function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])
            ->middleware('verified:admin.verification.notice')->name('dashboard');

        Route::get('/profile', [AdminUserController::class, 'profile'])
            ->middleware('verified:admin.verification.notice')->name('profile');

        Route::post('/updateprofile', [AdminUserController::class, 'updateProfile'])
            ->middleware('verified:admin.verification.notice')->name('updateProfile');

        Route::get('/change-password', [ChangePasswordController::class, 'index'])
            ->name('change-password.index');

        Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::get('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::resources([
            'admin-users' => AdminUserController::class,
            'settings' => SettingController::class,
            'pages' => PagesController::class,
            'users' => UsersController::class,
            'admin-roles' => RoleController::class,
            'hooks' => EmailHooksController::class,
            'email-preferences' => EmailPreferencesController::class,
            'email-templates' => EmailTemplatesController::class,
            'categories' => CategoriesController::class,
            'contacts' => ContactController::class,
            'states' => StatesController::class,
            'cities' => CitiesController::class,
            'localities' => LocalitiesController::class,
            'marketings' => MarketingsController::class,
        ]);
        
        Route::get('/blocked', [UsersController::class, 'blockedUsers'])->name('users.blocks');
        Route::get('/reported', [UsersController::class, 'reportedUsers'])->name('users.reported');
        Route::get('/report-detail/{user}', [UsersController::class, 'reportDetail'])->name('users.reportDetail');

        Route::get('/payout-total', [PayoutController::class, 'payoutTotal'])->name('payout.total');

        Route::get('/pending-renewal', [SubscriptionController::class, 'getPendingRenewal'])->name('getPendingRenewal');
        Route::get('/not-prime', [SubscriptionController::class, 'getNotPrime'])->name('getNotPrime');
        Route::get('/total-prime', [SubscriptionController::class, 'getTotalPrime'])->name('getTotalPrime');

        Route::get('/pending-kyc', [KycController::class, 'getPendingKyc'])->name('getPendingKyc');
        Route::get('/mark-re-kyc', [KycController::class, 'getMarkReKyc'])->name('getMarkReKyc');
        Route::get('/total-kyc', [KycController::class, 'getTotalKyc'])->name('getTotalKyc');
        Route::get('/single-kyc/{kycId}', [KycController::class, 'getSingleKyc'])->name('getSingleKyc');
        Route::post('/update-kyc', [KycController::class, 'updateKyc'])->name('updateKyc');

        Route::get('/contact-list', [PersonalDataController::class, 'getContactList'])->name('getContactList');
        Route::get('/saved-products', [PersonalDataController::class, 'getSavedProducts'])->name('getSavedProducts');
        Route::get('/saved-customers', [PersonalDataController::class, 'getSavedCustomers'])->name('getSavedCustomers');

        Route::get('/business-listing', [ServiceProfileController::class, 'businessListing'])->name('businessListing');
        Route::get('/blocked-spam', [ServiceProfileController::class, 'blockedSpam'])->name('blockedSpam');
        Route::get('/running-listing', [ServiceProfileController::class, 'runningListing'])->name('runningListing');

        Route::get('/total-service', [ServiceProductController::class, 'getTotalService'])->name('getTotalService');
        Route::get('/deleted-service', [ServiceProductController::class, 'getDeletedService'])->name('getDeletedService');
        Route::get('/service-product-delete/{id}', [ServiceProductController::class, 'serviceProductDelete'])->name('serviceProductDelete');
        Route::get('/service-product-show/{id}', [ServiceProductController::class, 'serviceProductShow'])->name('serviceProductShow');

        Route::get('contact-edit/{id}', [ContactController::class, 'contactEdit'])->name('contacts.contactEdit');
        Route::patch('contact-update/{id}', [ContactController::class, 'contactReply'])->name('contacts.contactUpdate');
        Route::delete('contact/delete/{id}', [ContactController::class, 'deleteContact'])->name('contacts.delete');
        
        Route::get('state-import', [StatesController::class, 'importView'])->name('states.import');
        Route::post('state-import', [StatesController::class, 'import'])->name('states.import');
        Route::get('city-import', [CitiesController::class, 'importView'])->name('cities.import');
        Route::post('city-import', [CitiesController::class, 'import'])->name('cities.import');
        Route::get('city-state/{stateId?}', [CitiesController::class, 'getCityByStateId'])->name('cities.cityState');
        Route::get('locality-import', [LocalitiesController::class, 'importView'])->name('localities.import');
        Route::post('locality-import', [LocalitiesController::class, 'import'])->name('localities.import');
        Route::get('category-import', [CategoriesController::class, 'importView'])->name('categories.import');
        Route::post('category-import', [CategoriesController::class, 'import'])->name('categories.import');
    }
);
