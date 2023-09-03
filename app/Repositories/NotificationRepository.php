<?php

namespace App\Repositories;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function is;
use function is_null;
use Exception;

class NotificationRepository
{

    /**
     * createNotification
     *
     * @param  mixed $request
     * @return obj
     */
    public static function createNotification($data, $user, $notificationType = '')
    {
        try {
            $notification = new Notification();
            $notification->user_id = $user->id;
            if (!empty($notificationType)) {
                switch ($notificationType) {
                    case 'property':
                        $notification->item_id = $data->id;
                        if ($data->product_type == 'Other') {
                            $notification->type = 'other';
                            if ($data->product_for == 'Sell') {
                                $notification->sub_type = 'sell';
                            } else if ($data->product_for == 'Buy') {
                                $notification->sub_type = 'buy';
                            }
                        } else if ($data->product_type == 'Property') {
                            $notification->type = 'property';
                            if ($data->product_for == 'Sell') {
                                $notification->sub_type = 'sell';
                            } else if ($data->product_for == 'Rent/Lease') {
                                $notification->sub_type = 'rent';
                            } else if ($data->product_for == 'Requirement') {
                                $notification->sub_type = 'requirement';
                            }
                        }
                        break;
                    case 'withdrawal':
                        $notification->type = 'payment_withdrawal_status';
                        $notification->status = 'in-progress';
                        if (!empty($data)) {
                            $notification->status = $data->status;
                        }
                        break;
                    case 'kyc':
                        $kycStatus = config('constants.KYC_STATUS');
                        $notification->type = 'kyc';
                        $notification->status = $kycStatus[$data->is_kyc];
                        break;
                    case 'admin_marketing':
                        $notification->type = 'admin_marketing';
                        break;
                    case 'admin_block':
                        $notification->type = 'admin_block';
                        break;
                    default:
                        $notification->type = 0;
                        break;
                }
            }
            if ($notification->save()) {
                \Artisan::call('send:notification');
                return [
                    'status' => true,
                    'data' => $notification
                ];
            } else {
                return [
                    'status' => false,
                    'data' => []
                ];;
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'data' => $e->getMessage()
            ];
        }
    }

}
