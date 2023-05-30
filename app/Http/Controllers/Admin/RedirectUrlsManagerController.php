<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\RedirectUrl;
use App\Http\Requests\RedirectUrlsRequest;
use DB;
use Illuminate\Support\Facades\Storage;
use Gate;

class RedirectUrlsManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */ 
    public function index(Request $request)
    {
        $response = Gate::inspect('user-index', "redirecturls");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }        
        $allowed_columns = ['id', 'old_url', 'new_url'];
        $sort            = in_array($request->get('sort'), $allowed_columns) ? $request->get('sort') : 'id';
        $order           = $request->get('direction') === 'asc' ? 'asc' : 'desc';
        $query = RedirectUrl::status($request->query('status'))->filter($request->query('keyword'))->orderBy('id', 'asc');
        $redirectUrl  = $query->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.redirecturls.index',compact('redirectUrl'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $response = Gate::inspect('user-index', "redirecturls");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }        
        return view('Admin.redirecturls.createOrUpdate');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(RedirectUrlsRequest $request)
    {
        try {
            RedirectUrl::create($request->all());
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.redirecturls.index')->with('success', 'Redirect url has been saved Successfully');
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $response = Gate::inspect('user-index', "redirecturls");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        $redirect = RedirectUrl::find($id);
        return view('Admin.redirecturls.show',compact('redirect'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $redirectUrl = RedirectUrl::find($id);
        return view('Admin.redirecturls.createOrUpdate',compact('redirectUrl'));
    }

     /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(RedirectUrlsRequest $request, $id) {
        try {
            $redirect = RedirectUrl::find($id);
            $redirect->old_url   = $request->input('old_url');
            $redirect->new_url   = $request->input('new_url');
            $redirect->updated_at = date("Y-m-d h:i:s");
            $redirect->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }

        return redirect()->route('admin.redirecturls.index')->with('success', 'Redirect url has been updated Successfully');
    }

   /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id) {
        $response = Gate::inspect('user-index', "redirecturls");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        DB::beginTransaction();
        $redirect = RedirectUrl::find($id);
        try {
            $redirect->delete();
            DB::commit();
            $responce = ['status' => true, 'message' => 'This redirect urls has been deleted Successfully!', 'data' => $redirect];
        } catch (\Exception $e) {
            DB::rollBack();
            $responce = ['status' => false, 'message' => $e->getMessage()];
        }
        return $responce;
    }

    
}