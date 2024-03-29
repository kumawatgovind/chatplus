<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Repositories\NotificationRepository;
use Carbon\Carbon;
use Gate;

class UsersController extends Controller
{


    /**
     * Instantiate a new UserController instance.
     */
    public function __construct()
    {
        //
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $users = User::sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));

        return view('Admin.users.index', compact('users'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function blockedUsers(Request $request)
    {
        $users = User::block()->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));

        return view('Admin.users.blocked', compact('users'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function reportedUsers(Request $request)
    {
        $users = User::has('reportedUser')
            ->with(['reportedUser', 'reportedUser.userByReport'])
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.users.reported', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        return view('Admin.users.createOrUpdate');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(User $user)
    {
        return view('Admin.users.show', compact('user'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function reportDetail($userId)
    {
        $user = User::with(['reportedUser', 'reportedUser.userByReport'])
            ->where('id', $userId)
            ->first();
        return view('Admin.users.report_detail', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('Admin.users.createOrUpdate', compact('user'));
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
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $requestData = $request->all();
            $requestData['status'] = (isset($requestData['status'])) ? 1 : 0;
            $requestData['is_block'] = $isBlock = (isset($requestData['is_block'])) ? 1 : 0;
            $user->fill($requestData);
            $user->save();
            if ($isBlock > 0) {
                NotificationRepository::createNotification([], $user, 'admin_block');
                
            }
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.users.index')->with('success', 'This user has been updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $response = [];
        // $response = Gate::inspect('check-user', "users-create");
        // if (!$response->allowed()) {
        //     return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        // }
        DB::beginTransaction();
        try {
            $userData = User::findOrFail($user->id);
            $user->delete();
            DB::commit();
            $response = ['status' => true, 'message' => 'User has been deleted successfully.', 'data' => $user];
        } catch (\Exception $e) {
            DB::rollBack();
            $response = ['status' => false, 'message' => $e->getMessage()];
        }
        return $response;
    }


    public function userApprove(Request $request, $id)
    {
        $response = Gate::inspect('check-user', "users-create");
        if (!$response->allowed()) {
            $responce = ['status' => false, 'message' =>  $response->message(), 'data' => []];
        }
        DB::beginTransaction();
        try {
            $userData = User::where(['id' => $id, 'is_approved' => 2])->first();
            if (!empty($userData)) {
                $message = 'approved';
                $userData->is_approved = 1;
                $userData->save();
            }
            $userData->sendApprovedEnail($userData);
            $type = 'advertisers';
            DB::commit();
            $responce = ['status' => true, 'message' => 'This ' . $type . ' has been ' . $message . ' successfully.', 'data' => []];
        } catch (\Exception $e) {
            DB::rollBack();
            $responce = ['status' => false, 'message' => $e->getMessage()];
        }
        return $responce;
    }
}
