<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Repositories\CustomerRepository;
use Exception;


class CustomersController extends Controller
{

    /**
     * createCustomer
     *
     * @param  mixed $request
     * @return void
     */
    public static function createCustomer(Request $request)
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
            if ($customer = CustomerRepository::create($request)) {
                $postResponse = CustomerRepository::getSingle($customer->id);
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
     * updateCustomer
     *
     * @param  mixed $request
     * @return void
     */
    public static function updateCustomer(Request $request)
    {
        $data = [];
        try {
            // dd($request->all());
            $validator = (object) Validator::make($request->all(), [
                'customer_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            if ($customer = CustomerRepository::update($request)) {
                $customerId = $request->input('customer_id', false);
                $postResponse = CustomerRepository::getSingle($customerId);
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
     * getCustomer
     *
     * @param  mixed $request
     * @return void
     */
    public function getCustomer(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            $validator = (object) Validator::make($request->all(), [
                'customer_id' => 'required',
            ]);
            if ($validator->fails()) {
                return ApiGlobalFunctions::sendError('Validation Error.', $validator->messages(), 404);
            }
            $customerId = $request->input('customer_id', 0);
            if ($customerId > 0) {
                $postResponse = CustomerRepository::getSingle($customerId);
                if (!empty($postResponse)) {
                    $data['status'] = true;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('record_found');
                    $data['data'] = $postResponse;
                } else {
                    $data['status'] = false;
                    $data['code'] = config('response.HTTP_OK');
                    $data['message'] = ApiGlobalFunctions::messageDefault('record_not_found');
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
     * getCustomers
     *
     * @param  mixed $request
     * @return void
     */
    public function getCustomers(Request $request)
    {
        $data = [];
        $user = $request->get('Auth');
        try {
            $serviceResponse = CustomerRepository::list($request);
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
     * deleteCustomer
     *
     * @param  mixed $request
     * @return void
     */
    public function deleteCustomer(Request $request)
    {
        $data = [];
        try {
            if (CustomerRepository::deleteCustomer($request)) {
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
