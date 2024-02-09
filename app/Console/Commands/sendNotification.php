<?php

namespace App\Console\Commands;

use App\Models\Notification;
use App\Repositories\ServiceProductRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiGlobalFunctions;

/**
 * Class sendNotification
 * @package App\Console\Commands
 */
class sendNotification extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'send:notification';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'This will send all related user notification.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->sendNotification();
	}

	private function sendNotification()
	{
		Log::channel('notification')->info("sync script");
		$config = config('constants.NOTIFICATION_MESSAGE');
		$notificationList = Notification::where('is_sent', 0)->get();

		foreach ($notificationList as $notification) {
			Log::channel('notification')->info("notification ID - $notification->id.");
			$title = $message = '';
			switch ($notification->type) {
				case 'property':
					Log::channel('notification')->info("notification type - $notification->type. Start Sending");
					$users = ServiceProductRepository::getServiceProductNotificationList($notification->item_id);
					if ($users->count() > 0) {
						if ($notification->sub_type == 'sell') {
							$title = $config['service_product_property_sell']['title'];
							$message = $config['service_product_property_sell']['message'];
						} elseif ($notification->sub_type == 'rent') {
							$title = $config['service_product_property_rent']['title'];
							$message = $config['service_product_property_rent']['message'];
						} elseif ($notification->sub_type == 'requirement') {
							$title = $config['service_product_property_requirement']['title'];
							$message = $config['service_product_property_requirement']['message'];
						}
						foreach ($users as $user) {
							if (!empty($user->fcm_token)) {
								$reqData = [
									'fcm_token' => $user->fcm_token,
									'notification_title' => $title,
									'notification_message' => $message,
									'notification_type' => $notification->type,
									'notification_sub_type' => $notification->sub_type,
									'notification_item_id' => $notification->item_id
								];
								ApiGlobalFunctions::sendNotification($reqData);
							}
						}
					}
					Log::channel('notification')->info("notification type - $notification->type. End Sending");
					break;
				case 'other':
					Log::channel('notification')->info("notification type - $notification->type. Start Sending");
					$users = ServiceProductRepository::getServiceProductNotificationList($notification->item_id);
					if ($notification->sub_type == 'sell') {
						$title = $config['service_product_other_sell']['title'];
						$message = $config['service_product_other_sell']['message'];
					} elseif ($notification->sub_type == 'buy') {
						$title = $config['service_product_other_buy']['title'];
						$message = $config['service_product_other_buy']['message'];
					}
					if ($users->count() > 0) {
						foreach ($users as $user) {
							if (!empty($user->fcm_token)) {
								$reqData = [
									'fcm_token' => $user->fcm_token,
									'notification_title' => $title,
									'notification_message' => $message,
									'notification_type' => $notification->type,
									'notification_sub_type' => $notification->sub_type,
									'notification_item_id' => $notification->item_id
								];
								ApiGlobalFunctions::sendNotification($reqData);
							}
						}
					}
					Log::channel('notification')->info("notification type - $notification->type. End Sending");
					break;

				case 'payment_withdrawal_status':
					$user = UserRepository::getUser($notification->user_id);
					if ($notification->status == 'complete') {
						$title = $config['payment_withdrawal_complete']['title'];
						$message = $config['payment_withdrawal_complete']['message'];
					} elseif ($notification->status == 'in-progress') {
						$title = $config['payment_withdrawal_inProgress']['title'];
						$message = $config['payment_withdrawal_inProgress']['message'];
					} elseif ($notification->status == 'cancelled') {
						$title = $config['payment_withdrawal_cancelled']['title'];
						$message = $config['payment_withdrawal_cancelled']['message'];
					} elseif ($notification->status == 'pending') {
						$title = $config['payment_withdrawal_pending']['title'];
						$message = $config['payment_withdrawal_pending']['message'];
					}
					if ($user) {
						if (!empty($user->fcm_token)) {
							$reqData = [
								'fcm_token' => $user->fcm_token,
								'notification_title' => $title,
								'notification_message' => $message,
								'notification_type' => $notification->type,
								'notification_status' => $notification->status,
								'notification_item_id' => ''
							];
							ApiGlobalFunctions::sendNotification($reqData);
						}
					}
					break;
				case 'kyc':
					$user = UserRepository::getUser($notification->user_id);
					if ($notification->status == 'Completed') {
						$title = $config['kyc_complete']['title'];
						$message = $config['kyc_complete']['message'];
					} elseif ($notification->status == 'In-Progress') {
						$title = $config['kyc_inProgress']['title'];
						$message = $config['kyc_inProgress']['message'];
					} elseif ($notification->status == 'Failed') {
						$title = $config['kyc_failed']['title'];
						$message = $config['kyc_failed']['message'];
					}
					if ($user) {
						if (!empty($user->fcm_token)) {
							$reqData = [
								'fcm_token' => $user->fcm_token,
								'notification_title' => $title,
								'notification_message' => $message,
								'notification_type' => $notification->type,
								'notification_status' => $notification->status,
								'notification_item_id' => ''
							];
							ApiGlobalFunctions::sendNotification($reqData);
						}
					}
					break;
				case 'admin_marketing':
					$users = UserRepository::getUsers();
					$title = $config['admin_marketing']['title'];
					$message = $config['admin_marketing']['message'];
					if ($users->count() > 0) {
						foreach ($users as $user) {
							if (!empty($user->fcm_token)) {
								$reqData = [
									'fcm_token' => $user->fcm_token,
									'notification_title' => $title,
									'notification_message' => $message,
									'notification_type' => $notification->type,
									'notification_sub_type' => '',
									'notification_item_id' => ''
								];
								ApiGlobalFunctions::sendNotification($reqData);
							}
						}
					}
					Log::channel('notification')->info("notification type - $notification->type. End Sending");
					break;
				case 'admin_block':
					$user = UserRepository::getUser($notification->user_id);
					$title = $config['admin_block_message']['title'];
					$message = $config['admin_block_message']['message'];
					if ($user) {
						if (!empty($user->fcm_token)) {
							$reqData = [
								'fcm_token' => $user->fcm_token,
								'notification_title' => $title,
								'notification_message' => $message,
								'notification_type' => $notification->type,
								'notification_sub_type' => '',
								'notification_item_id' => ''
							];
							ApiGlobalFunctions::sendNotification($reqData);
						}
					}
					break;
				case 'default':
			}
			$notification->is_sent = 1;
			$notification->save();
		}
	}

}
