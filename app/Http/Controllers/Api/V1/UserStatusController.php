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
            // dd($request->all());
            // $validator = (object) Validator::make($request->all(), [
            //     'post_type' => 'required',
            //     'content' => 'nullable',
            //     'post_visibility' => 'required',
            // ]);
            // if ($validator->fails()) {
            //     return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            // }
            $response = UserStatusRepository::create($request);
            if ($response['status']) {
                $postResponse = UserStatusRepository::getSingle($response['data']->id);
                $data['status'] = true;
                $data['code'] = config('response.HTTP_OK');
                $data['message'] = ApiGlobalFunctions::messageDefault('post_save');
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
     * updateProduct
     *
     * @param  mixed $request
     * @return void
     */
    public static function updateProduct(Request $request)
    {
        $data = [];
        try {
            // dd($request->all());
            $validator = (object) Validator::make($request->all(), [
                'product_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            if ($product = ProductRepository::update($request)) {
                $productId = $request->input('product_id', false);
                $postResponse = ProductRepository::getSingle($productId);
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
     * getProduct
     *
     * @param  mixed $request
     * @return void
     */
    public function getProduct(Request $request)
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
            $serviceProductId = $request->input('product_id', 0);
            if ($serviceProductId > 0) {
                $postResponse = ProductRepository::getSingle($serviceProductId);
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
     * getProducts
     *
     * @param  mixed $request
     * @return void
     */
    public function getProducts(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            $serviceResponse = ProductRepository::list($request);
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
     * deleteProduct
     *
     * @param  mixed $request
     * @return void
     */
    public function deleteProduct(Request $request)
    {
        $data = [];
        try {
            if (ProductRepository::deleteProduct($request)) {
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
}
