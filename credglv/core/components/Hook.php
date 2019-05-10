<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 * @since 1.0
 *
 */

namespace credglv\core\components;


use credglv\core\BaseObject;
use credglv\core\interfaces\ComponentInterface;

use credglv\core\Template;
use credglv\models\CourseModel;
use credglv\models\FieldModel;

class Hook extends BaseObject implements ComponentInterface {
	const CREDGLV_HOOK_ADMIN_MENU = 'credglv_admin_menu';
	const CREDGLV_MODEL_READY = 'credglv_model_ready';
	const CREDGLV_AFTER_CORE_RESOURCES = 'credglv_after_core_resources';
	const CREDGLV_AUTH_ROLES = 'credglv_auth_roles';
	const CREDGLV_AUTH_CAPS = 'credglv_auth_caps';
	const CREDGLV_SHORTCODE_REGISTER = 'credglv_shortcode_register';
	const CREDGLV_SHORTCODE_EXTENDS = 'credglv_shortcode_extends';
	const CREDGLV_COMPONENTS = 'credglv_components';
	const CREDGLV_RUN = 'credglv_run';
	const CREDGLV_SEND_MAIL                    = 'credglv_send_mail';
	/**
	 * Default hooks for Cred GLV plugin
	 * @var array
	 */
	protected $actions = [
		'show_admin_bar'        => [
			'\credglv\models\UserModel' => 'showAdminBar'
		],
		'admin_init'            => [
			'\credglv\core\components\ResourceManager' => 'registerCoreResource',
		],
		'wp_enqueue_scripts'    => [
			'\credglv\core\components\ResourceManager' => 'registerCoreResource',
		],
		'admin_enqueue_scripts' => [
			'\credglv\core\components\ResourceManager' => 'registerCoreResource',
		],
		'admin_menu'            => [
			'\credglv\admin\AdminTemplate' => 'addAdminMenu'
		],
		'wp_logout'             => [
			'\credglv\models\UserModel' => 'redirectLogout'
		],
		'save_post'             => [

		],
		/*'author_link'           => [
			'\credglv\models\UserModel' => [ 'authorLink', 10, 2 ]
		],
		*/
		'the_post'              => [
			'\credglv\core\components\ResourceManager' => 'registerShortcodeAssets',
		],
		/*'login_redirect'             => [
			'\credglv\models\UserModel' => [ 'gotoProfile', 10, 3 ],
		],
		*/


		'set_user_role' => [
			'\credglv\models\UserModel' => [ 'add_registered_for_referrer', 10, 3 ],
		],


	];

	public function __construct( array $config = [] ) {
		parent::__construct( $config );
		$this->defaultHooks();
	}

	/**
	 * Init Hook
	 * Call register all hooks after init
	 */
	public function init() {
		parent::init(); // TODO: Change the autogenerated stub

	}

	/**
	 * Register all default hooks
	 */
	public function defaultHooks() {
		foreach ( $this->actions as $action => $param ) {
			foreach ( $param as $class => $method ) {
				if ( ! is_object( $class ) ) {
					/** @var BaseObject $class */
					$class = $class::getInstance();
				}
				if ( is_string( $method ) ) {
					$this->listenHook( $action, [ $class, $method ] );
				} else {
					$this->listenFilter( $action, [ $class, $method[0] ], $method[1], $method[2] );
				}
			}
		}
	}

	/**
	 * @param $name
	 * @param $params
	 */
	public function registerHook( $name, $params ) {
		$params = func_get_args();

		return call_user_func_array( 'do_action', $params );
	}

	/**
	 * @param $name
	 * @param $param
	 *
	 * @return mixed
	 */
	public function registerFilter( $name, $param ) {
		$params = func_get_args();

		return call_user_func_array( 'apply_filters', $params );
	}

	/**
	 * @param $name
	 * @param $callable
	 */
	public function listenHook( $name, $callable, $priority = 10, $accepted_args = 1 ) {
		credglv()->wp->add_action( $name, $callable, $priority, $accepted_args );

	}

	/**
	 * @param $name
	 * @param $callable
	 */
	public function listenFilter( $name, $callable, $priority = 10, $accepted_args = 1 ) {
		return credglv()->wp->add_filter( $name, $callable, $priority, $accepted_args );
	}


}