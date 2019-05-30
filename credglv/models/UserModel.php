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
		$allowed_roles = array( 'customer', 'credglv_member', 'credglv_student' );
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
		$wpdb->query( 'DROP FUNCTION IF EXISTS `followers_count`' );
		$sql = "
                CREATE FUNCTION `followers_count`(`parent_id` INT, `return_value` VARCHAR(1024)) 
                RETURNS VARCHAR(1024)
                BEGIN
                DECLARE rv,q,queue,queue_children2 VARCHAR(1024);
                DECLARE queue_length,front_id,pos INT;
                DECLARE no_of_followers INT;

                SET rv = parent_id;
                SET queue = parent_id;
                SET queue_length = 1;
                SET no_of_followers = 0;

                WHILE queue_length > 0 DO

                SET front_id = FORMAT(queue,0);
                IF queue_length = 1 THEN
                SET queue = '';
                ELSE
                SET pos = LOCATE(',',queue) + 1;
                SET q = SUBSTR(queue,pos);
                SET queue = q;
                END IF;
                SET queue_length = queue_length - 1;

                SELECT IFNULL(qc,'') INTO queue_children2
                FROM (SELECT GROUP_CONCAT(user_id) qc
                FROM " . $tableName . " WHERE referral_parent IN (front_id)) A;

                IF LENGTH(queue_children2) = 0 THEN
                IF LENGTH(queue) = 0 THEN
                SET queue_length = 0;
                END IF;
                ELSE
                IF LENGTH(rv) = 0 THEN
                SET rv = queue_children2;
                ELSE
                SET rv = CONCAT(rv,',',queue_children2);
                END IF;
                IF LENGTH(queue) = 0 THEN
                SET queue = queue_children2;
                ELSE
                SET queue = CONCAT(queue,',',queue_children2);
                END IF;
                SET queue_length = LENGTH(queue) - LENGTH(REPLACE(queue,',','')) + 1;
                END IF;
                END WHILE;

                IF(return_value = 'count') THEN
                SELECT count(*) into no_of_followers  FROM " . $tableName . " WHERE active = 1 AND FIND_IN_SET(referral_parent, rv );

                RETURN no_of_followers;
                ELSE
                RETURN rv;
                END IF;
                END";

		$wpdb->query( $sql );
		if ( ! in_array( $tableName, $wpdb->tables ) ) {
			$charset_collate = $wpdb->get_charset_collate();
			$sql             = "CREATE TABLE $tableName (
                     `id` int(11) NOT NULL AUTO_INCREMENT,
                     `user_id` int(11) NOT NULL,
                     `referral_parent` int(11) NULL,
                     `active` tinyint(1) NULL DEFAULT '0',
                     `referral_code` varchar(50),
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
				'label' => 'Active',
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


	/*
		 * Retrieve total number of followers
		 */
	function count_referral_user( $user_id ) {
		global $wpdb;
		//return 0;
		$followers = $wpdb->get_var( 'SELECT followers_count(' . $user_id . ', \'count\' )' );

		return $followers;
	}


	public function referral_user( $user_field, $where, $user_id ) {
		global $wpdb;

		return $wpdb->get_var(
			'SELECT ' . $user_field . ' FROM ' . $this->table_name . ' WHERE ' . $where . ' = "' . $user_id . '"'
		);
	}


	public function get_url_share_link() {

		$code = wp_get_current_user();
		$code = $code->data->user_login;
		/*$current_user_id = $this->referral_user( 'user_id', 'user_id', get_current_user_id() );

		if ( $current_user_id ) {
			$code = $this->referral_user( 'referral_code', 'user_id', $current_user_id );
		}*/
		if ( get_option( 'woocommerce_myaccount_page_id', false ) ) {
			$link_share = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . credglv()->config->getUrlConfigs( 'credglv_register' ).'?ru=' . $code;
		} else {
			$link_share = home_url() . '?ru=' . $code;
		}

		return $link_share;
	}

	/* check actived referral
	 *
	 *
	 *
	*/
	public function check_actived_referral( $user_id, $status = 1 ) {
		global $wpdb;
		$tablename = self::getTableName();
		$prepare   = $wpdb->prepare( "SELECT user_id FROM {$tablename} where user_id=%s and active=%s", $user_id, $status );
		$result    = $wpdb->get_results( $prepare );

		return $result;
	}

	/*  update active status user referral
	 *
	 * */
	public function update_active_status( $user_id, $status = 1 ) {
		global $wpdb;
		$wpdb->update(
			self::getTableName(),
			array(
				'active'      => $status,
				'update_date' => date( "Y-m-d H:i:s" ),
			),
			array(
				'user_id' => $user_id
			)
		);
	}

	/**
	 * Get referral parent
	 */
	public function get_referral_parent( $user_id ) {
		global $wpdb;
		$tablename = self::getTableName();
		$prepare   = $wpdb->prepare( "SELECT referral_parent FROM {$tablename} where user_id=%s", $user_id );
		$result    = $wpdb->get_results( $prepare );
		$result    = reset( $result );

		return $result;
	}

	/**
	 * Get referral parent user_login
	 * return ID,user_login object
	 */
	public function get_referral_parent_name( $user_id ) {
		global $wpdb;
		$tablename = self::getTableName();
		$prepare   = $wpdb->prepare( "select DISTINCT refer.referral_parent as ID, u.user_login from {$tablename} refer INNER JOIN {$wpdb->prefix}users u on u.ID=refer.referral_parent and refer.user_id=%s", $user_id );
		$result    = $wpdb->get_results( $prepare );
		$result    = reset( $result );

		return $result;
	}

	/*
	 * get referral tree
	 * Not limit level
	 * */
	// write [when active = 1] and to function mysql to turn of debug
	public function recursive_tree_referral_user( $id, $level = 0 ) {
		$user   = get_user_by( 'ID', $id );
		$user_fullname = get_user_meta($id,'user_fullname',true);
		$avatar = get_user_meta($id,'avatar');

		$subarr = array(
			'ID'           		=> $id,
			'display_name' 		=> $user->data->user_login,
			'display_fullname' 	=> $user_fullname,
			'photo'        		=> $avatar,
			'level'        		=> $level,
		);
		if ( $this->count_referral_user( $id ) ) {
			$subarr['children'] = $this->get_children_referral_user( $id, $level );
		} else {
			$subarr['children'][] = (object) array(
				'ID'           		=> '0',
				'display_name' 		=> __( 'Undefined', 'credglv' ),
				'display_fullname' 	=> __( 'Undefined', 'credglv' ),
				'photo'        		=> get_avatar_url( '', [ 'default' => 'mysteryman' ] )
			);
		}

		return (object) $subarr;
	}


	public function get_children_referral_user( $id, $level = 0 ) {
		global $wpdb;
		$level ++;
		if ( $this->count_referral_user( $id ) ) {
			$tablename = self::getTableName();
			$prepare   = $wpdb->prepare( "select ID,display_name,user_login from " . $wpdb->prefix . "users where ID in (select user_id from {$tablename} where referral_parent=%s)", $id );
			$result    = $wpdb->get_results( $prepare, ARRAY_A );

			$subarr = array();
			foreach ( $result as $k => $v ) {
				$avatar = get_user_meta($v['ID'],'avatar') ? get_user_meta($v['ID'],'avatar') : get_avatar_url( $id, array( 'default' => 'mysteryman' ) );
				$user_fullname = get_user_meta($v['ID'],'user_fullname',true);
				if ( $this->count_referral_user( $v['ID'] ) ) {
					$subarr[] = (object) array(
						'ID'          		=> $v['ID'],
						'display_name' 		=> $v['user_login'],
						'display_fullname' 	=> $user_fullname,
						'photo'        		=> $avatar,
						'level'        		=> $level,
						'children'     		=> $this->get_children_referral_user( $v['ID'], $level )
					);
				} else {
					$subarr[] = (object) array(
						'ID'           		=> $v['ID'],
						'display_name' 		=> $v['user_login'],
						'display_fullname' 	=> $user_fullname,
						'photo'        		=> $avatar,
						'level'        		=> $level,
					);
				}
			}

			return $subarr;
		} else {


		}

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

	public function init() {
		parent::init(); // TODO: Change the autogenerated stub
		$this->active = 0;
	}
}