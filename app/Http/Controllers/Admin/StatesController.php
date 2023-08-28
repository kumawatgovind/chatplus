<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StateRequest;
use App\Models\State;
use App\Models\City;
use App\Models\ServiceProduct;
use App\Helpers\Helper;
use Gate, DB;


class StatesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $states = State::sortable(['created_at' => 'desc'])
        ->with('country')
        ->filter($request->query('keyword'))
        ->paginate(config('get.ADMIN_PAGE_LIMIT'));

        return view('Admin.states.index', compact('states'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Admin.states.createOrUpdate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StateRequest $request)
    {
        try {
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            $requestData['country_id'] = config('constants.DEFAULT_COUNTRY');
            State::create($requestData);
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.states.index')->with('success', 'State has been saved successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(State $state)
    {
        dd('no');
        // return view('Admin.states.show', compact('state'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $state = State::findOrFail($id);

        return view('Admin.states.createOrUpdate', compact('state'));
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
    public function update(StateRequest $request, $id)
    {
        
        try {
            $state = State::findOrFail($id);
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            $requestData['country_id'] = config('constants.DEFAULT_COUNTRY');
            $state->fill($requestData);
            $state->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.states.index')->with('success', 'State has been updated successfully.');
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
            $state = State::findOrFail($id);
            $adsQuery = ServiceProduct::query();
            if (!empty($state)) {
                $cityQuery = City::where('state_id', $state->id);
                $adsQuery->where('state_id', $state->id);
                if ($adsQuery->count() > 0 || $cityQuery->count() > 0) {
                    $response = ['status' => false, 'message' => "State not able to delete due to some associated data."];
                } else {
                    $state->delete();
                    DB::commit();
                    $response = ['status' => true, 'message' => 'This state has been deleted successfully.', 'data' => ''];
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
        return view('Admin.states.import');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $targetPath = $request->file('import')->store('files');
        $sPath = storage_path('app');
        $filePath = $sPath.'/'.$targetPath;
        $importData = Helper::getXlsxData($filePath);
        $requestData = [];
        foreach ($importData as $data) {
            $stateName = $data[0];
            if ($stateName) {
                $stateData = State::where('name', $stateName)->first();
                if (empty($stateData)) {
                    $requestData['status'] = 1;
                    $requestData['country_id'] = config('constants.DEFAULT_COUNTRY');
                    $requestData['name'] = $stateName;
                    State::create($requestData);
                }
            }
        }
        unlink(storage_path('app/'.$targetPath));
        return redirect()->route('admin.states.index')->with('success', 'All state has been imported successfully');
    }

}
