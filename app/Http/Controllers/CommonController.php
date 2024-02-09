<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CommonController extends Controller
{

    /**
     * clearData.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return [].
     */
    public function clearData()
    {
        try {
            DB::select('TRUNCATE TABLE `addresses`;');
            DB::select('TRUNCATE TABLE `contacts`;');
            DB::select('TRUNCATE TABLE `contact_sync`;');
            DB::select('TRUNCATE TABLE `customers`;');
            DB::select('TRUNCATE TABLE `friends`;');
            DB::select('TRUNCATE TABLE `kyc_document`;');
            DB::select('TRUNCATE TABLE `marketings`;');
            DB::select('TRUNCATE TABLE `payment_logs`;');
            DB::select('TRUNCATE TABLE `products`;');
            DB::select('TRUNCATE TABLE `product_images`;');
            DB::select('TRUNCATE TABLE `property_attributes`;');
            DB::select('TRUNCATE TABLE `razor_pay_contacts`;');
            DB::select('TRUNCATE TABLE `razor_pay_fund_accounts`;');
            DB::select('TRUNCATE TABLE `razor_pay_payouts`;');
            DB::select('TRUNCATE TABLE `recent_searches`;');
            DB::select('TRUNCATE TABLE `reported_spams`;');
            DB::select('TRUNCATE TABLE `service_business_hours`;');
            DB::select('TRUNCATE TABLE `service_images`;');
            DB::select('TRUNCATE TABLE `service_products`;');
            DB::select('TRUNCATE TABLE `service_product_bookmark`;');
            DB::select('TRUNCATE TABLE `service_product_images`;');
            DB::select('TRUNCATE TABLE `service_profiles`;');
            DB::select('TRUNCATE TABLE `sponsors`;');
            DB::select('TRUNCATE TABLE `statuses`;');
            DB::select('TRUNCATE TABLE `status_medias`;');
            DB::select('TRUNCATE TABLE `status_views`;');
            DB::select('TRUNCATE TABLE `subscription_payments`;');
            DB::select('TRUNCATE TABLE `users`;');
            DB::select('TRUNCATE TABLE `user_earnings`;');
            DB::select('TRUNCATE TABLE `user_subscriptions`;');
            return '<h1>All Data Cleared</h1><a href='. route("admin.dashboard") .'>Back To dashboard</a>';
        } catch (Exception $e) {
            return '<h1>Error: '.$e->getMessage().'</h1>';
        }
    }

}
