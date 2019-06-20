<?php
/**
 * @project  edu
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\front\controllers;


use credglv\core\components\Script;
use credglv\core\interfaces\FrontControllerInterface;
use credglv\models\UserModel;

class FooterController extends FrontController implements FrontControllerInterface {

	/**
	 * Cred home page
	 * @return string
	 */
	public function credglv_add_script_to_footer() {
		$user_model = UserModel::getInstance();


		$share_link = __( 'Be a GLV Member and enjoy many benefits ', 'credglv' ) . $user_model->get_url_share_link();
		echo '<script type="text/javascript">
            
        PullToRefresh.init({
            mainElement: \'body\',
            onRefresh: function(){ window.location.reload(); }
        });
        </script>
        
        <script>
            function showAndroidShare() {
                try {
                    webkit.messageHandlers.callbackHandler.postMessage("' . $share_link . '");
                } catch (err) {
                    console.log(\'The native context does not exist yet\');
                }
                try {
                    android.showShareNative( "' . $share_link . '" );
                } catch (err) {
                    console.log(\'The android native context does not exist yet\');
                }
                try {
                    myOwnJSHandler.receiveMessageFromJS("' . $share_link . '");
                } catch (err) {
                    console.log(\'The myOwnJSHandler context does not exist yet\');
                }
            }</script>
        ';
	}


	function cred_add_class_body( $classes ) {
		if ( credglv_get_woo_myaccount() && ! is_user_logged_in() ) {
			$classes[] = 'login-register';
		}

		return $classes;

	}


	function credglv_init_hook( $classes ) {
		add_filter( 'body_class', [ $this, 'cred_add_class_body' ] );

	}


	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {
		// TODO: Implement registerAction() method.
		return [
			'actions' => [
				'init'      => [ self::getInstance(), 'credglv_init_hook' ],
				'wp_footer' => [ self::getInstance(), 'credglv_add_script_to_footer' ],

			],
			'ajax'    => [


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