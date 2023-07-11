<?php

namespace App\Helpers;

use Illuminate\Http\Request;
use App\Models\ServiceProfile;
use App\Models\Sponsor;
use App\Models\UserEarning;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use PSpell\Config;

class ReferralSystem
{
    /**
     * Referral Code
     *
     * @var string
     */
    protected static $referralCode;

    /**
     * Create a referral code and store it on the User.
     *
     * @return string
     */
    public static function createReferralCode($length): string
    {
        if (empty(self::$referralCode)) {
            // attempt to create a referral code until the one you have is unique
            $referralCode = self::generateReferralCode($length);
            if (!self::hasUniqueReferralCode($referralCode)) {
                $referralCode = self::generateReferralCode($length);
            }
            self::$referralCode = $referralCode;
        }
        return self::$referralCode;
    }

    /**
     * Generate a referral code.
     *
     * @return string
     */
    protected static function generateReferralCode($length = 8): string
    {
        // String of all alphanumeric character
        $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        // Shuffle the $str_result and returns substring
        // of specified length
        $stripped = substr(str_shuffle($str_result), 0, $length);
        // format the final referral code
        return strtoupper($stripped);
    }

    /**
     * Check if the referral code is unique.
     *
     * @param  string  $referralCode
     *
     * @return boolean
     */
    protected static function hasUniqueReferralCode(string $referralCode): bool
    {
        return ServiceProfile::where('referral_code', $referralCode)->exists();
        // check against database to enforce uniqueness
    }

    // Function to check the current level of a user
    public static function getCurrentLevel($userId, $level = 1)
    {
        // Check if the user has a referrer
        $sponsorUser = Sponsor::where('sponsored_user_id', $userId)->select('sponsor_user_id')->first();

        if (!empty($sponsorUser) && $sponsorUser->sponsor_user_id) {
            $sponsorUserId = $sponsorUser->sponsor_user_id;
            // User has a referrer, continue checking the next level
            return self::getCurrentLevel($sponsorUserId, $level + 1);
        }
        // User does not have a referrer, reached the top-level
        return $level;
    }
    /**
     * Check if the referral code is unique.
     *
     * @param  string  $referralCode
     *
     * @return boolean
     */
    public static function checkReferral(Request $request)
    {
        $responseData = [];
        // Define commission percentages for each level
        $commissionPercentages = Config('constants.COMMISSION_PERCENTAGES');
        $authUser = $request->get('Auth');
        $userId = $authUser->id;
        $isReferCodeUser = UserRepository::checkReferralCode($request);
        if (!empty($isReferCodeUser)) {
            $userLevel = self::getCurrentLevel($isReferCodeUser->id);
        }
        // dump('UserId ' . $userId);
        // dump('IsReferCode UserId ' . $isReferCodeUser->id);
        // dd('userLevel ' . $userLevel);
        $subscription = SubscriptionRepository::getSingle($request);
        $responseData['subscription_price'] = $subscriptionPrice = $subscription->price;
        $userId = 0;
        if (empty($isReferCodeUser)) {
            // dd($userLevel, 'No Refer');
            $responseData['earning'] = $earning = $subscriptionPrice;
        } else {
            // dd($userLevel, 'Refer');
            $userId = $isReferCodeUser->id;
            if ($userLevel <= 7) {
                $responseData['earning'] = $earning = ($commissionPercentages[$userLevel] / 100) * $subscriptionPrice;
            }
        }
        $responseData['user_id'] =  $userId;
        if ($userId == 0) {
            $adminEarning = $subscriptionPrice;
        } else {
            $adminEarning = $subscriptionPrice - $earning;
        }
        $responseData['admin_earning'] = $adminEarning;
        UserEarning::insert($responseData);
    }
}
