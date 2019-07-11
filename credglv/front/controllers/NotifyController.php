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

class NotifyController extends FrontController implements FrontControllerInterface {

	function credglv_assets_enqueue() {
		global $post, $wp_query;

	}

	public function notifyPage() {
		$notify_model = new NotifyModel();
		$user_id = get_current_user_id();
		$notification = $notify_model->get_user_notification($user_id);
		$data['test'] = $notification;
		return $this->render( 'notify', [ 'data' => $data ], false );
	}

	function init_hook(){
		global $wp_query;
		//remove_action( 'storefront_footer', 'custom_storefront_credit', 20 );
	}

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {


		return [
			'actions' => [
				'init'                            => [ self::getInstance(), 'init_hook' ],
				'wp_enqueue_scripts'              => [ self::getInstance(), 'credglv_assets_enqueue' ],
			],
			'ajax'    => [

			],
			'pages'   => [
				'front' => [
					'notify' =>
						[
							'notifyPage',
							[
								'title' => __( 'Cred GLV - Notification Page', 'credglv' ),
//                                'single' => true
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