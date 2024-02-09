<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Repositories\UserStatusRepository;
use Exception;


class UserStatusController extends Controller
{

    /**
     * createUserStatus
     *
     * @param  mixed $request
     * @return void
     */
    public static function createUserStatus(Request $request)
    {
        $data = [];
        try {
            $response = UserStatusRepository::create($request);
            if ($response['status']) {
                $postResponse = UserStatusRepository::myStories($request);;
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('save_records');
                $data['data'] = $postResponse;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                if (isset($response['data']) && !empty($response['data'])) {
                    $data['message'] = $response['data'];
                } else {
                    $data['message'] = ApiGlobalFunctions::messageDefault('oops');
                }
            }
        } catch (Exception $e) {
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
     * getUserStatus
     *
     * @param  mixed $request
     * @return void
     */
    public function getUserStatus(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            $myStoriesResponse = UserStatusRepository::myStories($request);
            $friendsStoriesResponse = UserStatusRepository::friendsStories($request);
            if (!empty($friendsStoriesResponse) || !empty($myStoriesResponse)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = [
                    'myStories' => $myStoriesResponse,
                    'friendsStories' => $friendsStoriesResponse
                ];
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
     * myStatus
     *
     * @param  mixed $request
     * @return void
     */
    public function myStatus(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            $myStoriesResponse = UserStatusRepository::myStories($request);
            if (!empty($myStoriesResponse)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] =  $myStoriesResponse;
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
     * deleteStatus
     *
     * @param  mixed $request
     * @return void
     */
    public function deleteStatus(Request $request)
    {
        $data = [];
        try {
            if (UserStatusRepository::deleteStatus($request)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('records_delete');
                $data['data'] = [];
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
     * statusView
     *
     * @param  mixed $request
     * @return void
     */
    public function statusView(Request $request)
    {
        $data = [];
        try {
            if (UserStatusRepository::statusViewUpdate($request)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('update_successfully');
                $data['data'] = [];
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
     * statusViewList
     *
     * @param  mixed $request
     * @return void
     */
    public function statusViewList(Request $request)
    {
        $data = [];
        try {
            $statusViewList = UserStatusRepository::statusViewList($request);
            if ($statusViewList->count() > 0) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('update_successfully');
                $data['data'] = [
                    'count' => $statusViewList->count(),
                    'list' => $statusViewList,
                ];
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

}
