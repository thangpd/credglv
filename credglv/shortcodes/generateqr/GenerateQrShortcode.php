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
use credglv\models\UserModel;
use Endroid\QrCode\QrCode;

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
			'width'  => '200'
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

		$data           = $this->getData( $data );
		$link_file      = '';
		$referral       = new UserModel();
		$url_share_link = $referral->get_url_share_link();

		$qrCode       = new QrCode( $url_share_link );
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

		return $this->render( $this->contentView, array( 'data' => $data['data'], 'link_file' => $link_file ), true );
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



}