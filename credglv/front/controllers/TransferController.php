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


class TransferController extends FrontController implements FrontControllerInterface {


	function credglv_assets_enqueue() {
		global $post, $wp_query;


	}

	public function registerPage() {
		$data = [];

		return $this->render( 'transfer', [ 'data' => $data ] );
	}

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {


		return [
			'actions' => [

				'woocommerce_register_form_start' => [ self::getInstance(), 'credglv_extra_register_fields' ],
				'woocommerce_register_form'       => [
					self::getInstance(),
					'credglv_extra_otp_register_fields'
				],
//				'woocommerce_save_account_details_errors' => [ self::getInstance(), 'credglv_edit_save_fields' ],
				'woocommerce_register_post'       => [
					self::getInstance(),
					'credglv_validate_extra_register_fields',
					10,
					3,
				],
				'woocommerce_created_customer'    => [
					self::getInstance(),
					'credglv_validate_extra_register_fields_update'
				],
				'wp_enqueue_scripts'              => [ self::getInstance(), 'credglv_assets_enqueue' ],
			],
			'ajax'    => [

			],
			'pages'   => [
				'front' => [
					'transfer' =>
						[
							'registerPage',
							[
								'title' => __( 'Cred GLV - Transfer Page', 'credglv' ),
//                                'single' => true
							]
						],

				]
			],
			'assets'  => [
				'css' => [
					[
						'id'           => 'credglv-user-register',
						'isInline'     => false,
						'url'          => '/front/assets/css/register.css',
						'dependencies' => [ 'credglv-style', 'select2' ]
					],
				],
				'js'  => [
					/*[
						'id'       => 'credglv-register-page-js',
						'isInline' => false,
						'url'      => '/front/assets/js/register.js',
					],*/
					[
						'id'       => 'credglv-main-js',
						'isInline' => false,
						'url'      => '/front/assets/js/main.js',
					]
				]
			]
		];
	}

}