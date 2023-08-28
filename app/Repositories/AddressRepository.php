<?php

namespace App\Repositories;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function is;
use function is_null;
use Exception;

class AddressRepository
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
            $address = new Address();
            $address->user_id = $userId = $authUser->id;
            $address->street_name = $request->input('street_name', false);
            $address->building_name = $request->input('building_name', false);
            $address->pin_code = $request->input('pin_code', false);
            $address->city_id = $request->input('city_id', false);
            $address->state_id = $request->input('state_id', false);
            $address->locality_id = $request->input('locality_id', false);
            $address->latitude = $request->input('latitude', false);
            $address->longitude = $request->input('longitude', false);
            $address->status = 1;
            $address->is_recent = 1;
            if (Address::where('user_id', $userId)->exists()) {
                Address::where('user_id', $userId)->update([
                    'is_recent' => 0
                ]);
            }
            if ($address->save()) {
                DB::commit();
                return $address;
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
     * @return bool
     */
    public static function update(Request $request)
    {
        try {
            $update = [];
            $authUser = $request->get('Auth');
            $userId = $authUser->id;
            $addressId = $request->input('address_id', false);
            if ($request->input('street_name', false)) {
                $update['street_name'] = $request->input('street_name', false);
            }
            if ($request->input('building_name', false)) {
                $update['building_name'] = $request->input('building_name', false);
            }
            if ($request->input('pin_code', false)) {
                $update['pin_code'] = $request->input('pin_code', false);
            }
            if ($request->input('city_id', false)) {
                $update['city_id'] = $request->input('city_id', false);
            }
            if ($request->input('state_id', false)) {
                $update['state_id'] = $request->input('state_id', false);
            }
            if ($request->input('locality_id', false)) {
                $update['locality_id'] = $request->input('locality_id', false);
            }            
            if ($request->input('latitude', false)) {
                $update['latitude'] = $request->input('latitude', false);
            }
            if ($request->input('longitude', false)) {
                $update['longitude'] = $request->input('longitude', false);
            }
            if ($request->input('is_recent', false)) {
                $update['is_recent'] = $request->input('is_recent', false);
                Address::where('user_id', $userId)->update([
                    'is_recent' => 0
                ]);
            }
            if (Address::where('id', $addressId)->update($update)) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * getSingle
     *
     * @param  mixed $request
     * @return obj
     */
    public static function getSingle($addressId)
    {
        $query = Address::status();
        $query = $query->with([
            'state' => function ($q) {
                $q->select('id', 'name');
            },
            'city' => function ($q) {
                $q->select('id', 'name');
            },
            'locality' => function ($q) {
                $q->select('id', 'name');
            }]);
        $query = $query->where('id', $addressId);
        $addressData = $query->first();
        return $addressData;
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
        $query = Address::status();
        $query = $query->with([
            'state' => function ($q) {
                $q->select('id', 'name');
            },
            'city' => function ($q) {
                $q->select('id', 'name');
            },
            'locality' => function ($q) {
                $q->select('id', 'name');
            }]);
        if ($request->input('user_id', false)) {
            $query = $query->where('user_id', $request->input('user_id', false));
        } else {
            $query = $query->where('user_id', $userId);
        }
        $customers = $query->orderBy('is_recent', 'desc')->paginate($limit);
        return $customers;
    }

    /**
     * deleteAddress
     *
     * @param  mixed $request
     * @return void
     */
    public static function deleteAddress(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            if ($request->input('address_id', false) > 0) {
                $addressId = $request->input('address_id', false);
                Address::where([
                    'id' => $addressId,
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
