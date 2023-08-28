<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceProfile;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Gate, DB;


class ServiceProfileController extends Controller
{
    /**
     * Display a listing of the Business Listing.
     *
     * @return \Illuminate\Http\Response
     */
    public function businessListing(Request $request)
    {
        $serviceProfile = ServiceProfile::with('user')
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.serviceProfile.index', compact('serviceProfile'));
    }

    /**
     * Display a listing of the Blocked or Spam Business Listing.
     *
     * @return \Illuminate\Http\Response
     */
    public function blockedSpam(Request $request)
    {
        $serviceProfile = ServiceProfile::with('user')
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.serviceProfile.index', compact('serviceProfile'));
    }

    /**
     * Display a listing of the Running Listing.
     *
     * @return \Illuminate\Http\Response
     */
    public function runningListing(Request $request)
    {
        $serviceProfile = ServiceProfile::with('user')
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.serviceProfile.index', compact('serviceProfile'));
    }
}
