<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Category;
use Exception;
use Carbon\Carbon;


class CommonController extends Controller
{
    use ApiGlobalFunctions;

    /**
     * Roles List.
     * 
     * @param Request $request Illuminate\Http\Request
     * @return [].
     */
    public function categoryList($categoryId = 0)
    {
        
        $data = [];
        try {
            $categories = Category::select('id', 'name', 'icon');
            if ($categoryId) {
                $categories = $categories->where(['status' => 1, 'parent_id' => $categoryId]);
            } else {
                $categories = $categories->where(['status' => 1, 'parent_id' => 0]);
            }
            $categoryData = $categories->get();
            $result = [];
            if ($categoryData->count() > 0) {
                foreach ($categoryData as $cKey => $category) {
                    $result[$cKey]['id'] = $category->id;
                    $result[$cKey]['name'] = $category->name;
                    if (!empty($category->icon)) {
                        $result[$cKey]['icon'] = $category->icon_url;
                    }
                }
            }
            $data['status'] = true;
            $data['code'] = config('response.HTTP_OK');
            $data['message'] = $this->messageDefault('record_found');
            $data['data'] = $result;
        } catch (Exception $e) {
            $data['status'] = false;
            $data['code'] =  $e->getCode();
            $data['message'] = 'Error: ' . $e->getMessage();
        }
        return $this->responseBuilder($data);
    }

}
