<?php

namespace App\Repositories;

use App\Models\KycDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function is;
use function is_null;
use Exception;

class KycRepository
{

    /**
     * getSingle
     *
     * @param  mixed $request
     * @return obj
     */
    public static function create(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $request->get('Auth');
            $kycDocument = KycDocument::where('user_id', $authUser->id)->first();
            if (empty($kycDocument)) {
                $kycDocument = new KycDocument();
                $kycDocument->user_id = $authUser->id;
            }
            $kycDocument->aadhar_number = $request->input('aadhar_number', false);
            $kycDocument->aadhar_front = $request->input('aadhar_front', false);
            $kycDocument->aadhar_back = $request->input('aadhar_back', false);
            $kycDocument->pan_number = $request->input('pan_number', false);
            $kycDocument->pan_front = $request->input('pan_front', false);
            $kycDocument->bank_account_number = $request->input('bank_account_number', false);
            $kycDocument->account_holder_name = $request->input('account_holder_name', false);
            $kycDocument->bank_ifsc_code = $request->input('bank_ifsc_code', false);
            $kycDocument->bank_name = $request->input('bank_name', false);
            $kycDocument->passbook_image = $request->input('passbook_image', false);
            $kycDocument->is_kyc = 2;
            $kycDocument->is_default = 1;
            if ($kycDocument->save()) {
                DB::commit();
                return $kycDocument;
            } else {
                DB::rollBack();
                return false;
            }
        } catch (Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * update
     *
     * @param  mixed $request
     * @return obj
     */
    public static function update(Request $request)
    {
        try {
            $authUser = $request->get('Auth');
            $kycDocument = KycDocument::where(['user_id' => $authUser->id])->first();
            $kycDocument->is_kyc = $request->input('is_kyc', false);
            if ($kycDocument->save()) {
                return $kycDocument;
            }
        } catch (Exception $e) {
            return false;
        }
    }


    /**
     * getSingle
     *
     * @param  mixed $request
     * @return obj
     */
    public static function getSingle(Request $request, $kycDocumentId = 0)
    {
        $query = new KycDocument;
        if ($kycDocumentId > 0) {
            $query = $query->where('id', $kycDocumentId);
        } else {
            $authUser = $request->get('Auth');
            $query = $query->where('user_id', $authUser->id);
        }
        $response = [];
        if ($postResponse = $query->first()) {
            $response['kyc_status'] = $postResponse->is_kyc;
            $response['kyc_detail'] = $postResponse;
        } else {
            $response['kyc_status'] = 0;
            $response['kyc_detail'] = (object)[];
        }
        return $response;
    }


    /**
     * list
     *
     * @param  mixed $request
     * @return void
     */
    public static function list(Request $request)
    {
        if ($request->input('limit', false)) {
            $limit  = $request->input('limit', 0);
        } else {
            $limit = config('get.FRONT_END_PAGE_LIMIT');
        }
        $query = KycDocument::with('user');
        if ($request->input('user_id', false)) {
            $query = $query->where('user_id', $request->input('user_id', false));
        }
        $list = $query->orderBy('id', 'desc')->paginate($limit);
        return $list;
    }
}
