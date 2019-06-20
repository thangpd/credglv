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


class RankingController extends FrontController implements FrontControllerInterface {


	function credglv_assets_enqueue() {
		global $post, $wp_query;


	}

	public function registerPage() {
		$data = [];

		return $this->render( 'ranking', [ 'data' => $data ] );
	}

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {


		return [
			'actions' => [

				'wp_enqueue_scripts'              => [ self::getInstance(), 'credglv_assets_enqueue' ],
			],
			'ajax'    => [

			],
			'pages'   => [
				'front' => [
					'rank' =>
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