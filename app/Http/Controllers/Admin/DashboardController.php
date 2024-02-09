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

class DashboardController extends Controller {

    /**
     * Admin Dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        /* Register Users Statics  */
        $userQuery = new User();
        $userTodayTotal = Helper::thousandsFormat(
            $userQuery->whereDate('created_at', Carbon::today())->count()
        );
        // dd(Carbon::now()->month);
        $userWeeklyTotal = Helper::thousandsFormat(
            $userQuery->whereBetween(
                'created_at',
                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
            )->count()
        );
        $userMonthTotal = Helper::thousandsFormat(
            $userQuery->whereMonth(
                'created_at',
                '=',
                Carbon::now()->month
            )->count()
        );
        $userTotal = Helper::thousandsFormat($userQuery->count());

        $payoutTodayTotal = $payoutWeeklyTotal = $payoutMonthTotal = $payoutTotal = 0;

        /* KYC Document */
        $kycStatus = config('constants.KYC_STATUS');
        $kycFailedTotal = $userQuery->whereHas('kycDocument', function ($q) {
            $q->where('is_kyc', 3);
        })->count();
        $kycPendingTotal = $userQuery->whereHas('kycDocument', function ($q) {
            $q->whereIn('is_kyc', [0, 2]);
        })->count();
        $kycTotal = $userQuery->whereHas('kycDocument', function ($q) use ($kycStatus) {
            $q->whereIn('is_kyc', array_keys($kycStatus));
        })->count();
        // dd($kycFailedTotal,$kycPendingTotal,$kycTotal);
        /* Subscribe Users Statics  */
        $subscriptionTodayTotal = Helper::thousandsFormat(
            $userQuery->whereHas('userSubscription', function ($q) {
                $q->whereDate('created_at', Carbon::today())->where('is_active', 1);
            })->count()
        );
        $subscriptionWeeklyTotal = Helper::thousandsFormat(
            $userQuery->whereHas('userSubscription', function ($q) {
                $q->whereBetween(
                    'created_at',
                    [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
                )->where('is_active', 1);
            })->count()
        );
        $subscriptionMonthTotal = Helper::thousandsFormat(
            $userQuery->whereHas('userSubscription', function ($q) {
                $q->whereMonth(
                    'created_at',
                    '=',
                    Carbon::now()->month
                )->where('is_active', 1);
            })->count()
        );
        $subscriptionTotal = Helper::thousandsFormat(
            $userQuery->whereHas('userSubscription', function ($q) {
                $q->where('is_active', 1);
            })->count());
        $subscriptionTotalAmount = Helper::thousandsFormat(
            UserSubscription::where('is_active', 1)->sum('subscription_price')
        );

        $renewalPending = Helper::thousandsFormat(
            $userQuery->select('users.*', 'user_subscriptions.user_id')
                ->leftJoin('user_subscriptions', 'user_subscriptions.user_id', '=', 'users.id')
                ->whereNull('user_subscriptions.user_id')->count()
        );
        $topSellerEarning = $userQuery->withCount([
            'userEarnings' => function ($query) {
                $query->select(DB::raw("SUM(earning)"))->where('status', 1);
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
                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
            )->count()
        );
        $adsListMonthTotal = Helper::thousandsFormat(
            $serviceProductQuery->whereMonth(
                'created_at',
                '=',
                Carbon::now()->month
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
                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
            )->count()
        );
        $businessListMonthTotal = Helper::thousandsFormat(
            $serviceProfileQuery->whereMonth(
                'created_at',
                '=',
                Carbon::now()->month
            )->count()
        );
        $businessListTotal = $runningBusinessListing = Helper::thousandsFormat($serviceProfileQuery->count());
        $deleteBusinessListing = Helper::thousandsFormat($serviceProfileQuery->onlyTrashed()->count());


        $soldOnChatPlus = $totalRunningAd = 0;

        /* Total Earning data of admin Statics */
        $incomeQuery = new UserEarning();

        $incomeTodayTotal = Helper::thousandsFormat(
            $incomeQuery->where('status', 1)->whereDate('created_at', Carbon::today())->sum('admin_earning')
        );
        $incomeWeeklyTotal = Helper::thousandsFormat(
            $incomeQuery->where('status', 1)->whereBetween(
                'created_at',
                [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]
            )->sum('admin_earning')
        );
        $incomeMonthTotal = Helper::thousandsFormat(
            $incomeQuery->where('status', 1)->whereMonth(
                'created_at',
                '=',
                Carbon::now()->month
            )->sum('admin_earning')
        );
        $incomeTotal = Helper::thousandsFormat($incomeQuery->where('status', 1)->sum('admin_earning'));

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
                'kycFailedTotal',
                'kycPendingTotal',
                'kycTotal',
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
