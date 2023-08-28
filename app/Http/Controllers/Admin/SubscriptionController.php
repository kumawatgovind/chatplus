<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Gate, DB;


class SubscriptionController extends Controller
{
    /**
     * Display a listing of the pending renewal.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPendingRenewal(Request $request)
    {
        $startDate = Carbon::now()->startOfMonth()->subMonthsNoOverflow()->toDateString();
        $endDay = Carbon::now()->endOfMonth()->modify('+1 month')->toDateString();
        $users = User::join('user_subscriptions', 'user_subscriptions.user_id', '=', 'users.id')
            ->whereBetween('end_date', [
                $startDate,
                $endDay
            ])
            ->orWhere('is_active', 0)
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        // dd($users);
        return view('Admin.subscriptions.pending', compact('users'));
    }

    /**
     * Display a listing of the pending renewal.
     *
     * @return \Illuminate\Http\Response
     */
    public function getNotPrime(Request $request)
    {

        $users = User::leftJoin('user_subscriptions', 'user_subscriptions.user_id', '=', 'users.id')
            ->whereNull('user_subscriptions.id')
            ->select(
                'users.id',
                'users.name',
                'users.country_code',
                'users.phone_number',
                'users.created_at as user_created_at',
                'user_subscriptions.user_id',
                'user_subscriptions.end_date',
            )
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        // dd($users);
        return view('Admin.subscriptions.notPrime', compact('users'));
    }

    /**
     * Display a listing of the pending renewal.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotalPrime(Request $request)
    {
        $todayDate = Carbon::now()->toDateString();
        $users = User::leftJoin('user_subscriptions', 'user_subscriptions.user_id', '=', 'users.id')
            ->orWhere('is_active', 1)
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.subscriptions.totalPrime', compact('users'));
    }
}
