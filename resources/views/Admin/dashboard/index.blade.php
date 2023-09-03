<x-layouts.admin>
    @section('title', 'Dashborad')
    <!-- Content Header (Page header) -->
    <x-slot name="breadcrumb">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1>Dashboard</h1>
            </div>
            <div class="col-sm-12">
                {{-- Breadcrumbs::render('common',['append' => [['label'=> 'Dashboard']]]) --}}
            </div>
        </div>
    </x-slot>
    <x-slot name="content">
        <!-- User Start -->
        <div class="row">
            <div class="col-sm-12">
                <h2>User Register</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $userTodayTotal }}</h3>
                        <p>Today</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $userWeeklyTotal }}</h3>
                        <p>Week</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $userMonthTotal }}</h3>
                        <p>Month</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $userTotal }}</h3>
                        <p>Total</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- User End -->

        <!-- Payout Start -->
        <div class="row">
            <div class="col-sm-12">
                <h2>Payout amount</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $payoutTodayTotal }}</h3>
                        <p>Today</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $payoutWeeklyTotal }}</h3>
                        <p>Week</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $payoutMonthTotal }}</h3>
                        <p>Month</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $payoutTotal }}</h3>
                        <p>Total</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Payout End -->
        <!-- Kyc Start -->
        <div class="row">
            <div class="col-sm-12">
                <h2>User Kyc</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $kycPendingTotal }}</h3>
                        <p>Pending Kyc</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-check-double"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $kycFailedTotal }}</h3>
                        <p>Failed kyc</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-check-double"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $kycTotal }}</h3>
                        <p>Total Kyc</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-check-double"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Kyc End -->

        <!-- Prime Subscription Start -->
        <div class="row">
            <div class="col-sm-12">
                <h2>Prime Subscription</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $subscriptionTodayTotal }}</h3>
                        <p>Today</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $subscriptionWeeklyTotal }}</h3>
                        <p>Week</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $subscriptionMonthTotal }}</h3>
                        <p>Month</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $subscriptionTotal }}</h3>
                        <p>Total</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="col-lg-12 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $subscriptionTotalAmount }}</h3>
                            <p>Total Amount</p>
                        </div>
                        <div class="icon">
                            <i class="nav-icon fas fa-money-bill"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $renewalPending }}</h3>
                            <p>Renewal Pending</p>
                        </div>
                        <div class="icon">
                            <i class="nav-icon fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Top 10 Sellers</h3>
                    </div>
                    <div class="box-body">
                        @if($topSellerEarning->count() > 0)
                        <ul class="products-list product-list-in-box">
                            @foreach ($topSellerEarning as $topSeller)
                            <li class="item">
                                <div class="product-img">
                                    <img src="/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                                </div>
                                <div class="product-info">
                                    <a href="javascript:void(0)" class="product-title">{{ $topSeller->name }}
                                        @if($topSeller->user_earnings_count <= 100) <span class="label label-danger pull-right"><i class="fas fa-rupee-sign"></i> {{ \App\Helpers\Helper::thousandsFormat($topSeller->user_earnings_count) }}</span>
                                            @elseif($topSeller->user_earnings_count <= 500) <span class="label label-warning pull-right"><i class="fas fa-rupee-sign"></i> {{ \App\Helpers\Helper::thousandsFormat($topSeller->user_earnings_count) }}</span>
                                                @else
                                                <span class="label label-success pull-right"><i class="fas fa-rupee-sign"></i> {{ \App\Helpers\Helper::thousandsFormat($topSeller->user_earnings_count) }}</span>
                                                @endif
                                    </a>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p>No record avalible.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Prime Subscription End -->

        <!-- Ad Listing Start -->
        <div class="row">
            <div class="col-sm-12">
                <h2>Ad Listing</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $adsListTodayTotal }}</h3>
                        <p>Today</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-puzzle-piece"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $adsListWeeklyTotal }}</h3>
                        <p>Week</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-puzzle-piece"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $adsListMonthTotal }}</h3>
                        <p>Month</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-puzzle-piece"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $adsListTotal }}</h3>
                        <p>Total</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-puzzle-piece"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-12">
                <div class="col-lg-12 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ $totalRunningAd }}</h3>
                            <p>Running Ad</p>
                        </div>
                        <div class="icon">
                            <i class="nav-icon fas fa-users"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ $soldOnChatPlus }}</h3>
                            <p>Sold on Chatplus</p>
                        </div>
                        <div class="icon">
                            <i class="nav-icon fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Top 10 Ad Listing user</h3>
                    </div>
                    <div class="box-body">

                        @if($topAdsList->count() > 0)
                        <ul class="products-list product-list-in-box">
                            @foreach ($topAdsList as $topAds)
                            <li class="item">
                                <div class="product-img">
                                    <img src="/dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
                                </div>
                                <div class="product-info">
                                    <a href="javascript:void(0)" class="product-title">{{ $topAds->name }}
                                        @if($topAds->service_product_count <= 50) <span class="label label-danger pull-right">{{ \App\Helpers\Helper::thousandsFormat($topAds->service_product_count) }}</span>
                                            @elseif($topAds->service_product_count <= 100) {{ \App\Helpers\Helper::thousandsFormat($topAds->service_product_count) }}</span>
                                                @else
                                                <span class="label label-success pull-right">{{ \App\Helpers\Helper::thousandsFormat($topAds->service_product_count) }}</span>
                                                @endif
                                    </a>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @else
                        <p>No record avalible.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- Ad Listing End -->

        <!-- Business Listing Start -->
        <div class="row">
            <div class="col-sm-12">
                <h2>Business Listing</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $businessListTodayTotal }}</h3>
                        <p>Today</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $businessListWeeklyTotal }}</h3>
                        <p>Week</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $businessListMonthTotal }}</h3>
                        <p>Month</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $businessListTotal }}</h3>
                        <p>Total</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $runningBusinessListing }}</h3>
                        <p>Running Listing</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $deleteBusinessListing }}</h3>
                        <p>Delete Listing</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Business Listing End -->

        <!-- ChatPlus Income Start -->
        <div class="row">
            <div class="col-sm-12">
                <h2>Chatplus Income</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $incomeTodayTotal }}</h3>
                        <p>Today</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $incomeWeeklyTotal }}</h3>
                        <p>Week</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3>{{ $incomeMonthTotal }}</h3>
                        <p>Month</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>{{ $incomeTotal }}</h3>
                        <p>Total</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-money-bill"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- ChatPlus Income End -->

        <!-- User Personal Data Start -->
        <div class="row">
            <div class="col-sm-12">
                <h2>User Personal Data</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $savedProductTotal }}</h3>
                        <p>Saved Product List</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3>{{ $savedCustomerTotal }}</h3>
                        <p>Saved Customer List</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- User Personal Data End -->

        <!-- User Contact Data Start -->
        <div class="row">
            <div class="col-sm-12">
                <h2>User Contact Data</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-6">
                <!-- small box -->
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>{{ $userContactListTotal }}</h3>
                        <p>User Contact List Data</p>
                    </div>
                    <div class="icon">
                        <i class="nav-icon fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- User Contact Data End -->
    </x-slot>

</x-layouts.admin>