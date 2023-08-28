<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CityRequest;
use App\Models\City;
use App\Models\Advertisement;
use App\Models\User;
use Gate, DB;


class PayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function payoutTotal(Request $request)
    {
        // $response = Gate::inspect('check-user', "locations-index");
        // if (!$response->allowed()) {
        //     return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        // }
        $payout = User::sortable(['created_at' => 'desc'])->filter($request->query('keyword'))->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.payout.index', compact('payout'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $response = Gate::inspect('check-user', "locations-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }

        return view('Admin.cities.createOrUpdate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CityRequest $request)
    {
        $response = Gate::inspect('check-user', "locations-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        try {
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            $requestData['country_id'] = config('constants.DEFAULT_COUNTRY');
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $response = Gate::inspect('check-user', "locations-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        $city = City::findOrFail($id);

        return view('Admin.cities.createOrUpdate', compact('city'));
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
    public function update(CityRequest $request, $id)
    {

        $response = Gate::inspect('check-user', "locations-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        try {
            $city = City::findOrFail($id);
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            $requestData['country_id'] = config('constants.DEFAULT_COUNTRY');
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

        $response = Gate::inspect('check-user', "locations-create");
        if (!$response->allowed()) {
            // return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
            $responce = ['status' => false, 'message' => $response->message()];
        } else {
            DB::beginTransaction();
            try {
                $city = City::findOrFail($id);
                $adsQuery = Advertisement::query();
                if (!empty($city)) {
                    $adsQuery->where('city_id', $city->id);
                }
                $adsCount = $adsQuery->count();
                if ($adsCount) {
                    $responce = ['status' => false, 'message' => "Business category not able to delete due to some associated data."];
                } else {
                    $city->delete();
                    DB::commit();
                    $responce = ['status' => true, 'message' => 'This city has been deleted successfully.', 'data' => ['id' => $id]];
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $responce = ['status' => false, 'message' => $e->getMessage()];
            }
        }
        return $responce;
    }
}
