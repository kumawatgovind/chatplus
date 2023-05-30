<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiGlobalFunctions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\State;
use App\Models\Country;
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
    public function categoryList()
    {
        $data = [];
        try {
            $categories = Category::select('id', 'title', 'parent_id')
            ->with(['childrenCategory' => function($q) {
                return $q->select('id', 'title', 'parent_id');
            }])->where(['status' => 1, 'parent_id' => 0])->get();
            $result = [];
            if ($categories->count() > 0) {
                foreach ($categories as $cKey => $category) {
                    $result[$cKey]['id'] = $category->id;
                    $result[$cKey]['title'] = $category->title;
                    if ($category->childrenCategory->count() > 0) {
                        foreach ($category->childrenCategory as $sCkey => $childrenCategory) {
                            $result[$cKey]['sub_category'][$sCkey]['id'] = $childrenCategory->id;
                            $result[$cKey]['sub_category'][$sCkey]['title'] = $childrenCategory->title;
                        }
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
