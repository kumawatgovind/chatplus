<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ReferralSystem;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Http\Request;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Repositories\SponsorRepository;
use App\Repositories\RazorPayRepository;
use Stripe;
use Mail, Exception, Auth, Hash, Session;

class SubscriptionsController extends Controller
{
    /**
     * index
     *
     * @param  mixed $request
     * @return json
     */
    public function index(Request $request)
    {
        $data = [];
        try {
            $postResponse = SubscriptionRepository::list($request);
            if (!empty($postResponse)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $postResponse;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('list_not_found');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * userSubscribe
     *
     * @param  mixed $request
     * @return json
     */
    public function userSubscribe(Request $request)
    {
        $data = [];
        try {
            $validator = (object) Validator::make($request->all(), [
                'subscription_id' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $subscriptionId = $request->input('subscription_id', 0);
            $authUserId = $request->get('Auth')->id;
            $userSubscribe = SubscriptionRepository::checkUserSubscribe($authUserId);
            if (empty($checkUserSubscribe) || $userSubscribe->active_subscription_count == 0) {
                if (SubscriptionRepository::userSubscribe($subscriptionId, $authUserId)) {
                    $data['status'] = true;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('user_subscribe');
                    $data['data'] = [];
                } else {
                    $data['status'] = false;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('record_not_found');
                }
            } else {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('user_subscribe');
                $data['data'] = $userSubscribe->activeSubscription;
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * checkUserSubscribe
     *
     * @param  mixed $request
     * @return json
     */
    public function checkUserSubscribe(Request $request)
    {
        $data = [];
        try {
            $checkUserSubscribe = SubscriptionRepository::checkUserSubscribe($request);
            if ($checkUserSubscribe->active_subscription_count > 0) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('user_subscribed');
                $data['data'] = $checkUserSubscribe;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('user_not_subscribed');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * paymentRequest
     *
     * @param  mixed $request
     * @return json
     */
    public function paymentRequest(Request $request)
    {
        $data = [];
        try {
            $validator = (object) Validator::make($request->all(), [
                'referral_code' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            // $authUser = $request->get('Auth');
            // $isReferCodeUser = UserRepository::checkReferralCode($request);
            // $userLevelArray = ReferralSystem::getCurrentLevelArray($authUser->id);
            // $userLevel = ReferralSystem::getCheckCurrentLevel($isReferCodeUser->id);
            // dd($isReferCodeUser->id, $userLevelArray);
            // Add Sponsors after checking referral code
            $sponsors = SponsorRepository::addSponsors($request);
            extract($sponsors);
            if ($status) {
                ReferralSystem::manageReferral($request, $sponsorData);
                $request->merge(['sponsor_id' => $sponsorData->id]);
                if ($paymentRequest = SubscriptionRepository::paymentRequest($request)) {
                    $data['status'] = true;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('payment_request');
                    $data['data'] = $paymentRequest;
                } else {
                    $data['status'] = false;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('record_not_found');
                }
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = $message;
            }
            // } else {
            //     $data['status'] = false;
            //     $data['code'] = config('response.HTTP_OK');
            //     $data['message'] = ApiGlobalFunctions::messageDefault('referral_code_notActive');
            // }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * checkPaymentStatus
     *
     * @param  mixed $request
     * @return json
     */
    public function checkPaymentStatus(Request $request)
    {
        $data = [];
        try {
            $validator = (object) Validator::make($request->all(), [
                'referral_code' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            if ($paymentRequest = SubscriptionRepository::checkPaymentStatus($request)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('payment_request');
                $data['data'] = $paymentRequest;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_not_found');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

     /**
     * paymentRequest
     *
     * @param  mixed $request
     * @return json
     */
    public function payoutRequest(Request $request)
    {
        $minimumPayout = config('constants.MINIMUM_PAYOUT');
        $data = [];
        try {
            $validator = (object) Validator::make($request->all(), [
                'payout_amount' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $payoutAmount = $request->input('payout_amount', 0);
            if ($payoutAmount >= $minimumPayout) {
                $totalTransaction = SponsorRepository::getTransactionHistory($request);
                if ($totalTransaction['total_remaining'] > $payoutAmount) {
                    $payoutContact = RazorPayRepository::createPayoutContact($request);
                    extract($payoutContact);
                    if ($contactStatus) {
                        $payoutFundAccount = RazorPayRepository::createPayoutFundAccount($request, $contactData);
                        extract($payoutFundAccount);
                        if ($accountStatus) {
                            $payout = RazorPayRepository::createPayout($request, $fundData);
                            extract($payout);
                            if ($payoutStatus) {
                                if ($payoutRequest = SubscriptionRepository::payoutRequest($request)) {
                                    $data['status'] = true;
                                    $data['code'] = config('response.HTTP_OK');
                                    $data['message'] = ApiGlobalFunctions::messageDefault('payout_success');
                                    $data['data'] = $payoutRequest;
                                } else {
                                    $data['status'] = false;
                                    $data['code'] = config('response.HTTP_OK');
                                    $data['message'] = ApiGlobalFunctions::messageDefault('record_not_found');
                                }
                            } else {
                                $data['status'] = false;
                                $data['code'] = config('response.HTTP_OK');
                                $data['message'] = $error;
                            }
                        } else {
                            $data['status'] = false;
                            $data['code'] = config('response.HTTP_OK');
                            $data['message'] = $error;
                        }
                    } else {
                        $data['status'] = false;
                        $data['code'] = config('response.HTTP_OK');
                        $data['message'] = $error;
                    }
                } else {
                    $data['status'] = false;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = sprintf(ApiGlobalFunctions::messageDefault('payout_amount_not_available'), $totalTransaction['total_remaining']);
                }
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = sprintf(ApiGlobalFunctions::messageDefault('payout_minimum_not_available'), $minimumPayout);
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * checkPayout
     *
     * @param  mixed $request
     * @return json
     */
    public function checkPayout(Request $request)
    {
        $data = [];
        try {
            $payout = RazorPayRepository::checkPayout($request);
            extract($payout);
            if ($payoutStatus) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('payout_success');
                $data['data'] = $payoutData;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = $error;
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }
}
