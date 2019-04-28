<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\models;


use credglv\core\components\RoleManager;
use credglv\core\CustomModel;
use credglv\core\interfaces\MigrableInterface;
use credglv\core\interfaces\ModelInterface;
use credglv\helpers\GeneralHelper;

class UserModel extends CustomModel implements ModelInterface, MigrableInterface {
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


	public function redirectLoginUrl( $login_url, $redirect, $force_reauth ) {
		if ( $myaccount_page = credglv_get_woo_myaccount() && !is_ajax() ) {
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
		if ( $myaccount_page = credglv_get_woo_myaccount() ) {
			if ( ! is_admin() ) {
				wp_redirect( $myaccount_page );
			}
			if ( ! current_user_can( 'administrator' ) ) {
				wp_redirect( $myaccount_page );
			}
		}
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

	const TABLE_NAME = 'credglv_user_refer';

	/**
	 * Run this function when plugin was activated
	 * We need create something like data table, data roles, caps etc..
	 * @return mixed
	 */
	public function onActivate() {
		global $wpdb;
		$tableName = $this->getName();
		if ( ! in_array( $tableName, $wpdb->tables ) ) {
			$charset_collate = $wpdb->get_charset_collate();
			$sql             = "CREATE TABLE $tableName (
                     `id` int(11) NOT NULL AUTO_INCREMENT,
                     `user_id` int(11) NOT NULL,
                     `referral_parent` int(11) NOT NULL,
                     `active` tinyint(1) NOT NULL DEFAULT '0',
                     `referral_code` varchar(5),
                     `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                     `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                     PRIMARY KEY (`id`)
                ) $charset_collate;";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}

	/**
	 * Run this function when plugin was deactivated
	 * We need clear all things when we leave.
	 * Please be a polite man!
	 * @return mixed
	 */
	public function onDeactivate() {

	}

	/**
	 * Run if current version need to be upgraded
	 *
	 * @param string $currentVersion
	 *
	 * @return mixed
	 */
	public function onUpgrade( $currentVersion ) {
		if ( version_compare( $currentVersion, '1.0.1', '<' ) ) {

		}
	}

	/**
	 * @return mixed
	 * example :
	 * return [
	 *    'name' => [
	 *        'label' => 'Name',
	 *        'validate' => ['text', ['length' => 200, 'required' => true, 'message' => 'Please enter a valid name']]
	 *    ],
	 *    'age' => [
	 *        'label' => 'Age',
	 *        'validate' => ['number', ['max' => 100, 'min' => 0, 'message' => 'Please enter a valid age']]
	 *    ]
	 * ]
	 */
	public function getAttributes() {
		/*
		`ID` int(11) NOT NULL AUTO_INCREMENT,
                     `user_id` int(11) NOT NULL,
                     `referral_parent` int(11) NOT NULL,
                     `active` tinyint(1) NOT NULL DEFAULT '0',
                     `referral_code` varchar(5),
                     `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                     `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',*/
		return [
			'ID'              => [
				'label' => 'ID'
			],
			'user_id'         => [
				'label' => 'User Id'
			],
			'referral_parent' => [
				'label' => 'Referral parent'
			],
			'active'          => [
				'label' => 'Active'
			],
			'referral_code'   => [
				'label' => 'Referral Code'
			],
			'created_date'    => [
				'label' => 'Create Date'
			],
			'update_date'     => [
				'label' => 'Update date'
			],

		];
	}

	/**
	 * Abstract function get name of table/model
	 * @return mixed
	 */
	public function getName() {
		return self::getTableName();
	}

	/**
	 * Get table name of this model
	 * @return string
	 */
	public static function getTableName() {
		global $wpdb;

		return $wpdb->prefix . self::TABLE_NAME;
	}


	/**
	 * Get all rating status of an object
	 *
	 * @param $type
	 * @param $object_id
	 * @param string $mode
	 *
	 * @return object [
	 *      'total' => NUMBER,
	 *      'avg'   => NUMBER
	 * ]
	 */
	public static function getRateStatus( $type = 'course', $object_id, $mode = 'simple' ) {
		global $wpdb;
		$tableName = self::getTableName();

		if ( $type == 'course' ) {
			if ( $mode == 'simple' ) {
				if ( ! is_array( $object_id ) ) {
					$result = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(a.id) as total, (SUM(a.rate)/COUNT(a.id)) as `avg` FROM {$tableName} a inner join {$wpdb->users} u on a.user_id=u.ID WHERE a.type = %s AND a.object_id = %d", $type, $object_id ) );
				} else {
					$ids    = implode( ',', $object_id );
					$result = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(a.id) as total, (SUM(a.rate)/COUNT(a.id)) as `avg` FROM {$tableName} a inner join {$wpdb->users} u on a.user_id=u.ID WHERE a.type = %s AND a.object_id IN ({$ids})", $type ) );
				}
			} else {
				$startQuery = [];
				for ( $i = 1; $i <= 5; $i ++ ) {
					$startQuery[] = sprintf( '(SELECT COUNT(rate%4$s.id) FROM %1$s rate%4$s inner join %5$s u on rate%4$s.user_id=u.ID WHERE rate%4$s.type = \'%2$s\' AND rate%4$s.object_id = %3$s AND rate%4$s.rate = %4$s) as rate%4$s', $tableName, $type, $object_id, $i, $wpdb->users );
				}
				$startQuery = implode( ',', $startQuery );
				$result     = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(a.id) as total, (SUM(a.rate)/COUNT(a.id)) as `avg`, {$startQuery} FROM {$tableName} a inner join {$wpdb->users} u on a.user_id=u.ID WHERE a.type = %s AND a.object_id = %d", $type, $object_id ) );
			}
			$result = apply_filters( 'credglv_rating_stats', $result, $type, $object_id );
			if ( ! empty( $result ) ) {
				$result = array_shift( $result );

				return $result;
			}
		} else {
			$status = (object) [ 'total' => 0, 'avg' => 0 ];
		}

		return $status;
	}

	/**
	 * @param $objectId
	 *
	 * @return array|null|object
	 */
	public static function getAllReviews( $objectId ) {
		global $wpdb;
		$table         = $wpdb->prefix . self::TABLE_NAME;
		$list_comments = $wpdb->get_results( $wpdb->prepare( "SELECT a.* FROM {$table} a inner join {$wpdb->users} u on a.user_id=u.ID  WHERE object_id = %d ", $objectId ), OBJECT );

		return $list_comments;
	}


	/**
	 * Delete a object by primary key
	 *
	 * @param $id
	 *
	 * @return boolean
	 */
	public function delete() {
		global $wpdb;
		$table = $wpdb->prefix . self::TABLE_NAME;
		if ( isset( $this->id ) ) {
			$deleted = $wpdb->delete( $table, array( 'id' => $this->id ), array( '%d' ) );
		} else {
			$deleted = false;
		}

		return $deleted;
	}




	/**
	 * Override current behavior after model was saved
	 *
	 * @param $postId
	 *
	 * @return mixed
	 */
	public function afterSave( $postId, $post = null, $update = false ) {
		return false;
	}


	/**
	 * @param int $limit
	 * @param string $type
	 *
	 * @return array|null|object
	 */
	public static function getTopRating( $limit = 10, $type = 'course' ) {
		global $wpdb;
		$table = self::getTableName();
		$query = $wpdb->prepare( "SELECT a.object_id, sum(a.rate)/count(a.id) total FROM {$table} a inner join {$wpdb->users} u on a.user_id=u.ID WHERE a.type=%s GROUP BY a.object_id ORDER BY a.total DESC LIMIT %d", $type, $limit );

		return $wpdb->get_results( $query );
	}

	/**
	 * check status course
	 *
	 * */

	public function checkStatus() {
		global $wpdb;
		$objectId = $this->object_id;
		$userId   = $this->user_id;
		$table    = self::getTableName();
		$query    = $wpdb->prepare( "SELECT * FROM {$table} a WHERE a.object_id=%d and a.user_id=%d", $objectId, $userId );
		$row      = $wpdb->get_row( $query );
		if ( isset( $row ) ) {
			$this->isNew = false;
			$this->id    = $row->id;
		}

		return true;
	}



	public static function get_referralcode() {
		$help_general = new GeneralHelper();

		return $help_general->getRandomString();

	}

	/**
	 * Run when learn master was uninstalled
	 * @return mixed
	 */
	public function onUninstall() {
		global $wpdb;
		$tableName = $this->getName();
		try {
			$wpdb->query( "DROP TABLE {$tableName}" );
		} catch ( \Exception $e ) {

		}
	}

	public static function getPosttypeConfig() {
		// TODO: Implement getPosttypeConfig() method.
	}
}