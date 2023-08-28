<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\AdminUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Rules\CheckPhone;
use Gate;

class AdminUserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $query = AdminUser::whereNotIn('id', [auth()->guard('admin')->user()->id, 1])->with(['role'])->status(app('request')->query('status'))->filter(app('request')->query('keyword'));
        $adminUsers = $query->sortable(['created_at' => 'DESC'])->paginate(10);
        //dd($adminUsers);
        return view('Admin.adminUsers.index', compact('adminUsers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = AdminRole::active()->whereNotIn('id', [1])->pluck('title', 'id');
        return view('Admin.adminUsers.createOrUpdate', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdminUserRequest $request)
    {

        $response = Gate::inspect('check-user', "admin_users-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        DB::beginTransaction();
        try {
            $requestData = $request->input();
            $requestData['password'] = Hash::make($request->password);
            $requestData['dob'] = date('Y-m-d', strtotime($requestData['dob']));
            $adminUser = AdminUser::create($requestData);
            event(new Registered($adminUser));
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.admin-users.index', app('request')->query())->with('success', 'Admin user has been saved successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AdminUser  $adminUser
     * @return \Illuminate\Http\Response
     */
    public function show(AdminUser $adminUser)
    {
        $response = Gate::inspect('check-user', "admin_users-index");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        $roles = AdminRole::active()->pluck('title', 'id');
        return view('Admin.adminUsers.show', compact('adminUser', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AdminUser  $adminUser
     * @return \Illuminate\Http\Response
     */
    public function edit(AdminUser $adminUser)
    {
        $response = Gate::inspect('check-user', "admin_users-create");
        if (!$response->allowed() || auth()->guard('admin')->user()->id == $adminUser->id || $adminUser->id == 1) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        $roles = AdminRole::active()->pluck('title', 'id');
        return view('Admin.adminUsers.createOrUpdate', compact('adminUser', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AdminUser  $adminUser
     * @return \Illuminate\Http\Response
     */
    public function update(AdminUserRequest $request, AdminUser $adminUser)
    {
        // dd($request);
        if (!isset($request->status)) {
            $request['status'] = 0;
        } else {
            $request['status'] = 1;
        }
        $response = Gate::inspect('check-user', "admin_users-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        DB::beginTransaction();
        try {
            $adminUser->fill($request->input());
            $adminUser->dob = date('Y-m-d', strtotime($adminUser->dob));
            $adminUser->save();
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
        if (app('request')->query('redirect')) {
            return redirect(app('request')->query('redirect'))->with('success', 'Your profile has been updated successfully');
        }
        return redirect()->route('admin.admin-users.index', app('request')->query())->with('success', 'Admin User has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AdminUser  $adminUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdminUser $adminUser)
    {
        $response = Gate::inspect('check-user', "admin_users-index");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }

        DB::beginTransaction();
        try {
            $adminUser->delete();
            DB::commit();
            $responce = ['status' => true, 'message' => 'This admin user has been deleted successfully.', 'data' => $adminUser];
        } catch (\Exception $e) {
            DB::rollBack();
            $responce = ['status' => false, 'message' => $e->getMessage()];
        }
        return $responce;
    }


    public function reports()
    {
        $start_date = '';
        $end_date   = '';
        $recordType = [];
        $type  = request('type');

        return view('Admin.adminUsers.report', compact('recordType', 'start_date', 'end_date', 'type'));
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AdminUser  $adminUser
     * @return \Illuminate\Http\Response
     */
    public function profile(Request $request)
    {
        $adminUser = AdminUser::status(1)->where('id', \Auth::user()->id)->first();
        return view('Admin.adminUsers.profile', compact('adminUser'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'first_name' => 'required|regex:/^[\pL\s\-.\']+$/u|min:2|max:100',
            'last_name' => 'nullable|regex:/^[\pL\s\-.\']+$/u|min:2|max:100',
            'mobile' => ['required', 'numeric', 'min:10'],
        ]);
        $adminUser = AdminUser::status(1)->where('id', \Auth::user()->id)->first();
        //dd($request->all());

        DB::beginTransaction();
        try {
            $adminUser->fill($request->input());
            $adminUser->dob = !empty($request['dob']) ? date('Y-m-d', strtotime($request['dob'])) : null;
            $adminUser->save();
            DB::commit();
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }
        return redirect()->route('admin.dashboard')->with('success', 'Your profile has been updated successfully');
    }
}
