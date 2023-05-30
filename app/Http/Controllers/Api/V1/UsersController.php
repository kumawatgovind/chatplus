<?php

namespace App\Http\Controllers\Api\V1;

use App\Traits\ApiGlobalFunctions;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Page;
use App\Models\User;
use App\Models\ContactSync;
use Carbon\Carbon;
use Mail, Exception, DB;

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
            if (!empty($verifiedMobile)) {
                $updateData = [];
                $updateData['device_id'] = isset($request->device_id) ? str_replace('"', '', $request->device_id) : "";
                $updateData['device_type'] = isset($request->device_type) ? $request->device_type : "";
                $updateData['api_token'] = $verifiedMobile->createToken(env('APP_NAME'))->plainTextToken;
                User::where('id', $verifiedMobile->id)->update($updateData);
                $userData = User::select(config('constants.USER_SELECT_FIELDS'))
                ->with(['profiles' => function ($query) {
                    $query->select(config('constants.USER_SELECT_FIELDS'));
                }])
                ->where('id', $verifiedMobile->id)->first();
                $updateResponse = [];
                if (!empty($userData)) { 
                    if ($userData->profiles) {
                        foreach ($userData->profiles as $profile) {
                            $updateResponse[] = $profile;
                        }
                        unset($userData->profiles);
                    }
                    $updateResponse[] = $userData;
                }
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('mobile_verified');
                $data['data'] = $updateResponse;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('verification_failed');
                $data['data'] = [];
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
                $data['data'] = [];
            } else {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('username_available');
                $data['data'] = [];
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
     * create Profile.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return [].
     */
    public function createProfile(Request $request)
    {
        $data = [];
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|unique:users,username',
                'name' => 'required|min:3',
                'email' => 'nullable|email',
                'country_code' => 'required',
                'phone_number' => 'required',
                'profile_image' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $user = new User();
            $user->username = $request->request->get('username', false);
            $user->parent_id = $request->request->get('parent_id', 0);
            $user->name = $request->request->get('name', false);
            $user->email = $request->request->get('email', false);
            $user->password = bcrypt('123456');
            $user->country_code = $request->request->get('country_code', false);
            $user->phone_number = $request->request->get('phone_number', false);
            $user->profile_image = $request->request->get('profile_image', false);
            $user->device_id = $request->request->get('device_id', false);
            $user->device_type = $request->request->get('device_type', false);
            $user->firebase_email = $request->request->get('firebase_email', false);
            $user->firebase_password = $request->request->get('firebase_password', false);
            $user->uId = $request->request->get('uId', false);
            $user->status = 1;
            $updateData = [];
            if ($user->save()) {
                $updateData['api_token'] = $user->createToken(env('APP_NAME'))->plainTextToken;
                User::where('id', $user->id)->update($updateData);
                $userData = User::select(config('constants.USER_SELECT_FIELDS'))->where('id', $user->id)->first();
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('profile_created');
                $data['data'] = $userData;
            } else {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('profile_not_created');
                $data['data'] = [];
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
     * uploadDocument.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return [].
     */
    public function uploadDocument(Request $request)
    {
        $data = [];
        try {
            $validator = Validator::make($request->all(), [
                'upload_file' => 'required',
                'type' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $type = $request->request->get('type', false);
            $responseData = [];
            if (isset($_FILES['upload_file']['name']) && $_FILES['upload_file']['name'] != '') {
                switch ($type) {
                    case 'profile_image':
                        $responseData[$type] = self::fileUpload($request);
                        break;
                    case 'pan_card':
                        $responseData[$type] = self::fileUpload($request);
                        break;
                    case 'passport':
                        $responseData[$type] = self::fileUpload($request);
                        break;
                    case 'dl_front':
                        $responseData[$type] = self::fileUpload($request);
                        break;
                    case 'dl_back':
                        $responseData[$type] = self::fileUpload($request);
                        break;
                    case 'aadhar_front':
                        $responseData[$type] = self::fileUpload($request);
                        break;
                    case 'aadhar_back':
                        $responseData[$type] = self::fileUpload($request);
                        break;
                    case 'other':
                        $responseData[$type] = self::fileUpload($request);
                        break;
                    default:
                        $responseData['no_file'] = '';
                }
            }
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = ApiGlobalFunctions::messageDefault('file_upload');
            $data['data'] = $responseData;
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
     * fileUpload.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return [].
     * use in ApiGlobalFunctions::uploadDocument
     */
    public static function fileUpload(Request $request)
    {
        $data = [];
        try {
            $path = '';
            $type = $request->request->get('type', false);
            $fileName = $request->file('upload_file');
            $upload_file = time() . '.' . $fileName->getClientOriginalExtension();
            if ($type == 'profile_picture') {
                $path = asset('storage/profile_images/');
                $fileName->move(storage_path('app/public/profile_images/'), $upload_file);
            } else {
                $path = asset('storage/documents/');
                $fileName->move(storage_path('app/public/documents/'), $upload_file);
            }
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = ApiGlobalFunctions::messageDefault('file_upload');
            $data['data'] =  [
                'upload_file' => $upload_file,
                'upload_path' => $path,
            ];
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
     * contactSync
     *
     * @param  mixed $request
     * @return void
     */
    public function contactSync(Request $request)
    {
        $data = [];
        try {
            $user = $request->get('Auth');
            $validator = Validator::make($request->all(), [
                'contacts' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $contacts = $contactData = (array) $request->request->get('contacts');
            $phoneNumbers = array_column($contacts, 'number');

            $existUsers = User::whereIn('phone_number', $phoneNumbers)->where('parent_id', 0)->get();

            if (!empty($contacts)) {
                foreach ($contacts as $cKey => $contact) {
                    $contacts[$cKey]['user_id'] = $user->id;
                    $contacts[$cKey]['cid'] = $contact['id'];
                    unset($contacts[$cKey]['id']);
                    $contactData[$cKey]['exist'] = false;
                    $contactData[$cKey]['profile'] = (object) [];
                    if ($existUsers->count() > 0) {
                        foreach ($existUsers as $user) {
                            if ($contact['number'] == $user->phone_number) {
                                $contactData[$cKey]['exist'] = true;
                                $contactData[$cKey]['profile'] = $user;
                            }
                        }
                    }
                }
            }
            $uniquely = ['number'];
            $update = ['user_id', 'code', 'cid', 'name'];
            ContactSync::upsert($contacts, $update, $uniquely);
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
            $data['data'] = $contactData;
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
     * follwUserRequest
     *
     * @param  mixed $request
     * @return void
     */
    public function follwUserRequest(Request $request)
    {
        $data = [];
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $authUser = $request->get('Auth');
            if ($authUser->id == $request->user_id) {
                $data['status'] = false;
                $data['code'] =  config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('self_follow_not');
                return ApiGlobalFunctions::responseBuilder($data);
            }

            $authUserObj = User::find($authUser->id);
            $user = User::find($request->user_id);
            $response = $authUserObj->toggleFollow($user);
            $status = false;
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = ApiGlobalFunctions::messageDefault('unfollow_update');
            if (!empty($response['attached'])) {
                $status = true;
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('follow_update');
            }
            $data['data'] = [
                'user_id' => $user->id,
                'followers_count' => $authUserObj->followers->count(),
                'following_count' => $authUserObj->followings->count(),
                'follow_status' => $status,
            ];
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
     * getFollowerFollowing
     *
     * @param  mixed $request
     * @return void
     */
    public function getFollowerFollowing(Request $request)
    {
        $data = $response = [];
        try {
            $authUser = $request->get('Auth');
            $authUserObj = User::find($authUser->id);
            $response = [
            'followers_count' => $authUserObj->followers->count(),
            'followers' => $authUserObj->followers,
            'following_count' => $authUserObj->followings->count(),
            'following' => $authUserObj->followings,
            ];
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
            $data['data'] = $response;
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
                $userId = $request->request->get('user_id', 0);
                if ($userId > 0) {
                    $userId = $request->request->get('user_id');
                } else {
                    $userId = $user->id;
                }
                $userDeatils = User::withCount(['followers','followings'])->where('id', $userId)->first();
                if (!empty($userDeatils)) {
                    $data['status'] = true;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('profile_get');
                    $data['data'] = $userDeatils;
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
     * updateProfile
     *
     * @param  mixed $request
     * @return void
     */
    public function updateProfile(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            $validator = (object) Validator::make($request->all(), [
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $dob = $request->request->get('dob', false);
            $update['name'] = $request->request->get('name', false);
            $update['bio'] = $request->request->get('bio', false);
            $update['website'] = $request->request->get('website', false);
            $update['dob'] = date('Y-m-d', $dob);
            $update['janam_din'] = $dob;
            $update['cover_image'] = $request->request->get('cover_image', false);
            $update['profile_image'] = $request->request->get('profile_image', false);
           
            if(User::where('id', $user->id)->update($update)) {
                $userDeatils = User::where('id', $user->id)->first();
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('profile_edit');
                $data['data'] = $userDeatils;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('invalid_request');
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


    public function login(Request $request)
    {

        $input = $request->all();
        $data = [];
        try {
            $validator = Validator::make($input, ['email' => 'required', 'password' => 'required', 'device_type' => 'required']);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->errors()->first(), '200');
            } else {
                $query = User::query();
                $email_exist = $query->where('email', request('email'))->count();
                if ($email_exist == 0) {
                    return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('invalid_login'), '', '200');
                } elseif (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
                    $user = Auth::user();
                    if (!$user->status) {
                        return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('not_active') . 'support for assistance.', '', '200');
                    } elseif (!$user->email_verified_at) {
                        return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('not_verified'), '', '200');
                    } else {
                        $input = $request->all();
                        $data = $user;
                        $data['device_id'] = isset($input['device_id']) ? str_replace('"', '', $input['device_id']) : $data['device_id'];
                        $data['device_type'] = isset($input['device_type']) ? $input['device_type'] : $data['device_type'];
                        $data['api_token'] = $user->createToken('Laravel')->plainTextToken;
                        User::where('id', $user->id)->update(['api_token' => $data['api_token'], 'device_id' => $data['device_id'], 'device_type' => $data['device_type']]);
                        $userData = User::where('id', $user->id)->first();
                        return ApiGlobalFunctions::sendResponse($userData, ApiGlobalFunctions::messageDefault('login_success'));
                    }
                } else {
                    return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('invalid_login'), '', '200');
                }
            }
        } catch (\Exception $e) {

            return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('oops'), '', '200');
        }
    }


    public function forgotPassword(Request $request)
    {
        $input = $request->all();
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
            ], [

                'email.required' => 'Please provide email id.',

            ]);
            if ($validator->fails()) {

                return ApiGlobalFunctions::sendError('Validation Error.', $validator->errors()->first(), '200');
            }
            $query = User::where(['email' => $request->email]);
            if ($query->exists()) {
                $user = $query->first();
                if (!$user->status) {
                    return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('not_activated'), '', '200');
                } elseif (!$user->email_verified_at) {
                    return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('not_verified'), '', '200');
                }
                $code = rand(111111, 999999);
                $result = User::where('id', $user->id)->update(['verification_code' => $code]);
                $hook = "forgot-password-verification-code";
                $replacement['RESET_CODE'] = $code;
                $data = ['template' => $hook, 'hooksVars' => $replacement];
                Mail::to($user->email)->send(new \App\Mail\ManuMailer($data));
                $data = (object) [];
                return ApiGlobalFunctions::sendResponse($data, ApiGlobalFunctions::messageDefault('Your reset password code has been sent to your registered email.'));
            } else {

                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('This email address is not registered with us.'), '', '200');
            }
        } catch (\Exception $e) {
            return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('oops'), '', '200');
        }
    }

    public function resetPassword(Request $request)
    {
        $input = $request->all();
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required',
                'password' => 'required',
                'verification_code' => 'required',
            ], [
                'email.required' => 'Please provide email id.',
                'password.required' => 'Please provide email id.',
                'verification_code.required' => 'Please provide verification code.',

            ]);

            if ($validator->fails()) {

                return ApiGlobalFunctions::sendError('Validation Error.', $validator->errors()->first(), '200');
            }

            $query = User::where(['email' => $request->email, 'verification_code' => $request->verification_code]);

            if ($query->exists()) {

                $user = $query->first();

                // Check if user is active

                if (!$user->status) {

                    return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('not_activated'), '', '200');
                } elseif (!$user->email_verified_at) {

                    return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('not_verified'), '', '200');
                }

                $password = bcrypt($request->password);

                $result = User::where('id', $user->id)->update(['password' => $password]);

                $data = (object) [];

                return ApiGlobalFunctions::sendResponse($data, ApiGlobalFunctions::messageDefault('Your Password Changed Successfully.'));
            } else {

                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('This code not valid'), '', '200');
            }
        } catch (\Exception $e) {

            return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('oops'), '', '200');
        }
    }

    public function changePassword(Request $request)
    {

        $input = $request->all();

        $user = $request->get('Auth');

        $validator = Validator::make($request->all(), [

            'old_password' => 'required',

            'new_password' => 'required|min:6',

        ], [

            'old_password.required' => 'Please provide your old password.',

            'new_password.required' => 'Please provide your new password.',

            'new_password.min' => 'Password length should be more than 6 character.',

        ]);

        if ($validator->fails()) {

            return ApiGlobalFunctions::sendError('Validation Error.', $validator->errors()->first(), '200');
        }

        try {

            $user = User::find($user->id);

            $old_password = $user->password;

            $user->password = bcrypt($request->new_password);

            $data = (object) [];

            if (strcmp($request->old_password, $request->new_password) == 0) {

                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('New password cannot be same as your current password.'), '', '200');
            } elseif (!Hash::check($request->old_password, $old_password)) {

                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('The current password is incorrect.'), '', '200');
            } elseif ($user->save()) {

                return ApiGlobalFunctions::sendResponse($data, ApiGlobalFunctions::messageDefault('change_password_success'));
            } else {

                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('process_failed'), '', '200');
            }
        } catch (\Exception $e) {

            return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('oops'));
        }
    }

    public function homeList(Request $request)
    {
        $color_codes = config('constants.CATEGORE_COLOR_CODE');
        $data = $request->all();
        $user = $request->get('Auth');
        try {
            $adsCategory = Category::where('status', 1)->where('parent_id', '0')->get(['id', 'title', 'slug', 'icon', 'color']);
            $adsCategory->each(function ($record) use ($color_codes) {
                $record->color = $color_codes[$record->color];
            });
            if (count($adsCategory) > 0) {
                $result = [];
                $adsQuery = Advertisement::with('user')->where('is_featured', 1)->where('is_publish', 1)->where('status', 1)
                    ->with([
                        'advertisement_images',
                        'location' => function ($q) {
                            $q->select('id', 'title', 'area_id');
                        },
                        'location.area' => function ($q) {
                            $q->select('id', 'title', 'city_id');
                        },
                        'city' => function ($q) {
                            $q->select('id', 'title');
                        },
                        'area' => function ($q) {
                            $q->select('id', 'title', 'city_id');
                        },
                        'location.area.city' => function ($q) {
                            $q->select('id', 'title');
                        },
                        'location.area.city' => function ($q) {
                            $q->select('id', 'title');
                        },
                        'sub_business_category' => function ($q) {
                            $q->select('id', 'title', 'parent_id');
                        },
                        'sub_business_category.parent' => function ($q) {
                            $q->select('id', 'title');
                        },
                    ]);
                $advertisements = $adsQuery->get();

                $adsArr = array();
                foreach ($advertisements as $key => $ad) {
                    $user_wishlist = UserWishlist::where(['advertisement_id' => $ad->id, 'user_id' => $user->id])->count();
                    if ($user_wishlist > 0) {
                        $is_wishlisted = 1;
                    } else {
                        $is_wishlisted = 0;
                    }
                    $advertisements[$key]['is_wishlisted'] = $is_wishlisted;
                }

                $result['adsCategory'] = $adsCategory;
                $result['featured_ads'] = $advertisements;
                return ApiGlobalFunctions::sendResponse($result, ApiGlobalFunctions::messageDefault('list found successfully.'));
            } else {
                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('list not found.'), '', '200');
            }
        } catch (\Exception $e) {

            return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('oops'), '', '200');
        }
    }

    public function config(Request $request)
    {
        $data = $request->all();
        try {
            $city = City::where('status', 1)->get();
            if (count($city) > 0) {
                $area = Area::where('status', 1)->get();
                $result['city'] = $city;
                $result['area'] = $area;
                return ApiGlobalFunctions::sendResponse($result, ApiGlobalFunctions::messageDefault('list found successfully.'));
            } else {
                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('list not found.'), '', '200');
            }
        } catch (\Exception $e) {

            return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('oops'), '', '200');
        }
    }

 

    public function categoryList(Request $request)
    {
        $data = $request->all();
        $user = $request->get('Auth');
        try {
            $subCategory = Subscription::where('user_id', $user->id)->where('start_date', '<=', Carbon::now())->where('end_date', '>=', Carbon::now())->pluck('category_id');

            if (count($subCategory) > 0) {
                $categoryList = Category::where('status', 1)->whereIn('id', $subCategory)->get(['id', 'title', 'slug', 'icon']);
                return ApiGlobalFunctions::sendResponse($categoryList, ApiGlobalFunctions::messageDefault('list found successfully.'));
            } else {
                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('list not found.'), '', '200');
            }
        } catch (\Exception $e) {

            return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('oops'), '', '200');
        }
    }

    /* submitting contact us form request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return contactResponse
     */
    public function contactUs(Request $request)
    {
        $inputs = $request->all();
        $user = $request->get('Auth');
        try {
            //validate the input data in api
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required',
                'message' => 'required',
                'subject' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->errors()->first(), '200');
            }
            $insertInput = array();
            $insertInput["name"] = $inputs['name'];
            $insertInput["email"] = $inputs['email'];
            $insertInput["subject"] = $inputs['subject'];
            $insertInput["message"] = $inputs['message'];
            $enquiryData = new Contact();

            if ($enquiryData->fill($insertInput)->save()) {
                $contactData = array();
                $contactData['name'] = $enquiryData->name;
                $contactData['email'] = $enquiryData->email;
                $contactData['subject'] = $enquiryData->subject;
                $contactData['message'] = $enquiryData->message;

                return ApiGlobalFunctions::sendResponse($contactData, ApiGlobalFunctions::messageDefault('Your enquiry has been submitted successfully.'));
            } else {
                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('Your enquiry has not submitted successfully.'), '', '200');
            }
        } catch (\Exception $e) {
            return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('oops'), '', '200');
        }
    }

    /* Event notification list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return notificationList
     */
    public function notificationList(Request $request)
    {
        $data = $request->all();
        $user = $request->get('Auth');
        try {
            $notification = EventNotification::with(['getAdsInfo.advertisement_images' => function ($q) {
                $q->where('main', 1);
            }])->where('receiver_id', $user->id)->orderByDesc('id')->get();
            if (!empty($notification)) {
                $result['notificationList'] = $notification;
                $result['first_name'] = $user->first_name;
                // $result['last_name'] = $user->last_name; 
                $result['last_name'] = isset($user->last_name) ? $user->last_name : "";
                $result['email'] = $user->email;
                $result['phone'] = $user->phone;

                return ApiGlobalFunctions::sendResponse($result, ApiGlobalFunctions::messageDefault('list found successfully.'));
            } else {
                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('list not found.'), '', '200');
            }
        } catch (\Exception $e) {

            return ApiGlobalFunctions::sendError($e->getMessage());
        }
    }

    /* Internal Messaging list.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return internalMessagingList
     */
    public function internalMessagingList(Request $request)
    {
        $user = $request->get('Auth');

        try {
            $discussion = Discussion::with(
                [
                    'discussionMsg',
                    'advertisement:id,title,slug,user_id',
                    'sender:id,first_name,profile_picture,last_name',
                    'receiver:id,first_name,profile_picture,last_name',
                ]
            )
                ->distinct('advertisement_id')
                ->orWhere(['receiver_id' => $user->id, 'sender_id' => $user->id])
                ->orderBy('created_at', 'DESC')->get();

            if ($discussion && count($discussion) > 0) {
                foreach ($discussion as $key => $discussionMsg) {
                    // dd($discussionMsg->sender->last_name);
                    $discussion[$key]['lastMessage'] = $discussionMsg->discussionMsg()->orderBy('created_at', 'DESC')->first();
                    //  dd($discussionMsg->receiver->last_name);
                    //   ======================== Last send check if null then send empty ============================
                    if (isset($discussionMsg->receiver->last_name) == null) {
                        $discussionMsg->receiver->last_name = "";
                    }
                    if (($discussionMsg->sender->last_name) == null) {
                        $discussionMsg->sender->last_name = "";
                    }
                    //   ======================== close Last send check if null then send empty ============================

                    if ($user->id == $discussionMsg->receiver->id) {
                        $discussion[$key]['imageSenderpath'] = !empty($discussionMsg->sender->profile_picture) ? asset('storage/profile_pictures/' . $discussionMsg->sender->profile_picture) : asset('img/no-image.png');
                    } else {
                        $discussion[$key]['imageSenderpath'] = !empty($discussionMsg->receiver->profile_picture) ? asset('storage/profile_pictures/' . $discussionMsg->receiver->profile_picture) : asset('img/no-image.png');
                    }
                }
            }

            if (!empty($discussion)) {
                return ApiGlobalFunctions::sendResponse($discussion, ApiGlobalFunctions::messageDefault('list found successfully.'));
            } else {
                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('list not found.'), '', '200');
            }

            if (!empty($discussion)) {
                return ApiGlobalFunctions::sendResponse($discussion, ApiGlobalFunctions::messageDefault('list found successfully.'));
            } else {
                return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('list not found.'), '', '200');
            }
        } catch (\Exception $e) {

            return ApiGlobalFunctions::sendError($e->getMessage());
        }
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


    public function pageList(Request $request)
    {
        $input = $request->all();
        try {
            $where = array('1', '6', '18');
            //::whereIn('id',$where)->
            $settings = Page::get(['title', 'slug', 'description']);
            $singleArray = [];
            foreach ($settings as $key => $value) {
                $slug = str_replace('-', '', $value->slug);
                $singleArray[$slug] = $value->description;
            }
            if (!empty($singleArray)) {
                return ApiGlobalFunctions::sendResponse($singleArray, ApiGlobalFunctions::messageDefault('List found successfully.'));
            } else {
                return ApiGlobalFunctions::sendResponse((object) [], ApiGlobalFunctions::messageDefault('Record not found.'));
            }
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
        $user = $request->get('Auth');
        $input = $request->all();
        $result = User::where('id', $user->id)->update(['api_token' => '', 'device_type' => '', 'device_id' => '']);
        if ($result) {
            $data = (object) [];
            return ApiGlobalFunctions::sendResponse($data, ApiGlobalFunctions::messageDefault('Logout successfully.'));
        } else {
            return ApiGlobalFunctions::sendError(ApiGlobalFunctions::messageDefault('process_failed'));
        }
    }

    public function userCheck(Request $request)
    {
        $settings = User::get();
        return ApiGlobalFunctions::sendResponse($settings, ApiGlobalFunctions::messageDefault('List found successfully.'));
    }
}
