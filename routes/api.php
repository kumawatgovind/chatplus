<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\UsersController;
use App\Http\Controllers\Api\V1\CommonController;
use App\Http\Controllers\Api\V1\ServiceProductsController;
use App\Http\Controllers\Api\V1\ProductsController;
use App\Http\Controllers\Api\V1\SubscriptionsController;
use App\Http\Controllers\Api\V1\CustomersController;
use App\Http\Controllers\Api\V1\UserStatusController;
use App\Http\Controllers\Api\V1\KycController;
use App\Http\Controllers\Api\V1\AddressesController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['namespace' => 'Api\V1', 'as' => 'api.', 'middleware' => ['api.header', 'cors']], function () {
  // User register and check
  Route::post('/check-phone-number', [UsersController::class, 'checkPhoneNumber']);
  Route::post('/check-username', [UsersController::class, 'checkUsername']);
  Route::post('/register', [UsersController::class, 'register']);
  
  
  // File Upload
  Route::post('/upload-document', [CommonController::class, 'uploadDocument']);
  Route::post('/check-notification', [CommonController::class, 'checkNotification']);


  Route::group(['middleware' => ['api.header', 'api.authToken']], function () {

    // User and service profile route
    Route::post('/check-referral-code', [UsersController::class, 'checkReferralCode']);
    Route::post('/create-service-profile', [UsersController::class, 'createServiceProfile']);
    Route::post('/update-service-profile', [UsersController::class, 'updateServiceProfile']);
    Route::post('/get-profile', [UsersController::class, 'getProfile']);
    Route::post('/edit-profile', [UsersController::class, 'updateProfile']);
    Route::post('/get-service-profiles', [UsersController::class, 'getServiceProfiles']);
    Route::post('/fcm-update', [UsersController::class, 'updateFcmUpdate']);
    Route::post('/spam-reported', [UsersController::class, 'spamReported']);
    Route::post('/logout', [UsersController::class, 'logout']);

    // User Sponsors  
    Route::post('/get-user-level', [UsersController::class, 'getUserLevel']);
    Route::get('/my-referrals', [UsersController::class, 'myReferrals']);
    Route::get('/sponsors-history', [UsersController::class, 'sponsorsHistory']);
    Route::get('/transaction-history', [UsersController::class, 'transactionHistory']);

    // Master data Routes
    Route::get('/category-list', [CommonController::class, 'categoryList']);
    Route::get('/master-data', [CommonController::class, 'masterData']);
    Route::post('/contact-sync', [CommonController::class, 'contactSync']);
    Route::get('/check-limit', [CommonController::class, 'checkLimit']);
    Route::post('/create-contact', [CommonController::class, 'createContact']);
    Route::post('/get-state', [CommonController::class, 'getState']);
    Route::post('/get-district', [CommonController::class, 'getDistrict']);
    Route::post('/get-locality', [CommonController::class, 'getLocality']);
    Route::post('/add-recent-search', [CommonController::class, 'addRecentSearch']);
    Route::post('/get-recent-search', [CommonController::class, 'getRecentSearch']);
    Route::get('/page-list', [CommonController::class, 'pageList']);
    Route::get('/page/{slug}', [CommonController::class, 'getPage']);
    
    // Kyc Routes
    Route::post('/add-kyc', [KycController::class, 'addKyc']);
    Route::get('/get-kyc', [KycController::class, 'getKyc']);
    Route::post('/update-kyc', [KycController::class, 'updateKyc']);
    Route::post('/get-kyc-list', [KycController::class, 'getKycList']);

    // Service Products Routes
    Route::post('/create-service-product', [ServiceProductsController::class, 'createServiceProduct']);
    Route::post('/update-service-product', [ServiceProductsController::class, 'updateServiceProduct']);
    Route::post('/get-service-product', [ServiceProductsController::class, 'getServiceProduct']);
    Route::post('/get-service-products', [ServiceProductsController::class, 'getServiceProducts']);
    Route::post('/delete-service-product', [ServiceProductsController::class, 'deleteServiceProduct']);
    Route::post('/service-product-status', [ServiceProductsController::class, 'serviceProductStatus']);
    Route::post('/bookmark-service-product', [ServiceProductsController::class, 'bookmarkServiceProduct']);
    Route::post('/list-bookmark-service-products', [ServiceProductsController::class, 'listBookmarkServiceProduct']);

    // Products Routes
    Route::post('/create-product', [ProductsController::class, 'createProduct']);
    Route::post('/update-product', [ProductsController::class, 'updateProduct']);
    Route::post('/get-product', [ProductsController::class, 'getProduct']);
    Route::post('/get-products', [ProductsController::class, 'getProducts']);
    Route::post('/delete-product', [ProductsController::class, 'deleteProduct']);

    // Customers Routes
    Route::post('/create-customer', [CustomersController::class, 'createCustomer']);
    Route::post('/update-customer', [CustomersController::class, 'updateCustomer']);
    Route::post('/get-customer', [CustomersController::class, 'getCustomer']);
    Route::post('/get-customers', [CustomersController::class, 'getCustomers']);
    Route::post('/delete-customer', [CustomersController::class, 'deleteCustomer']);

    // Addresses Routes
    Route::post('/create-address', [AddressesController::class, 'createAddress']);
    Route::post('/update-address', [AddressesController::class, 'updateAddress']);
    Route::post('/get-address', [AddressesController::class, 'getAddress']);
    Route::post('/get-addresses', [AddressesController::class, 'getAddresses']);
    Route::post('/delete-address', [AddressesController::class, 'deleteAddress']);

    // Subscriptions Routes
    Route::post('/get-subscriptions', [SubscriptionsController::class, 'index']);
    Route::post('/user-subscribe', [SubscriptionsController::class, 'userSubscribe']);
    Route::get('/check-user-subscribe', [SubscriptionsController::class, 'checkUserSubscribe']);
    Route::post('/payment-request', [SubscriptionsController::class, 'paymentRequest']);
    Route::post('/check-payment-status', [SubscriptionsController::class, 'checkPaymentStatus']);
    
    // Payout Route
    Route::post('/payout-request', [SubscriptionsController::class, 'payoutRequest']);
    Route::get('/check-payout', [SubscriptionsController::class, 'checkPayout']);

    // User Status Routes
    Route::post('/create-user-status', [UserStatusController::class, 'createUserStatus']);
    Route::post('/update-user-status', [UserStatusController::class, 'updateUserStatus']);
    Route::post('/get-user-status', [UserStatusController::class, 'getUserStatus']);
    Route::post('/delete-user-status', [UserStatusController::class, 'deleteCustomer']);
    
  });
});
