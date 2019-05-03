<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\front\controllers;

use credglv\core\interfaces\FrontControllerInterface;


class MycredController extends FrontController implements FrontControllerInterface {













	public function init_hook() {
	}


	function credglv_assets_enqueue() {

	}

	public static function registerAction() {


		/*'login_url'             => [
			'\credglv\models\UserModel' => [ 'redirectLoginUrl', 10, 3 ],
		],*/
		return [
			'actions' => [
				'init'               => [ self::getInstance(), 'init_hook' ],
				'wp_enqueue_scripts' => [ self::getInstance(), 'credglv_assets_enqueue' ],
			],
			'assets'  => [
				/*'css' => [
					[
						'id'           => 'credglv-user-profile',
						'isInline'     => false,
						'url'          => '/front/assets/css/credglv-user-profile.css',
						'dependencies' => [ 'credglv-style', 'font-awesome' ]
					],
					[
						'id'       => 'category',
						'isInline' => false,
						'url'      => '/front/assets/css/category.css'
					],
				],
				'js' => [
					[
						'id'       => 'credglv-main-js',
						'isInline' => false,
						'url'      => '/front/assets/js/main.js',
					]
				]*/
			],
			'ajax'    => [
//				'ajax_update_profile' => [ self::getInstance(), 'updateProfile' ],
			]
		];
	}

}