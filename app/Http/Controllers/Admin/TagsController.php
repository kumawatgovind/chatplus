<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TagRequest;
use App\Models\Tag;
use App\Models\Advertisement;
use Gate, DB;


class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tags = Tag::withCount('mentionedPosts')->sortable(['created_at' => 'desc'])->filter($request->query('keyword'))->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Admin.tags.createOrUpdate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TagRequest $request)
    {
        try {
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            
            Tag::create($requestData);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.tags.index')->with('success', 'Tag has been saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(Tag $tag)
    {
        return view('Admin.tags.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tag = Tag::findOrFail($id);

        return view('Admin.tags.createOrUpdate', compact('tag'));
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
    public function update(TagRequest $request, $id)
    {

        try {
            $tag = Tag::findOrFail($id);
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            $tag->fill($requestData);
            $tag->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.tags.index')->with('success', 'Tag has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $responce = [];
        try {
            $tag = Tag::findOrFail($id);
            if ($tag->mentionedPosts()->count() == 0) {
                $tag->delete();
                $status = true;
                $message = 'This tag has been deleted successfully.';
            } else {
                $status = false;
                $message = 'Not able to delete due to associated post existed.';
            }
            $responce = ['status' => $status, 'message' => $message, 'data' => ['id' => $id]];
        } catch (\Exception $e) {
            $responce = ['status' => false, 'message' => $e->getMessage()];
        }  
        return $responce;
    }
}
