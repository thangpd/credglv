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

	public function send_notify() {
		// $serviceAccount = ServiceAccount::fromJsonFile('/Applications/XAMPP/xamppfiles/htdocs/Outsource/GLV/wp-content/plugins/credglv/glv-test-firebase-adminsdk-swohm-ad70b50da3.json');
		// $firebase = (new Factory)
		//     ->withServiceAccount($serviceAccount)
		//     ->withDatabaseUri('https://glv-test.firebaseio.com/')
		//     ->create();
		// print_r($firebase);
		// $database = $firebase->getDatabase();
		// $auth = $firebase->getAuth();

		// $messaging = $firebase->getMessaging();
		// $deviceToken = $data['device_token'] ? $data['device_token'] : '';
		// $message = CloudMessage::fromArray([
		// 	'token'			=> $deviceToken,
		//     'notification' 	=> ['title' => 'GLV', 'body' => 'Welcome'], // optional
		//     'data' 			=> [], // optional
		// ]);

		//$messaging->send($message);
		if(!$_GET['device_token']){
			$result['success'] = 'error';
			$result['message'] = 'device_token is invalid';
			return $result;
		}
		$singleID = $_GET['device_token'];
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
			'Authorization: key=AAAAWBLwt9s:APA91bG4rSuKFxGEtx-fTbnxIib-guizmW_fYDcYt1fvubEGvIrKgFAG6ohyzPtHqSuh2cwtcwMOCD7OxLkE4W_uHYJZdkjjUd97sxVQKcEqsLKMwVewz1Ojm1cStdMNokMVFQv5Z2iv',
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

	public function register () {
		if(!$_GET['device_token']){
			$result['success'] = 'error';
			$result['message'] = 'device_token is invalid';
			return $result;
		}
		if(!$_GET['username']){
			$result['success'] = 'error';
			$result['message'] = 'userrname is invalid';
			return $result;
		}
		$deviceToken = $_GET['device_token'];
		$user = get_user_by('login', $_GET['username']);
		$user_id = $user->ID;
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

	public function init_hook(){
		add_action( 'rest_api_init', function () {
		  register_rest_route( '/v1', '/send_notify', array(
		    'methods' => 'GET',
		    'callback' => __CLASS__.'::send_notify',
		    'args' => array(
		      'device_token'
		      )
		  ) );
		} );
		add_action( 'rest_api_init', function () {
		  register_rest_route( '/v1', '/register_device_token', array(
		    'methods' => 'GET',
		    'callback' => __CLASS__.'::register',
		    'args' => array(
		      'device_token',
		      'username',
		      )
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
			],
			'ajax'    => [

			],
			// 'pages'   => [
			// 	'front' => [
			// 		'push_notify' =>
			// 			[
			// 				'pushNotifyPage',
			// 				[
			// 					'title' => __( 'Push', 'credglv' ),
			// 				]
			// 			],
			// 	]
			// ],
			'assets'  => [
				'js'  => [
					/*[
						'id'       => 'credglv-register-page-js',
						'isInline' => false,
						'url'      => '/front/assets/js/register.js',
					],*/
					// [
					// 	'id'       => 'credglv-main-js',
					// 	'isInline' => false,
					// 	'url'      => '/front/assets/js/main.js',
					// ]
				]
			]
		];
	}
}