<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\front\controllers;

use credglv\core\components\RoleManager;
use credglv\models\UserModel;
use credglv\core\interfaces\FrontControllerInterface;
use http\Client\Curl\User;
use PHPUnit\Runner\Exception;

// use Kreait\Firebase;
// use Kreait\Firebase\Factory;
// use Kreait\Firebase\ServiceAccount;
// use Kreait\Firebase\Messaging\CloudMessage;


class PushNotifyController extends FrontController implements FrontControllerInterface {
	const API_ACCESS_KEY = 'AAAAkjudY3w:APA91bG-xcfpDASkXSQX_QUIA6x95MUxbueQ_6z6yeAzh9ttb283eAoaI_iRYFAuP9YEKg5ZkrOHxWscE6dsEbcz5pA1jHe_B0hJv1NFoHb2NYZF9dFUdbNMbGtW6QVT1J_SVdjdsJXw';

	public function send_notify($data) {
		$singleID = $data['device_token'];
		$fcmMsg = array(
			'body' => 'here is a message. message',
			'title' => 'This is title #1',
			'sound' => "default",
		    'color' => "#203E78" 
		);
		$fcmFields = array(
			'to' => $singleID,
		    'priority' => 'high',
			'notification' => $fcmMsg
		);

		$headers = array(
			'Authorization: key=AAAAkjudY3w:APA91bG-xcfpDASkXSQX_QUIA6x95MUxbueQ_6z6yeAzh9ttb283eAoaI_iRYFAuP9YEKg5ZkrOHxWscE6dsEbcz5pA1jHe_B0hJv1NFoHb2NYZF9dFUdbNMbGtW6QVT1J_SVdjdsJXw',
			'Content-Type: application/json'
		);
		 
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_POST, true );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fcmFields ) );

		$result = curl_exec($ch );
		curl_close( $ch );
		return json_decode($result);
	}

	function register ($data) {
		$deviceToken = $data['device_token'];
		$user = get_user_by('login', $data['username']);
		$user_id = $user->ID;
		if(!$user_id){
			$result['success'] = 'error';
			$result['message'] = 'Please log in account first.';
			return $result;
		}
		$token_exist = get_user_meta($user_id,'device_token',true);
		if($deviceToken){
			if($token_exist == ''){
				$result_add = add_user_meta($user_id,'device_token',$deviceToken);
				if($result_add != false)
					$check_token = true;
			}
			else{
				if($token_exist != $deviceToken)
					$check_token = update_user_meta($user_id,'device_token',$deviceToken);
				else
					$check_token = true;
			}
		}else{
			$result['success'] = 'error';
			$result['message'] = 'No have device_token';
			echo json_encode($result);
			exit;
		}
		if($check_token == true){
			$result['success'] = 'OK';
			$result['device_token'] = $deviceToken;
		}else{
			$result['success'] = 'error';
			$result['message'] = 'unknown';
		}
		return $result;
	}

	function init_hook(){
		add_action( 'rest_api_init', function () {
		  register_rest_route( 'send_notify/v1', '/deviceToken=(?P<device_token>[a-zA-Z0-9-]+)', array(
		    'methods' => 'GET',
		    'callback' => __CLASS__.'::send_notify',
		  ) );
		} );
		add_action( 'rest_api_init', function () {
		  register_rest_route( 'register_devide_token/v1', '/deviceToken=(?P<device_token>[a-zA-Z0-9-]+)&username=(?P<username>[a-zA-Z0-9-]+)', array(
		    'methods' => 'GET',
		    'callback' => __CLASS__.'::register',
		  ) );
		} );

	}

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {


		return [
			'actions' => [
				'init'				=> [ self::getInstance(), 'init_hook' ],
				'wp_login'			=> [ self::getInstance(), 'get_user_id' ],
			],
			'ajax'    => [

			],
			'assets'  => [
				'js'  => [
				]
			]
		];
	}
}