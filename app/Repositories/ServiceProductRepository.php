<?php

namespace App\Repositories;

use App\Models\PropertyAttribute;
use App\Models\ServiceProduct;
use App\Models\ServiceProductImage;
use App\Models\User;
use App\Models\Locality;
use App\Models\Category;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function is;
use function is_null;
use Exception;

class ServiceProductRepository
{
    /**
     * createUpdate
     *
     * @param  mixed $request
     * @return obj
     */
    public static function createUpdate(Request $request)
    {
        DB::beginTransaction();
        try {
            $isNew = true;
            $authUser = $request->get('Auth');
            if ($serviceProductId = $request->input('service_id', false)) {
                $isNew = false;
                $serviceProduct = ServiceProduct::where('id', $serviceProductId)->first();
            } else {
                $serviceProduct = new ServiceProduct();
            }
            $serviceProduct->user_id = $authUser->id;
            $serviceProduct->product_type = $serviceProductType = $request->input('product_type', false);
            $serviceProduct->product_for = $request->input('product_for', false);
            $serviceProduct->category_id = $request->input('category_id', 0);
            $serviceProduct->sub_category_id = $request->input('sub_category_id', 0);
            $serviceProduct->title = $request->input('title', false);
            $serviceProduct->locality_id = $request->input('locality_id', 0);
            $serviceProduct->city_id = $request->input('city_id', 0);
            $serviceProduct->state_id = $request->input('state_id', 0);
            // $serviceProduct->locality = $request->input('locality', false);
            // $serviceProduct->city = $request->input('city', false);
            // $serviceProduct->state = $request->input('state', false);
            $serviceProduct->address = $request->input('address', false);
            $serviceProduct->price = $request->input('price', false);
            $serviceProduct->description = $request->input('description', false);
            $serviceProduct->latitude = $request->input('latitude', false);
            $serviceProduct->longitude = $request->input('longitude', false);
            $serviceProductImages = $request->input('service_product_images') ?? [];
            $propertyAttribute = $request->input('property_attribute') ?? [];
            if ($serviceProduct->save()) {
                if (!empty($serviceProductImages)) {
                    if ($request->input('service_id', false)) {
                        ServiceProductImage::where([
                            'service_product_id' => $serviceProductId,
                        ])->delete();
                    }
                    $ordering = 1;
                    foreach ($serviceProductImages as $value) {
                        if (!empty($value)) {
                            $attachmentData = [];
                            $attachmentData['service_product_id'] = $serviceProduct->id;
                            $attachmentData['name'] = $value;
                            $attachmentData['ordering'] = $ordering;
                            ServiceProductImage::create($attachmentData);
                            $ordering++;
                        }
                    }
                }
                if (!empty($propertyAttribute)) {
                    if ($isNew) {
                        $propertyAttributeObj = new PropertyAttribute();
                        $propertyAttributeObj->service_product_id = $serviceProduct->id;
                    } else {
                        $propertyAttributeObj = PropertyAttribute::where('service_product_id', $request->input('service_id', false))->first();
                    }
                    if ($serviceProductType == 'Other') {
                        $propertyAttributeObj->property_condition = $propertyAttribute['property_condition'] ?? 0;
                    } else {
                        $propertyAttributeObj->property_requirement = $propertyAttribute['property_requirement'] ?? '';
                        $propertyAttributeObj->property_category = $propertyAttribute['property_category'] ?? '';
                        $propertyAttributeObj->property_category_type = $propertyAttribute['property_category_type'] ?? '0';
                        $propertyAttributeObj->project_authority = $propertyAttribute['project_authority'] ?? '';
                        $propertyAttributeObj->property_bedroom = $propertyAttribute['property_bedroom'] ?? 0;
                        $propertyAttributeObj->property_bathroom = $propertyAttribute['property_bathroom'] ?? 0;
                        $propertyAttributeObj->property_floor = $propertyAttribute['property_floor'] ?? 0;
                        $propertyAttributeObj->property_furnishing = $propertyAttribute['property_furnishing'] ?? 0;
                        $propertyAttributeObj->property_facing = $propertyAttribute['property_facing'] ?? 0;
                        $propertyAttributeObj->property_status = $propertyAttribute['property_status'] ?? 0;
                        $propertyAttributeObj->property_carpet_area = $propertyAttribute['property_carpet_area'] ?? 0;
                        $propertyAttributeObj->carpet_area_unit = $propertyAttribute['carpet_area_unit'] ?? 0;
                        $propertyAttributeObj->property_super_area = $propertyAttribute['property_super_area'] ?? 0;
                        $propertyAttributeObj->super_area_unit = $propertyAttribute['super_area_unit'] ?? 0;
                        $propertyAttributeObj->property_length = $propertyAttribute['property_length'] ?? 0;
                        $propertyAttributeObj->length_unit = $propertyAttribute['length_unit'] ?? 0;
                        $propertyAttributeObj->property_breadth = $propertyAttribute['property_breadth'] ?? 0;
                        $propertyAttributeObj->breadth_unit = $propertyAttribute['breadth_unit'] ?? 0;
                        $propertyAttributeObj->property_road_width = $propertyAttribute['property_road_width'] ?? 0;
                        $propertyAttributeObj->road_width_unit = $propertyAttribute['road_width_unit'] ?? 0;
                    }
                    $propertyAttributeObj->save();
                }
                DB::commit();
                return $serviceProduct;
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
    public static function getSingle($serviceProductId, $user = [])
    {
        $query = ServiceProduct::with([
            'category' => function ($q) {
                $q->select('id', 'name', 'icon');
            }, 'subCategory' => function ($q) {
                $q->select('id', 'name', 'icon');
            },
            'state' => function ($q) {
                $q->select('id', 'name');
            },
            'city' => function ($q) {
                $q->select('id', 'name');
            },
            'locality' => function ($q) {
                $q->select('id', 'name');
            },
            'serviceProductImage',
            'propertyAttribute',
            'serviceUser',
            'serviceUser.kycVerified',
            'serviceUser.userServicesProfile',
            'serviceUser.userServicesProfile.serviceImages',
            'serviceUser.userServicesProfile.state' => function ($q) {
                $q->select('id', 'name');
            },
            'serviceUser.userServicesProfile.city' => function ($q) {
                $q->select('id', 'name');
            },
            'serviceUser.userServicesProfile.locality' => function ($q) {
                $q->select('id', 'name');
            }
        ]);
        $query = $query->where('id', $serviceProductId);
        $serviceProductData = $query->first();
        if (!empty($serviceProductData)) {
            if (!empty($serviceProductData->serviceProductImage)) {
                $responseImage = [];
                foreach ($serviceProductData->serviceProductImage as $serviceProductImage) {
                    $responseImage[] = $serviceProductImage->name;
                }
                unset($serviceProductData->serviceProductImage);
                $serviceProductData->serviceProductImages = $responseImage;
            }
            if (!empty($serviceProductData->serviceUser->userServicesProfile->serviceImages)) {
                $responseServiceImage = [];
                foreach ($serviceProductData->serviceUser->userServicesProfile->serviceImages as $serviceImage) {
                    $responseServiceImage[] = $serviceImage->name;
                }
                unset($serviceProductData->serviceUser->userServicesProfile->serviceImages);
                $serviceProductData->serviceUser->userServicesProfile->serviceImages = $responseServiceImage;
            }
            if (!empty($user)) {
                $serviceProductData->is_bookmark = $serviceProductData->is_bookmarked($user);
            }
        }
        return $serviceProductData;
    }
    /**
     * getServiceProductNotificationList
     *
     * @param  mixed $request
     * @return obj
     */
    public static function getServiceProductNotificationList($serviceProductId)
    {
        $query = ServiceProduct::status()->where('id', $serviceProductId);
        $serviceProductData = $query->first();
        $users = User::leftJoin('service_profiles', 'service_profiles.user_id', '=', 'users.id')
        ->where('locality_id', $serviceProductData->locality_id)
        ->OrWhere('category_id', $serviceProductData->category_id)->get();
        return $users;
    }

    /**
     * list
     *
     * @param  mixed $request
     * @return obj
     */
    public static function list(Request $request)
    {
        if ($request->input('limit', false)) {
            $limit  = $request->input('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        $user = $request->get('Auth');
        $cityArray = $localityArray = $categoryArray = [];
        if ($request->input('keyword', false)) {
            $cityArray = City::filter($request->input('keyword', false))->pluck('id')->toArray();
            $localityArray = Locality::filter($request->input('keyword', false))->pluck('id')->toArray();
            $categoryArray = Category::filter($request->input('keyword', false))->pluck('id')->toArray();
        }
        $query = ServiceProduct::with([
            'category' => function ($q) {
                $q->select('id', 'name', 'icon');
            }, 'subCategory' => function ($q) {
                $q->select('id', 'name', 'icon');
            }, 'serviceProductImage'  => function ($q) {
                $q->select('id', 'name', 'service_product_id');
            },
            'state' => function ($q) {
                $q->select('id', 'name');
            },
            'city' => function ($q) {
                $q->select('id', 'name');
            },
            'locality' => function ($q) {
                $q->select('id', 'name');
            },
            'propertyAttribute',
            'serviceUser',
            'serviceUser.userServicesProfile',
            'serviceUser.kycVerified',
            'serviceUser.userServicesProfile.serviceImages',
            'serviceUser.userServicesProfile.state' => function ($q) {
                $q->select('id', 'name');
            },
            'serviceUser.userServicesProfile.city' => function ($q) {
                $q->select('id', 'name');
            },
            'serviceUser.userServicesProfile.locality' => function ($q) {
                $q->select('id', 'name');
            }
        ]);
        // dd($localityArray,$categoryArray);
        if (!empty($cityArray) || !empty($localityArray) || !empty($categoryArray)) {
            $query = $query->whereIn('city_id', $cityArray)
            ->orWhereIn('locality_id', $localityArray)
            ->orWhereIn('category_id', $categoryArray)
            ->orWhereIn('sub_category_id', $categoryArray);
        }
    
        if ($request->input('category_id', false)) {
            $categoryId  = $request->input('category_id', 0);
            $query = $query->where('category_id', $categoryId);
        }
        if ($request->input('user_id', false)) {
            $userId  = $request->input('user_id', 0);
            $query = $query->where('user_id', $userId);
        } else {
            $query = $query->status();
        }
        $serviceProducts = $query->orderBy('id', 'desc')->paginate($limit);

        if (!empty($serviceProducts)) {
            foreach ($serviceProducts as $sKey => $serviceProduct) {
                if (!empty($serviceProduct->serviceProductImage)) {
                    $responseProductsImage = [];
                    foreach ($serviceProduct->serviceProductImage as $serviceProductImage) {
                        $responseProductsImage[] = $serviceProductImage->name;
                    }
                    unset($serviceProducts[$sKey]->serviceProductImage);
                    $serviceProducts[$sKey]->serviceProductImages = $responseProductsImage;
                }
                if (!empty($serviceProduct->serviceUser->userServicesProfile->serviceImages)) {
                    $responseServiceImage = [];
                    foreach ($serviceProduct->serviceUser->userServicesProfile->serviceImages as $serviceImage) {
                        $responseServiceImage[] = $serviceImage->name;
                    }
                    unset($serviceProducts[$sKey]->serviceUser->userServicesProfile->serviceImages);
                    $serviceProducts[$sKey]->serviceUser->userServicesProfile->serviceImage = $responseServiceImage;
                }
                $serviceProducts[$sKey]->is_bookmark = $serviceProduct->is_bookmarked($user);
            }
        }
        return $serviceProducts;
    }

    /**
     * deleteServiceProduct
     * 
     *
     * @param  mixed $request
     * @return void
     */
    public static function deleteServiceProduct(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            if ($request->input('service_product_id', false) > 0) {
                $serviceProductId = $request->input('service_product_id', false);
                ServiceProduct::where([
                    'id' => $serviceProductId,
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

    /**
     * productStatus
     *
     * @param  mixed $request
     * @return void
     */
    public static function serviceProductStatus(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            if ($request->input('service_product_id', false) > 0) {
                $serviceProductId = $request->input('service_product_id', false);
                $serviceProduct = ServiceProduct::where([
                    'id' => $serviceProductId,
                    'user_id' => $authUser->id,
                ])->first();
                if ($serviceProduct->status == 1) {
                    $serviceProduct->status = 0;
                } else {
                    $serviceProduct->status = 1;
                }
                $serviceProduct->save();
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
     * bookmarkServiceProduct
     *
     * @param  mixed $request
     * @return obj
     */
    public static function bookmarkServiceProduct($request)
    {
        try {
            $authUser = $request->get('Auth');
            if ($request->input('service_product_id', false) > 0) {
                $serviceProductId = $request->input('service_product_id', false);
                $serviceProduct = ServiceProduct::where('id', $serviceProductId)->first();
                if (!empty($serviceProduct)) {
                    $user = User::where('id', $authUser->id)->first();
                    // if (!$serviceProduct->userServiceProductBookmark()->where('service_product_bookmark.user_id', $authUser->id)->exists()) {
                    $serviceProduct->userServiceProductBookmarkUpdate($user);
                    return self::getSingle($serviceProductId, $authUser);
                    // }
                }
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * listBookmarkServiceProduct
     *
     * @param  mixed $request
     * @return obj
     */
    public static function listBookmarkServiceProduct(Request $request)
    {
        if ($request->input('limit', false)) {
            $limit  = $request->input('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        $user = $request->get('Auth');
        $query = ServiceProduct::whereHas('userServiceProductBookmark', function ($q) use ($user) {
            $q->where('service_product_bookmark.user_id', '=', $user->id);
        })->with([
            'category' => function ($q) {
                $q->select('id', 'name', 'icon');
            }, 'subCategory' => function ($q) {
                $q->select('id', 'name', 'icon');
            }, 'serviceProductImage'  => function ($q) {
                $q->select('id', 'name', 'service_product_id');
            },
            'state' => function ($q) {
                $q->select('id', 'name');
            },
            'city' => function ($q) {
                $q->select('id', 'name');
            },
            'locality' => function ($q) {
                $q->select('id', 'name');
            },
            'propertyAttribute',
            'serviceUser',
            'serviceUser.userServicesProfile',
            'serviceUser.userServicesProfile.serviceImages',
            'serviceUser.userServicesProfile.state' => function ($q) {
                $q->select('id', 'name');
            },
            'serviceUser.userServicesProfile.city' => function ($q) {
                $q->select('id', 'name');
            },
            'serviceUser.userServicesProfile.locality' => function ($q) {
                $q->select('id', 'name');
            }
        ]);
        if ($request->input('category_id', false)) {
            $categoryId  = $request->input('category_id', 0);
            $query = $query->where('category_id', $categoryId);
        }
        $serviceProducts = $query->status()->orderBy('id', 'desc')->paginate($limit);
        if (!empty($serviceProducts)) {
            foreach ($serviceProducts as $sKey => $serviceProduct) {
                if (!empty($serviceProduct->serviceProductImage)) {
                    $responseProductsImage = [];
                    foreach ($serviceProduct->serviceProductImage as $serviceProductImage) {
                        $responseProductsImage[] = $serviceProductImage->name;
                    }
                    unset($serviceProducts[$sKey]->serviceProductImage);
                    $serviceProducts[$sKey]->serviceProductImages = $responseProductsImage;
                }
                if (!empty($serviceProduct->serviceUser->userServicesProfile->serviceImages)) {
                    $responseServiceImage = [];
                    foreach ($serviceProduct->serviceUser->userServicesProfile->serviceImages as $serviceImage) {
                        $responseServiceImage[] = $serviceImage->name;
                    }
                    unset($serviceProducts[$sKey]->serviceUser->userServicesProfile->serviceImages);
                    $serviceProducts[$sKey]->serviceUser->userServicesProfile->serviceImage = $responseServiceImage;
                }
                $serviceProducts[$sKey]->is_bookmark = $serviceProduct->is_bookmarked($user);
            }
        }
        return $serviceProducts;
    }
}
