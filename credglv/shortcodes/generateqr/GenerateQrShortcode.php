<?php
/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\shortcodes\generateqr;

use credglv\core\Shortcode;
use Endroid\QrCode\QrCode;
use Mockery\Exception;

class GenerateQrShortcode extends Shortcode {

	const SHORTCODE_ID = 'credglv_generateqr';

	public $contentView = 'generateqr';


	/**
	 * Get Id of shortcode
	 * @return string
	 */
	public function getId() {
		return self::SHORTCODE_ID;
	}


	/**
	 * Shortcode options
	 * @return array
	 */
	public function getAttributes() {
		return [
			'layout' => '',
		];
	}

	/**
	 * Render shortcode content
	 *
	 * @param array $data
	 * @param array $params
	 *
	 * @return string
	 */
	public function getShortcodeContent( $data = [], $params = [], $key = '' ) {

		$data = $this->getData( $data );

		$qrCode       = new QrCode( '' );
		$file_qr_code = 'qr_code' . get_current_user_id() . '.png';
		$link_file    = CREDGLV_QR_CODE . DIRECTORY_SEPARATOR . $file_qr_code;
		if ( is_dir( CREDGLV_QR_CODE ) ) {
			if ( ! is_file( $link_file ) ) {
				$qrCode->writeFile( $link_file );
			}
		} else {
			throwException( new \Exception( 'cant write qrcode' . CREDGLV_QR_CODE ) );
		}
		$link_file = CREDGLV_QR_CODE_URI . $file_qr_code;

		return $this->render( $this->contentView, compact( 'data', 'link_file' ), true );
	}

	/**
	 * @return array
	 */
	public function getStatic() {
		return [
			[
				'type'         => 'script',
				'id'           => 'credglv-shortcode-generate-qr-script',
				'url'          => 'assets/scripts/credglv-shortcode-generate-qr.js',
				'dependencies' => [ 'credglv', 'credglv.shortcode', 'credglv.ui' ]
			],
			[
				'type'         => 'style',
				'id'           => 'credglv-shortcode-generate-qr-style',
				'url'          => 'assets/styles/credglv-shortcode-generate-qr.css',
				'dependencies' => [ 'credglv-shortcode-style' ]
			]
		];
	}

	/**
	 * list action post ajax
	 * @return array
	 */
	public function actions() {
		return [
			'ajax' => [
				'ajax_checkout_button' => [ $this, 'ajax_button_checkout' ],
			]
		];
	}


	public function ajax_button_checkout() {
		$success = false;
		$message = esc_html__( 'Bad Request.', 'credglv' );
		if ( ! is_user_logged_in() ) {
			header( 'Content-Type: application/json' );
			echo json_encode( array( 'message' => esc_html__( 'requires login', 'credglv' ) ) );
			exit();
		}

		if ( ! empty( $_POST ) ) {
			$user     = wp_get_current_user();
			$fullname = $user->display_name;
			$email    = $user->user_email;
			$data     = ! empty( $_POST['params'] ) ? esc_attr( $_POST['params'] ) : '';
			/*			let price = button.data('price');
					let params = button.data('params');
					let post_type_id = button.data('post_type_id');
					let post_type = button.data('post_type');*/
			$quantity = 1;
			$price    = abs( floatval( ! empty( $_POST['price'] ) ? esc_html( $_POST['price'] ) : 0 ) );

			$post_type_id = ! empty( $_POST['post_type_id'] ) ? esc_attr( $_POST['post_type_id'] ) : '';
			$expire_date  = ! empty( $_POST['expire_date'] ) ? ( $_POST['expire_date'] ) : '';
			$post_type    = ! empty( $_POST['post_type'] ) ? esc_attr( $_POST['post_type'] ) : '';
			$url_return   = ! empty( $_POST['url_return'] ) ? esc_attr( $_POST['url_return'] ) : home_url();
			$title        = esc_html__( $post_type . ' ' . $post_type_id . credglv()->helpers->general->getRandomString( 6 ), 'credglv' );
			$valid_info   = true;
			if ( empty( $fullname ) ) {
				$valid_info = false;
				$message    = esc_html__( 'Fullname not valid.', 'credglv' );
			}
			if ( $valid_info && ! is_email( $email ) ) {
				$valid_info = false;
				$message    = esc_html__( 'Email not valid.', 'credglv' );
			}

			if ( $valid_info ) {
				$order_id = wp_insert_post( array(
					'post_type'   => OrderModel::POST_TYPE,
					'post_status' => 'publish',
					'post_title'  => $title
				), true );
				$total    = floatval( $price * $quantity );
				if ( ! is_wp_error( $order_id ) ) {
					add_post_meta( $order_id, 'credglv_order_user_id', $user->ID );
					add_post_meta( $order_id, 'post_type_product', $post_type );
					if ( BundleModel::POST_TYPE == $post_type ) {
						add_post_meta( $order_id, BundleModel::POST_TYPE, $post_type_id );
					}
					add_post_meta( $order_id, 'fullname', $fullname );
					add_post_meta( $order_id, 'email', $email );
					add_post_meta( $order_id, 'price', $price );
					add_post_meta( $order_id, 'data', $data );
					add_post_meta( $order_id, 'quantity', $quantity );
					update_post_meta( $order_id, 'total', $total );
					add_post_meta( $order_id, 'payment_method', 'paypal' );
					add_post_meta( $order_id, 'status', self::STATUS_SPENDING );
					$callback_url = admin_url( 'admin-ajax.php' ) . '?action=ajax_checkout_button_callback&post_type=' . $post_type . '&post_id=' . $post_type_id . '&url_return=' . $url_return;
					if ( ! empty( $expire_date ) ) {

						$dt2  = new \DateTime( "+" . $expire_date . " month" );
						$date = $dt2->format( "Y-m-d" );
						add_post_meta( $order_id, OrderModel::ORDER_EXPIRABLE, $date );
					}
					switch ( $post_type ) {
						//why add first? for test purpose. You can migrate this code to ajax_button_checkout_callback when payment is success;
						case BundleModel::POST_TYPE:
							add_post_meta( $order_id, $post_type, $post_type_id );
							$bundle_item = get_post_meta( $post_type_id, BundleModel::POST_META );
							if ( ! empty( $bundle_item ) ) {
								foreach ( $bundle_item as $courseID ) {
									OrderController::add_item_to_order( $order_id, $courseID, 1 );
								}
							}
							break;
						case OrderModel::ORDER_EXPIRABLE:
							$courseModel = new CourseModel();
							$courseList  = $courseModel->getAll( array(), false );

							if ( ! empty( $courseList ) ) {
								foreach ( $courseList as $course ) {
									OrderController::add_item_to_order( $order_id, $course->ID, 1 );
								}
							}
							break;
					}
					try {
						$checkout_url = credglv_pay( 'credglv-paypal-gateway', $title, $price, $quantity, $callback_url, $callback_url );

						$transaction_token = '';
						if ( preg_match( '/token=(.*?)&/', $checkout_url, $transaction_token ) ) {
							$transaction_token = $transaction_token[1];
						}
						add_post_meta( $order_id, 'transaction_token', $transaction_token );

						$success = true;
						$message = '';
					} catch ( \Exception $e ) {
						$message      = $e->getMessage();
						$checkout_url = null;
					}
				} else {
					$message = esc_html__( 'Create order failed.', 'credglv' );
				}
			}
		}
//		$total = get_post_meta( $order_id );
		header( 'Content-Type: application/json' );
		echo json_encode( compact( 'success', 'message', 'checkout_url', 'total' ) );
		exit();
	}

}