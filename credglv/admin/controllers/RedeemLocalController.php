<?php
/**
 * @project  cred
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\admin\controllers;


use credglv\core\components\Form;
use credglv\core\components\Hook;
use credglv\core\interfaces\ControllerInterface;
use credglv\core\RuntimeException;
use credglv\models\OrderModel;
use credglv\models\UserModel;


class RedeemLocalController extends AdminController implements ControllerInterface {
	public $viewPath = '';

	public function init() {
		$this->viewPath = dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'views/' . $this->getControllerName();
		parent::init();
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
		$records      = $order->findAllrecordsUser( '', OrderModel::ORDER_TYPE_LOCAL );

		if ( ! empty( $records ) ) {
			foreach ( $records as $val ) {
				$user_name = get_user_by( 'ID', $val->user_id );
				$user_name = $user_name->data->user_login;
				$log       = json_decode( $val->data );

				$log = $log->message;
				if ( ! empty( $user_name ) ) {
					$user_name = $log->user_name;
				}
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

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {
		// TODO: Implement registerAction() method.


		return [
			'pages'   => [
				'admin' => [
					'credglv-redeem-local' => [
						'title'      => 'Redeem Local Bank',
						'capability' => 'activate_plugins',
						'action'     => [ self::getInstance(), 'generateTabs' ],
						'menu'       => 'credglv-redeem-local'
					]
				]
			],
			'actions' => [
				'admin_enqueue_scripts' => [ self::getInstance(), 'disableAutosave' ],
			],
			'ajax'    => [
				'setting-data-save'    => [ self::getInstance(), 'tabData' ],
				'setting-cache-save'   => [ self::getInstance(), 'tabCache' ],
				'options-cache-save'   => [ self::getInstance(), 'tabCache' ],
				'setting-general-save' => [ self::getInstance(), 'tabGeneral' ],
				'setting-payment-save' => [ self::getInstance(), 'tabPayment' ],
			]
		];
	}
}