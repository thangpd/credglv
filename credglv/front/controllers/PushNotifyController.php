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
use credglv\models\NotifyModel;
use credglv\core\interfaces\FrontControllerInterface;
use http\Client\Curl\User;
use PHPUnit\Runner\Exception;

// use Kreait\Firebase;
// use Kreait\Firebase\ServiceAccount;
// use Kreait\Firebase\Messaging;
// use Kreait\Firebase\Messaging\CloudMessage;


class PushNotifyController extends FrontController implements FrontControllerInterface {
	public function push($deviceToken='',$title='',$body='',$type='0',$link=''){
		$url = admin_url('/').'wp-json/v1/send_notify?deviceToken='.$deviceToken.'&title='.$title.'&body='.$body;
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, $url );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

		$result = curl_exec( $ch );
		curl_close( $ch );

		$notify = new NotifyModel();
		$user_id = $notify->get_user_by_device_token($deviceToken);
		$notify->add_user_notification($user_id,$body,$type,$link);
	}

	public function send_notify($request) {
		if(!$_GET['device_token']){
			$result['success'] = 'error';
			$result['message'] = 'device_token is invalid';
			return $result;
		}
		$params = $request->get_params();
		$token = $params['device_token'];
		$os = 'ios';
		$title = $params['title'];
		$body = $params['body'];

	    $notification = array(
	    	'body'=>$body,
	    	'title'=>$title,
	    );

	    $msg = array(
	    	'message'=> 'This is message',
	    );

	    if($os == 'ios'){
	        $fields = array(
	        	"content_available" => true,
	        	"priority" => "high",
	        	'to'=> $token,
	        	'data'=> $msg
	        );
	    }else{
	        $fields = array(
	        	'notification' => $notification,
	        	"content_available" => false,
	        	"priority" => 'high',
	        	'to'=> $token,
	        	'data'=> $msg
	        );
	    }

	    //return $fields;

		$headers = array(
			'Authorization: key=AAAAWBLwt9s:APA91bG4rSuKFxGEtx-fTbnxIib-guizmW_fYDcYt1fvubEGvIrKgFAG6ohyzPtHqSuh2cwtcwMOCD7OxLkE4W_uHYJZdkjjUd97sxVQKcEqsLKMwVewz1Ojm1cStdMNokMVFQv5Z2iv',
			'Content-Type: application/json'
		);
		 
		$ch = curl_init();
		curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
		curl_setopt( $ch,CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
		curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

		$result = curl_exec($ch );
		curl_close( $ch );
		return json_decode($result);
	}

	public function register ($request) {
		$params = $request->get_params();
		$deviceToken = $params['device_token'];
		$user_id = $params['user_id'];
		if(!$deviceToken){
			$result['success'] = 'error';
			$result['message'] = 'device_token is invalid';
			return $result;
		}
		if(!$user_id){
			$result['success'] = 'error';
			$result['message'] = 'username is invalid';
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
		  register_rest_route( '/v1', '/send_notify', array(
		    'methods' => 'GET',
		    'callback' => __CLASS__.'::send_notify',
		    'args' => array(
		      'device_token',
		      'title',
		      'body'
		      )
		  ) );
		} );
		add_action( 'rest_api_init', function () {
		  register_rest_route( '/v1', '/register_device_token', array(
		    'methods' => 'GET',
		    'callback' => __CLASS__.'::register',
		    'args' => array(
		      	'device_token',
		      	'user_id' => array(
		      		'default' => get_current_user_id(),
		      	),
		      )
		  ) );
		} );
		//add_action('init','push');
	}

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {


		return [
			'actions' => [
				'init'	=> [ self::getInstance(), 'init_hook' ],
			],
			'ajax'    => [

			],
			'pages'   => [
				'front' => [
					'push_notify' =>
						[
							'test',
							[
								'title' => __( 'Push', 'credglv' ),
								'single' => true,
							]
						],
				]
			],
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