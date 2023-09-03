<?php

namespace App\Traits;

use App\Models\Booking;
use App\Models\User;

trait ApiGlobalFunctions
{

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public static function sendResponse($result, $message)
    {
        $response = [
            'status' => true,
            'code' => 200,
            'message' => $message,
            'data' => $result

        ];
        return response()->json($response, 200);
    }

    /**
     * success response builder.
     *
     * @return \Illuminate\Http\Response
     */
    public static function responseBuilder($data)
    {
        $response = [
            'status' => $data['status'] ?? false,
            'code' => $data['code'],
            'message' => $data['message'],
        ];
        if (isset($data['data'])) {
            $response['data'] = $data['data'];
        }
        return response()->json($response);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public static function sendError($error, $errorMessages = [], $code = 200)
    {
        if (!empty($errorMessages)) {
            $errorMsg = $errorMessages;
        } else {
            $errorMsg[] = $error;
        }
        $response = [
            'status' => false,
            'code' => config('response.HTTP_OK'),
            'message' => $errorMsg,
            'data' => (object)[]
        ];
        return response()->json($response, $code);
    }

    /**
     * return error For Version response.
     *
     * @return \Illuminate\Http\Response
     */
    public static function sendErrorForVersion($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'status' => false,
            'code' => config('response.HTTP_OK'),
            'message' => !empty($errorMessages) ? $errorMessages : $error,
            'data' => (object)[]
        ];
        return response()->json($response, $code);
    }

    public static function messageDefault($label)
    {
        $msgArray = [
            'mobile_verified' => 'Number found in our DB.',
            'verification_failed' => 'Number not found in our DB.',
            'username_available' => 'User name available.',
            'block_by_admin' => 'The user was blocked by the admin please content to support.',
            'username_not_available' => 'User name already taken by another.',
            'params_not_available' => 'This required parameter is not available in the request',
            'profile_created' => 'Profile Create successfully.',
            'profile_not_created' => 'Error while profile creating.',
            'signup_success' => 'Your account have been registered successfully.',
            'signup_error' => 'The user could not be saved. Please, try again',
            'validate_error' => 'A validation error occurred',
            'invalid_login' => 'Email or password is incorrect',
            'invalid_token' => 'Invalid token',
            'invalid_csrf_token' => 'Invalid CSRF token',
            'invalid_request' => 'Invalid request',
            'invalid_account' => 'Invalid account, You are not authorize to access this',
            'invalid_access' => 'You are not authorize to access this',
            'not_verified' => 'Your account is not verified. Please check your email and verify it.',
            'not_activated' => 'Your account is not activated, Please check contact admin',
            'verified_success' => 'Your account has verified successfully',
            'logout_success' => 'You have logged out successfully',
            'login_success' => 'You have logged in successfully',
            'forgot_success' => 'A verification code has been sent to your email, Please check your email for the code',
            'forgot_app_success' => 'New password has been sent to your registered email',
            'resend_otp_success' => 'New verification code generated and has been sent to your email',
            'profile_edit' => 'Your profile has been updated successfully.',
            'profile_get' => 'Your profile data',
            'password_update' => 'Your password has been updated successfully.',
            'token_not_match' => 'Token not match',
            'list_found' => 'List found.',
            'record_found' => 'Record found.',
            'list_not_found' => 'List not found.',
            'record_not_found' => 'Record not found.',
            'records_delete' => "Records has been deleted successfully.",
            'process_failed' => 'Your process failed. Please try again.',
            'record_exists' => 'Request data is already exists.',
            'save_records' => 'Record has been saved successfully.',
            'post_save' => 'Post has been saved successfully.',
            'save_failed' => 'The user could not be saved. Please, try again.',
            'not_register_email' => 'Email address is not registered with us.',
            'not_active' => 'Your account has been deactivated, Please contact ',
            'change_password_success' => 'Your password changed successfully.',
            'oops' => 'Something went wrong.',
            'file_upload' => 'Uploaded successfully.',
            'user_subscribe' => 'User subscribe successfully.',
            'user_subscribed' => 'User subscribed.',
            'user_not_subscribed' => 'User not subscribed.',
            'referral_code_notActive' => 'Referral code not active.',
            'payment_request' => 'Payment request.',
            'invalid_payload' => 'Invalid payload.',
            'invalid_signature' => 'Invalid signature.',
            'payment_success' => 'Payment successfully.',
            'bookmark_success' => 'Bookmark update successfully.',
            'payout_minimum_not_available' => 'You need minimum Rs. %s amount in your wallet for withdrawal.',
            'payout_amount_not_available' => 'Your wallet balance (%s) less then requested amount.',
            'payout_success' => 'Payout initiated successfully.',
            'update_kyc' => 'Please update KYC first before the payout initiate.',
        ];
        return isset($msgArray[$label]) ? $msgArray[$label] : $label;
    }

    public static function sendNotificationForTesting($fcm = '')
    {
        $device_type = 'android';
        $params = [
            'fcm_token' => $fcm,
            'notification_title' => 'Test Notification.',
            'notification_message' => 'This is test Notification.',
            'notification_type' => 'kyc',
            'notification_sub_type' => '',
            'notification_item_id' => 0
        ];
        $status = false;
        if ($device_type == 'iOS' || $device_type == 'ios') {
            self::ios($params);
            $status = true;
        } else {
            self::android($params);
            $status = true;
        }
        return $status;
    }
    
    /**
     * static
     *
     * @param  mixed $params
     * @return boolean
     */
    public static function sendNotification($params = [])
    {
        $device_type = 'android';
        $status = false;
        if ($device_type == 'iOS' || $device_type == 'ios') {
            self::ios($params);
            $status = true;
        } else {
            self::android($params);
            $status = true;
        }
        return $status;
    }



    public static function android($params = [])
    {
        $fcmToken = $params['fcm_token'];
        $notificationTitle = $params['notification_title'];
        $notificationMessage = $params['notification_message'];
        $notificationType = $params['notification_type'] ?? '';
        $notificationSubType = $params['notification_sub_type'] ?? '';
        $notificationStatus = $params['notification_status'] ?? '';
        $notificationItemId = $params['notification_item_id'] ?? 0;
        
        // API access key from Google API's Console
        $server_key = 'AAAA_Pf6c8o:APA91bGb1Iqnyr5ol51mWRkOeczcEFT9Hm1tfJ1ppPJ39ey4HJotoaOIb-SloVUDxk7V6AQ4drRsEKfURLM9surWnNL29RkmWTeOVhJz531Lq4CUR3ZAKxS3EVbBYwDBhGz12AxoDvVe';

        $msg = array(
            'sound' => 'default',
            'priority' => 'high',
            'data' => array(
                'service_id' => $notificationItemId,
                'type' => $notificationType,
                'sub_type' => $notificationSubType,
                'status' => $notificationStatus,
                'title' => $notificationTitle,
                'body' => $notificationMessage,
                )
        );

        // echo "<prE>"; print_r($msg); die('android'); 
        $fields = array(
            'to' => $fcmToken,
            'notification' => $msg,
            'data' => $msg,
            'priority' => 'high'
        );

        $headers = array(
            'Authorization: key=' . $server_key,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        $results = json_decode($result);

        $status = false;
        if (isset($results)) {
            if ($results->success == 1) {
                $status = true;
            }
        }
        curl_close($ch);
        return $status;
        die;
    }

    /*
     * Method : ios
     * Des : send puch notification by fcm
     */

     public static function ios($params = [])
    {
        //$server_key = 'AIzaSyDQR7mNCTAyv1KRfi6B8ySeZGMJmSqSZ4A';
        // $server_key = 'AAAAHmrDJ_w:APA91bH7UPRRCUh_2WOkZ1Rzaltih5PibtEk9jbvPOy2p8lfl2ZSBDJgPMsFdoJg6Itv8oUT7RNE5Ibv-2emxoLVxJUZZ_QDGAPeU_xSJudFjlsK1md2ZbNG2pDFCVRGOFZXC_JBBUTqK5rrZRgOKiMbNzlJoBFkDg';
        // $server_key = 'AAAAuVmXPqU:APA91bG5V-6eN9KPI1x7LVKOKcSYXjPssJOdLHq5ERzOXc_lVnfOPD3WGhpWFt_V2a_tBX7atGaB96cqP9b5I312NV7Zz4Blg9KdtQ4kN5PMUepsomil0DBdjc7-Dz4gaD72xu4c_WwN';
        // if (!empty($deviceId)) {
            // $device_badge['badge_count'] = 1;
            // $msg = array(
            //     'body' => $senderMsg,
            //     'icon' => 'myicon',
            //     'sound' => 'default'
            // );

            // $data = array(
            //     'icon' => 'myicon',
            //     'sound' => 'default',
            //     'priority' => 'high'
            // );

            // $fields = array(
            //     'to' => $deviceId,
            //     'notification' => $msg,
            //     'data' => $data,
            //     'priority' => 'high',
            //     'content_available' => true
            // );
            // $bookingid = isset($booking_id) ? $booking_id : '';

            // /*$msg = array(
            //     'title' => '',
            //     'body' => $senderMsg,
            //     'data' => array('type' => $notificationType,'booking_id'=>$bookingid)
            // );*/

            // $formdata['message'] = $senderMsg;
            // $bookingid = isset($booking_id) ? $booking_id : '';
            // $msg = array(
            //     'title' => '',
            //     'body' => $senderMsg,
            //     'type' => $notificationType,
            //     'booking_id' => isset($booking_id) ? $booking_id : '',
            //     'show_in_foreground' => true,
            //     'sound' => 'default',
            //     // 'color' => '#EC1C2B',
            //     'priority' => 'high',
            //     'icon' => url('/') . 'img/app_icon.png',
            //     'data' => array('type' => $notificationType, 'booking_id' => $bookingid, 'icon' => url('/') . 'img/app_icon.png')
            // );

            // // echo "<prE>"; print_r($msg); die('ios'); 
            // $fields = array(
            //     'to' => $deviceId,
            //     'notification' => $msg,
            //     'data' => $msg
            // );

            // $url = 'https://fcm.googleapis.com/fcm/send';
            // $headers = array(
            //     'Authorization:key=' . $server_key,
            //     'Content-Type: application/json'
            // );

            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_POST, false);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // $result = curl_exec($ch);
            // $results = json_decode($result);

            // $status = false;
            // if (isset($results)) {
            //     if ($results->success == 1) {
            //         $status = true;
            //     }
            // }
            // curl_close($ch);
            // return $status;
            //ob_flush();
        // }
    }
}
