<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MarketingRequest;
use App\Models\Marketing;
use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MarketingsController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Marketing::sortable(['created_at' => 'desc'])
            ->status()
            ->filter($request->query('keyword'));
        $marketings = $query->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.marketings.index', compact('marketings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Admin.marketings.createOrUpdate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(MarketingRequest $request)
    {
        try {
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            $requestData['type'] = ($requestData['type'] == 1) ? 'image' : 'video';
            
            if (!empty($request->file('media_name'))) {
                $mediaName = $request->file('media_name');
                $uploadFile = time() . rand(100, 999) . '.' . $mediaName->getClientOriginalExtension();
                $mediaName->move(storage_path('app/public/banner/'), $uploadFile);
                $requestData['media_name'] = $uploadFile;
            } elseif ($request->input('url_link', false)) {
                $requestData['media_name'] = $request->input('url_link', false);
            }
            $marketingResponse = Marketing::create($requestData);
            // NotificationRepository::createNotification($marketingResponse, [], 'admin_block');
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.marketings.index')->with('success', 'Marketing has been saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $marketing = Marketing::findOrFail($id);
        return view('Admin.marketings.show', compact('marketing'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $marketing = Marketing::findOrFail($id);
        return view('Admin.marketings.createOrUpdate', compact('marketing'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(MarketingRequest $request, $id)
    {
        try {
            $marketing = Marketing::findOrFail($id);
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            if (!empty($request->file('media_name'))) {
                $mediaName = $request->file('media_name');
                $uploadFile = time() . rand(100, 999) . '.' . $mediaName->getClientOriginalExtension();
                $mediaName->move(storage_path('app/public/banner/'), $uploadFile);
                $requestData['media_name'] = $uploadFile;
            }
            $marketing->fill($requestData);
            $marketing->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.marketings.index')->with('success', 'Marketing has been updated successfully.');
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
            $marketing = Marketing::findOrFail($id);
            unlink(storage_path('app/public/banner/'.$marketing->media_name));
            $marketing->delete();
            DB::commit();
            $responce = ['status' => true, 'message' => 'Marketing has been deleted successfully.', 'data' => ''];
        } catch (\Exception $e) {
            DB::rollBack();
            $responce = ['status' => false, 'message' => $e->getMessage()];
        }
        return $responce;
    }

}
