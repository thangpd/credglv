<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\models;


use credglv\core\BaseObject;
use credglv\core\components\RoleManager;

class UserModel extends BaseObject {
	/**
	 * @return string Profile url
	 */
	public function getProfileUrl() {
		return site_url();
	}


	/**
	 * add_registered_for_referrer  template hook
	 */
	public function add_registered_for_referrer( $user_id, $role, $old_roles ) {
		/*TODO::if user in role*/
		if ( $role == RoleManager::CREDGLV_MEMBER_1 ) {
			add_user_meta( $user_id, 'referrer_unikey', md5( $user_id . 'referrer_unikey' ), true );
		}
	}


	/**
	 * @param $redirect_to
	 * @param $request
	 * @param $user
	 *
	 * @return bool
	 */
	public function gotoProfile( $redirect_to, $user ) {
		if ( empty( $redirect_to ) || $redirect_to == '/' || get_admin_url() == $redirect_to || $redirect_to == site_url() ) {
			/** @var \WP_User $user */
			if ( isset( $user->roles ) && is_array( $user->roles ) ) {
				if ( in_array( 'credglv_member', $user->roles ) ) {
//					$member = new Instructor( $user );
					return 'link profile';
//					return $member->getProfileUrl();
				} else if ( in_array( 'credglv_student', $user->roles ) ) {
//					$student = new Student( $user );
					return 'link profile';
//					return $student->getProfileUrl();
				}
			}
		}

		return $redirect_to;
	}


	public function redirectLoginUrl( $login_url, $redirect, $force_reauth ) {
		if ( $myaccount_page = credglv_get_woo_myaccount() ) {
			if ( preg_match( '#wp-login.php#', $login_url ) ) {
				if ( ! is_admin() ) {
					wp_redirect( $myaccount_page );
				}
				if ( ! current_user_can( 'administrator' ) ) {
					wp_redirect( $myaccount_page );
				}
			}
		}
	}

	public function redirectLogout() {
		wp_redirect( home_url() );
		exit();
	}

	public function showAdminBar() {
		$user          = wp_get_current_user();
		$allowed_roles = array( 'credglv_member', 'credglv_student' );
		if ( empty( $user->ID ) || array_intersect( $allowed_roles, $user->roles ) ) {
			return false;
		}

		return true;
	}


}