<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\AdminRole;
use DB;
use Gate;

class ModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($role_id)
    {
        $response = Gate::inspect('check-user', "admin_users-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        if ($role_id != \Auth::user()->role_id) {
            // if ($role_id == 1) {
            //     return redirect()->route('admin.dashboard', app('request')->query())->with('error', "Super Admin permission modification cannot be possible!");
            // } else {
                $modules = Module::with('roles')->where('status', 1)->get();
                $roles = AdminRole::where(['status' => 1, 'id' => $role_id])->get();
            // }
        } else {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', "Self permission modification cannot be possible!");
        }
    
        return view('Admin.modules.createOrUpdate', compact('modules', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        $request->validate(['module' => 'required|array|min:1']);

        $response = Gate::inspect('check-user', "admin_users-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        try {
            $requestData = $request->all();
            $reqModel = $requestData['module'];
            DB::table('admin_role_module')->where('admin_role_id', $requestData['admin_role_id'])->delete();
            $modules = Module::where('status', 1)->get();
            $requestPermissionData = [];
            foreach ($modules as $module) {
                if (array_key_exists($module->id, $reqModel)) {
                    $requestPermissionData['module_id'] = $module->id;
                    $requestPermissionData['admin_role_id'] = $requestData['admin_role_id'];
                    // dump($requestPermissionData);
                    DB::table('admin_role_module')->insert($requestPermissionData);
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.adminroles.index')->with('success', 'Permission has been updated successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
