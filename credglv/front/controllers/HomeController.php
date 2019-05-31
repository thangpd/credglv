<?php
/**
 * @project  edu
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\front\controllers;


use credglv\core\components\Script;
use credglv\core\interfaces\FrontControllerInterface;

class HomeController extends FrontController implements FrontControllerInterface {

	/**
	 * Cred home page
	 * @return string
	 */
	public function credglvHome() {
		global $post;

		/*		credglv()->wp->wp_enqueue_script( 'credglv' );
				credglv()->wp->wp_enqueue_script( 'credglv.ui' );*/

		return $this->render( 'index', [
			'page' => $post,
		] );
	}


	public function redirectUserLoggedIn() {
		$page_name = get_query_var( 'name' );
		if ( ! is_user_logged_in() && $page_name != 'credglv-home' ) {
//			wp_redirect( home_url( '/credglv-home' ) );
//			exit;
		}

		if ( is_user_logged_in() && $page_name == 'register' ) {
			wp_redirect( home_url() );
			exit;
		}
	}


	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {
		// TODO: Implement registerAction() method.
		return [
			'actions' => [
				'template_redirect' => [ self::getInstance(), 'redirectUserLoggedIn' ],
			],
			'pages'   => [
				'front' => [
					'credglv-home' => [
						'credglvHome',
						[
							'title'  => 'Gold Leaf Ventures',
							'single' => true,
						]
					]
				]
			],

		];
	}
}