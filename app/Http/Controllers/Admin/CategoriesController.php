<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Gate;
use File;

class CategoriesController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $response = Gate::inspect('check-user', "categories-index");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        $parentCategories = Category::where(['status' => 1, 'parent_id' => 0])->pluck('name', 'id')->toArray();
        $query = Category::with('parent')
            ->sortable(['created_at' => 'desc'])
            ->status()
            ->filter($request->query('keyword'));
        if ($request->query('category_id') > 0) {
            $query = $query->where('parent_id', $request->query('category_id'));
        } elseif ($request->query('category_id') == -1) {
            $query = $query->where('parent_id', 0);
        }
        $categories = $query->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $response = Gate::inspect('check-user', "categories-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }

        $parentIds = Category::select('id', 'name', 'parent_id')->where(['status' => 1, 'parent_id' => 0])->get();
        $categories = $this->makeTree($parentIds);
        return view('Admin.categories.createOrUpdate', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        try {
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            if (empty($requestData['parent_id'])) {
                $requestData['parent_id'] = 0;
            }
            if (!empty($request->file('icon'))) {
                $iconName = $request->file('icon');
                $uploadFile = time() . rand(100, 999) . '.' . $iconName->getClientOriginalExtension();
                $iconName->move(storage_path('app/public/category/'), $uploadFile);
                $requestData['icon'] = $uploadFile;
            }
            Category::create($requestData);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.categories.index')->with('success', 'Category has been saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $response = Gate::inspect('check-user', "categories-index");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        $category = Category::with('parent')->findOrFail($id);
        return view('Admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $response = Gate::inspect('check-user', "categories-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }

        $parentIds = Category::select('id', 'name', 'parent_id')->where(['status' => 1, 'parent_id' => 0])->where('id', '!=', $id)->get();

        $category = Category::findOrFail($id);
        $categories = $this->makeTree($parentIds, $category->parent_id);
        return view('Admin.categories.createOrUpdate', compact('category', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(CategoryRequest $request, $id)
    {
        try {
            $category = Category::findOrFail($id);
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            if (empty($requestData['parent_id'])) {
                $requestData['parent_id'] = 0;
            }
            if (!empty($request->file('icon'))) {
                $iconName = $request->file('icon');
                $uploadFile = time() . rand(100, 999) . '.' . $iconName->getClientOriginalExtension();
                $iconName->move(storage_path('app/public/category/'), $uploadFile);
                $requestData['icon'] = $uploadFile;
            }
            $category->fill($requestData);
            $category->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.categories.index')->with('success', 'Category has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id = null)
    {
        DB::beginTransaction();
        try {
            $category = Category::findOrFail($id);
            $category->delete();
            DB::commit();
            $responce = ['status' => true, 'message' => 'Category has been deleted successfully.', 'data' => ''];
        } catch (\Exception $e) {
            DB::rollBack();
            $responce = ['status' => false, 'message' => $e->getMessage()];
        }
        return $responce;
    }


    function makeTree($categories, $selected = '0')
    {
        $data = [];
        $k = 0;
        /* First Lavel */
        foreach ($categories as $category) {
            $data[$k]['id'] = $category->id;
            $data[$k]['text'] = $category->name;
            $data[$k]['prefix'] = "";
            $data[$k]['position'] = "";
            $data[$k]['selected'] = false;
            if ($selected == $category->id) {
                $data[$k]['selected'] = true;
            }
            $k++;
        }
        return $data;
    }



    function getCategoryTree($parent_id = 0, $tree_array = array())
    {
        $categories = Category::select('id', 'name', 'parent_id')->with('children')->where('parent_id', '=', $parent_id)->orderBy('parent_id')->get();
        foreach ($categories as $item) {
            $tree_array[] = $item;
            $tree_array = $this->getCategoryTree($item->id, $tree_array);
        }
        return $tree_array;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function importView()
    {
        $categories = Category::where('parent_id', 0)->pluck('name', 'id')->toArray();
        return view('Admin.categories.import', compact('categories'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'import' => 'required|max:10000|mimes:xls,xlsx'
        ]); 
        $categoryId = $request->input('category_id', 0);
        $targetPath = $request->file('import')->store('files');
        $sPath = storage_path('app');
        $filePath = $sPath.'/'.$targetPath;
        $importData = Helper::getXlsxData($filePath);
        $requestData = [];
        foreach ($importData as $data) {
            $categoryName = $data[0];
            if ($categoryName) {
                $categoryData = Category::where([
                    'name' => $categoryName,
                    'parent_id' => $categoryId
                    ])->first();
                if (empty($categoryData)) {
                    $requestData['status'] = 1;
                    $requestData['parent_id'] = $categoryId;
                    $requestData['name'] = $categoryName;
                    Category::create($requestData);
                }
            }
        }
        unlink(storage_path('app/'.$targetPath));
        return redirect()->route('admin.categories.index')->with('success', 'All category has been imported successfully');
    }
}
