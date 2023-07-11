<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\ContactSync;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ServiceProduct;
use App\Models\ServiceProfile;
use App\Models\User;
use App\Models\UserEarning;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Carbon\Carbon;

class DashboardController extends Controller
{

    /**
     * Admin Dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        /* Register Users Statics  */
        $userQuery = new User();
        $userTodayTotal = Helper::thousandsFormat(
            $userQuery->whereDate('created_at', Carbon::today())->count()
        );
        $userWeeklyTotal = Helper::thousandsFormat(
            $userQuery->whereBetween(
                'created_at',
                [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
            )->count()
        );
        $userMonthTotal = Helper::thousandsFormat(
            $userQuery->whereMonth(
                'created_at',
                '=',
                Carbon::now()->subMonth()->month
            )->count()
        );
        $userTotal = Helper::thousandsFormat($userQuery->count());

        $payoutTodayTotal = $payoutWeeklyTotal = $payoutMonthTotal =  $payoutTotal = 0;

        /* Subscribe Users Statics  */
        $subscriptionTodayTotal = Helper::thousandsFormat(
            $userQuery->whereHas('userSubscription', function ($q) {
                $q->whereDate('created_at', Carbon::today());
            })->count()
        );
        $subscriptionWeeklyTotal = Helper::thousandsFormat(
            $userQuery->whereHas('userSubscription', function ($q) {
                $q->whereBetween(
                    'created_at',
                    [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
                );
            })->count()
        );
        $subscriptionMonthTotal = Helper::thousandsFormat(
            $userQuery->whereHas('userSubscription', function ($q) {
                $q->whereMonth(
                    'created_at',
                    '=',
                    Carbon::now()->subMonth()->month
                );
            })->count()
        );
        $subscriptionTotal = $subscriptionTotalAmount = Helper::thousandsFormat($userQuery->whereHas('userSubscription')->count());
        $subscriptionTotalAmount = Helper::thousandsFormat(UserSubscription::where('is_active', 1)->sum('subscription_price'));
        $renewalPending  = Helper::thousandsFormat(
            $userQuery->select('users.* ')
                ->join('user_subscriptions', 'user_subscriptions.user_id', '=', 'users.id')
                ->groupBy('users.id')
                ->count()
        );
        $topSellerEarning = $userQuery->withCount([
            'userEarnings' => function ($query) {
                $query->select(DB::raw("SUM(earning)"));
            }
        ])->having('user_earnings_count', '>', 0)
            ->orderBy('user_earnings_count', 'DESC')
            ->take(10)
            ->get();

        /* Service Product Listing Statics */
        $serviceProductQuery = new ServiceProduct();
        $adsListTodayTotal = Helper::thousandsFormat(
            $serviceProductQuery->whereDate('created_at', Carbon::today())->count()
        );
        $adsListWeeklyTotal = Helper::thousandsFormat(
            $serviceProductQuery->whereBetween(
                'created_at',
                [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
            )->count()
        );
        $adsListMonthTotal = Helper::thousandsFormat(
            $serviceProductQuery->whereMonth(
                'created_at',
                '=',
                Carbon::now()->subMonth()->month
            )->count()
        );
        $adsListTotal = Helper::thousandsFormat($serviceProductQuery->count());
        $topAdsList = $userQuery
            ->withCount('serviceProduct')
            ->having('service_product_count', '>', 0)
            ->orderBy('service_product_count', 'DESC')
            ->take(10)->get();

        /* Service Profile Listing Statics */
        $serviceProfileQuery = new ServiceProfile();
        $businessListTodayTotal = Helper::thousandsFormat(
            $serviceProfileQuery->whereDate('created_at', Carbon::today())->count()
        );
        $businessListWeeklyTotal = Helper::thousandsFormat(
            $serviceProfileQuery->whereBetween(
                'created_at',
                [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
            )->count()
        );
        $businessListMonthTotal = Helper::thousandsFormat(
            $serviceProfileQuery->whereMonth(
                'created_at',
                '=',
                Carbon::now()->subMonth()->month
            )->count()
        );
        $businessListTotal = $runningBusinessListing = Helper::thousandsFormat($serviceProfileQuery->count());
        $deleteBusinessListing = Helper::thousandsFormat($serviceProfileQuery->onlyTrashed()->count());


        $soldOnChatPlus = $totalRunningAd = 0;

        /* Total Earning data of admin Statics */
        $incomeQuery = new UserEarning();
        $incomeTodayTotal = Helper::thousandsFormat(
            $incomeQuery->whereDate('created_at', Carbon::today())->sum('admin_earning')
        );
        $incomeWeeklyTotal = Helper::thousandsFormat(
            $incomeQuery->whereBetween(
                'created_at',
                [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]
            )->sum('admin_earning')
        );
        $incomeMonthTotal = Helper::thousandsFormat(
            $incomeQuery->whereMonth(
                'created_at',
                '=',
                Carbon::now()->subMonth()->month
            )->sum('admin_earning')
        );
        $incomeTotal = Helper::thousandsFormat($incomeQuery->sum('admin_earning'));

        /* Saved Product and Customer Statics */
        $savedProductTotal = Helper::thousandsFormat(Product::count());
        $savedCustomerTotal = Helper::thousandsFormat(Customer::count());
        /* Contact List Sync Statics */
        $userContactListTotal = Helper::thousandsFormat(ContactSync::count());
        return view(
            'Admin.dashboard.index',
            compact(
                'userTodayTotal',
                'userWeeklyTotal',
                'userMonthTotal',
                'userTotal',
                'payoutTodayTotal',
                'payoutWeeklyTotal',
                'payoutMonthTotal',
                'payoutTotal',
                'subscriptionTodayTotal',
                'subscriptionWeeklyTotal',
                'subscriptionMonthTotal',
                'subscriptionTotal',
                'subscriptionTotalAmount',
                'topSellerEarning',
                'renewalPending',
                'adsListTodayTotal',
                'adsListWeeklyTotal',
                'adsListMonthTotal',
                'adsListTotal',
                'topAdsList',
                'totalRunningAd',
                'soldOnChatPlus',
                'businessListTodayTotal',
                'businessListWeeklyTotal',
                'businessListMonthTotal',
                'businessListTotal',
                'runningBusinessListing',
                'deleteBusinessListing',
                'incomeTodayTotal',
                'incomeWeeklyTotal',
                'incomeMonthTotal',
                'incomeTotal',
                'savedProductTotal',
                'savedCustomerTotal',
                'userContactListTotal',
            )
        );
    }
}
