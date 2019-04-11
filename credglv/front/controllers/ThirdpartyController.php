<?php
/**
 * Date: 4/5/19
 * Time: 12:56 AM
 */

namespace credglv\front\controllers;

use PHPUnit\Runner\Exception;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

use credglv\core\interfaces\FrontControllerInterface;

class ThirdpartyController extends FrontController implements FrontControllerInterface {


//	private $data_sitekey = "6Lc38psUAAAAAJuh9FtinaKCMZPGnTIYk2VFSrlA";//real
//	private $secretKey = "6Lc38psUAAAAABkaRrqrlTlgFgAuT7jml5asYyLz";//real
	private $data_sitekey = "6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI";//test
	private $secretKey = "6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe";//test


	public function captcha_field( $callback = array(), $return = false ) {
		$string_data = '';
		if ( ! empty( $callback ) ) {
			foreach ( $callback as $k => $v ) {
				$string_data .= 'data-' . $k . '="' . $v . '" ';
			}
		}
		$field_catpcha = '<div class="g-recaptcha" ' . $string_data . ' data-theme="dark" data-sitekey="' . $this->data_sitekey . '"></div>';
		if ( ! $return ) {
			echo $field_catpcha;
		} else {
			return $field_catpcha;
		}
	}

	public function verify_captcha( $data ) {
		if ( isset( $data['captcha'] ) ) {
			$captcha = str_replace( 'g-recaptcha-response=', '', $data['captcha'] );
		} else {
			$this->responseJson( array( 'code' => 403, 'message' => 'Please verify captcha' ) );
		}
		if ( empty( $captcha ) ) {
			$this->responseJson( array( 'code' => 403, 'message' => 'Please verify captcha' ) );
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
			// post request to server
			$url          = 'https://www.google.com/recaptcha/api/siteverify?secret='
			                . urlencode( $this->secretKey ) .
			                '&response=' . urlencode( $captcha ) .
			                '&remoteip=' . $ip;
			$response     = file_get_contents( $url );
			$responseKeys = json_decode( $response, true );
			// should return JSON with success as true
			if ( $responseKeys["success"] ) {
				return true;
			} else {
				return false;
			}
		}

	}

	public function sendphone_message() {
		$res = array( 'status' => 'success', 'message' => __( 'No phone number', 'credglv' ) );


		if ( isset( $_POST['phone'] ) && ! empty( $_POST['phone'] ) ) {

// Your Account SID and Auth Token from twilio.com/console
			$account_sid = 'ACbe3df4e270fa38fa4b4db4a3a53c26fc';
			$auth_token  = '5b00046a9b8b90b8278d3152f4e7521e';
// In production, these should be environment variables. E.g.:
// $auth_token = $_ENV["TWILIO_ACCOUNT_SID"]

// A Twilio number you own with SMS capabilities
			$twilio_number = "+15672264603";

			$phone_number = "+" . $_POST['phone'];
			if ( strlen( $phone_number ) > 15 ) {
				$this->responseJson( array(
					'code'    => 403,
					'message' => __( 'No number longer than 15digit', 'credglv' )
				) );
			}

			if ( $trainsient = get_transient( $phone_number ) ) {
				$res['status']  = 'error';
				$res['message'] = __( 'Did not expire yet', 'credglv' );
			} else {
				$send_otp_number = mt_rand( 1000, 9999 );
				set_transient( $_POST['phone'], $send_otp_number, MINUTE_IN_SECONDS );
				try {
					$client = new Client( $account_sid, $auth_token );

					$client->messages->create(
					// Where to send a text message (your cell phone?)
						$phone_number,
						array(
							'from' => $twilio_number,
							'body' => $send_otp_number . __( ' is your code from GLV', 'credglv' ),
						)
					);
				} catch ( TwilioException $e ) {
					$this->responseJson( array(
						'code'    => 403,
						'message' => __( $e->getMessage(), 'credglv' ),
					) );
				}
				$this->responseJson( array(
					'code'    => 200,
					'message' => __( "We sent code verify to your phone. " . $phone_number . ". Expire in 60s", 'credglv' )
				) );
			}

			echo json_encode( $res );

		} else {
			echo json_encode( $res );
		}
		wp_die();

	}

	/**
	 * verify_otp
	 *
	 */
	public function verify_otp( $data ) {
		$res = array( 'status' => 'success', 'message' => 'no data' );

		if ( ! empty( $data['phone'] && ! empty( $data['otp'] ) ) ) {
			$phone_number = "+" . $data['data'];
			$otp          = $data['otp'];
			if ( $trainsient = get_transient( $phone_number ) ) {
				if ( $otp == $trainsient ) {
					return true;
				} else {
					$this->responseJson( array(
						'code'    => 400,
						'message' => __( 'OTP is not matched ', 'credglv' )
					) );
				}
			} else {
				$this->responseJson( array(
					'code'    => 403,
					'message' => __( 'OTP expired', 'credglv' )
				) );
			}
		} else {
			$this->responseJson( array(
				'code'    => 403,
				'message' => __( 'Missing parameter phone or otp', 'credglv' )
			) );
		}
		echo( json_encode( $res ) );
		wp_die();

	}


	public static function registerAction() {
		return [
			'ajax' => [
				'verify_captcha'    => [ self::getInstance(), 'verify_captcha' ],
				'sendphone_message' => [ self::getInstance(), 'sendphone_message' ],
				'verify_otp'        => [ self::getInstance(), 'verify_otp' ],
			],
		];

	}


}