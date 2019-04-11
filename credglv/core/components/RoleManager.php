<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\core\components;

use credglv\core\BaseObject;
use credglv\core\interfaces\ComponentInterface;
use credglv\core\interfaces\MigrableInterface;


class RoleManager extends BaseObject implements ComponentInterface, MigrableInterface {
	const CREDGLV_ROLE_ADMIN = 'credglv_admin';
	const CREDGLV_MEMBER_1 = 'credglv_member_1';
	const CREDGLV_MEMBER_2 = 'credglv_member_2';
	const CREDGLV_MEMBER_3 = 'credglv_member_3';
	const CREDGLV_ROLE_DEFAULT = 'credglv_default';


	public static function getlist_member() {
		return array( self::CREDGLV_MEMBER_1, self::CREDGLV_MEMBER_2, self::CREDGLV_MEMBER_3 );
	}

	/**
	 * List of available capabilities
	 * Currently load from config file
	 * @var array
	 */

	protected $capabilities = [];
	/**
	 * List of available credcoin glv roles
	 * @var array
	 */
	protected $roles = [];


	/**
	 * RoleManager constructor.
	 *
	 * @param array $config
	 */
	public function __construct( $config = [] ) {
		parent::__construct( $config );
		$this->capabilities = credglv()->config->get( 'roleManager/capabilities', [] );
		$this->capabilities = credglv()->hook->registerFilter( Hook::CREDGLV_AUTH_CAPS, $this->capabilities );
		$this->roles        = credglv()->config->get( 'roleManager/roles', [] );
		$this->roles        = credglv()->hook->registerFilter( Hook::CREDGLV_AUTH_ROLES, $this->roles );
	}

	/**
	 * Check current user access right
	 * @return bool
	 */
	public function checkAccessRight() {
		$user = credglv()->wp->wp_get_current_user();
		if ( in_array( self::CREDGLV_ROLE_DEFAULT, (array) $user->roles ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Run this function when plugin was activated
	 * We need create something like data table, data roles, caps etc..
	 * @return mixed
	 */
	public function onActivate() {
		foreach ( $this->roles as $name => $role ) {
			if ( get_role( $name ) == null ) {
				credglv()->wp->add_role( $name, $role['label'], $role['capabilities'] );
			}
		}

		$admin_role = get_role( 'administrator' );
		foreach ( $this->capabilities as $cap ) {
			if ( ! $admin_role->has_cap( $cap ) ) {
				$admin_role->add_cap( $cap );
			}
		}
	}

	/**
	 * Run this function when plugin was deactivated
	 * We need clear all things when we leave.
	 * Please be a polite man!
	 * @return mixed
	 */
	public function onDeactivate() {
		// TODO: Implement onDeactive() method.
		foreach ( $this->roles as $name => $role ) {
			credglv()->wp->remove_role( $name );
		}

		$adminrole = get_role( 'administrator' );
		foreach ( $this->capabilities as $cap ) {
			if ( $adminrole->has_cap( $cap ) ) {
				$adminrole->remove_cap( $cap );
			}
		}
	}

	/**
	 * Run if current version need to be upgraded
	 *
	 * @param string $currentVersion
	 *
	 * @return mixed
	 */
	public function onUpgrade( $currentVersion ) {
		// TODO: Implement onUpgrade() method.
	}

	/**
	 * Run when credcoin glv was uninstalled
	 * @return mixed
	 */
	public function onUninstall() {

	}
}

