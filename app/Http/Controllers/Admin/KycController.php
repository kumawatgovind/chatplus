<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\KycDocument;
use App\Models\User;
use App\Repositories\NotificationRepository;
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
            ->whereNull('kyc_document.aadhar_number')
            ->orWhere('kyc_document.is_kyc', 0)
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
            ->where('kyc_document.is_kyc', 3)
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
            ->whereNotNull('kyc_document.aadhar_number')
            ->whereIn('kyc_document.is_kyc', array_keys($kycStatus))
            ->sortable(['created_at' => 'desc'])
            ->filter($request->query('keyword'))
            ->paginate(config('get.ADMIN_PAGE_LIMIT'));
        return view('Admin.kycDocument.totalKyc', compact('users', 'kycStatus'));
    }
    
    /**
     * getSingleKyc
     *
     * @param  mixed $request
     * @param  mixed $kycId
     * @return void
     */
    public function getSingleKyc(Request $request, $kycId)
    {
        $kycStatus = config('constants.KYC_STATUS');
        $kycDetail = KycDocument::with('user')->whereId($kycId)->first();
        if (empty($kycDetail)) {
            return back()->with('success', 'Kyc data invalid.');
        }
        unset($kycStatus[0]);
        // dd($kycDetail);
        return view('Admin.kycDocument.kycDetail', compact('kycDetail', 'kycStatus'));
    }
    
    /**
     * updateKyc
     *
     * @param  mixed $request
     * @return void
     */
    public function updateKyc(Request $request)
    {
        try {
            $request->validate([
                'is_kyc' => 'required'
            ]); 
            $kycId = $request->input('kyc_id', 0);
            if ($kycId > 0) {
                $kycDetail = KycDocument::with('user')->whereId($kycId)->first();
                if (!empty($kycDetail)) {
                    $kycDetail->is_kyc = $request->input('is_kyc', 0);
                    if ($kycDetail->save()) {
                        $user = User::whereId($kycDetail->user_id)->first();
                        $kycUpdated = KycDocument::with('user')->whereId($kycId)->first();
                        NotificationRepository::createNotification($kycUpdated, $user, 'kyc');
                        if ($kycUpdated->is_kyc == 0) {
                            return redirect()->route('admin.getPendingKyc')->with('success', 'Kyc has been updated successfully.');
                        } elseif ($kycUpdated->is_kyc == 1 || $kycUpdated->is_kyc == 2) {
                            return redirect()->route('admin.getTotalKyc')->with('success', 'Kyc has been updated successfully.');
                        } elseif ($kycUpdated->is_kyc == 3) {
                            return redirect()->route('admin.getMarkReKyc')->with('success', 'Kyc has been updated successfully.');
                        }
                    }
                } else {
                    return back()->with('success', 'Kyc data invalid.');
                }
            } else {
                return back()->with('success', 'Kyc data invalid.');
            }
        } catch (\Exception $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        
    }
}
