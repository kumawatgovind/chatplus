<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\KycDocument;
use App\Models\User;
use DB;

class KycController extends Controller
{
    /**
     * Display a listing of the pending kyc.
     *
     * @return \Illuminate\Http\Response
     */
    public function getPendingKyc(Request $request)
    {
        $kycStatus = config('constants.KYC_STATUS');
        $users = User::leftJoin('kyc_document', 'kyc_document.user_id', 'users.id')
            ->select(
                'users.*',
                'kyc_document.id as kyc_document_id',
                'kyc_document.aadhar_number',
                'kyc_document.aadhar_front',
                'kyc_document.aadhar_back',
                'kyc_document.pan_number',
                'kyc_document.pan_front',
                'kyc_document.bank_account_number',
                'kyc_document.account_holder_name',
                'kyc_document.bank_ifsc_code',
                'kyc_document.bank_name',
                'kyc_document.passbook_image',
                'kyc_document.is_kyc',
            )
            ->whereNull('kyc_document.aadhar_number')->orWhere('kyc_document.is_kyc', 2)
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));

        return view('Admin.kycDocument.pendingKyc', compact('users', 'kycStatus'));
    }

    /**
     * Display a listing of the mark re kyc.
     *
     * @return \Illuminate\Http\Response
     */
    public function getMarkReKyc(Request $request)
    {
        $users = User::leftJoin('kyc_document', 'kyc_document.user_id', 'users.id')
            ->select(
                'users.*',
                'kyc_document.id as kyc_document_id',
                'kyc_document.aadhar_number',
                'kyc_document.aadhar_front',
                'kyc_document.aadhar_back',
                'kyc_document.pan_number',
                'kyc_document.pan_front',
                'kyc_document.bank_account_number',
                'kyc_document.account_holder_name',
                'kyc_document.bank_ifsc_code',
                'kyc_document.bank_name',
                'kyc_document.passbook_image',
                'kyc_document.is_kyc',
                'kyc_document.reason',
            )
            ->where('kyc_document.is_kyc', 4)
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        $kycStatus = config('constants.KYC_STATUS');
        return view('Admin.kycDocument.markReKyc', compact('users', 'kycStatus'));
    }

    /**
     * Display a listing of the pending renewal.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTotalKyc(Request $request)
    {
        $kycStatus = config('constants.KYC_STATUS');
        $users = User::leftJoin('kyc_document', 'kyc_document.user_id', 'users.id')
            ->select(
                'users.*',
                'kyc_document.id as kyc_document_id',
                'kyc_document.aadhar_number',
                'kyc_document.aadhar_front',
                'kyc_document.aadhar_back',
                'kyc_document.pan_number',
                'kyc_document.pan_front',
                'kyc_document.bank_account_number',
                'kyc_document.account_holder_name',
                'kyc_document.bank_ifsc_code',
                'kyc_document.bank_name',
                'kyc_document.passbook_image',
                'kyc_document.is_kyc',
                'kyc_document.created_at as kyc_done',
            )
            ->whereIn('kyc_document.is_kyc', array_keys($kycStatus))
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.kycDocument.totalKyc', compact('users', 'kycStatus'));
    }
}
