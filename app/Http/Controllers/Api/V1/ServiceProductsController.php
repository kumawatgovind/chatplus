<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ServiceProduct;
use App\Models\ServiceProductImage;
use App\Models\PropertyAttribute;
use App\Helpers\Helper;
use App\Repositories\ServiceProductRepository;
use Carbon\Carbon;
use Exception, DB;


class ServiceProductsController extends Controller
{

    /**
     * createServiceProduct
     *
     * @param  mixed $request
     * @return void
     */
    public static function createServiceProduct(Request $request)
    {
        $data = [];
        try {
            $user = $request->get('Auth');
            // dd($request->all());
            // $validator = (object) Validator::make($request->all(), [
            //     'post_type' => 'required',
            //     'content' => 'nullable',
            //     'post_visibility' => 'required',
            // ]);
            // if ($validator->fails()) {
            //     return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            // }
            if ($serviceProduct = ServiceProductRepository::create($request)) {
                $postResponse = ServiceProductRepository::getSingle($serviceProduct->id, $user);
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('post_save');
                $data['data'] = $postResponse;
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
     * getServiceProduct
     *
     * @param  mixed $request
     * @return void
     */
    public function getServiceProduct(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            $validator = (object) Validator::make($request->all(), [
                'service_product_id' => 'nullable',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $serviceProductId = $request->input('service_product_id', 0);
            if ($serviceProductId > 0) {
                $postResponse = ServiceProductRepository::getSingle($serviceProductId, $user);
                if (!empty($postResponse)) {
                    $data['status'] = true;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                    $data['data'] = $postResponse;
                } else {
                    $data['status'] = false;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('list_not_found');
                }
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
     * getServiceProduct
     *
     * @param  mixed $request
     * @return void
     */
    public function getServiceProducts(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            $serviceResponse = ServiceProductRepository::list($request);
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
     * deleteServiceProduct
     *
     * @param  mixed $request
     * @return void
     */
    public function deleteServiceProduct(Request $request)
    {
        $data = [];
        try {
            if (ServiceProductRepository::deleteServiceProduct($request)) {
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
     * serviceProductStatus
     *
     * @param  mixed $request
     * @return void
     */
    public function serviceProductStatus(Request $request)
    {
        $data = [];
        try {
            $validator = (object) Validator::make($request->all(), [
                'service_product_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            if (ServiceProductRepository::serviceProductStatus($request)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('save_records');
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
     * listBookmarkServiceProduct
     *
     * @param  mixed $request
     * @return void
     */
    public function listBookmarkServiceProduct(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            $serviceResponse = ServiceProductRepository::listBookmarkServiceProduct($request);
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
     * bookmarkServiceProduct
     *
     * @param  mixed $request
     * @return void
     */
    public function bookmarkServiceProduct(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            $validator = (object) Validator::make($request->all(), [
                'service_product_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $serviceResponse = ServiceProductRepository::bookmarkServiceProduct($request);
            if (!empty($serviceResponse)) {
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('bookmark_success');
                $data['data'] = $serviceResponse;
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
