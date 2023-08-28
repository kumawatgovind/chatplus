<?php

namespace App\Repositories;

use App\Helpers\ReferralSystem;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\ServiceBusinessHour;
use App\Models\ServiceProfile;
use App\Models\ServiceImage;
use App\Models\Referral;
use App\Models\User;
use App\Models\ReportedSpam;
use App\Models\ReferralCount;
use Illuminate\Support\Facades\DB;
use function is;
use function is_null;

class UserRepository
{

    /**
     * getUser.
     * 
     * @param $id
     * @return []
     */
    public static function getUser($id)
    {
        $userData = User::select(config('constants.USER_SELECT_FIELDS'))
            ->withCount('activeSubscription')->with([
                'userServicesProfile',
                'userServicesProfile.serviceImages',
                'userServicesProfile.serviceBusinessHour',
                'userServicesProfile.category' => function ($q) {
                    $q->select('id', 'name', 'icon');
                },
                'userServicesProfile.state' => function ($q) {
                    $q->select('id', 'name');
                },
                'userServicesProfile.city' => function ($q) {
                    $q->select('id', 'name');
                },
                'userServicesProfile.locality' => function ($q) {
                    $q->select('id', 'name');
                },
                'kycDocument' => function ($q) {
                    $q->orderBy('id', 'DESC');
                },
                'activeSubscription'
            ])
            ->where('id', $id)->first();

        $updateResponse = [];
        if (!empty($userData)) {
            if (!empty($userData->userServicesProfile)) {
                if (!empty($userData->userServicesProfile->sub_category_id)) {
                    $subCategoryId = json_decode($userData->userServicesProfile->sub_category_id);
                    $subCategory = Category::select('id', 'name')->whereIn('id', $subCategoryId)->get();
                    $userData->userServicesProfile->sub_category = $subCategory;
                }
                if (!empty($userData->userServicesProfile->serviceImages)) {
                    $responseImage = [];
                    foreach ($userData->userServicesProfile->serviceImages as $serviceImage) {
                        $responseImage[] = $serviceImage->name;
                    }
                    unset($userData->userServicesProfile->serviceImages);
                    $userData->userServicesProfile->serviceImage = $responseImage;
                }
            }

            if (!empty($userData->activeSubscription) && $userData->active_subscription_count > 0) {
                $subscription['start_date'] = strtotime($userData->activeSubscription->start_date);
                $subscription['end_date'] = strtotime($userData->activeSubscription->end_date);
            } else {
                $subscription = null;
            }
            unset($userData->activeSubscription);
            $userData->subscription = $subscription;
            $updateResponse = $userData;
        }
        return $updateResponse;
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return obj
     */
    public static function store(Request $request)
    {
        $user = new User();
        $user->username = $request->input('username', false);
        $user->parent_id = $request->input('parent_id', 0);
        $user->name = $request->input('name', false);
        $user->email = $request->input('email', false);
        $user->password = bcrypt('123456');
        $user->country_code = $request->input('country_code', false);
        $user->phone_number = $request->input('phone_number', false);
        $user->profile_image = $request->input('profile_image', false);
        $user->device_id = $request->input('device_id', false);
        $user->device_type = $request->input('device_type', false);
        $user->firebase_id = $request->input('firebase_id', false);
        $user->firebase_email = $request->input('firebase_email', false);
        $user->firebase_password = $request->input('firebase_password', false);
        $user->fcm_token = $request->input('fcm_token', false);
        $user->referral_code = ReferralSystem::createReferralCode(8);
        $user->status = 1;
        if ($user->save()) {
            return $user;
        } else {
            return false;
        }
    }

    /**
     * storeServiceProfile
     *
     * @param  mixed $request
     * @return obj
     */
    public static function storeServiceProfile(Request $request)
    {
        $authUserId = $request->get('Auth')->id;
        $user = User::where('id', $authUserId)->first();
        $referralCode = $user->referral_code;
        $serviceProfile = new ServiceProfile();
        $serviceProfile->user_id = $authUserId;
        $serviceProfile->category_id = $request->input('category_id', 0);
        $serviceProfile->sub_category_id = json_encode($request->input('sub_category_id'));
        $serviceProfile->service_name = $request->input('service_name', '');
        $serviceProfile->email = $request->input('email', '');
        $serviceProfile->contact_person = $request->input('contact_person', '');
        $serviceProfile->mobile_number = $request->input('mobile_number', '');
        $serviceProfile->street_name = $request->input('street_name', '');
        $serviceProfile->building_name = $request->input('building_name', '');
        $serviceProfile->pin_code = $request->input('pin_code', '');
        $serviceProfile->city_id = $request->input('city_id', 0);
        $serviceProfile->state_id = $request->input('state_id', 0);
        $serviceProfile->locality_id = $request->input('locality_id', 0);
        $serviceProfile->city = $request->input('city', '');
        $serviceProfile->state = $request->input('state', '');
        $serviceProfile->locality = $request->input('locality', '');
        $serviceProfile->website = $request->input('website', '');
        $serviceProfile->description = $request->input('description', '');
        $serviceProfile->latitude = $request->input('latitude', '');
        $serviceProfile->longitude = $request->input('longitude', '');
        $serviceProfile->status = 1;
        $serviceProfile->referral_code = $referralCode;
        $servicesProfileImages = $request->input('services_profile_images') ?? [];
        $serviceBusinessHour = $request->input('service_business_hour') ?? [];
        if ($serviceProfile->save()) {
            if (!empty($servicesProfileImages)) {
                $ordering = 1;
                foreach ($servicesProfileImages as $value) {
                    if (!empty($value)) {
                        $attachmentData = [];
                        $attachmentData['service_id'] = $serviceProfile->id;
                        $attachmentData['name'] = $value;
                        $attachmentData['ordering'] = $ordering;
                        ServiceImage::create($attachmentData);
                        $ordering++;
                    }
                }
            }
            if (!empty($serviceBusinessHour)) {
                foreach ($serviceBusinessHour as $value) {
                    if (!empty($value)) {
                        $attachmentData = [];
                        $attachmentData['service_id'] = $serviceProfile->id;
                        $attachmentData['day_name'] = $value['day_name'];
                        $attachmentData['is_open'] = $value['is_open'];
                        $attachmentData['time'] = json_encode($value['time']);
                        ServiceBusinessHour::create($attachmentData);
                    }
                }
            }
        }
        return $serviceProfile;
    }

    /**
     * updateProfile
     *
     * @param  mixed $request
     * @return obj
     */
    public static function updateProfile(Request $request)
    {
        $authUser = $request->get('Auth');
        $update = [];
        if ($request->input('name', false)) {
            $update['name'] = $request->input('name', false);
        }
        if ($request->input('email', false)) {
            $update['email'] = $request->input('email', false);
        }
        if ($request->input('bio', false)) {
            $update['bio'] = $request->input('bio', false);
        }
        if ($request->input('dob', false)) {
            $dob = $request->input('dob', false);
            $update['dob'] = $dob;
            $update['janam_din'] = date('Y-m-d', $dob);
        }
        if ($request->input('profile_image', false)) {
            $update['profile_image'] = $request->input('profile_image', false);
        }
        if ($request->input('gender', false)) {
            $update['gender'] = $request->input('gender', false);
        }
        if ($request->input('marital_status', false)) {
            $update['marital_status'] = $request->input('marital_status', false);
        }
        return User::where('id', $authUser->id)->update($update);
    }

    /**
     * updateServiceProfile
     *
     * @param  mixed $request
     * @return obj
     */
    public static function updateServiceProfile(Request $request)
    {
        $update = [];
        $serviceId = $request->input('service_id', false);
        if ($request->input('category_id', false)) {
            $update['category_id'] = $request->input('category_id', false);
        }
        if ($request->input('sub_category_id', false)) {
            $update['sub_category_id'] = json_encode($request->input('sub_category_id', false));
        }
        if ($request->input('service_name', false)) {
            $update['service_name'] = $request->input('service_name', false);
        }
        if ($request->input('email', false)) {
            $update['email'] = $request->input('email', false);
        }
        if ($request->input('contact_person', false)) {
            $update['contact_person'] = $request->input('contact_person', false);
        }
        if ($request->input('mobile_number', false)) {
            $update['mobile_number'] = $request->input('mobile_number', false);
        }
        if ($request->input('street_name', false)) {
            $update['street_name'] = $request->input('street_name', false);
        }
        if ($request->input('building_name', false)) {
            $update['building_name'] = $request->input('building_name', false);
        }
        if ($request->input('pin_code', false)) {
            $update['pin_code'] = $request->input('pin_code', false);
        }
        if ($request->input('city', false)) {
            $update['city'] = $request->input('city', false);
        }
        if ($request->input('state', false)) {
            $update['state'] = $request->input('state', false);
        }
        if ($request->input('locality', false)) {
            $update['locality'] = $request->input('locality', false);
        }
        if ($request->input('city_id', false)) {
            $update['city_id'] = $request->input('city_id', false);
        }
        if ($request->input('state_id', false)) {
            $update['state_id'] = $request->input('state_id', false);
        }
        if ($request->input('locality_id', false)) {
            $update['locality_id'] = $request->input('locality_id', false);
        }
        if ($request->input('website', false)) {
            $update['website'] = $request->input('website', false);
        }
        if ($request->input('description', false)) {
            $update['description'] = $request->input('description', false);
        }
        if ($request->input('latitude', false)) {
            $update['latitude'] = $request->input('latitude', false);
        }
        if ($request->input('longitude', false)) {
            $update['longitude'] = $request->input('longitude', false);
        }
        $servicesProfileImages = $request->input('services_profile_images') ?? [];
        if (ServiceProfile::where('id', $serviceId)->update($update)) {
            if (!empty($servicesProfileImages)) {
                ServiceImage::where([
                    'service_id' => $serviceId
                ])->delete();
                $ordering = 1;
                foreach ($servicesProfileImages as $value) {
                    if (!empty($value)) {
                        $attachmentData = [];
                        $attachmentData['service_id'] = $serviceId;
                        $attachmentData['name'] = $value;
                        $attachmentData['ordering'] = $ordering;
                        ServiceImage::create($attachmentData);
                        $ordering++;
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * checkReferralCode
     *
     * @param  mixed $request
     * @return obj
     */
    public static function checkReferralCode(Request $request)
    {
        $authUser = $request->get('Auth');
        if ($request->input('referral_code', false)) {
            return User::withCount('userSponsor')->where('referral_code', $request->input('referral_code', false))
                ->where('id', '!=', $authUser->id)->first();
        }
    }

    /**
     * activeSubscription
     *
     * @param  mixed $request
     * @return obj
     */
    public static function activeSubscription($authUserId)
    {
        return User::withCount('activeSubscription')->with('activeSubscription')->where('id', $authUserId)->first();
    }

    /**
     * listServiceProfile
     *
     * @param  mixed $request
     * @return obj
     */
    public static function listServiceProfile(Request $request)
    {
        if ($request->input('limit', false)) {
            $limit  = $request->input('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        $query = ServiceProfile::with([
            'category' => function ($q) {
                $q->select('id', 'name', 'icon');
            }, 'serviceImages'  => function ($q) {
                $q->select('id', 'name', 'service_id');
            },
        ]);
        if ($request->input('category_id', false)) {
            $categoryId  = $request->input('category_id', 0);
            $query = $query->where('category_id', $categoryId);
        }
        $serviceProfiles = $query->orderBy('id', 'desc')->paginate($limit);

        if (!empty($serviceProfiles)) {
            foreach ($serviceProfiles as $sKey => $serviceProfile) {

                if (!empty($serviceProfile->serviceImages)) {
                    $responseServiceImage = [];
                    foreach ($serviceProfile->serviceImages as $serviceImage) {
                        $responseServiceImage[] = $serviceImage->name;
                    }
                    unset($serviceProfiles[$sKey]->serviceImages);
                    $serviceProfiles[$sKey]->serviceImage = $responseServiceImage;
                }
            }
        }
        return $serviceProfiles;
    }
    
    /**
     * updateFcmUpdate
     *
     * @param  mixed $request
     * @return void
     */
    public static function updateFcmUpdate(Request $request)
    {
        $authUser = $request->get('Auth');
        if ($request->input('fcm_token', false)) {
            $update['fcm_token'] = $request->input('fcm_token', false);
            if (User::where('id', $authUser->id)->update($update)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * spamReported
     *
     * @param  mixed $request
     * @return void
     */
    public static function spamReported(Request $request)
    {
        $authUser = $request->get('Auth');
        $reportedSpam = new ReportedSpam();
        $reportedSpam->item_id = $request->input('reported_for', false);
        $reportedSpam->description = $request->input('description', false);
        $reportedSpam->type = 1;
        $reportedSpam->reported_by = $authUser->id;
        if ($reportedSpam->save()) {
            return true;
        } else {
            return false;
        }
    }
}
