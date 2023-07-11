<?php

namespace App\Repositories;

use App\Models\PropertyAttribute;
use App\Models\ServiceProduct;
use App\Models\ServiceProductImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function is;
use function is_null;
use Exception;

class ServiceProductRepository
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
            $serviceProduct = new ServiceProduct();
            $serviceProduct->user_id = $authUser->id;
            $serviceProduct->product_type = $serviceProductType = $request->input('product_type', false);
            $serviceProduct->product_for = $request->input('product_for', false);
            $serviceProduct->category_id = $request->input('category_id', false);
            $serviceProduct->sub_category_id = $request->input('sub_category_id', false);
            $serviceProduct->title = $request->input('title', false);
            $serviceProduct->locality = $request->input('locality', false);
            $serviceProduct->city = $request->input('city', false);
            $serviceProduct->address = $request->input('address', false);
            $serviceProduct->price = $request->input('price', false);
            $serviceProduct->description = $request->input('description', false);
            $serviceProduct->latitude = $request->input('latitude', false);
            $serviceProduct->longitude = $request->input('longitude', false);
            $serviceProductImages = $request->input('service_product_images') ?? [];
            $propertyAttribute = $request->input('property_attribute') ?? [];
            if ($serviceProduct->save()) {
                if (!empty($serviceProductImages)) {
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
                    $propertyAttributeObj = new PropertyAttribute();
                    $propertyAttributeObj->service_product_id = $serviceProduct->id;
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
    public static function getSingle($serviceProductId, $user)
    {
        $query = ServiceProduct::status()->with([
            'category' => function ($q) {
                $q->select('id', 'name', 'icon');
            }, 'subCategory' => function ($q) {
                $q->select('id', 'name', 'icon');
            },
            'serviceProductImage',
            'propertyAttribute',
            'serviceUser',
            'serviceUser.userServicesProfile',
            'serviceUser.userServicesProfile.serviceImages'
        ]);

        $query = $query->where('id', $serviceProductId);
        $serviceProductData = $query->first();
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
        $serviceProductData->is_bookmark = $serviceProductData->is_bookmarked($user);
        return $serviceProductData;
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
        $query = ServiceProduct::with([
            'category' => function ($q) {
                $q->select('id', 'name', 'icon');
            }, 'subCategory' => function ($q) {
                $q->select('id', 'name', 'icon');
            }, 'serviceProductImage'  => function ($q) {
                $q->select('id', 'name', 'service_product_id');
            },
            'propertyAttribute',
            'serviceUser',
            'serviceUser.userServicesProfile',
            'serviceUser.userServicesProfile.serviceImages'
        ]);
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
            $q->where('user_id', '=', $user->id);
        })->with([
            'category' => function ($q) {
                $q->select('id', 'name', 'icon');
            }, 'subCategory' => function ($q) {
                $q->select('id', 'name', 'icon');
            }, 'serviceProductImage'  => function ($q) {
                $q->select('id', 'name', 'service_product_id');
            },
            'propertyAttribute',
            'serviceUser',
            'serviceUser.userServicesProfile',
            'serviceUser.userServicesProfile.serviceImages'
        ]);
        if ($request->input('category_id', false)) {
            $categoryId  = $request->input('category_id', 0);
            $query = $query->where('category_id', $categoryId);
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
}
