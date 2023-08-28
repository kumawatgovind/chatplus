<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function is;
use function is_null;
use Exception;

class ProductRepository
{

    /**
     * getSingle
     *
     * @param  mixed $request
     * @return obj
     */
    public static function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            $product = new Product();
            $product->user_id = $authUser->id;
            $product->title = $request->input('title', false);
            // $product->locality = $request->input('locality', false);
            // $product->city = $request->input('city', false);
            $product->locality_id = $request->input('locality_id', 0);
            $product->city_id = $request->input('city_id', 0);
            $product->state_id = $request->input('state_id', 0);
            $product->address = $request->input('address', false);
            $product->price = $request->input('price', false);
            $product->description = $request->input('description', false);
            $product->latitude = $request->input('latitude', false);
            $product->longitude = $request->input('longitude', false);
            $productImages = $request->input('product_images') ?? [];
            if ($product->save()) {
                if (!empty($productImages)) {
                    $ordering = 1;
                    foreach ($productImages as $value) {
                        if (!empty($value)) {
                            $attachmentData = [];
                            $attachmentData['product_id'] = $product->id;
                            $attachmentData['name'] = $value;
                            $attachmentData['ordering'] = $ordering;
                            ProductImage::create($attachmentData);
                            $ordering++;
                        }
                    }
                }
                DB::commit();
                return $product;
            } else {
                DB::rollBack();
                return false;
            }
        } catch (Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            return false;
        }
    }

    public static function update(Request $request)
    {
        DB::beginTransaction();
        try {
            $update = [];
            $authUser = $request->get('Auth');
            $productId = $request->input('product_id', false);
            if ($request->input('title', false)) {
                $update['title'] = $request->input('title', false);
            }
            if ($request->input('locality_id', false)) {
                $update['locality_id'] = $request->input('locality_id', false);
            }
            if ($request->input('city_id', false)) {
                $update['city_id'] = $request->input('city_id', false);
            }
            if ($request->input('state_id', false)) {
                $update['state_id'] = $request->input('state_id', false);
            }
            if ($request->input('address', false)) {
                $update['address'] = $request->input('address', false);
            }
            if ($request->input('price', false)) {
                $update['price'] = $request->input('price', false);
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
            $productImages = $request->input('product_images') ?? [];
            if (Product::where('id', $productId)->update($update)) {
                if (!empty($productImages)) {
                    ProductImage::where([
                        'product_id' => $productId
                    ])->delete();
                    $ordering = 1;
                    foreach ($productImages as $value) {
                        if (!empty($value)) {
                            $attachmentData = [];
                            $attachmentData['product_id'] = $productId;
                            $attachmentData['name'] = $value;
                            $attachmentData['ordering'] = $ordering;
                            ProductImage::create($attachmentData);
                            $ordering++;
                        }
                    }
                }
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
    public static function getSingle($productId)
    {
        $query = Product::status()
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
                'productImage',
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

        $query = $query->where('id', $productId);
        $productData = $query->first();
        if (!empty($productData->productImage)) {
            $responseImage = [];
            foreach ($productData->productImage as $productImage) {
                $responseImage[] = $productImage->name;
            }
            unset($productData->productImage);
            $productData->productImages = $responseImage;
        }
        if (!empty($productData->serviceUser->userServicesProfile->serviceImages)) {
            $responseServiceImage = [];
            foreach ($productData->serviceUser->userServicesProfile->serviceImages as $serviceImage) {
                $responseServiceImage[] = $serviceImage->name;
            }
            unset($productData->serviceUser->userServicesProfile->serviceImages);
            $productData->serviceUser->userServicesProfile->serviceImages = $responseServiceImage;
        }
        return $productData;
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
        $query = Product::status()
            ->with([
                'productImage',
                'users',
                'state' => function ($q) {
                    $q->select('id', 'name');
                },
                'city' => function ($q) {
                    $q->select('id', 'name');
                },
                'locality' => function ($q) {
                    $q->select('id', 'name');
                },
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
        if ($request->input('user_id', false)) {
            $query = $query->where('user_id', $request->input('user_id', false));
        } else {
            $query = $query->where('user_id', $userId);
        }
        $products = $query->orderBy('id', 'desc')->paginate($limit);
        if (!empty($products)) {
            foreach ($products as $sKey => $product) {
                if (!empty($product->productImage)) {
                    $responseProductsImage = [];
                    foreach ($product->productImage as $productImage) {
                        $responseProductsImage[] = $productImage->name;
                    }
                    unset($products[$sKey]->productImage);
                    $products[$sKey]->productImages = $responseProductsImage;
                }
                if (!empty($product->serviceUser->userServicesProfile->serviceImages)) {
                    $responseServiceImage = [];
                    foreach ($product->serviceUser->userServicesProfile->serviceImages as $serviceImage) {
                        $responseServiceImage[] = $serviceImage->name;
                    }
                    unset($products[$sKey]->serviceUser->userServicesProfile->serviceImages);
                    $products[$sKey]->serviceUser->userServicesProfile->serviceImage = $responseServiceImage;
                }
            }
        }
        return $products;
    }

    /**
     * deleteProduct
     *
     * @param  mixed $request
     * @return void
     */
    public static function deleteProduct(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            if ($request->input('product_id', false) > 0) {
                $productId = $request->input('product_id', false);
                Product::where([
                    'id' => $productId,
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
