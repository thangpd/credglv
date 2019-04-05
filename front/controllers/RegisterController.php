<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\front\controllers;

use credglv\admin\controllers\PermissionController;
use credglv\core\components\RoleManager;
use credglv\core\RuntimeException;
use credglv\models\UserModel;
use credglv\core\components\Style;
use credglv\core\components\Script;
use credglv\core\interfaces\FrontControllerInterface;


class RegisterController extends FrontController implements FrontControllerInterface {

	/**
	 * referrer_ajax_search
	 */
	public function referrer_ajax_search() {
		if ( isset( $_GET['q'] ) ) {

			$results = array();

			// you can use WP_Query, query_posts() or get_posts() here - it doesn't matter
			$args = array(
//			'blog_id'      => $GLOBALS['blog_id'],
				'search'         => $_GET['q'] . '*',
				'search_columns' => array( 'user_nicename', 'display_name' ),
				'role__in'       => RoleManager::getlist_member(),
				'role__not_in'   => array(),
				'meta_key'       => '',
				'meta_value'     => '',
				'meta_compare'   => '',
				'meta_query'     => array(),
				'date_query'     => array(),
				'include'        => array(),
				'exclude'        => array(),
				'orderby'        => 'login',
				'order'          => 'ASC',
				'offset'         => '',
				'number'         => '',
				'count_total'    => false,
				'fields'         => 'all',
				'who'            => '',
			);

			$users = get_users( $args );
			foreach ( $users as $key => $value ) {
				$results[] = array( 'id' => $value->data->ID, 'text' => $value->data->user_nicename );
			}

			echo json_encode( array( 'results' => $results, 'pagination' => array( 'more' => true ) ) );
		} else {
			echo 'no $_GET[q]';
		}
		die;

	}

	public function registerPage() {
		return LoginController::getInstance()->checkLogin( 'register' );
	}

	public function add_custom_js() {
//		echo '<script src="https://www.google.com/recaptcha/api.js?render=6Lc38psUAAAAAJuh9FtinaKCMZPGnTIYk2VFSrlA" async defer >';
		echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
	}


	public function register_new_user() {
		echo 'register_new_user';
	}

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {
		return [
			'actions' => [
				'wp_head' => [ self::getInstance(), 'add_custom_js' ],
			],
			'ajax'    => [
				'referrer_ajax_search' => [ self::getInstance(), 'referrer_ajax_search' ],
				'register_new_user'    => [ self::getInstance(), 'register_new_user' ]
			],
			'pages'   => [
				'front' => [
					credglv()->config->getUrlConfigs( 'credglv_register' ) =>
						[
							'registerPage',
							[
								'title' => __( 'Cred GLV - Register', 'credglv' ),
//                                'single' => true
							]
						],

				]
			],
			'assets'  => [
				'css' => [
					[
						'id'           => 'credglv-user-login',
						'isInline'     => false,
						'url'          => '/front/assets/css/login.css',
						'dependencies' => [ 'credglv-style', 'select2' ]
					],
				],
				'js'  => [
					[
						'id'       => 'credglv-login-page-js',
						'isInline' => false,
						'url'      => '/front/assets/js/login.js',
					],
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