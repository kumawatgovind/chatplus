<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LocalityRequest;
use App\Models\City;
use App\Models\Locality;
use App\Models\ServiceProduct;
use App\Helpers\Helper;
use App\Models\State;
use Validator, Gate, DB;


class LocalitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $localities = Locality::sortable(['created_at' => 'desc'])
        ->with('city')->filter($request->query('keyword'))
        ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.localities.index', compact('localities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cities = City::pluck('name', 'id')->toArray();
        return view('Admin.localities.createOrUpdate', compact('cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LocalityRequest $request)
    {
        try {
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            Locality::create($requestData);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.localities.index')->with('success', 'Locality has been saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(Locality $locality)
    {
        dd('no');
        // return view('Admin.localities.show', compact('locality'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $locality = Locality::findOrFail($id);
        $cities = City::pluck('name', 'id')->toArray();
        return view('Admin.localities.createOrUpdate', compact('locality', 'cities'));
    }

  
    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(LocalityRequest $request, $id)
    {

        try {
            $locality = Locality::findOrFail($id);
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            $locality->fill($requestData);
            $locality->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.localities.index')->with('success', 'Locality has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = [];
        DB::beginTransaction();
        try {
            $locality = Locality::findOrFail($id);
            $adsQuery = ServiceProduct::query();
            if (!empty($locality)) {
                $adsQuery->where('locality_id', $locality->id);
                if ($adsQuery->count() > 0) {
                    $response = ['status' => false, 'message' => "Locality not able to delete due to some associated data."];
                } else {
                    $locality->delete();
                    DB::commit();
                    $response = ['status' => true, 'message' => 'This locality has been deleted successfully.', 'data' => ''];
                }
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => false, 'message' => $e->getMessage()];
        }
        return $response;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function importView()
    {
        $states = State::pluck('name', 'id')->toArray();
        return view('Admin.localities.import', compact('states'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $request->validate([
            'state_id' => 'required',
            'city_id' => 'required',
        ]); 
        $cityId = $request->input('city_id', 0);
        $targetPath = $request->file('import')->store('files');
        $sPath = storage_path('app');
        $filePath = $sPath.'/'.$targetPath;
        $importData = Helper::getXlsxData($filePath);
        $requestData = [];
        foreach ($importData as $data) {
            $localityName = $data[0];
            if ($localityName) {
                $localityData = Locality::where([
                    'name' => $localityName,
                    'city_id' => $cityId
                    ])->first();
                if (empty($localityData)) {
                    $requestData['status'] = 1;
                    $requestData['city_id'] = $cityId;
                    $requestData['name'] = $localityName;
                    Locality::create($requestData);
                }
            }
        }
        unlink(storage_path('app/'.$targetPath));
        return redirect()->route('admin.localities.index')->with('success', 'All locality has been imported successfully');
    }
}
