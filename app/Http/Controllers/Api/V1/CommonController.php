<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\ContactSync;
use App\Models\Contact;
use App\Models\User;
use App\Models\State;
use App\Models\City;
use App\Models\Friend;
use App\Models\Locality;
use App\Models\Marketing;
use App\Models\RecentSearch;
use App\Models\Page;
use App\Repositories\UserRepository;
use Exception;
use Carbon\Carbon;
use PHPUnit\Framework\Constraint\IsTrue;

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
                    case 'kyc_document':
                        $responseData = self::fileUpload($request);
                        break;
                    case 'user_status':
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
            case 'kyc_document':
                $path = asset('storage/document/');
                $fileName->move(storage_path('app/public/document/'), $upload_file);
                break;
            case 'user_status':
                $path = asset('storage/status/');
                $fileName->move(storage_path('app/public/status/'), $upload_file);
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
            $authUser = $request->get('Auth');
            $validator =  (object) Validator::make($request->all(), [
                'contacts' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $contacts = $contactData = (array) $request->request->get('contacts');
            $phoneNumbers = array_column($contacts, 'number');

            $existUsers = User::whereIn('phone_number', $phoneNumbers)->get();
            $friends = [];
            $syncByUserId = $authUser->id;
            if (!empty($contacts)) {
                foreach ($contacts as $cKey => $contact) {
                    if (strlen($contact['number']) > 5) {                    
                        $contacts[$cKey]['cid'] = $contact['id'];
                        unset($contacts[$cKey]['id']);
                        $contactData[$cKey]['exist'] = false;
                        $contactData[$cKey]['profile'] = (object) [];
                        $contacts[$cKey]['if_user_existing_id'] = $ifUserExistingId = 0;
                        $contacts[$cKey]['sync_by_user_id'] = $syncByUserId;
                        if ($existUsers->count() > 0) {
                            foreach ($existUsers as $user) {
                                if ($contact['number'] == $user->phone_number) {
                                    $contactData[$cKey]['exist'] = true;
                                    $contactData[$cKey]['profile'] = $user;
                                    $contacts[$cKey]['if_user_existing_id'] = $ifUserExistingId = $user->id;
                                    if ($syncByUserId != $ifUserExistingId) {
                                        $friends[] = [
                                            'user_id' => $syncByUserId,
                                            'friend_id' => $ifUserExistingId
                                        ];
                                    }
                                }
                            }
                        }
                        ContactSync::updateOrCreate(
                            [
                                'number' => $contact['number']
                            ],
                            [
                                'sync_by_user_id' => $syncByUserId,
                                'if_user_existing_id' => $ifUserExistingId,
                                'code' => $contact['code'],
                                'cid' => $contact['id'],
                                'name' => $contact['name'],
                                'number' => $contact['number']
                            ]
                        );
                    }
                }
            }
            Friend::upsert($friends, ['user_id', 'friend_id'], ['user_id', 'friend_id']);
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
     * createContact
     *
     * @param  mixed $request
     * @return void
     */
    public function createContact(Request $request)
    {
        $data = [];
        try {
            $authUser = $request->get('Auth');
            // $validator =  (object) Validator::make($request->all(), [
            //     'email' => 'required',
            // ]);
            // if ($validator->fails()) {
            //     return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            // }
            $contact = new Contact();
            $contact->user_id = $authUser->id;
            $contact->name = $request->input('name', false);
            $contact->email = $request->input('email', false);
            $contact->message = $request->input('message', false);
            if ($contact->save()) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('save_records');
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
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
    
    /**
     * getState
     *
     * @param  mixed $request
     * @return void
     */
    public function getState(Request $request)
    {
        $data = [];
        try {
            $validator = Validator::make($request->all(), [
                'keyword' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $keyword = $request->input('keyword', false);
            $stateData = State::mobile($keyword)->select('id', 'name')->get();
            if ($stateData->count() > 0) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $stateData;
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
     * getDistrict
     *
     * @param  mixed $request
     * @return void
     */
    public function getDistrict(Request $request)
    {
        $data = [];
        try {
            $validator = Validator::make($request->all(), [
                'keyword' => 'nullable',
                'state_id' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $keyword = $request->input('keyword', false);
            $stateId = $request->input('state_id', 0);
            $cityQuery = new City;
            if ($stateId > 0) {
                $cityQuery = $cityQuery->where('cities.state_id', $stateId);
            }
            $cityQuery = $cityQuery->select('id', 'state_id', 'name')
            ->with('state', function($q) use($keyword) {
                $q->select('id','name');
            })->mobile($keyword);
            if ($stateId == 0) {
                $cityQuery = $cityQuery->orWhereHas('state', function($q) use($keyword){
                    $q->mobile($keyword);
                });
            }
            $cityData = $cityQuery->get();
            if ($cityData->count() > 0) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $cityData;
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
     * getLocality
     *
     * @param  mixed $request
     * @return void
     */
    public function getLocality(Request $request)
    {
        $data = [];
        try {
            $validator = Validator::make($request->all(), [
                'keyword' => 'nullable',
                'city_id' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $keyword = $request->input('keyword', false);
            $cityId = $request->input('city_id', 0);
            $localityQuery = Locality::select('id', 'city_id', 'name')->with('city', function($q) {
                $q->select('id','name');
            });
            $localityQuery = $localityQuery->mobile($keyword);
            if ($cityId > 0) {
                $localityQuery = $localityQuery->where('city_id', $cityId);
            }
            $localityData = $localityQuery->get();
            if ($localityData->count() > 0) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $localityData;
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
     * addRecentSearch
     *
     * @param  mixed $request
     * @return void
     */
    public function addRecentSearch(Request $request)
    {
        $data = [];
        try {
            $authUserId = $request->get('Auth')->id;
            $validator = Validator::make($request->all(), [
                'type' => 'required',
                'state_id' => 'nullable',
                'city_id' => 'nullable',
                'locality_id' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $update = [];
            $recentSearch = new RecentSearch();
            $recentSearch->user_id = $update['user_id'] = $authUserId;
            $recentSearch->search_type = $update['search_type'] = $request->input('type', false);
            if ($request->input('city_id', 0) > 0 && $update['search_type'] == 'city') {
                $recentSearch->city_id = $update['city_id'] = $request->input('city_id', 0);
            }
            if ($request->input('locality_id', 0) > 0 && $update['search_type'] == 'locality') {
                $recentSearch->locality_id = $update['locality_id'] = $request->input('locality_id', 0);
            }
            if ($request->input('state_id', 0) > 0 && $update['search_type'] == 'state') {
                $recentSearch->state_id = $update['state_id'] = $request->input('state_id', 0);
            }
            if (RecentSearch::where($update)->first()) {
                // $addUpdateRecord = $recentSearchUpdate->save();
                $addUpdateRecord = RecentSearch::where($update)->update($update);
            } else {
                $addUpdateRecord = $recentSearch->save();
            }
            if ($addUpdateRecord) {
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
     * getRecentSearch
     *
     * @param  mixed $request
     * @return void
     */
    public function getRecentSearch(Request $request)
    {
        $data = [];
        try {
            $authUserId = $request->get('Auth')->id;
            $recentSearchQuery = new RecentSearch;
            $localityQuery = $recentSearchQuery->where([
                'recent_searches.search_type' => 'locality',
                'recent_searches.user_id' => $authUserId,
            ]);
            $cityQuery = $recentSearchQuery->where([
                'recent_searches.search_type' => 'city',
                'recent_searches.user_id' => $authUserId,
            ]);
            $stateQuery = $recentSearchQuery->where([
                'recent_searches.search_type' => 'state',
                'recent_searches.user_id' => $authUserId,
            ]);
            $localityQuery = $localityQuery->with([
                'locality' => function($q) {
                    $q->select('id', 'name', 'city_id')->with('city', function($q) {
                        $q->select('id','name', 'state_id',)->with('state', function($q) {
                            $q->select('id','name');
                        });
                    });
                }
            ]);
            $cityQuery = $cityQuery->with([
                'city' => function($q) {
                    $q->select('id', 'name', 'state_id')->with([
                        'state' => function($q) {
                            $q->select('id', 'name');
                        }
                    ]);
                }
            ]);
            $stateQuery = $stateQuery->with([
                'state' => function($q) {
                    $q->select('id', 'name');
                }
            ]);
            $cityData = $cityQuery->limit(5)->get();
            $localityData = $localityQuery->limit(5)->get();
            $stateData = $stateQuery->limit(5)->get();
            if ($cityData->count() > 0 || $localityData->count() > 0 || $stateData->count() > 0) {
                $responseData = [];
                foreach ($localityData as $locality) {
                    $responseData['locality'][] = [
                        'id' => $locality->locality->id,
                        'name' => $locality->locality->name,
                        'city_id' => $locality->locality->city_id,
                        'city' => $locality->locality->city,
                    ];
                }
                foreach ($cityData as $city) {
                    $responseData['city'][] = [
                        'id' => $city->city->id,
                        'name' => $city->city->name,
                        'state_id' => $city->city->state_id,
                        'state' => $city->city->state
                    ];
                }
                foreach ($stateData as $state) {
                    $responseData['state'][] = [
                        'id' => $state->state->id,
                        'name' => $state->state->name,
                    ];
                }
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $responseData;
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
     * checkNotification
     *
     * @param  mixed $request
     * @return void
     */
    public function checkNotification(Request $request)
    {
        $data = [];
        try {
            // $authUser = $request->get('Auth');
            $fcm = $request->input('fcm');
            // $user = User::where('id', $authUser->id)->first();
            // dd($user);
            // $deviceType = $user['device_type'];
            // $orderId = $user['order_id'];
            $result = ApiGlobalFunctions::sendNotificationForTesting($fcm);
            if ($result) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = 'Sent successfully.';
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
     * pageList
     *
     * @param  mixed $request
     * @return []
     */
    public function pageList(Request $request)
    {
        $data = [];
        try {
            $pages = Page::get(['title', 'slug', 'description']);
            $pagesList = [];
            foreach ($pages as $value) {
                $pagesList[] = [
                    'slug' => $value->slug,
                    'page_link' => route('frontend.page', $value->slug),
                    'title' => $value->title,
                    'description' => $value->description,
                ];
            }
            if (!empty($pagesList)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('list_found');
                $data['data'] = $pagesList;
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
     * getPage
     *
     * @param  mixed $request
     * @return []
     */
    public function getPage(Request $request, $pageSlug)
    {
        $data = [];
        try {
            $page = Page::where('slug', $pageSlug)->select(['title', 'slug', 'description'])->first();
            $page->page_link = route('frontend.page', $page->slug);
            if (!empty($page)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $page;
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
     * marketingList
     *
     * @param  mixed $request
     * @return []
     */
    public function marketingList(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            if ($request->input('limit', false)) {
                $limit  = $request->input('limit', 0);
            } else {
                $limit = config('get.FRONT_END_PAGE_LIMIT');
            }
            $query = Marketing::status();
            $marketingResponse = $query->orderBy('id', 'desc')->paginate($limit);
            
            if (!empty($marketingResponse)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $marketingResponse;
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
     * marketingBanner
     *
     * @param  mixed $request
     * @return []
     */
    public function marketingBanner(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            
            $query = Marketing::status();
            $marketingResponse = $query->orderBy('id', 'desc')->first();            
            if (!empty($marketingResponse)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                $data['data'] = $marketingResponse;
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
}
