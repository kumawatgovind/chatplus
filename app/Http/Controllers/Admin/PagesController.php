<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PageRequest;
use App\Models\Page;
use Gate;

class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pages = Page::sortable(['created_at' => 'desc'])->filter($request->query('keyword'))->paginate(config('get.ADMIN_PAGE_LIMIT'));

        return view('Admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $response = Gate::inspect('check-user', "pages-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        return view('Admin.pages.createOrUpdate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PageRequest $request)
    {
        $response = Gate::inspect('check-user', "pages-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        try {
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            // $requestData['status'] = 1;
            $requestData['position'] = (isset($requestData['position']) ? (is_array($requestData['position']) ? implode(',', $requestData['position']) : @$requestData['position']) : "left");
            Page::create($requestData);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.pages.index')->with('success', 'Informative Content has been saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(Page $page)
    {
        $response = Gate::inspect('check-user', "pages-index");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        return view('Admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $response = Gate::inspect('check-user', "pages-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        $page = Page::findOrFail($id);

        return view('Admin.pages.createOrUpdate', compact('page'));
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
    public function update(PageRequest $request, $id)
    {
        $response = Gate::inspect('check-user', "pages-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        try {
            $page = Page::findOrFail($id);
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            $page->fill($requestData);
            $page->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.pages.index')->with('success', 'Informative Content has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Page $page)
    {
        $response = Gate::inspect('check-user', "pages-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        try {
            $page->delete();
            $responce = ['status' => true, 'message' => 'This Informative Content has been deleted successfully.', 'data' => $page];
        } catch (\Exception $e) {

            $responce = ['status' => false, 'message' => $e->getMessage()];
        }
        return $responce;
    }
}
