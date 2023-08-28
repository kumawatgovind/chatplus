<?php

namespace App\Repositories;


use App\Models\UserSubscription;
use App\Models\Subscription;
use App\Models\SubscriptionPayment;
use App\Models\User;
use App\Models\Sponsor;
use App\Models\UserEarning;
use Illuminate\Http\Request;
use PSpell\Config;

use function is;
use function is_null;
use Stripe;

class SubscriptionRepository
{

    /**
     * list
     *
     * @param  mixed $request
     * @return obj
     */
    public static function getSingle(Request $request)
    {
        $query = Subscription::status();
        if ($request->input('subscription_id', false)) {
            $subscriptionId  = $request->input('subscription_id', 0);
            $data = $query->where('id', $subscriptionId)->first();
        } else {
            $data = $query->first();
        }
        return  $data;
    }

    /**
     * list
     *
     * @param  mixed $request
     * @return obj
     */
    public static function list(Request $request)
    {
        if ($request->input('limit', false)) {
            $limit  = $request->input('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        return  Subscription::select(['id', 'name', 'description', 'price'])
            ->status()->paginate($limit);
    }

    /**
     * getUserPostTimeline
     *
     * @param  mixed $request
     * @return obj
     */
    public static function userSubscribe($subscriptionId = 0, $authUserId = 0, $sponsorId = 0)
    {
        if ($subscriptionId > 0) {
            $subscriptionPlan = Subscription::where('id', $subscriptionId)->status()->first();
            if ($subscriptionPlan) {
                $paymentIntents = self::paymentIntentsCreate($subscriptionPlan);
                $userSubscriptionObj = new UserSubscription();
                $userSubscriptionObj->user_id = $authUserId;
                $userSubscriptionObj->subscription_id = $subscriptionPlan->id;
                $userSubscriptionObj->is_active = 0;
                $userSubscriptionObj->start_date = date('Y-m-d');
                $userSubscriptionObj->end_date = date('Y-m-d', strtotime("+1 year"));
                $userSubscriptionObj->subscription_price = $subscriptionPlan->price;
                if ($userSubscriptionObj->save()) {
                    $userSubscriptionId = $userSubscriptionObj->id;
                    $subscriptionPaymentObj = new SubscriptionPayment();
                    $subscriptionPaymentObj->transaction_id = uniqid();
                    $subscriptionPaymentObj->stripe_customer_id = $paymentIntents['customer_id'];
                    $subscriptionPaymentObj->user_id = $authUserId;
                    $subscriptionPaymentObj->sponsor_id = $sponsorId;
                    $subscriptionPaymentObj->user_subscription_id = $userSubscriptionId;
                    $subscriptionPaymentObj->subscription_price = $subscriptionPlan->price;
                    $subscriptionPaymentObj->payment_status = 'inProgress';
                    $subscriptionPaymentObj->payment_intent_id = $paymentIntents['payment_intent'];
                    $subscriptionPaymentObj->payment_response = json_encode($paymentIntents);
                    $subscriptionPaymentObj->save();

                    $userSubscriptionData = UserSubscription::with('subscriptionPayment')->where([
                        'id' => $userSubscriptionId,
                        'user_id' => $authUserId,
                    ])->first();
                    return $userSubscriptionData;
                }
            }
        }
    }

    /**
     * checkUserSubscribe
     *
     * @param  mixed $authUserId
     * @return obj
     */
    public static function checkUserSubscribe($authUserId)
    {
        return User::with(['activeSubscription', 'activeSubscription.subscriptionPayment'])
            ->withCount('activeSubscription')
            ->where('id', $authUserId)->first();
    }

    /**
     * paymentRequest
     *
     * @param  mixed $request
     * @return void
     */
    public static function paymentRequest(Request $request)
    {
        // if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
        //     return "On local environment payment not allowed";
        // }

        $authUserId = $request->get('Auth')->id;
        $checkUserSubscribe = self::checkUserSubscribe($authUserId);
        $subscription = self::getSingle($request);
        $subscriptionId = $subscription->id;
        $sponsorId = $request->input('sponsor_id', false);
        // if (!empty($checkUserSubscribe) && $checkUserSubscribe->active_subscription_count > 0) {
        //     $userSubscribe = $checkUserSubscribe->activeSubscription;
        // } else {
        $userSubscribe = self::userSubscribe($subscriptionId, $authUserId, $sponsorId);
        // }

        // return the request token
        $stripeToken = json_decode($userSubscribe->subscriptionPayment->payment_response, true);
        $stripeToken['publishable_Key'] = config('constants.STRIPE_PUBLISHABLE_KEY');
        return $stripeToken;
    }

    /**
     * paymentIntentsCreate
     *
     * @param  mixed $request
     * @return void
     */
    private static function paymentIntentsCreate($subscriptionPlan)
    {
        $stripe = new \Stripe\StripeClient(config('constants.STRIPE_SECRET_KEY'));
        // Completed/Canceled/Failed
        // Use an existing Customer ID if this is a returning customer.
        $customer = $stripe->customers->create();
        $ephemeralKey = $stripe->ephemeralKeys->create([
            'customer' => $customer->id,
        ], [
            'stripe_version' => config('constants.STRIPE_VERSION'),
        ]);
        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $subscriptionPlan->price * 100,
            'currency' => config('constants.CURRENCY'),
            'customer' => $customer->id,
            'automatic_payment_methods' => [
                'enabled' => 'true',
            ],
        ]);
        return [
            'payment_intent' => $paymentIntent->client_secret,
            'ephemeral_key' => $ephemeralKey->secret,
            'customer_id' => $customer->id
        ];
    }

    /**
     * checkPaymentStatus
     *
     * @param  mixed $request
     * @return void
     */
    public static function checkPaymentStatus(Request $request)
    {
        $customerId = $request->input('customer_id', false);
        $paymentStatus = $request->input('payment_status', false);
        $responseData = [];
        $subscriptionPaymentDetail = SubscriptionPayment::where([
            'stripe_customer_id' => $customerId
        ])->first();

        if (!empty($subscriptionPaymentDetail) && $subscriptionPaymentDetail->is_payment == 0) {
            switch ($paymentStatus) {
                case 'Completed':
                    $subscriptionPaymentDetail->is_payment = 1;
                    $subscriptionPaymentDetail->payment_status = $paymentStatus;
                    $subscriptionPaymentDetail->save();
                    $sponsorId = $subscriptionPaymentDetail->sponsor_id;
                    Sponsor::where('id', $sponsorId)->update(['status' => 1]);
                    UserEarning::where('sponsor_id', $sponsorId)->update(['status' => 1]);
                    UserSubscription::where([
                        'id', $subscriptionPaymentDetail->user_subscription_id,
                        'user_id', $subscriptionPaymentDetail->user_id,
                        ])->update(['is_active' => 1]);
                    $responseData['payment_status'] = 'Completed';
                    $responseData['data_message'] = 'Payment successfully completed';
                    break;
                case 'Canceled':
                    $subscriptionPaymentDetail->payment_status = $paymentStatus;
                    $subscriptionPaymentDetail->is_payment = 0;
                    $subscriptionPaymentDetail->save();
                    $sponsorId = $subscriptionPaymentDetail->sponsor_id;
                    Sponsor::where('id', $sponsorId)->delete();
                    UserEarning::where('sponsor_id', $sponsorId)->delete();
                    UserSubscription::where([
                        'id', $subscriptionPaymentDetail->user_subscription_id,
                        'user_id', $subscriptionPaymentDetail->user_id,
                        ])->delete();
                    $responseData['payment_status'] = 'Canceled';
                    $responseData['data_message'] = 'Payment Canceled';
                    break;
                case 'Failed':
                    $errorMessage = $request->input('error_message', false);
                    $subscriptionPaymentDetail->payment_status = $paymentStatus;
                    $subscriptionPaymentDetail->is_payment = 0;
                    $subscriptionPaymentDetail->save();
                    $sponsorId = $subscriptionPaymentDetail->sponsor_id;
                    Sponsor::where('id', $sponsorId)->delete();
                    UserEarning::where('sponsor_id', $sponsorId)->delete();
                    UserSubscription::where([
                        'id', $subscriptionPaymentDetail->user_subscription_id,
                        'user_id', $subscriptionPaymentDetail->user_id,
                        ])->delete();
                    $responseData['payment_status'] = 'Failed';
                    $responseData['data_message'] = $errorMessage;
                    break;
                default:
                    $responseData['payment_status'] = 'Error';
                    $responseData['data_message'] = 'Something went wrong.';
            }
        } elseif (!empty($subscriptionPaymentDetail) && $subscriptionPaymentDetail->is_payment == 1) {
            $responseData['payment_status'] = 'Completed';
            $responseData['data_message'] = 'Payment successfully completed';
        } else {
            $responseData['payment_status'] = 'Error';
            $responseData['data_message'] = 'Something went wrong.';
        }
        return $responseData;
    }

    
     /**
     * paymentRequest
     *
     * @param  mixed $request
     * @return void
     */
    public static function payoutRequest(Request $request)
    {
        try {
            // if (in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
            //     return "On local environment payout not allowed";
            // }
            $authUserId = $request->get('Auth')->id;
            $responseData['earning'] = 0;
            $responseData['user_id'] = $authUserId;
            $responseData['admin_earning'] = 0;
            $responseData['type'] = 2;
            $responseData['withdrawal'] = $request->input('payout_amount', false);
            return UserEarning::insert($responseData);
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
