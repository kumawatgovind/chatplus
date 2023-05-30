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

class DashboardController extends Controller
{

    /**
     * Admin Dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer = User::get()->count();
        return view('Admin.dashboard.index', compact('customer'));
    }
}