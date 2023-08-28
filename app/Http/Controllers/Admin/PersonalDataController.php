<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactSync;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;

use Gate, DB;


class PersonalDataController extends Controller
{
    /**
     * Display a listing of the Contact.
     *
     * @return \Illuminate\Http\Response
     */
    public function getContactList(Request $request)
    {
        $contactSync = ContactSync::with('users')
            ->sortable(['created_at' => 'desc'])
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.personalData.contactList', compact('contactSync'));
    }

    /**
     * Display a listing of the Saved Products.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSavedProducts(Request $request)
    {
        $savedProducts = Product::with('users')
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.personalData.savedProduct', compact('savedProducts'));
    }

    /**
     * Display a listing of the Saved Customers.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSavedCustomers(Request $request)
    {
        $savedCustomers = Customer::with('users')
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.personalData.savedCustomer', compact('savedCustomers'));
    }
}
