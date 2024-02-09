<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\PaymentLog;
use App\Models\Sponsor;
use App\Models\SubscriptionPayment;
use App\Models\UserEarning;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CCAvenueController extends Controller {

    /**
     * ccResponse
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public function ccAvenueRequestHandler(Request $request, $transactionId) {
        error_reporting(0);
        $transactionDetail = SubscriptionPayment::with([
            'user',
            'user.userServicesProfile',
            'user.userServicesProfile.state' => function ($q) {
                $q->select('id', 'name');
            },
            'user.userServicesProfile.city' => function ($q) {
                $q->select('id', 'name');
            },
            'user.userServicesProfile.locality' => function ($q) {
                $q->select('id', 'name');
            },
        ])->where("id", $transactionId)->first();
        $billing_address = $billing_city = $billing_state = $billing_zip = $mobile_number = '';
        $billing_email = $name = '';
        $postData = [
            "tid" => '',
            "merchant_id" => config('constants.CC_MERCHANT_ID'),
            "order_id" => '',
            "amount" => 1,
            "currency" => config('constants.CURRENCY'),
            "redirect_url" => route('ccAvenueResponseSuccess'),
            "cancel_url" => route('ccAvenueResponseCancel'),
            "language" => config('constants.LANGUAGE'),
            "billing_name" => $name,
            "billing_address" => $billing_address,
            "billing_city" => $billing_city,
            "billing_state" => $billing_state,
            "billing_zip" => $billing_zip,
            "billing_country" => 'India',
            "billing_tel" => $mobile_number,
            "billing_email" => $billing_email,
            "delivery_name" => $name,
            "delivery_address" => $billing_address,
            "delivery_city" => $billing_city,
            "delivery_state" => $billing_state,
            "delivery_zip" => $billing_zip,
            "delivery_country" => 'India',
            "delivery_tel" => $mobile_number,
        ];
        if($transactionDetail) {
            if($transactionDetail->user->userServicesProfile) {
                $state = $transactionDetail->user->userServicesProfile->state()->first();
                $city = $transactionDetail->user->userServicesProfile->city()->first();
                $locality = $transactionDetail->user->userServicesProfile->locality()->first();
                $billing_address = $transactionDetail->user->userServicesProfile->street_name.', '.
                    $transactionDetail->user->userServicesProfile->building_name.', '.
                    $locality->name;
                $billing_city = $city->name;
                $billing_state = $state->name;
                $billing_zip = $transactionDetail->user->userServicesProfile->pin_code;
                $mobile_number = $transactionDetail->user->phone_number;
                $billing_email = $transactionDetail->user->email;
            }
            $postData["tid"] = time();
            $postData["order_id"] = $transactionDetail->id;
            $postData["amount"] = $transactionDetail->subscription_price;
            $postData["billing_name"] = $transactionDetail->user->name;
            $postData["billing_address"] = $billing_address;
            $postData["billing_city"] = $billing_city;
            $postData["billing_state"] = $billing_state;
            $postData["billing_zip"] = $billing_zip;
            $postData["billing_country"] = 'India';
            $postData["billing_tel"] = $mobile_number;
            $postData["billing_email"] = $billing_email;
            $postData["delivery_name"] = $transactionDetail->user->name;
            $postData["delivery_address"] = $billing_address;
            $postData["delivery_city"] = $billing_city;
            $postData["delivery_state"] = $billing_state;
            $postData["delivery_zip"] = $billing_zip;
            $postData["delivery_country"] = 'India';
            $postData["delivery_tel"] = $mobile_number;
        }
        $merchantData = '';
        $workingKey = config('constants.CC_WORKING_KEY_TEST'); //Shared by CCAVENUES
        $accessCode = config('constants.CC_ACCESS_CODE_TEST'); //Shared by CCAVENUES
        if(config('constants.CC_IS_LIVE')) {
            $workingKey = config('constants.CC_WORKING_KEY'); //Shared by CCAVENUES
            $accessCode = config('constants.CC_ACCESS_CODE'); //Shared by CCAVENUES
        }
        // echo "<pre>";
        // print_r($postData);
        foreach($postData as $key => $value) {
            $merchantData .= $key.'='.$value.'&';
        }
        $encryptedData = Helper::encryptCC($merchantData, $workingKey); // Method for encrypting the data.
        $paymentUrl = 'https://test.ccavenue.com';
        if(config('constants.CC_IS_LIVE')) {
            $paymentUrl = 'https://secure.ccavenue.com';
        }
        return view('pages.payment', compact('paymentUrl', 'encryptedData', 'accessCode'));
    }
    /**
     * ccAvenueResponseHandler
     *
     * @param  mixed $request
     * @return \Illuminate\Contracts\View\Factory
     */
    public function ccAvenueResponseHandler(Request $request) {
        try {
            $workingKey = config('constants.CC_WORKING_KEY_TEST'); //Shared by CCAVENUES
            if(config('constants.CC_IS_LIVE')) {
                $workingKey = config('constants.CC_WORKING_KEY'); //Shared by CCAVENUES
            }
            //This is the response sent by the CCAvenue Server
            $encResponse = $request->input("encResp");
            //Crypto Decryption used as per the specified working key.
            $rcvdString = Helper::decryptCC($encResponse, $workingKey);
            $order_status = "";
            $decryptValues = explode('&', $rcvdString);
            $dataSize = sizeof($decryptValues);

            $responseData = [];
            for($i = 0; $i < $dataSize; $i++) {
                $information = explode('=', $decryptValues[$i]);
                $responseData[$information[0]] = $information[1];
                if($i == 3)
                    $order_status = $information[1];
            }
            $paymentLogObj = new PaymentLog();
            $paymentLogObj->response = json_encode($responseData);
            $paymentLogObj->type = 'payment';
            $paymentLogObj->save();
            if(isset($responseData['order_id'])) {

                $subscriptionPayment = SubscriptionPayment::where('id', $responseData['order_id'])->first();
                if($order_status === "Success") {
                    self::paymentStatusUpdate($subscriptionPayment, $responseData, 'Completed');
                } elseif($order_status === "Aborted") {
                    self::paymentStatusUpdate($subscriptionPayment, $responseData, 'Canceled');
                } else {
                    self::paymentStatusUpdate($subscriptionPayment, $responseData, 'Failed');
                }
            }

            return view('pages.paymentResponse');
        } catch (Exception $e) {
            return '<h1>Error: '.$e->getMessage().'</h1>';
        }
    }

    public static function paymentStatusUpdate($subscriptionPayment, $responseData, $paymentStatus) {
        if($subscriptionPayment) {
            $subscriptionPayment->is_payment = 0;
            $subscriptionPayment->payment_status = $paymentStatus;
            $subscriptionPayment->payment_response = json_encode($responseData);
            $subscriptionPayment->save();
            $userSubscriptionId = $subscriptionPayment->user_subscription_id;
            $userSubscriptionObj = UserSubscription::where('id', $userSubscriptionId)->first();
            if($userSubscriptionObj) {
                if($paymentStatus === 'Completed') {
                    $userSubscriptionObj->is_active = 1;
                } else {
                    $userSubscriptionObj->is_active = 0;
                }
                $userSubscriptionObj->save();
            }
            $sponsorId = $subscriptionPayment->sponsor_id;
            if($paymentStatus === 'Completed') {
                Sponsor::where('id', $sponsorId)->update(['status' => 1]);
                UserEarning::where('sponsor_id', $sponsorId)->update(['status' => 1]);
            } else {
                Sponsor::where('id', $sponsorId)->delete();
                UserEarning::where('sponsor_id', $sponsorId)->delete();
            }
        }
    }
}
