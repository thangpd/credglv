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

class OrderModel extends CustomModel implements ModelInterface, MigrableInterface {
	/**
	 * @return string Profile url
	 */

	const TABLE_NAME = 'credglv_redeem_order';

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
                     `active` tinyint(1) NULL DEFAULT '0',
                     `amount` varchar(50) NOT NULL,
                     `fee` float(11) NULL DEFAULT '0',
                     `data` varchar(255) NULL ,
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
		`id` int(11) NOT NULL AUTO_INCREMENT,
                     `user_id` int(11) NOT NULL,
                     `active` tinyint(1) NULL DEFAULT '0',
                     `amount` varchar(50) NOT NULL,
                     `data` varchar(255) NULL ,
                     `fee` int(11) NULL DEFAULT '0',
                     `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                     `update_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
                     */
		return [
			'ID'           => [
				'label' => 'ID'
			],
			'user_id'      => [
				'label' => 'User Id'
			],
			'active'       => [
				'label' => 'Active',
			],
			'amount'       => [
				'label' => 'Amount'
			],
			'fee'          => [
				'label' => 'Fee'
			],
			'data'         => [
				'label' => 'Data'
			],
			'created_date' => [
				'label' => 'Create Date'
			],
			'update_date'  => [
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