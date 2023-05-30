<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminRoleRequest;
use Illuminate\Http\Request;
use App\Models\AdminRole;
use Gate;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = Gate::inspect('check-user', "admin_users-index");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        $query = AdminRole::status(app('request')->query('status'))->filter(app('request')->query('keyword'));
        $adminRoles = $query->sortable(['created_at' => 'DESC'])->paginate(10);
        //dd($adminRoles);
        return view('Admin.adminRoles.index', compact('adminRoles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $response = Gate::inspect('check-user', "roles-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        return view('Admin.adminRoles.createOrUpdate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminRoleRequest $request)
    {
        DB::beginTransaction();
        try {
            $adminRole = AdminRole::create($request->input());
            event(new Registered($adminRole));
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.adminroles.index', app('request')->query())->with('success', 'Admin role has been saved successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AdminRole  $adminRole
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $role_id)
    {
        $adminRole = AdminRole::status(1)->where('id', $role_id)->first();
        return view('Admin.adminRoles.show', compact('adminRole'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AdminUser  $adminUser
     * @return \Illuminate\Http\Response
     */
    public function edit(AdminRole $adminRole)
    {
        $response = Gate::inspect('user-index', "roles");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        return view('Admin.adminRoles.createOrUpdate', compact('adminRole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\AdminRoleRequest  $request
     * @param  \App\Models\AdminRole  $adminRole
     * @return \Illuminate\Http\Response
     */
    public function update(AdminRoleRequest $request, AdminRole $adminRole)
    {
        DB::beginTransaction();
        try {
            
            $adminRole->fill($request->input());
            $adminRole->save();
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
        if (app('request')->query('redirect')) {
            return redirect(app('request')->query('redirect'))->with('success', 'Admin role has been updated successfully');
        }
        return redirect()->route('admin.adminroles.index', app('request')->query())->with('success', 'Admin role has been updated successfully');
    }
}
