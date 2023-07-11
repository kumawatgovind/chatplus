<?php

namespace App\Http\Controllers\Admin;

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
        $categories = Category::with('parent')
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));

        return view('Admin.categories.index', compact('categories'));
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
}
