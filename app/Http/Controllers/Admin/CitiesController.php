<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Models\State;
use App\Models\ServiceProduct;
use App\Helpers\Helper;
use Validator, Gate, DB;


class CitiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $cities = City::sortable(['created_at' => 'desc'])
        ->with('state')->filter($request->query('keyword'))
        ->paginate(config('get.ADMIN_PAGE_LIMIT'));

        return view('Admin.cities.index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = State::pluck('name', 'id')->toArray();
        return view('Admin.cities.createOrUpdate', compact('states'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CityRequest $request)
    {
        try {
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            City::create($requestData);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.cities.index')->with('success', 'City has been saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(City $city)
    {
        $response = Gate::inspect('check-user', "locations-index");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        dd('no');
        // return view('Admin.cities.show', compact('city'));
    }

    /**
     * List of city from state id.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCityByStateId($stateId = 0)
    {
        $cities = City::where('state_id', $stateId)->pluck('name', 'id')->toArray();
        dd($cities);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $city = City::findOrFail($id);
        $states = State::pluck('name', 'id')->toArray();
        return view('Admin.cities.createOrUpdate', compact('city', 'states'));
    }

  
    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(CityRequest $request, $id)
    {

        try {
            $city = City::findOrFail($id);
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            $city->fill($requestData);
            $city->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.cities.index')->with('success', 'City has been updated successfully.');
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
            $city = City::findOrFail($id);
            $adsQuery = ServiceProduct::query();
            if (!empty($city)) {
                $locality = Locality::where('city_id', $state->id);
                $adsQuery->where('city_id', $city->id);
                if ($adsQuery->count() > 0 || $locality->count() > 0) {
                    $response = ['status' => false, 'message' => "City not able to delete due to some associated data."];
                } else {
                    $city->delete();
                    DB::commit();
                    $response = ['status' => true, 'message' => 'This city has been deleted successfully.', 'data' => ''];
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
        return view('Admin.cities.import', compact('states'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $validated = $request->validate([
            'state_id' => 'required',
        ]); 
        $stateId = $request->input('state_id', 0);
        $targetPath = $request->file('import')->store('files');
        $sPath = storage_path('app');
        $filePath = $sPath.'/'.$targetPath;
        $importData = Helper::getXlsxData($filePath);
        $requestData = [];
        foreach ($importData as $data) {
            $cityName = $data[0];
            if ($cityName) {
                $cityData = City::where([
                    'name' => $cityName,
                    'state_id' => $stateId
                    ])->first();
                if (empty($cityData)) {
                    $requestData['status'] = 1;
                    $requestData['state_id'] = $stateId;
                    $requestData['name'] = $cityName;
                    City::create($requestData);
                }
            }
        }
        unlink(storage_path('app/'.$targetPath));
        return redirect()->route('admin.cities.index')->with('success', 'All city has been imported successfully');
    }
}
