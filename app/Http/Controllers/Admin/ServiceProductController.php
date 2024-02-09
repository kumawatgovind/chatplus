<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ServiceProduct;
use DB;

class ServiceProductController extends Controller
{
    /**
     * Display a listing of the total service.
     *
     * @return \Illuminate\Contracts\View\Factory
     */
    public function getTotalService(Request $request)
    {
        $serviceProduct = ServiceProduct::with('serviceUser')
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.serviceProduct.totalService', compact('serviceProduct'));
    }

    /**
     * Display a listing of the mark re kyc.
     *
     * @return \Illuminate\Contracts\View\Factory
     */
    public function getDeletedService(Request $request)
    {
        $serviceProduct = ServiceProduct::onlyTrashed()
            ->with('serviceUser')
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.serviceProduct.deletedService', compact('serviceProduct'));
    }


    /**
     * Display a listing of the mark re kyc.
     *
     * @return \Illuminate\Http\Response
     */
    public function serviceProductShow(Request $request, $serviceProductId)
    {
        $user = User::leftJoin('service_products', 'service_products.user_id', 'users.id')
            ->leftJoin('categories', 'categories.id', 'service_products.category_id')
            ->leftJoin('categories as subCategories', 'subCategories.id', 'service_products.sub_category_id')
            ->select(
                'users.*',
                'service_products.id as service_product_id',
                'service_products.title',
                'service_products.price',
                'service_products.product_type',
                'service_products.product_for',
                'categories.name as catName',
                'subCategories.name as subCatName',
                'service_products.status as product_status',
                'service_products.created_at as service_added',
                'service_products.updated_at as service_updated',
            )
            ->whereNull('service_products.deleted_at')
            ->where('service_products.id', $serviceProductId)
            ->sortable(['created_at' => 'desc'])
            ->first();
        return view('Admin.serviceProduct.show', compact('user'));
    }

    /**
     * serviceProductDelete
     *
     * @param  mixed $id
     * @return void
     */
    public function serviceProductDelete($id = 0)
    {
        DB::beginTransaction();
        try {
            $serviceProduct = ServiceProduct::findOrFail($id);
            $serviceProduct->delete();
            DB::commit();
            $responce = ['status' => true, 'message' => 'Ad has been deleted successfully.', 'data' => ''];
        } catch (\Exception $e) {
            DB::rollBack();
            $responce = ['status' => false, 'message' => $e->getMessage()];
        }
        return $responce;
    }
}
