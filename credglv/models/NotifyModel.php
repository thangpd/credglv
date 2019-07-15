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

class NotifyModel extends CustomModel implements ModelInterface, MigrableInterface {
	/**
	 * @return string Profile url
	 */

	const TABLE_NAME = 'credglv_user_notification';

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
			$sql     = "CREATE TABLE $tableName (
                     `id` int(11) NOT NULL AUTO_INCREMENT,
                     `user_id` int(11) NOT NULL,
                     `content` varchar(500) NOT NULL,
                     `type` tinyint(1) NOT NULL,
                     `active` tinyint(1) NOT NULL DEFAULT '0',
                     `link` varchar(500) NULL DEFAULT NULL,
                     `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
			'ID'             => [
				'label' => 'ID'
			],
			'user_id'        => [
				'label' => 'User Id'
			],
			'content'         => [
				'label' => 'Content',
			],
			'type'         => [
				'label' => 'Type'
			],
			'active'            => [
				'label' => 'Active'
			],
			'link'				=> [
				'link'  => 'Link'
			],
			'created_date'   => [
				'label' => 'Create Date'
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

	/*  add user notification
	 *
	 * */
	public function add_user_notification( $user_id, $content, $type, $link ){
		global $wpdb;
		$table  = self::getTableName();
		$data   = array( 'user_id' => $user_id, 'content' => $content, 'type' => $type, 'link' => $link, 'active' => '0' );
		$format = array( '%d', '%s', '%d', '%s', '%d' );
		$wpdb->insert( $table, $data, $format );
		$my_id = $wpdb->insert_id;
		return $my_id;
	}

	/*  update active status user referral
	 *
	 * */
	public function update_user_notification_status( $id, $status = 1 ) {
		global $wpdb;

		return $wpdb->update(
			self::getTableName(),
			array(
				'status'      => $status,
			),
			array(
				'ID' => $id
			)
		);
	}

	public function get_user_by_device_token( $deviceToken ){
		global $wpdb;

		if(!$deviceToken)
			return '';
		$user_id = $wpdb->get_var( "select user_id from " . $wpdb->prefix . "usermeta  where meta_key = 'device_token' and meta_value like '%" . $deviceToken . "%' " );

		return $user_id;
	}

	public function get_user_notification_unseen( $user_id , $status = 1 ){
		global $wpdb;

		$tableName = self::getTableName();

		$prepare = $wpdb->prepare( "select count(id) as total from {$tableName} where user_id=%s and active!=%s", $user_id, $status );
		$result = $wpdb->get_results( $prepare );
		//$result = reset($result);
		return $result;
	}

	public function get_user_notification( $user_id ){
		global $wpdb;

		$tableName = self::getTableName();

		$prepare = $wpdb->prepare( "select * from {$tableName} where user_id=%s order by created_date desc", $user_id );
		$result = $wpdb->get_results( $prepare );

		return $result;
	}

	public function get_log_id_by_tranfer_id( $tranfer_id ) {
		global $wpdb;

		$result = $wpdb->get_var("select id from ".$wpdb->prefix."myCRED_log where data like '%".$tranfer_id."%' order by id desc");

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

	/**
	 * Run when learn master was uninstalled
	 * @return mixed
	 */
	public function onUninstall() {
		global $wpdb;
		$tableName = $this->getName();
		try {
			$wpdb->query( "DROP TABLE {
				$tableName}" );
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