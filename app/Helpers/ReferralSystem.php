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

    // Function to get level of a users array
    public static function getCurrentLevelArray($userId)
    {
        $levelList = [];
        // Check if the user has a referrer
        for ($level = 1; $level <= 7; $level++) {
            $sponsorUser = Sponsor::where('sponsored_user_id', $userId)->select('sponsor_user_id')->first();
            if (!empty($sponsorUser) && $sponsorUser->sponsor_user_id) {
                $sponsorUserId = $sponsorUser->sponsor_user_id;
                $levelList[$level] = $sponsorUserId;
                // User has a referrer, continue checking the next level
                $userId = $sponsorUserId;
            }
        }
        return $levelList;
    }

    // Function to check the current level of a user
    public static function getCurrentLevel($userId, $level = 1)
    {
        // Check if the user has a referrer
        $sponsorUser = Sponsor::where('sponsored_user_id', $userId)->select('sponsor_user_id')->first();
        if (!empty($sponsorUser) && $sponsorUser->sponsor_user_id) {
            $userId = $sponsorUser->sponsor_user_id;
            // User has a referrer, continue checking the next level
            return self::getCurrentLevel($userId, $level + 1);
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
    public static function manageReferral(Request $request, $sponsorData)
    {
        $level = 1;
        $responseData = $userLevelArray = [];
        //$userLevel = 0;
        // Define commission percentages for each level
        $commissionPercentages = Config('constants.COMMISSION_PERCENTAGES');
        $authUser = $request->get('Auth');
        $userId = $authUser->id;
        $isReferCodeUser = UserRepository::checkReferralCode($request);
        if (!empty($isReferCodeUser)) {
            $userLevelArray = self::getCurrentLevelArray($userId);
        }
        // dump('UserId ' . $userId);
        // dump('IsReferCode UserId ' . $isReferCodeUser);
        // dump('userLevelArray ', $userLevelArray);
        $subscription = SubscriptionRepository::getSingle($request);
        $responseData['subscription_price'] = $subscriptionPrice = $subscription->price;
        $responseData['sponsor_id'] = $sponsorData->id;
        $userId = 0;
        if (empty($isReferCodeUser)) {
            // dd($userLevelArray, 'No Refer');
            $responseData['earning'] = 0;
            $responseData['user_id'] =  $userId;
            $adminEarning = $subscriptionPrice;
            $responseData['admin_earning'] = $adminEarning;
            $responseData['type'] = 1;
            UserEarning::insert($responseData);
        } else {
            // dd($userLevelArray, 'Refer');
            $userId = $isReferCodeUser->id;
            $userLevelCommission = [];
            if ($isReferCodeUser->user_sponsor_count <= 5) {
                $i = 0;
                $arrayLen = count($userLevelArray);
                // dd($arrayLen);
                $subscriptionPriceDivide = $subscriptionPrice;
                foreach($userLevelArray as $userLevelKey => $userLevelUserId) {
                    $userLevelCommission['user_id'] =  $userLevelUserId;
                    $userLevelCommission['earning'] = $earning = ($commissionPercentages[$userLevelKey] / 100) * $subscriptionPrice;
                    $userLevelCommission['subscription_price'] = $subscriptionPrice;
                    $userLevelCommission['sponsor_id'] = $sponsorData->id;
                    $userLevelCommission['type'] = 1;
                    $adminEarning = $subscriptionPriceDivide - $earning;
                    // After distribute all commission rest payment goes to Admin
                    if ($i == $arrayLen - 1) {
                        $userLevelCommission['admin_earning'] = $adminEarning;
                    } else {
                        $userLevelCommission['admin_earning'] = 0;
                    }
                    $subscriptionPriceDivide = $adminEarning;
                    $i++;
                    UserEarning::insert($userLevelCommission);
                }
            } else {
                // dd($userLevelArray, 'No Refer');
                $responseData['earning'] = 0;
                $responseData['user_id'] =  $userId;
                $adminEarning = $subscriptionPrice;
                $responseData['admin_earning'] = $adminEarning;
                $responseData['type'] = 1;
                UserEarning::insert($responseData);
            }
        }
        
    }

    // Function to check the current level of a user
    public static function getCheckCurrentLevel($userId)
    {
        $listLevel = [];
        $sponsorUserId = $userId;
        for ($level = 1; $level <= 7; $level++) {
            // Check if the user has a referrer
            $sponsorUser = Sponsor::where('sponsored_user_id', $sponsorUserId)->select('sponsor_user_id')->first();
            if (!empty($sponsorUser) && $sponsorUser->sponsor_user_id) {
                $sponsorUserId = $sponsorUser->sponsor_user_id;
                // User has a referrer, continue checking the next level
                $listLevel[] = [
                    'level' => $level,
                    'user_id' => $sponsorUserId,
                ];
            }
        }
        // User does not have a referrer, reached the top-level
        return $listLevel;
    }
}
