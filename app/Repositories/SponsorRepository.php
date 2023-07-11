<?php

namespace App\Repositories;

use App\Helpers\ReferralSystem;
use Illuminate\Http\Request;
use App\Models\Sponsor;
use App\Models\User;
use function is;
use function is_null;

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
                ->where('id', '!=', $sponsorUserId)->first();
            $sponsorUserId = $user->id;
        }
        $sponsoredUser = Sponsor::where('sponsored_user_id', $sponsoredUserId)->first();
        if (empty($sponsoredUser)) {
            $sponsor = new Sponsor();
            $sponsor->sponsor_user_id = $sponsorUserId;
            $sponsor->sponsored_user_id = $sponsoredUserId;
            if ($sponsor->save()) {
                $response['status'] = true;
                $response['message'] = 'User referred successfully.';
            } else {
                $response['status'] = false;
                $response['message'] = 'User not able to refer.';
            }
        } else {
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
}
