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

  Route::group(['middleware' => ['api.header', 'api.authToken']], function () {

    // User and service profile route
    Route::post('/check-referral-code', [UsersController::class, 'checkReferralCode']);
    Route::post('/create-service-profile', [UsersController::class, 'createServiceProfile']);
    Route::post('/update-service-profile', [UsersController::class, 'updateServiceProfile']);
    Route::post('/get-profile', [UsersController::class, 'getProfile']);
    Route::post('/edit-profile', [UsersController::class, 'updateProfile']);
    Route::post('/get-service-profiles', [UsersController::class, 'getServiceProfiles']);
    Route::post('/fcm-update', [UsersController::class, 'updateFcmUpdate']);

    // User Sponsors  
    Route::post('/get-user-level', [UsersController::class, 'getUserLevel']);

    // Master data Route
    Route::get('/category-list', [CommonController::class, 'categoryList']);
    Route::get('/master-data', [CommonController::class, 'masterData']);
    Route::post('/contact-sync', [CommonController::class, 'contactSync']);
    Route::get('/check-limit', [CommonController::class, 'checkLimit']);

    // Service Products Route
    Route::post('/create-service-product', [ServiceProductsController::class, 'createServiceProduct']);
    Route::post('/get-service-product', [ServiceProductsController::class, 'getServiceProduct']);
    Route::post('/get-service-products', [ServiceProductsController::class, 'getServiceProducts']);
    Route::post('/delete-service-product', [ServiceProductsController::class, 'deleteServiceProduct']);
    Route::post('/service-product-status', [ServiceProductsController::class, 'serviceProductStatus']);
    Route::post('/bookmark-service-product', [ServiceProductsController::class, 'bookmarkServiceProduct']);
    Route::post('/list-bookmark-service-products', [ServiceProductsController::class, 'listBookmarkServiceProduct']);

    // Products Route
    Route::post('/create-product', [ProductsController::class, 'createProduct']);
    Route::post('/update-product', [ProductsController::class, 'updateProduct']);
    Route::post('/get-product', [ProductsController::class, 'getProduct']);
    Route::post('/get-products', [ProductsController::class, 'getProducts']);
    Route::post('/delete-product', [ProductsController::class, 'deleteProduct']);

    // Customers Route
    Route::post('/create-customer', [CustomersController::class, 'createCustomer']);
    Route::post('/update-customer', [CustomersController::class, 'updateCustomer']);
    Route::post('/get-customer', [CustomersController::class, 'getCustomer']);
    Route::post('/get-customers', [CustomersController::class, 'getCustomers']);
    Route::post('/delete-customer', [CustomersController::class, 'deleteCustomer']);


    // Subscriptions Route
    Route::post('/get-subscriptions', [SubscriptionsController::class, 'index']);
    Route::post('/user-subscribe', [SubscriptionsController::class, 'userSubscribe']);
    Route::get('/check-user-subscribe', [SubscriptionsController::class, 'checkUserSubscribe']);
    Route::post('/payment-request', [SubscriptionsController::class, 'paymentRequest']);
    Route::post('/check-payment-status', [SubscriptionsController::class, 'checkPaymentStatus']);

    // User Status Route
    Route::post('/create-user-status', [UserStatusController::class, 'createUserStatus']);
    Route::post('/update-user-status', [UserStatusController::class, 'updateUserStatus']);
    Route::post('/get-user-status', [UserStatusController::class, 'getUserStatus']);
    Route::post('/delete-user-status', [UserStatusController::class, 'deleteCustomer']);

    // Route::get('/tag-list', [PostController::class, 'tagsList']);

    // Route::post('/users/timeline', [PostController::class, 'getTimeline']);
    // Route::post('/users/user-data', [PostController::class, 'getUserData']);
    // Route::post('/users/hashtag-timeline', [PostController::class, 'getHashTagTimeline']);
    // Route::post('/post-comment', [PostController::class, 'postComment']);

    // Route::post('/users/change-password', [UsersController::class, 'changePassword'])->name('user.changePassword');
    // Route::post('/users/update-profile', [UsersController::class, 'updateProfile'])->name('user.updateProfile');
    // Route::get('/users/home-list', [UsersController::class, 'homeList'])->name('user.homeList');
    // Route::get('/users/category-list', [UsersController::class, 'categoryList'])->name('user.categoryList');
    // Route::post('/users/contact-us', [UsersController::class, 'contactUs'])->name('user.contactUs');
    // Route::post('/users/notification-list', [UsersController::class, 'notificationList'])->name('user.notificationList');
    // Route::post('/users/internal-messaging-list', [UsersController::class, 'internalMessagingList'])->name('user.internalMessagingList');
    // Route::post('/users/message-conversation-list', [UsersController::class, 'messageConversationList'])->name('user.messageConversationList');
    // Route::post('/users/message-send-text', [UsersController::class, 'sendInternalMessagingText'])->name('user.sendInternalMessagingText');
    // Route::post('/users/message-send-file', [UsersController::class, 'sendInternalMessagingFile'])->name('user.sendInternalMessagingFile');
    // Route::get('/users/logout', [UsersController::class, 'logout'])->name('user.logout');


  });
});
