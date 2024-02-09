<?php

namespace App\Repositories;

use App\Helpers\ReferralSystem;
use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\UserEarning;
use App\Models\User;
use App\Models\SubscriptionPayment;
use function is;
use function is_null;
use Illuminate\Support\Facades\DB;

class SponsorRepository
{

    /**
     * addSponsors
     *
     * @param  mixed $request
     * @return array
     */
    public static function addSponsors(Request $request)
    {
        $response = [];
        $authUser = $request->get('Auth');
        $sponsoredUserId = $authUser->id;
        $sponsorUserId = 0;
        if ($request->input('referral_code', false)) {
            $user = User::where('referral_code', $request->input('referral_code', false))
                ->where('id', '!=', $sponsoredUserId)->first();
            $sponsorUserId = $user->id;
        }

        $sponsoredUser = Sponsor::where([
            'sponsored_user_id' => $sponsoredUserId,
            'status' => 1
        ])->orderBy('id', 'DESC')->first();
        if (empty($sponsoredUser)) {
            $sponsoredUserNotCompleted = Sponsor::where([
                'sponsored_user_id' => $sponsoredUserId,
                'status' => 0
            ])->orderBy('id', 'DESC')->first();
            if (!empty($sponsoredUserNotCompleted)) {
                $response['status'] = true;
                $response['message'] = 'User referred successfully.';
                $response['sponsorData'] = $sponsoredUserNotCompleted;
            } else {
                $sponsor = new Sponsor();
                $sponsor->sponsor_user_id = $sponsorUserId;
                $sponsor->sponsored_user_id = $sponsoredUserId;
                if ($sponsor->save()) {
                    $response['status'] = true;
                    $response['message'] = 'User referred successfully.';
                    $response['sponsorData'] = $sponsor;
                } else {
                    $response['status'] = false;
                    $response['message'] = 'User not able to refer.';
                }
            }
        } else {
            // $response['status'] = true;
            // $response['message'] = 'User referred successfully.';
            // $response['sponsorData'] = $sponsoredUser;
            /* for testing bypass the already sponsored message */
            $response['status'] = false;
            $response['message'] = 'User already sponsored.';
        }
        return $response;
    }

    /**
     * getSponsors
     *
     * @param  mixed $sponsoredUserId
     * @return obj
     */
    public static function getSponsors($sponsoredUserId)
    {
        $response = [];

        $sponsorDetail = Sponsor::with(['userSponsor', 'userSponsored'])->where('sponsored_user_id', $sponsoredUserId)->first();
        if (!empty($sponsorDetail)) {
            $response = [
                'sponsors' => $sponsorDetail->userSponsor,
                'sponsoredUser' => $sponsorDetail->userSponsored
            ];
        } else {
            $response['status'] = false;
            $response['message'] = 'You have No sponsored.';
        }
        return $response;
    }


    /**
     * getMySponsors
     *
     * @param  mixed $request
     * @return void
     */
    public static function getMySponsors(Request $request)
    {
        $response = [];
        $authUser = $request->get('Auth');
        $sponsorDetail = Sponsor::with(['userSponsored', 'userSponsored.userEarnings', 'userSponsored.userSponsor'])
            ->where('sponsor_user_id', $authUser->id)
            ->where('status', '>', 0)
            ->get();
        // dd($sponsorDetail);
        if (!empty($sponsorDetail)) {
            $mySponsored = [];
            if ($sponsorDetail->count() > 0) {
                foreach ($sponsorDetail as $sponsor) {
                    $userSponsoredDetail = $sponsor->userSponsored;
                    $totalSponsored = $userSponsoredDetail->userSponsor->count();
                    $userEarnings = number_format($userSponsoredDetail->userEarnings->sum('earning'), 2);
                    $userSponsoredDetail->userEarning = $userEarnings;
                    $userSponsoredDetail->totalReferrals = $totalSponsored;
                    unset($userSponsoredDetail->userEarnings);
                    unset($userSponsoredDetail->userSponsor);
                    $mySponsored[] = $userSponsoredDetail;
                }
            }
            $response = $mySponsored;
        } else {
            $response['status'] = false;
            $response['message'] = 'You have No sponsored.';
        }
        return $response;
    }

    /**
     * getSponsorsHistory
     *
     * @param  mixed $request
     * @return void
     */
    public static function getSponsorsHistory(Request $request)
    {
        $response = [];
        $authUser = $request->get('Auth');
        $sponsorDetail = UserEarning::with([
            'sponsor',
            'sponsor.userSponsor',
            'sponsor.userSponsored'
        ])
            ->where('user_id', $authUser->id)
            ->get();
        if ($sponsorDetail->count() > 0) {
            $mySponsored = [];
            foreach ($sponsorDetail as $sponsor) {
                $mySponsored[] =
                    [
                        'earning' => $sponsor->earning,
                        'user_sponsors' => $sponsor->sponsor->userSponsor,
                        'user_sponsored' => $sponsor->sponsor->userSponsored,
                    ];
            }
            $response = $mySponsored;
        } else {
            $response['status'] = false;
            $response['message'] = 'You have No sponsors.';
        }
        return $response;
    }

    /**
     * getTransactionHistory
     *
     * @param  mixed $request
     * @return []
     */
    public static function getTransactionHistory(Request $request)
    {
        $response = [];
        $typeOfTransaction = config('constants.TRANSACTION_TYPE');
        $authUser = $request->get('Auth');
        $sponsoredUser = Sponsor::where([
            'sponsored_user_id' => $authUser->id,
            'status' => 1
        ])->first();
        $subscriptionPayment = SubscriptionPayment::where([
            'user_id' => $authUser->id,
        ])->orderBy('id', 'DESC')->get();
        // dd($subscriptionPayment);
        $earning = [];
        $mySponsored = [
            'total_earning' => 0.00,
            'total_remaining' => 0.00,
            'total_withdrawal' => 0.00,
        ];
        if ($sponsoredUser) {
            $sponsorEarning = UserEarning::where([
                'user_id' => $authUser->id,
                'status' => 1
            ])
                ->orderBy('created_at', 'DESC')->get();
            // $sponsorDetail = UserEarning::where('sponsor_id', $sponsoredUser->id)
            //     ->orderBy('id', 'ASC')->take(1)->get();
            // dd($authUser->id, $sponsoredUser->id, $sponsorEarning);
            $totalEarning = $totalRemaining = $totalWithdrawal = 0;
            if ($sponsorEarning->count() > 0) {
                $totalEarning = $sponsorEarning->sum('earning');
                $totalWithdrawal = $sponsorEarning->sum('withdrawal');
                $totalRemaining = $totalEarning - $totalWithdrawal;
            }
            $mySponsored = [
                'total_earning' => number_format($totalEarning, 2),
                'total_remaining' => number_format($totalRemaining, 2),
                'total_withdrawal' => number_format($totalWithdrawal, 2),
            ];
            $transactionType = $payment = '';
            $earning = collect($sponsorEarning);
        }
        if (!empty($earning)) {
            $subscriptionCol = collect($subscriptionPayment);
            $earingSponsor = $earning->merge($subscriptionCol);
        } else {
            $earingSponsor = collect($subscriptionPayment);
        }
        $payment = 0.00;
        // $earingSponsor = $earning;
        if ($earingSponsor->count() > 0) {
            foreach ($earingSponsor as $sponsor) {
                if ($sponsor->withdrawal > 0 && $sponsor->earning == 0) {
                    // echo "wi";
                    $payment = $sponsor->withdrawal;
                    $transactionType = 'outgoing';
                } elseif (isset($sponsor->sponsor_id) && isset($sponsor->payment_status)) {
                    $payment = $sponsor->subscription_price;
                    if ($sponsor->payment_status == 'Completed') {
                        $transactionType = 'outgoing';
                        $sponsor->type = 3;
                    } elseif ($sponsor->payment_status == 'inProgress') {
                        $transactionType = 'inProgress';
                        $sponsor->type = 4;
                    } elseif ($sponsor->payment_status == 'Canceled') {
                        $transactionType = 'canceled';
                        $sponsor->type = 5;
                    } else {
                        $transactionType = 'failed';
                        $sponsor->type = 6;
                    }
                } elseif ($sponsor->earning > 0 && $sponsor->withdrawal == 0) {
                    // echo "in";
                    $payment = $sponsor->earning;
                    $transactionType = 'incoming';
                }
                $mySponsored['transaction'][] = [
                    'payment' => number_format($payment, 2),
                    'transaction_type' => $transactionType,
                    'type' => $typeOfTransaction[$sponsor->type],
                    'datetime' => strtotime($sponsor->created_at)
                ];
            }
            // dd($mySponsored);
            $response['statusTransaction'] = true;
            $response['dataTransaction'] = $mySponsored;
        } else {
            $response['statusTransaction'] = false;
            $response['messageTransaction'] = 'You have no transaction.';
            $response['dataTransaction'] = [];
        }
        // } else {
        //     $response['status'] = false;
        //     $response['message'] = 'You have no transaction.';
        // }
        return $response;
    }
}
