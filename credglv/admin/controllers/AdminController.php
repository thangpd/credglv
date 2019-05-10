<?php

namespace credglv\admin\controllers;


use credglv\core\interfaces\AdminControllerInterface;
use credglv\models\OrderModel;


class AdminController extends \credglv\core\Controller implements AdminControllerInterface {
	public function init() {
		$this->viewPath = dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'views/' . $this->getControllerName();
		parent::init();
	}

	public function tabReferral() {
		$format       = '<p class="tr">
            <span>
        <span class="title">Referrer: <br class="no-style-break"></span>
        %1$s
      </span>
            <br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Referal Parent: <br class="no-style-break"></span>
        %2$s
      </span><br class="no-style-break"><br class="no-style-break">

            <span>
        <span class="title">Active: <br class="no-style-break"></span>
        %3$s
      </span><br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Point: <br class="no-style-break"></span>
        %4$s
      </span><br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Gold: <br class="no-style-break"></span>
       %5$s
      </span><br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Cash: <br class="no-style-break"></span>
       %6$s
      </span><br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Local Wallet: <br class="no-style-break"></span>
       %7$s
      </span><br class="no-style-break"><br class="no-style-break">
        </p>
        <p class="spacer">&nbsp;</p>
';
		$data         = [];
		$data['html'] = '';
		$args         = array(
			'blog_id'      => $GLOBALS['blog_id'],
			'role'         => '',
			'role__in'     => array(),
			'role__not_in' => array(),
			'meta_key'     => '',
			'meta_value'   => '',
			'meta_compare' => '',
			'meta_query'   => array(),
			'date_query'   => array(),
			'include'      => array(),
			'exclude'      => array(),
			'orderby'      => 'login',
			'order'        => 'ASC',
			'offset'       => '',
			'search'       => '',
			'number'       => '',
			'count_total'  => false,
			'fields'       => array( 'ID', 'user_login' ),
			'who'          => '',
		);
		$users        = get_users( $args );
		$users_model  = new UserModel();
		foreach ( $users as $val ) {
			$referrer        = '<a href="' . get_edit_user_link( $val->ID ) . '">' . $val->user_login . '</a>';
			$referral_parent = $users_model->get_referral_parent_name( $val->ID );
			$referral_parent = ! empty( $referral_parent->user_login ) ? $referral_parent->user_login : '';

			$check_active   = ! empty( $users_model->check_actived_referral( $val->ID ) ) ? '<label class="switch ">
          <input type="checkbox" name="credglv_active_user" data-user_id="' . $val->ID . '" checked class="primary">
          <span class="slider round"></span>
        </label>' :
				'<label class="switch ">
          <input type="checkbox" name="credglv_active_user" data-user_id="' . $val->ID . '" class="primary">
          <span class="slider round"></span>
        </label>';
			$mycred_balance = mycred_get_users_balance( $val->ID );

			$data['html'] .= sprintf( $format, $referrer, $referral_parent, $check_active, '0', $mycred_balance, '0', '0' );
		}

		if ( defined( 'DOING_AJAX' ) ) {
			$this->render( '_referral', [
				'data'    => $data,
				'message' => __( 'Your change saved successfully', 'credglv' )
			] );
			exit;
		}

		return $this->render( '_referral', [
			'data' => $data
		], true );
	}


	/**
	 * Generate setting tabs page
	 */
	public function generateTabs() {
		$format = '<p class="tr">
<span>
        <span class="title">#<br class="no-style-break"></span>
        %1$s
      </span>
            <br class="no-style-break"><br class="no-style-break">
            <span> 
        <span class="title">User<br class="no-style-break"></span>
        %2$s
      </span>
            <br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Log: <br class="no-style-break"></span>
        %3$s
      </span><br class="no-style-break"><br class="no-style-break">

            <span>
        <span class="title">Status: <br class="no-style-break"></span>
        %4$s
      </span><br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Amount: <br class="no-style-break"></span>
        %5$s
      </span><br class="no-style-break"><br class="no-style-break">
            <span>
        <span class="title">Fee: <br class="no-style-break"></span>
       %6$s
      </span><br class="no-style-break"><br class="no-style-break">
      <span>
        <span class="title">Create Date: <br class="no-style-break"></span>
       %7$s
      </span><br class="no-style-break"><br class="no-style-break">
        </p>
        <p class="spacer">&nbsp;</p>
';

		$order        = new OrderModel();
		$data         = [];
		$data['html'] = '';
		$records      = $order->findAllrecordsUser();
		if ( ! empty( $records ) ) {
			foreach ( $records as $val ) {
				$user_name = get_user_by( 'ID', $val->user_id );
				$user_name = $user_name->data->user_login;
				$log       = json_decode( $val->data );

				$log         = $log->message;
				$status      = $val->active == 0 ? '<label class="switch ">
          <input type="checkbox" name="credglv_active_order" data-order_id="' . $val->id . '" class="primary">
          <span class="slider round"></span>
        </label>' : '<label class="switch ">
          <input type="checkbox" name="credglv_active_order" data-order_id="' . $val->id . '" checked class="primary">
          <span class="slider round"></span>
        </label>';
				$amount      = $val->amount;
				$fee         = $val->fee;
				$create_date = $val->created_date;

				$data['html'] .= sprintf( $format, $val->id, $user_name, $log, $status, $amount, $fee, $create_date );
			}
		}

		return $this->render( 'admin', array( 'data' => $data ) );
	}

	public function disableAutosave() {
		wp_enqueue_script( 'credglv-sw-admin-js', plugins_url( 'assets/js/admin-sw-js.js', __DIR__ ), array( 'jquery' ), '1.0' );
		wp_enqueue_style( 'credglv-sw-referrer-css', plugins_url( 'assets/css/setting-admin.css', __DIR__ ) );
	}

	/**
	 * @return array
	 */

	public function credglv_ajax_active_order() {
		if ( isset( $_POST['order_id'] ) ) {
			$order = OrderModel::getInstance();
			$order->update_active_status( $_POST['order_id'], $_POST['active'] );

			$this->responseJson( array( 'code' => 200, 'Updated user' ) );
		} else {
			$this->responseJson( array( 'code' => 404, 'message' => 'No order_id' ) );
		}
	}

	public static function registerAction() {

		return [
			'pages'   => [
				'admin' => [
					'credglv-redeem-point' => [
						'title'      => 'Cred GLV settings',
						'capability' => 'activate_plugins',
						'action'     => [ self::getInstance(), 'generateTabs' ],
						'menu'       => 'credglv-redeem-point'
					]
				]
			],
			'actions' => [
				'admin_enqueue_scripts' => [ self::getInstance(), 'disableAutosave' ],
			],
			'ajax'    => [
				'credglv_ajax_active_order' => [ self::getInstance(), 'credglv_ajax_active_order' ],
			],
		];
	}

}