<?php

namespace App\Repositories;

use App\Models\UserStatus;
use App\Models\UserStatusMedia;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function is;
use function is_null;
use Exception;

class UserStatusRepository
{

    /**
     * getSingle
     *
     * @param  mixed $request
     * @return obj
     */
    public static function create(Request $request)
    {

        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            $userStatus = new UserStatus();
            $userStatus->user_id = $authUser->id;
            $userStatus->status_type = $statusType = $request->input('status_type', false);
            $userStatus->status_text = $request->input('status_text', false);
            $userStatusMedias = $request->input('user_status_medias') ?? [];
            if ($userStatus->save()) {
                if (!empty($userStatusMedias) && $statusType != 'text') {
                    $ordering = 1;
                    foreach ($userStatusMedias as $value) {
                        if (!empty($value)) {
                            $attachmentData = [];
                            $attachmentData['status_id'] = $userStatus->id;
                            $attachmentData['name'] = $value;
                            $attachmentData['ordering'] = $ordering;
                            UserStatusMedia::create($attachmentData);
                            $ordering++;
                        }
                    }
                }
                DB::commit();
                return [
                    'status' => true,
                    'data' => $userStatus
                ];
            } else {
                DB::rollBack();
                return [
                    'status' => false,
                    'data' => []
                ];;
            }
        } catch (Exception $e) {
            DB::rollBack();
            return [
                'status' => false,
                'data' => $e->getMessage()
            ];
        }
    }

    /**
     * getSingle
     *
     * @param  mixed $request
     * @return obj
     */
    public static function getSingle($statusId)
    {
        $query = UserStatus::status()
            ->with([
                'userStatusMedia',
                'user',
                'user.userServicesProfile',
                'user.userServicesProfile.serviceImages'
            ]);

        $query = $query->where('id', $statusId);
        $statusData = $query->first();
        if (!empty($statusData->userStatusMedia)) {
            $responseImage = [];
            foreach ($statusData->userStatusMedia as $userStatusMedia) {
                $responseImage[] = $userStatusMedia->name;
            }
            unset($statusData->userStatusMedia);
            $statusData->userStatusMedias = $responseImage;
        }
        if (!empty($statusData->user->userServicesProfile->serviceImages)) {
            $responseServiceImage = [];
            foreach ($statusData->user->userServicesProfile->serviceImages as $serviceImage) {
                $responseServiceImage[] = $serviceImage->name;
            }
            unset($statusData->user->userServicesProfile->serviceImages);
            $statusData->user->userServicesProfile->serviceImages = $responseServiceImage;
        }
        return $statusData;
    }


    /**
     * list
     *
     * @param  mixed $request
     * @return void
     */
    public static function list(Request $request)
    {
        if ($request->input('limit', false)) {
            $limit  = $request->input('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        $query = UserStatus::status()
            ->with([
                'userStatusMedia',
                'user',
                'user.userServicesProfile',
                'user.userServicesProfile.serviceImages'
            ]);
        $userStatus = $query->orderBy('id', 'desc')->paginate($limit);
        // dd($userStatus);
        if (!empty($userStatus)) {
            foreach ($userStatus as $sKey => $status) {
                if (!empty($status->userStatusMedia)) {
                    $responseMedia = [];
                    foreach ($status->userStatusMedia as $userStatusMedia) {
                        $responseMedia[] = $userStatusMedia->name;
                    }
                    unset($userStatus[$sKey]->userStatusMedia);
                    $userStatus[$sKey]->userStatusMedias = $responseMedia;
                }
                if (!empty($status->user->userServicesProfile->serviceImages)) {
                    $responseServiceImage = [];
                    foreach ($status->user->userServicesProfile->serviceImages as $serviceImage) {
                        $responseServiceImage[] = $serviceImage->name;
                    }
                    unset($userStatus[$sKey]->user->userServicesProfile->serviceImages);
                    $userStatus[$sKey]->user->userServicesProfile->serviceImage = $responseServiceImage;
                }
            }
        }
        return $userStatus;
    }

    /**
     * deleteProduct
     *
     * @param  mixed $request
     * @return void
     */
    public static function deleteProduct(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            if ($request->input('product_id', false) > 0) {
                $productId = $request->input('product_id', false);
                Product::where([
                    'id' => $productId,
                    'user_id' => $authUser->id,
                ])->delete();
                DB::commit();
                return true;
            } else {
                DB::rollBack();
                return false;
            }
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
