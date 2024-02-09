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
use App\Repositories\NotificationRepository;
use App\Repositories\ServiceProductRepository;
use Carbon\Carbon;
use Exception, DB;


class ServiceProductsController extends Controller
{

    /**
     * createServiceProduct
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public static function createServiceProduct(Request $request)
    {
        $data = [];
        try {
            $user = $request->get('Auth');
            $productType = $request->input('product_type', false);
            $productFor = $request->input('product_for', false);
            if ($productType == 'Other') {
                $validationObj = [
                    'product_for' => 'required',
                    'category_id' => 'required',
                    'sub_category_id' => 'required',
                    'title' => 'required',
                    'locality_id' => 'required',
                    'city_id' => 'required',
                    'state_id' => 'required',
                    'price' => 'required',
                    'service_product_images' => 'required',
                    'property_attribute.property_condition' => 'required',
                ];
            } elseif ($productType == 'Property' && $productFor == 'Requirement') {
                $validationObj = [
                    'product_for' => 'required',
                    'category_id' => 'required',
                    'sub_category_id' => 'required',
                    'locality_id' => 'required',
                    'city_id' => 'required',
                    'state_id' => 'required',
                    'price' => 'required',
                    'property_attribute.property_requirement' => 'required',
                    'property_attribute.property_category' => 'required',
                    'property_attribute.property_category_type' => 'required',
                    'property_attribute.property_carpet_area' => 'required',
                    'property_attribute.carpet_area_unit' => 'required',
                ];

            } elseif ($productType == 'Property' && $productFor != 'Requirement') {
                $validationObj = [
                    'product_type' => 'required',
                    'product_for' => 'required',
                    'category_id' => 'required',
                    'sub_category_id' => 'required',
                    'locality_id' => 'required',
                    'city_id' => 'required',
                    'state_id' => 'required',
                    'price' => 'required',
                    'service_product_images' => 'required',
                    'property_attribute.property_category' => 'required',
                    'property_attribute.property_category_type' => 'required',
                    'property_attribute.property_facing' => 'required',
                    'property_attribute.property_status' => 'required',
                    'property_attribute.property_carpet_area' => 'required',
                    'property_attribute.carpet_area_unit' => 'required',
                    'property_attribute.property_super_area' => 'required',
                    'property_attribute.super_area_unit' => 'required',
                    'property_attribute.property_length' => 'required',
                    'property_attribute.length_unit' => 'required',
                    'property_attribute.property_breadth' => 'required',
                    'property_attribute.breadth_unit' => 'required',
                ];
            } else {
                $validationObj = [
                    'product_type' => 'required',
                ];
            }
            $validator = (object) Validator::make($request->all(), $validationObj);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->errors()->first(), 200);
            }
            if ($serviceProduct = ServiceProductRepository::createUpdate($request)) {
                $postResponse = ServiceProductRepository::getSingle($serviceProduct->id, $user);
                NotificationRepository::createNotification($postResponse, $user, 'property');
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('save_records');
                $data['data'] = $postResponse;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
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
     * updateServiceProduct
     *
     * @param  mixed $request
     * @return \Illuminate\Http\Response
     */
    public static function updateServiceProduct(Request $request)
    {
        $data = [];
        try {
            $user = $request->get('Auth');
            $productType = $request->input('product_type', false);
            $productFor = $request->input('product_for', false);
            if ($productType == 'Other') {
                $validationObj = [
                    'service_id' => 'required',
                    'product_for' => 'required',
                    'category_id' => 'required',
                    'sub_category_id' => 'required',
                    'title' => 'required',
                    'locality_id' => 'required',
                    'city_id' => 'required',
                    'state_id' => 'required',
                    'price' => 'required',
                    'service_product_images' => 'required',
                    'property_attribute.property_condition' => 'required',
                ];
            } elseif ($productType == 'Property' && $productFor == 'Requirement') {
                $validationObj = [
                    'service_id' => 'required',
                    'product_for' => 'required',
                    'category_id' => 'required',
                    'sub_category_id' => 'required',
                    'locality_id' => 'required',
                    'city_id' => 'required',
                    'state_id' => 'required',
                    'price' => 'required',
                    'property_attribute.property_requirement' => 'required',
                    'property_attribute.property_category' => 'required',
                    'property_attribute.property_category_type' => 'required',
                    'property_attribute.property_carpet_area' => 'required',
                    'property_attribute.carpet_area_unit' => 'required',
                ];

            } elseif ($productType == 'Property' && $productFor != 'Requirement') {
                $validationObj = [
                    'service_id' => 'required',
                    'product_type' => 'required',
                    'product_for' => 'required',
                    'category_id' => 'required',
                    'sub_category_id' => 'required',
                    'locality_id' => 'required',
                    'city_id' => 'required',
                    'state_id' => 'required',
                    'price' => 'required',
                    'service_product_images' => 'required',
                    'property_attribute.property_category' => 'required',
                    'property_attribute.property_category_type' => 'required',
                    'property_attribute.property_facing' => 'required',
                    'property_attribute.property_status' => 'required',
                    'property_attribute.property_carpet_area' => 'required',
                    'property_attribute.carpet_area_unit' => 'required',
                    'property_attribute.property_super_area' => 'required',
                    'property_attribute.super_area_unit' => 'required',
                    'property_attribute.property_length' => 'required',
                    'property_attribute.length_unit' => 'required',
                    'property_attribute.property_breadth' => 'required',
                    'property_attribute.breadth_unit' => 'required',
                ];
            } else {
                $validationObj = [
                    'service_id' => 'required',
                    'product_type' => 'required',
                ];
            }
            $validator = (object) Validator::make($request->all(), $validationObj);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->errors()->first(), 200);
            }
            if ($serviceProduct = ServiceProductRepository::createUpdate($request)) {
                $postResponse = ServiceProductRepository::getSingle($serviceProduct->id, $user);
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('save_records');
                $data['data'] = $postResponse;
            } else {
                $data['status'] = false;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
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
     * getServiceProducts
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
            $data['code'] = $e->getCode();
            if (config('constants.DEBUG_MODE')) {
                $data['message'] = 'Error: ' . $e->getMessage();
            } else {
                $data['message'] = ApiGlobalFunctions::messageDefault('oops');
            }
        }
        return ApiGlobalFunctions::responseBuilder($data);
    }
}
