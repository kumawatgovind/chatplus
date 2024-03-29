<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function is;
use function is_null;
use Exception;

class CustomerRepository
{

    /**
     * create
     *
     * @param  mixed $request
     * @return obj
     */
    public static function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            $customer = new Customer();
            $customer->user_id = $authUser->id;
            $customer->name = $request->input('name', false);
            $customer->contact_number = $request->input('contact_number', false);
            $customer->state_id = $request->input('state_id', false);
            $customer->city_id = $request->input('city_id', false);
            $customer->locality_id = $request->input('locality_id', false);
            $customer->description = $request->input('description', false);
            $customer->latitude = $request->input('latitude', false);
            $customer->longitude = $request->input('longitude', false);
            if ($customer->save()) {
                DB::commit();
                return $customer;
            } else {
                DB::rollBack();
                return false;
            }
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }
    
    /**
     * update
     *
     * @param  mixed $request
     * @return void
     */
    public static function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            $customerId = $request->input('customer_id', false);
            if ($request->input('name', false)) {
                $update['name'] = $request->input('name', false);
            }
            if ($request->input('contact_number', false)) {
                $update['contact_number'] = $request->input('contact_number', false);
            }
            if ($request->input('state_id', false)) {
                $update['state_id'] = $request->input('state_id', false);
            }
            if ($request->input('city_id', false)) {
                $update['city_id'] = $request->input('city_id', false);
            }
            if ($request->input('locality_id', false)) {
                $update['locality_id'] = $request->input('locality_id', false);
            }
            if ($request->input('description', false)) {
                $update['description'] = $request->input('description', false);
            }
            if ($request->input('latitude', false)) {
                $update['latitude'] = $request->input('latitude', false);
            }
            if ($request->input('longitude', false)) {
                $update['longitude'] = $request->input('longitude', false);
            }
            if (Customer::where('id', $customerId)->update($update)) {
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
    /**
     * getSingle
     *
     * @param  mixed $request
     * @return obj
     */
    public static function getSingle($customerId)
    {
        $query = Customer::status()
            ->with([
                'state' => function ($q) {
                    $q->select('id', 'name');
                },
                'city' => function ($q) {
                    $q->select('id', 'name');
                },
                'locality' => function ($q) {
                    $q->select('id', 'name');
                },
                'users',
                'users.userServicesProfile',
                'users.userServicesProfile.serviceImages',
                'users.userServicesProfile.state' => function ($q) {
                    $q->select('id', 'name');
                },
                'users.userServicesProfile.city' => function ($q) {
                    $q->select('id', 'name');
                },
                'users.userServicesProfile.locality' => function ($q) {
                    $q->select('id', 'name');
                },
            ]);

        $query = $query->where('id', $customerId);
        $customerData = $query->first();
        if (!empty($customerData->user->userServicesProfile->serviceImages)) {
            $responseServiceImage = [];
            foreach ($customerData->user->userServicesProfile->serviceImages as $serviceImage) {
                $responseServiceImage[] = $serviceImage->name;
            }
            unset($customerData->user->userServicesProfile->serviceImages);
            $customerData->user->userServicesProfile->serviceImages = $responseServiceImage;
        }
        return $customerData;
    }


    /**
     * list
     *
     * @param  mixed $request
     * @return void
     */
    public static function list(Request $request)
    {
        $authUser = $request->get('Auth');
        $userId = $authUser->id;
        if ($request->input('limit', false)) {
            $limit  = $request->input('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        $query = Customer::status()
            ->with([
                'state' => function ($q) {
                    $q->select('id', 'name');
                },
                'city' => function ($q) {
                    $q->select('id', 'name');
                },
                'locality' => function ($q) {
                    $q->select('id', 'name');
                },
                'users',
                'users.userServicesProfile',
                'users.userServicesProfile.serviceImages',
                'users.userServicesProfile.state' => function ($q) {
                    $q->select('id', 'name');
                },
                'users.userServicesProfile.city' => function ($q) {
                    $q->select('id', 'name');
                },
                'users.userServicesProfile.locality' => function ($q) {
                    $q->select('id', 'name');
                }
            ]);
        if ($request->input('user_id', false)) {
            $query = $query->where('user_id', $request->input('user_id', false));
        } else {
            $query = $query->where('user_id', $userId);
        }
        $customers = $query->orderBy('id', 'desc')->paginate($limit);
        if (!empty($customers)) {
            foreach ($customers as $sKey => $customer) {
                if (!empty($customer->user->userServicesProfile->serviceImages)) {
                    $responseServiceImage = [];
                    foreach ($customer->user->userServicesProfile->serviceImages as $serviceImage) {
                        $responseServiceImage[] = $serviceImage->name;
                    }
                    unset($customers[$sKey]->user->userServicesProfile->serviceImages);
                    $customers[$sKey]->user->userServicesProfile->serviceImage = $responseServiceImage;
                }
            }
        }
        return $customers;
    }

    /**
     * deleteCustomer
     *
     * @param  mixed $request
     * @return void
     */
    public static function deleteCustomer(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            if ($request->input('customer_id', false) > 0) {
                $customerId = $request->input('customer_id', false);
                Customer::where([
                    'id' => $customerId,
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
