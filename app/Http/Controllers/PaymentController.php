<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Http\Request;
use App\Models\PaymentLog;
use App\Models\SubscriptionPayment;
use App\Models\UserSubscription;
use App\Models\Sponsor;
use App\Models\UserEarning;
use Stripe;

class PaymentController extends Controller
{
    /**
     * checkRoute
     *
     * @param  mixed $request
     * @return json
     */
    public function checkRoute()
    {
        echo 'stripeWebhook';
        die;
    }

    /**
     * stripeWebhook
     *
     * @param  mixed $request
     * @return json
     */
    public function stripeWebhook(Request $request)
    {
        $data = [];
        \Stripe\Stripe::setApiKey(config('constants.STRIPE_SECRET_KEY'));
        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = config('constants.STRIPE_WEBHOOK_SECRET');
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json([
                'message' => ApiGlobalFunctions::messageDefault('invalid_payload'),
            ], config('response.HTTP_OK'));
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json([
                'message' => ApiGlobalFunctions::messageDefault('invalid_signature'),
            ], config('response.HTTP_OK'));
        }
        $paymentLogObj = new PaymentLog();
        $paymentLogObj->response = json_encode($event);
        $paymentLogObj->type = 'payment';
        $paymentLogObj->save();
        if ($event->type == "payment_intent.succeeded") {
            //As I understand here is where I should do things like send order info by mail and deplete stock accordingly

            $intentObj = $event->data->object;
            $clientSecret = $intentObj->client_secret;
            //$this->completeOrderInDatabase()
            //$this->sendMail();
            $subscriptionPayment = SubscriptionPayment::where('payment_intent_id', $clientSecret)->first();
            if ($subscriptionPayment) {
                $subscriptionPayment->is_payment = 1;
                $subscriptionPayment->payment_status = 'Completed';
                $subscriptionPayment->save();
                $userSubscriptionId = $subscriptionPayment->user_subscription_id;
                $userSubscriptionObj = UserSubscription::where('id', $userSubscriptionId)->first();
                $userSubscriptionObj->is_active = 1;
                $userSubscriptionObj->save();
                $sponsorId = $subscriptionPayment->sponsor_id;
                Sponsor::where('id', $sponsorId)->update(['status' => 1]);
                UserEarning::where('sponsor_id', $sponsorId)->update(['status' => 1]);
            }
            return response()->json([
                'intentId' => $intentObj->id,
                'message' => 'Payment succeeded'
            ], config('response.HTTP_OK'));
        } elseif ($event->type == "payment_intent.payment_failed") {
            //Payment failed to be completed
            $intentObj = $event->data->object;
            $subscriptionPayment = SubscriptionPayment::where('payment_intent_id', $clientSecret)->first();
            if ($subscriptionPayment) {
                $subscriptionPayment->is_payment = 0;
                $subscriptionPayment->payment_status = 'Failed';
                $subscriptionPayment->save();
                $userSubscriptionId = $subscriptionPayment->user_subscription_id;
                $userSubscriptionObj = UserSubscription::where('id', $userSubscriptionId)->first();
                $userSubscriptionObj->is_active = 0;
                $userSubscriptionObj->save();
                $sponsorId = $subscriptionPayment->sponsor_id;
                Sponsor::where('id', $sponsorId)->delete();
                UserEarning::where('sponsor_id', $sponsorId)->delete();
            }
            $error_message = $intentObj->last_payment_error ? $intentObj->last_payment_error->message : "";
            return response()->json([
                'intentId' => $intentObj->id,
                'message' => 'Payment failed: ' . $error_message
            ], config('response.HTTP_BAD_REQUEST'));
        }

        return $data;
    }
}
