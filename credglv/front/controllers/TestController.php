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


class TestController extends FrontController implements FrontControllerInterface {


	function credglv_assets_enqueue() {
		global $post, $wp_query;


	}

	public function registerPage() {
		$data = [];

		return $this->render( 'profile', [ 'data' => $data ] );
	}

	public function init_hook(){
		echo 'a';
	}
	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {


		return [
			'actions' => [
				'init' => [ self::getInstance(), 'init_hook' ],
				//'wp_enqueue_scripts'              => [ self::getInstance(), 'credglv_assets_enqueue' ],
			],
			'ajax'    => [

			],
			'pages'   => [
				'front' => [
					'test_controller' =>
						[
							'registerPage',
							[
								'title' => __( 'test controller', 'credglv' ),
                                //'single' => true
							]
						],

				]
			],
			'assets'  => [
				
			]
		];
	}

}