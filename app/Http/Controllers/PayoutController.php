<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Http\Request;
use App\Models\PaymentLog;
use App\Models\RazorPayPayout;
use App\Models\SubscriptionPayment;
use App\Models\UserSubscription;
use App\Models\Sponsor;
use App\Models\User;
use App\Models\UserEarning;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Log;

class PayoutController extends Controller
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
    public function payoutWebhook(Request $request)
    {
        // http_response_code(200); // Always respond with a 200 OK status

        $payload = @file_get_contents('php://input');
        Log::channel('payout')->info("payout Webhook payload - $payload");
        $data = json_decode($payload, true);
        // Verify the webhook event using the Razorpay signature
        $webhook_secret = 'Be95Ltp4yaeRV3x7fqzMCeOdZz7gVrj1'; // Replace with your actual webhook secret
        $signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'];

        $expected_signature = hash_hmac('sha256', $payload, $webhook_secret);

        if ($signature !== $expected_signature) {
            // Invalid signature, handle accordingly
            // You might want to log this for investigation
            return;
        }
        $paymentLogObj = new PaymentLog();
        $paymentLogObj->response = json_encode($data);
        $paymentLogObj->type = 'payout';
        $paymentLogObj->save();
        // Now you can process the payout status
        if (!empty($data['event'])) {
            $payoutId = $data['payload']['payout']['entity']['id'];
            $payoutData = RazorPayPayout::where('payout_id', $payoutId)->first();
            $userEarningUpdate = [
                'user_payout_id' => $payoutData->id,
                'user_id' => $payoutData->user_id
            ];
            // Perform actions of the payout processed status
            if ($data['event'] === 'payout.processed' || $data['event'] === 'payout.updated') {
                $payoutData->status = 'complete';
                // update database
                $payoutData->save();
                UserEarning::where($userEarningUpdate)->update(['status' => 1]);
                // Perform actions of the payout reversed status
            } elseif ($data['event'] === 'payout.reversed' || $data['event'] === 'payout.initiated') {
                $payoutData = RazorPayPayout::where('payout_id', $payoutId)->first();
                $payoutData->status = 'processing';
                // update database
                $payoutData->save();
                UserEarning::where($userEarningUpdate)->delete();
            } elseif ($data['event'] === 'payout.rejected') { // Perform actions of the payout rejected status
                $payoutData = RazorPayPayout::where('payout_id', $payoutId)->first();
                $payoutData->status = 'cancelled';
                // update database
                $payoutData->save();
                UserEarning::where($userEarningUpdate)->delete();
            } elseif ($data['event'] === 'payout.pending') { // Perform actions of the payout pending status
                $payoutData = RazorPayPayout::where('payout_id', $payoutId)->first();
                $payoutData->status = 'pending';
                // update database
                $payoutData->save();
                UserEarning::where($userEarningUpdate)->delete();
            }
            $user = User::where('id', $payoutData->user_id)->first();
            // send notifications
            $notificationData = RazorPayPayout::where('payout_id', $payoutId)->first();
            NotificationRepository::createNotification($notificationData, $user, 'withdrawal');
        }
        return $data;
    }
}
