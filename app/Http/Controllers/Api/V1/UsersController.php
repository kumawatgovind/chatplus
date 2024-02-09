<?php

namespace App\Http\Controllers\Api\V1;

use App\Helpers\ReferralSystem;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Sponsor;
use App\Repositories\SponsorRepository;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Mail, Exception, Auth, Hash, Session;

class UsersController extends Controller
{

    /**
     * Check Phone Number in database.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return []
     */
    public function checkPhoneNumber(Request $request)
    {
        $data = [];
        try {
            $validator = Validator::make($request->all(), [
                'country_code' => 'required',
                'phone_number' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $phoneNumber = $request->phone_number;
            $countryCode = $request->country_code;
            $verifiedMobile = User::where([
                'country_code' => $countryCode,
                'phone_number' => $phoneNumber,
                'status' => 1
            ])->first();
            if ($verifiedMobile) {
                if ($verifiedMobile->is_block) {
                    $data['status'] = false;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('block_by_admin');
                } else {
                    $updateData = [];
                    $updateData['device_id'] = isset($request->device_id) ? str_replace('"', '', $request->device_id) : "";
                    $updateData['device_type'] = isset($request->device_type) ? $request->device_type : "";
                    $updateData['api_token'] = $verifiedMobile->createToken(env('APP_NAME'))->plainTextToken;
                    User::where('id', $verifiedMobile->id)->update($updateData);
                    $updateResponse = UserRepository::getUser($verifiedMobile->id);
                    $data['status'] = true;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('mobile_verified');
                    $data['data'] = $updateResponse;
                }
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('verification_failed');
            }
        } catch (Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }


    /**
     * Check UserName in database.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return []
     */
    public function checkUsername(Request $request)
    {
        $data = [];
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $userName = $request->request->get('username', false);
            $checkUserName = User::where([
                'username' => $userName,
                'status' => 1,
            ])->first();
            if (!empty($checkUserName)) {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('username_not_available');
            } else {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('username_available');
                $data['data'] = $checkUserName;
            }
        } catch (Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * create Profile.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return [].
     */
    public function register(Request $request)
    {
        $data = [];
        try {
            $validator = Validator::make($request->all(), [
                // 'username' => 'required|unique:users,username',
                'name' => 'required|min:3',
                'email' => 'nullable|email',
                'country_code' => 'required',
                'phone_number' => 'required|unique:users,phone_number',
                'profile_image' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $updateData = [];
            if ($user = UserRepository::store($request)) {
                $updateData['api_token'] = $user->createToken(env('APP_NAME'))->plainTextToken;
                User::where('id', $user->id)->update($updateData);
                $userData = UserRepository::getUser($user->id);
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('profile_created');
                $data['data'] = $userData;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('profile_not_created');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * createServiceProfile.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return [].
     */
    public function createServiceProfile(Request $request)
    {
        $data = [];
        try {
            // $validator = Validator::make($request->all(), [
            //     'username' => 'required|unique:users,username',
            //     'name' => 'required|min:3',
            //     'email' => 'nullable|email',
            //     'country_code' => 'required',
            //     'phone_number' => 'required',
            //     'profile_image' => 'nullable',
            // ]);
            // if ($validator->fails()) {
            //     return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            // }
            if (UserRepository::storeServiceProfile($request)) {
                $authUserId = $request->get('Auth')->id;
                $updateResponse = UserRepository::getUser($authUserId);
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('profile_created');
                $data['data'] = $updateResponse;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('profile_not_created');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }


    /**
     * getProfile
     *
     * @param  mixed $request
     * @return void
     */
    public function getProfile(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            $validator = (object) Validator::make($request->all(), [
                'user_id' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            if ($user->id) {
                $userId = $request->input('user_id', 0);
                if ($userId > 0) {
                    $userId = $request->input('user_id');
                } else {
                    $userId = $user->id;
                }
                $userDetails = UserRepository::getUser($userId);
                if (!empty($userDetails)) {
                    $data['status'] = true;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('profile_get');
                    $data['data'] = $userDetails;
                } else {
                    $data['status'] = false;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('list_not_found');
                }
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('invalid_request');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }



    /**
     * updateProfile
     *
     * @param  mixed $request
     * @return void
     */
    public function updateProfile(Request $request)
    {
        $data = $update = [];
        try {
            // $validator = (object) Validator::make($request->all(), [
            // ]);
            // if ($validator->fails()) {
            //     return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            // }
            $authUser = $request->get('Auth');
            if (UserRepository::updateProfile($request)) {
                $userDetails = UserRepository::getUser($authUser->id);
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('profile_edit');
                $data['data'] = $userDetails;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('invalid_request');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * updateServiceProfile.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return [].
     */
    public function updateServiceProfile(Request $request)
    {
        $data = $update = [];
        try {
            // $validator = Validator::make($request->all(), [
            //     'username' => 'required|unique:users,username',
            //     'name' => 'required|min:3',
            //     'email' => 'nullable|email',
            //     'country_code' => 'required',
            //     'phone_number' => 'required',
            //     'profile_image' => 'nullable',
            // ]);
            // if ($validator->fails()) {
            //     return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            // }
            if (UserRepository::updateServiceProfile($request)) {
                $authUser = $request->get('Auth');
                $userDetails = UserRepository::getUser($authUser->id);
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('profile_edit');
                $data['data'] = $userDetails;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('invalid_request');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * checkReferralCode.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return [].
     */
    public function checkReferralCode(Request $request)
    {
        $data = $responseData = [];
        try {
            $validator = (object) Validator::make($request->all(), [
                'referral_code' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            if ($referralProfile = UserRepository::checkReferralCode($request)) {
                $responseData['name'] = $referralProfile->name;
                $responseData['referral_code'] = $referralProfile->referral_code;
                $responseData['referral_code_expire'] = false;
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('payment_request');
                $data['data'] = $responseData;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('referral_code_notActive');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /* Get Service Profiles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return []
     */
    public function getServiceProfiles(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            $serviceResponse = UserRepository::listServiceProfile($request);
            if (!empty($serviceResponse)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $serviceResponse;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('list_not_found');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }


    /**
     * updateFcmUpdate
     *
     * @param  mixed $request
     * @return void
     */
    public function updateFcmUpdate(Request $request)
    {
        $data = $update = [];
        try {
            // $validator = Validator::make($request->all(), [
            //     'fcm' => 'required',
            // ]);
            // if ($validator->fails()) {
            //     return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            // }
            if (UserRepository::updateFcmUpdate($request)) {
                $authUser = $request->get('Auth');
                $userDetails = UserRepository::getUser($authUser->id);
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('profile_edit');
                $data['data'] = $userDetails;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('invalid_request');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }


    /* Get User Level.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return []
     */
    public function getUserLevel(Request $request)
    {
        $data = [];
        try {
            $authUser = $request->get('Auth');
            $userId = $authUser->id;
            if ($request->input('user_id', false)) {
                $userId = $request->input('user_id', false);
            }
            $sponsorUser = Sponsor::where('sponsored_user_id', $userId)->select('sponsor_user_id')->first();

            if ($sponsorUser) {
                $level = ReferralSystem::getCheckCurrentLevel($userId);
            } else {
                $level = 'N/A';
            }
            $sponsorsDetail = SponsorRepository::getSponsors($userId);
            if ($level) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = [
                    'level' => $level,
                    'sponsorsDetail' => $sponsorsDetail,
                ];
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('list_not_found');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /* myReferrals.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return []
     */
    public function myReferrals(Request $request)
    {
        $data = [];
        try {
            $authUser = $request->get('Auth');
            $userId = $authUser->id;
            if ($request->input('user_id', false)) {
                $userId = $request->input('user_id', false);
            }
            $sponsorsDetail = SponsorRepository::getMySponsors($request);
            if (true) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $sponsorsDetail;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('list_not_found');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /* sponsorsHistory.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return []
     */
    public function sponsorsHistory(Request $request)
    {
        $data = [];
        try {
            $sponsorsDetail = SponsorRepository::getSponsorsHistory($request);
            if (true) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $sponsorsDetail;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('list_not_found');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /* transactionHistory.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return []
     */
    public function transactionHistory(Request $request)
    {
        $data = [];
        try {
            $sponsorsDetail = SponsorRepository::getTransactionHistory($request);
            extract($sponsorsDetail);
            if ($statusTransaction) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $dataTransaction;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('list_not_found');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /**
     * spamReported
     *
     * @param  mixed $request
     * @return void
     */
    public function spamReported(Request $request)
    {
        $data = $update = [];
        try {
            // $validator = Validator::make($request->all(), [
            //     'reported_for' => 'required',
            // ]);
            // if ($validator->fails()) {
            //     return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            // }
            if (UserRepository::spamReported($request)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('save_records');
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('invalid_request');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    /* Message Conversation list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return messageConversationList
     */
    public function messageConversationList(Request $request)
    {
        $user = $request->get('Auth');
        $data = $request->all();
        try {

            //validate the input data in api
            $validator = Validator::make($request->all(), [
                'discussion_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->errors()->first(), '200');
            }

            $discussionChat = Discussion::with([
                'discussionMsg', 'advertisement:id,title,slug,user_id', 'sender:id,first_name,profile_picture,last_name',
                'receiver:id,first_name,profile_picture,last_name',
            ])->distinct('advertisement_id')->orWhere(['receiver_id' => $user->id, 'sender_id' => $user->id])->orderBy('created_at', 'DESC')->where(['id' => $data['discussion_id']])->first();

            if (!empty($discussionChat)) {
                foreach ($discussionChat->discussionMsg as $key => $discussionVal) {
                    if (!empty($discussionVal->attachment)) {
                        $attachmentArray = json_decode($discussionVal->attachment, true);

                        if (!empty($attachmentArray)) {
                            $attArray = array();
                            $attNameArray = array();
                            foreach ($attachmentArray as $aKey => $aAttachment) {
                                $attArray[] = asset('storage/discussion/' . $aAttachment);
                                $attNameArray[] = $aAttachment;
                            }
                            $discussionChat->discussionMsg[$key]['attachment'] = $attNameArray;
                            $discussionChat->discussionMsg[$key]['attachments'] = $attArray;
                        }
                    }
                }
            }
            if (!empty($discussionChat)) {
                return ApiGlobalFunctions::sendResponse($discussionChat, ApiGlobalFunctions::messageDefault('list found successfully.'));
            } else {
                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('list not found.'), '', '200');
            }
        } catch (\Exception $e) {

            return ApiGlobalFunctions::sendError($e->getMessage());
        }
    }


    public function sendInternalMessagingText(Request $request)
    {
        $user = $request->get('Auth');
        try {
            //   validate the input data in api
            $validator = Validator::make($request->all(), [
                //   'discussion_id'   => 'required',
                'receiver_id' => 'required',
                //    'message'         => 'required|string',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->errors()->first(), '200');
            }
            $data = $request->all();
            $sender_id = $user->id;
            // $sender_id = 91;
            $receiver_id = $request->receiver_id;
            $advertisement_id = $request->advertisement_id;
            //  $chat = 0;
            // $chat = Discussion::where('sender_id', $sender_id)->where('receiver_id', $data['receiver_id'])->first();
            // if(!empty($chat)){
            //    // dd($chat->toArray());
            //     $chat->advertisement_id   = isset($data['advertisement_id']) ? $data['advertisement_id'] : $chat->advertisement_id;
            //     $chat->created_at = Carbon::now();
            // }
            // else
            // if (isset($data['advertisement_id']) && isset($data['discussion_id'])) {
            //    // dd("a");
            //     $discussion_id = $data['discussion_id'];
            //     $chat = Discussion::where('advertisement_id', $data['advertisement_id'])->where('id', $data['discussion_id'])->first();
            //     $chat->receiver_id = $receiver_id;
            //     $chat->sender_id = $sender_id;
            //     $chat->advertisement_id = $data['advertisement_id'];
            //     $chat->status = 1;
            //     $chat->created_at = Carbon::now();
            // } else

            if (isset($data['discussion_id'])) {
                $chat = Discussion::find($data['discussion_id']);

                if (!empty($chat)) {
                    // $chat->advertisement_id   = isset($data['advertisement_id']) ? $data['advertisement_id'] : "";
                    $chat->created_at = Carbon::now();
                } else {
                    return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('Discussion id does not exist.'), '', '200');
                }
            } else {
                $validator = Validator::make($request->all(), [
                    'advertisement_id' => 'required|numeric',
                ]);
                if ($validator->fails()) {
                    return ApiGlobalFunctions::sendError('Validation Error.', $validator->errors()->first(), '200');
                }
                $chat = new Discussion;
                $chat->receiver_id = $receiver_id;
                $chat->sender_id = $sender_id;
                $chat->advertisement_id = $data['advertisement_id'];
                $chat->status = 1;
            }
            if ($chat->save()) {
                $count = ($request->hasFile('attachment')) ? count($request->file('attachment')) : 0;
                /*For message only*/
                if ($count == 0) {
                    $chatHistory = new DiscussionMessage;
                    $chatHistory->receiver_id = $receiver_id;
                    $chatHistory->discussion_id = $chat->id;
                    $chatHistory->sender_id = $sender_id;
                    if (!empty($request->message)) {
                        $chatHistory->message = $request->message;
                    }
                    //  $chatHistory->message          = isset($request->message) ? $request->message : 0;
                    $chatHistory->save();
                } else {
                    /*For message with multiple attach only*/
                    if ($count > 0) {
                        if ($request->hasFile('attachment')) {
                            $chatHistory = new DiscussionMessage;
                            $chatHistory->receiver_id = $receiver_id;
                            $chatHistory->discussion_id = $chat->id;
                            $chatHistory->sender_id = $sender_id;
                            $chatHistory->message = $request->message;
                            $attached = [];
                            $attArray = array();
                            $attNameArray = array();
                            foreach ($request->file('attachment') as $key => $file) {
                                $filename = random_int(1000, 9999) . time() . '.' . $file->guessExtension();
                                $path = $file->storeAs(
                                    'public/discussion',
                                    $filename
                                );
                                $attached[] = $filename;
                                $attArray[] = asset('storage/discussion/' . $filename);
                                $attNameArray[] = $filename;
                            }
                            $chatHistory->attachment = json_encode($attached, true);
                            $chatHistory->save();
                        }
                        $chatHistory->attachment_new = $attArray;
                    }
                }
            }
            $chatid = ($chat->id) ? $chat->id : 0;

            if (!empty($chatHistory)) {
                ApiGlobalFunctions::sendNotificationForReceivingMessage($receiver_id, $sender_id, $chatid);
                return ApiGlobalFunctions::sendResponse($chatHistory, ApiGlobalFunctions::messageDefault('Message Sent Successfully.'));
            } else {
                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('Message Not Sent Successfully.'), '', '200');
            }

            /*For message with multiple attach only*/
        } catch (\Exception $e) {

            return ApiGlobalFunctions::sendError($e->getMessage());
        }
    }

    public function faqList(Request $request)
    {
        $input = $request->all();
        try {

            $faqs = Faq::where('status', 1)->get();
            if (count($faqs) > 0) {
                return ApiGlobalFunctions::sendResponse($faqs, ApiGlobalFunctions::messageDefault('Faq list found successfully.'));
            } else {
                return ApiGlobalFunctions::sendResponse((object) [], ApiGlobalFunctions::messageDefault('Faq not found.'));
            }
        } catch (\Exception $e) {
            return ApiGlobalFunctions::sendError($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        $data = [];
        try {
            $user = $request->get('Auth');
            $result = User::where('id', $user->id)->update([
                'api_token' => '',
                'device_type' => '',
                'device_id' => '',
                'fcm_token' => ''
            ]);
            if ($result) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = 'Logout successfully.';
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('invalid_request');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    public function removeUser(Request $request)
    {
        $data = [];
        try {
            $user = $request->get('Auth');
            $result = User::where('id', $user->id)->delete();
            if ($result) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = 'Removed successfully.';
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('invalid_request');
            }
        } catch (\Exception $e) {
            $data['status'] = false;
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }

    public function userCheck(Request $request)
    {
        $settings = User::get();
        return ApiGlobalFunctions::sendResponse($settings, ApiGlobalFunctions::messageDefault('List found successfully.'));
    }
}
