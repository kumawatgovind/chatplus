<?php

namespace App\Repositories;

use App\Models\KycDocument;
use App\Models\User;
use App\Models\RazorPayContact;
use App\Models\RazorPayFundAccount;
use App\Models\RazorPayPayout;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
use function is;
use function is_null;
use Exception;

class RazorPayRepository
{

    /**
     * createPayout
     *
     * @param  mixed $request
     * @return []
     */
    public static function createPayoutContact(Request $request)
    {
        $authUser = $request->get('Auth');
        try {
            $user = User::whereId($authUser->id)->first();
            $razorPayContact = RazorPayContact::whereUserId($authUser->id)->first();
            if (empty($razorPayContact)) {
                $razorPayContact = new RazorPayContact;
                $razorPayContact->user_id = $authUser->id;
                $contactData = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'contact' => $user->phone_number,
                    'type' => 'employee',
                ];
                $createContact = Helper::curlRequest($contactData, 'contacts');
                $razorPayContact->contacts = $contactResponse = $createContact['response'];
                $contactDetail = json_decode($contactResponse, true);
                if (isset($contactDetail['error'])) {
                    $razorPayContact->save();
                    return [
                        'contactStatus' => false,
                        'message' => 'Payout error.',
                        'error' => $contactDetail['error']['description']
                    ];
                } else {
                    $razorPayContact->payout_contact_id = $contactDetail['id'];
                    if ($razorPayContact->save()) {
                        // self::fundAccount($user, $contactDetail, $payoutAmount);
                        return [
                            'contactStatus' => true,
                            'message' => 'Create payout contact successfully.',
                            'contactData' => $contactDetail
                        ];
                    }
                }
            } else {
                $contactDetail = json_decode($razorPayContact->contacts, true);
                // self::fundAccount($user,$contactDetail, $payoutAmount);
                return [
                    'contactStatus' => true,
                    'message' => 'Create payout contact successfully.',
                    'contactData' => $contactDetail
                ];
            }
            
        } catch (Exception $e) {
            return [
                'contactStatus' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage(),
           ];
        }
    }
        
    /**
     * createPayoutFundAccount
     *
     * @param  mixed $request
     * @return void
     */
    public static function createPayoutFundAccount(Request $request, $contactDetail)
    {
        $authUser = $request->get('Auth');
        try {
            $razorPayFundAccount = RazorPayFundAccount::whereUserId($authUser->id)->first();
            if ($razorPayFundAccount) {
                $fundResponse = $razorPayFundAccount->fund_accounts;
                $fundDetail = json_decode($fundResponse, true);
                if (isset($fundDetail['id'])) {
                    $apiURL = 'fund_accounts/'.$fundDetail['id'];
                    $fundStatus = Helper::curlRequest([], $apiURL, 'GET');
                    $razorPayFundAccount->fund_accounts = $fundStatusResponse = $fundStatus['response'];
                    $razorPayFundAccount->save();
                    $fundAccountDetail = json_decode($fundStatusResponse, true);
                    return [
                        'accountStatus' => true,
                        'message' => 'Payout fund account added successfully.',
                        'fundData' => $fundAccountDetail
                    ];
                    // self::payouts($user, $fundAccountDetail, $payoutAmount);
                } else {
                    $kycDocument = KycDocument::whereUserId($authUser->id)->first();
                    $razorPayFundAccount->user_id = $authUser->id;
                    if (!empty($kycDocument)) {
                        $contactBankAccount = [
                            'contact_id' => $contactDetail['id'],
                            'account_type' => 'bank_account',
                            'bank_account' => [
                                'name' => $kycDocument->account_holder_name,
                                'ifsc' => $kycDocument->bank_ifsc_code,
                                'account_number' => $kycDocument->bank_account_number,
                            ]
                        ];
                        $createFund = Helper::curlRequest($contactBankAccount, 'fund_accounts');
                        $razorPayFundAccount->fund_accounts = $fundResponse = $createFund['response'];
                        $fundDetail = json_decode($fundResponse, true);
                        if (isset($fundDetail['error'])) {
                            $razorPayFundAccount->save();
                            return [
                                'accountStatus' => false,
                                'message' => 'Payout error.',
                                'error' => $fundDetail['error']['description']
                            ];
                        } else {
                            $razorPayFundAccount->payout_account_id = $fundDetail['id'];
                            if($razorPayFundAccount->save()) {
                                // $apiURL = 'fund_accounts/'.$fundDetail['id'];
                                // $fundStatus = Helper::curlRequest([], $apiURL, 'GET');
                                // $razorPayFundAccount->fund_accounts = $fundStatusResponse = $fundStatus['response'];
                                // $razorPayFundAccount->save();
                                // $fundAccountDetail = json_decode($fundStatusResponse, true);
                                return [
                                    'accountStatus' => true,
                                    'message' => 'Payout fund account added successfully.',
                                    'fundData' => $fundDetail
                                ];
                                // self::payouts($user, $fundAccountDetail, $payoutAmount);
                            }
                        }
                    } 
                }
            } else {
                $kycDocument = KycDocument::where(['user_id' => $authUser->id, 'is_kyc' => 1])->first();
                $razorPayFundAccount = new RazorPayFundAccount;
                $razorPayFundAccount->user_id = $authUser->id;
                if (!empty($kycDocument)) {
                    $contactBankAccount = [
                        'contact_id' => $contactDetail['id'],
                        'account_type' => 'bank_account',
                        'bank_account' => [
                            'name' => $kycDocument->account_holder_name,
                            'ifsc' => $kycDocument->bank_ifsc_code,
                            'account_number' => $kycDocument->bank_account_number,
                        ]
                    ];
                    $createFund = Helper::curlRequest($contactBankAccount, 'fund_accounts');
                    $razorPayFundAccount->fund_accounts = $fundResponse = $createFund['response'];
                    $fundDetail = json_decode($fundResponse, true);
                    if (isset($fundDetail['error'])) {
                        $razorPayFundAccount->save();
                        return [
                            'accountStatus' => false,
                            'message' => 'Payout error.',
                            'error' => $fundDetail['error']['description']
                        ];
                    } else {
                        $razorPayFundAccount->payout_account_id = $fundDetail['id'];
                        if($razorPayFundAccount->save()) {
                            // $apiURL = 'fund_accounts/'.$fundDetail['id'];
                            // $fundStatus = Helper::curlRequest([], $apiURL, 'GET');
                            // $razorPayFundAccount->fund_accounts = $fundStatusResponse = $fundStatus['response'];
                            // $razorPayFundAccount->save();
                            // $fundAccountDetail = json_decode($fundStatusResponse, true);
                            return [
                                'accountStatus' => true,
                                'message' => 'Payout fund account added successfully.',
                                'fundData' => $fundDetail
                            ];
                            // self::payouts($user, $fundAccountDetail, $payoutAmount);
                        }
                    }
                } else {
                    return [
                        'accountStatus' => false,
                        'message' => 'Payout error.',
                        'error' => 'Please update KYC first before the payout initiate.'
                    ];
                }
            }
        } catch (Exception $e) {
            return [
                'accountStatus' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
           ];
        }
    }
    
     
    /**
     * createPayout
     *
     * @param  mixed $request
     * @param  mixed $fundData
     * @return void
     */
    public static function createPayout(Request $request, $fundData)
    {
        $authUser = $request->get('Auth');
        try {
            $payoutAmount = $request->input('payout_amount', 0);
            // $razorPayPayout = RazorPayPayout::whereUserId($authUser->id)->first();
            // if ($razorPayPayout) {
            //     $payouts = $razorPayPayout->payouts;
            //     $payoutDetail = json_decode($payouts, true);
            //     if (isset($payoutDetail['id'])) {
            //         $apiURL = 'payouts/'.$payoutDetail['id'];
            //         $payoutStatus = Helper::curlRequest([], $apiURL, 'GET');
            //         $razorPayPayout->payouts = $payoutStatusResponse = $payoutStatus['response'];
            //         $razorPayPayout->save();
            //         $payoutStatusDetail = json_decode($payoutStatusResponse, true);
            //         return [
            //             'payoutStatus' => true,
            //             'message' => 'Payout initiated successfully.',
            //             'payoutData' => $payoutStatusDetail
            //         ];
            //     } else {
            //         $payout = [
            //             'account_number' => config('constants.PAYOUT_ACCOUNT_NUMBER'),
            //             'fund_account_id' => $fundData['id'],
            //             'amount' => $payoutAmount*100,
            //             'currency' => config('constants.CURRENCY'),
            //             'mode' => config('constants.PAYMENT_MODE'),
            //             'purpose' => config('constants.PAYMENT_PURPOSE'),
            //             'queue_if_low_balance' => true,
            //         ];
            //         $createPayout = Helper::curlRequest($payout, 'payouts');
            //         $razorPayPayout->payouts = $payoutResponse = $createPayout['response'];
            //         $payoutDetail = json_decode($payoutResponse, true);
            //         if (isset($payoutDetail['error'])) {
            //             $razorPayPayout->save();
            //             return [
            //                 'payoutStatus' => false,
            //                 'message' => 'Payout error.',
            //                 'error' => $payoutDetail['error']['description']
            //             ];
            //         } else {
            //             $razorPayPayout->payout_id = $payoutDetail['id'];
            //             if($razorPayPayout->save()) {
            //                 if (isset($payoutDetail['id'])) {
            //                     // $apiURL = 'payouts/'.$payoutDetail['id'];
            //                     // $payoutStatus = Helper::curlRequest([], $apiURL, 'GET');
            //                     // $razorPayLog->payouts = $payoutStatus['response'];
            //                     // $razorPayLog->save();
            //                     return [
            //                         'payoutStatus' => true,
            //                         'message' => 'Payout initiated successfully.',
            //                         'payoutData' => $payoutDetail
            //                     ];
            //                 }
            //             }
            //         }
            //     }
            // } else {
                if (!empty($fundData)) {
                    $razorPayPayout = new RazorPayPayout;
                    $razorPayPayout->user_id = $authUser->id;
                    $payout = [
                        'account_number' => config('constants.PAYOUT_ACCOUNT_NUMBER'),
                        'fund_account_id' => $fundData['id'],
                        'amount' => $payoutAmount*100,
                        'currency' => config('constants.CURRENCY'),
                        'mode' => config('constants.PAYMENT_MODE'),
                        'purpose' => config('constants.PAYMENT_PURPOSE'),
                        'queue_if_low_balance' => true,
                    ];
                    $createPayout = Helper::curlRequest($payout, 'payouts');
                    $razorPayPayout->payouts = $payoutResponse = $createPayout['response'];
                    $payoutDetail = json_decode($payoutResponse, true);
                    if (isset($payoutDetail['id']) && !empty($payoutDetail['id'])) {
                        $razorPayPayout->payout_id = $payoutDetail['id'];
                        $razorPayPayout->amount = $payoutDetail['amount']/100;
                        $razorPayPayout->fees = $payoutDetail['fees']/100;
                        $razorPayPayout->tax = $payoutDetail['tax']/100;
                        $razorPayPayout->status = $payoutDetail['status'];
                        if($razorPayPayout->save()) {
                            return [
                                'payoutStatus' => true,
                                'message' => 'Payout initiated successfully.',
                                'payoutData' => $payoutDetail
                            ];
                        }
                    } else {
                        $razorPayPayout->save();
                        return [
                            'payoutStatus' => false,
                            'message' => 'Payout error.',
                            'error' => $payoutDetail['error']['description']
                        ];
                        
                    }
                }
            // }
        } catch (Exception $e) {
            return [
                'payoutStatus' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
           ];
        }
    }

    public static function checkPayout(Request $request)
    {
        $authUser = $request->get('Auth');
        try {
            $razorPayPayout = RazorPayPayout::whereUserId($authUser->id)->orderBy('created_at', 'DESC')->first();
            $payoutId = $razorPayPayout->payout_id;
            $apiURL = 'payouts/'.$payoutId;
            $payoutStatus = Helper::curlRequest([], $apiURL, 'GET');
            $payoutResponse = $payoutStatus['response'];
            $payoutD = json_decode($payoutResponse, true);
            if (isset($payoutD['id']) && !empty($payoutD['id'])) {
                $razorPayPayout->payout_id = $payoutD['id'];
                $razorPayPayout->amount = $payoutD['amount']/100;
                $razorPayPayout->fees = $payoutD['fees']/100;
                $razorPayPayout->tax = $payoutD['tax']/100;
                $razorPayPayout->status = $payoutD['status'];
                if($razorPayPayout->save()) {
                    return [
                        'payoutStatus' => true,
                        'message' => 'Payout initiated successfully.',
                        'payoutData' => $payoutD
                    ];
                } else {
                    return [
                        'payoutStatus' => false,
                        'message' => 'Payout error.',
                        'error' => 'Payout error.'
                    ];
                }
            } else {
                $razorPayPayout->save();
                return [
                    'payoutStatus' => false,
                    'message' => 'Payout error.',
                    'error' => $payoutD['error']['description']
                ];
                
            }
        } catch (Exception $e) {
            return [
                'payoutStatus' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
           ];
        }
    }
}
