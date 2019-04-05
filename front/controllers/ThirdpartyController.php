<?php
/**
 * Date: 4/5/19
 * Time: 12:56 AM
 */

namespace credglv\front\controllers;


use credglv\core\interfaces\FrontControllerInterface;

class ThirdpartyController extends FrontController implements FrontControllerInterface {


	private $data_sitekey = "6Lc38psUAAAAAJuh9FtinaKCMZPGnTIYk2VFSrlA";

	public function captcha_field( $return = false ) {
		$field_catpcha = '<div class="g-recaptcha" data-theme="dark" data-sitekey="' . $this->data_sitekey . '"></div>';
		if ( ! $return ) {
			echo $field_catpcha;
		} else {
			return $field_catpcha;
		}
	}

	public function verify_captcha() {
		if ( isset( $_POST['g-recaptcha-response'] ) ) {
			$captcha = $_POST['g-recaptcha-response'];
		}
		if ( ! $captcha ) {
			echo '<h2>Please check the the captcha form.</h2>';
			exit;
		}
		$secretKey = "Put your secret key here";
		$ip        = $_SERVER['REMOTE_ADDR'];
		// post request to server
		$url          = 'https://www.google.com/recaptcha/api/siteverify?secret='
		                . urlencode( $secretKey ) .
		                '&response=' . urlencode( $captcha ) .
		                '&remoteip=' . $ip;
		$response     = file_get_contents( $url );
		$responseKeys = json_decode( $response, true );
		// should return JSON with success as true
		if ( $responseKeys["success"] ) {
			echo '<h2>Thanks for posting comment</h2>';
		} else {
			echo '<h2>You are spammer ! Get the @$%K out</h2>';
		}
	}

	public static function registerAction() {
		// TODO: Implement registerAction() method.
	}


}