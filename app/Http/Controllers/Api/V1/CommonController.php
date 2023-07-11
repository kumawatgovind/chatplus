<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\ContactSync;
use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Carbon\Carbon;


class CommonController extends Controller
{
    use ApiGlobalFunctions;

    /**
     * categoryList.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return [].
     */
    public function categoryList()
    {
        $data = [];
        try {
            $categories = Category::select('id', 'name', 'icon')
                ->with(['childrenCategory' => function ($q) {
                    return $q->select('id', 'name', 'parent_id');
                }]);
            $categories = $categories->where(['status' => 1, 'parent_id' => 0]);
            $categoryData = $categories->get();
            $result = [];
            if ($categoryData->count() > 0) {
                foreach ($categoryData as $cKey => $category) {
                    $result[$cKey]['id'] = $category->id;
                    $result[$cKey]['name'] = $category->name;
                    $result[$cKey]['icon'] = $category->icon_url;
                    if ($category->childrenCategory->count() > 0) {
                        foreach ($category->childrenCategory as $sKey => $childrenCategory) {
                            $result[$cKey]['sub_category'][$sKey]['id'] = $childrenCategory->id;
                            $result[$cKey]['sub_category'][$sKey]['name'] = $childrenCategory->name;
                        }
                    }
                }
            }
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = $this->messageDefault('record_found');
            $data['data'] = $result;
        } catch (Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            $data['message'] = 'Error: ' . $e->getMessage();
        }
        return $this->responseBuilder($data);
    }

    /**
     * categoryList.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return [].
     */
    public function masterData()
    {
        $data = [];
        try {
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = $this->messageDefault('record_found');
            $data['data'] = config('constants.MASTER_DATA');
        } catch (Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            $data['message'] = 'Error: ' . $e->getMessage();
        }
        return $this->responseBuilder($data);
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
            $type = $request->input('type', false);
            $responseData = [];
            if (isset($_FILES['upload_file']['name']) && $_FILES['upload_file']['name'] != '') {
                switch ($type) {
                    case 'profile_photo':
                        $responseData = self::fileUpload($request);
                        break;
                    case 'services_profile_images':
                        $responseData = self::fileUpload($request);
                        break;
                    case 'service_product_images':
                        $responseData = self::fileUpload($request);
                        break;
                    case 'product_images':
                        $responseData = self::fileUpload($request);
                        break;
                    case 'post_images':
                        $responseData = self::fileUpload($request);
                        break;
                    default:
                        $responseData = [
                            'upload_file' => '',
                            'upload_path' => '',
                        ];
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
        $path = '';
        $type = $request->input('type', false);
        $fileName = $request->file('upload_file');
        $upload_file = time() . rand(100, 999) . '.' . $fileName->getClientOriginalExtension();

        switch ($type) {
            case 'profile_photo':
                $path = asset('storage/profile/');
                $fileName->move(storage_path('app/public/profile/'), $upload_file);
                break;
            case 'services_profile_images':
                $path = asset('storage/services/');
                $fileName->move(storage_path('app/public/services/'), $upload_file);
                break;
            case 'service_product_images':
                $path = asset('storage/services/products/');
                $fileName->move(storage_path('app/public/services/products/'), $upload_file);
                break;
            case 'product_images':
                $path = asset('storage/products/');
                $fileName->move(storage_path('app/public/products/'), $upload_file);
                break;
            case 'post_images':
                $path = asset('storage/posts/');
                $fileName->move(storage_path('app/public/posts/'), $upload_file);
                break;
            default:
                $upload_file = '';
                $path = '';
        }
        return  [
            'upload_file' => $upload_file,
            'upload_path' => $path,
        ];
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
            $validator =  (object) Validator::make($request->all(), [
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
            $data['message'] = ApiGlobalFunctions::messageDefault('list_found');
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
     * contactSync
     *
     * @param  mixed $request
     * @return void
     */
    public function checkLimit(Request $request)
    {
        $data = [];
        try {
            $user = $request->get('Auth');
            $limit = config('constants.SERVICE_PRODUCT_LIMIT');
            $userDetail = User::withCount(['serviceProduct', 'activeSubscription'])->where('id', $user->id)->first();
            $subscription = false;
            if ($userDetail->active_subscription_count > 0) {
                $subscription = true;
            }
            $serviceProductCount = $userDetail->service_product_count;
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = ApiGlobalFunctions::messageDefault('list_found');
            $data['data'] = [
                'limit' => $limit,
                'usedLimit' => $serviceProductCount,
                'availableLimit' => $limit - $serviceProductCount,
                'subscription' => $subscription
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
}
