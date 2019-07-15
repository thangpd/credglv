<?php
/**
 * Plugin Name: myCRED Partial Payment - WooCommerce
 * Description: Allows partial payments of a WooCommerce orders using points.
 * Version: 1.4
 * Tags: points, credit, partial, payment, woocommerce
 * Plugin URI: https://www.mycred.me/store/partial-payments-woo/
 * Author: myCred
 * Author URI: https://wpexperts.io/
 * Author Email: support@mycred.me
 * Requires at least: WP 4.2
 * Tested up to: WP 4.8.2
 * Text Domain: mycredpartwoo
 * Domain Path: /lang
 */
if ( ! class_exists( 'myCRED_WooCommerce_Partial' ) ) :
	final class myCRED_WooCommerce_Partial {

		// Plugin Version
		public $version = '1.4';

		public $slug = 'mycred-partial-woo';

		// Instnace
		protected static $_instance = null;

		// Current session
		public $session = null;

		public $domain = 'mycredpartwoo';
		public $update_url = 'https://mycred.me/api/plugins/';

		/**
		 * Setup Instance
		 * @since 1.0
		 * @version 1.2
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Not allowed
		 * @since 1.0
		 * @version 1.2
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', $this->version );
		}

		/**
		 * Not allowed
		 * @since 1.0
		 * @version 1.2
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', $this->version );
		}

		/**
		 * Define
		 * @since 1.0
		 * @version 1.2
		 */
		private function define( $name, $value, $definable = true ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			} elseif ( ! $definable && defined( $name ) ) {
				_doing_it_wrong( 'myCRED_WooCommerce_Partial->define()', 'Could not define: ' . $name . ' as it is already defined somewhere else!', $this->version );
			}
		}

		/**
		 * Require File
		 * @since 1.0
		 * @version 1.2
		 */
		public function file( $required_file ) {
			if ( file_exists( $required_file ) ) {
				require_once $required_file;
			} else {
				_doing_it_wrong( 'myCRED_WooCommerce_Partial->file()', 'Requested file ' . $required_file . ' not found.', $this->version );
			}
		}

		/**
		 * Construct
		 * @since 1.0
		 * @version 1.2
		 */
		public function __construct() {

			$this->define_constants();
			$this->includes();

			$this->mycred();
			$this->woocommerce();

			register_activation_hook( MYCRED_PARTWOO_THIS, array( __CLASS__, 'activate_plugin' ) );

			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_plugin_update' ), 88 );
			add_filter( 'plugins_api', array( $this, 'plugin_api_call' ), 88, 3 );
			add_filter( 'plugin_row_meta', array( $this, 'plugin_view_info' ), 88, 3 );

		}

		/**
		 * Define Constants
		 * First, we start with defining all requires constants if they are not defined already.
		 * @since 1.0
		 * @version 1.2
		 */
		private function define_constants() {

			$this->define( 'MYCRED_PARTWOO_VERSION', $this->version );
			$this->define( 'MYCRED_PARTWOO_SLUG', $this->slug );

			$this->define( 'MYCRED_PARTWOO_THIS', __FILE__ );
			$this->define( 'MYCRED_PARTWOO_ROOT_DIR', plugin_dir_path( MYCRED_PARTWOO_THIS ) );
			$this->define( 'MYCRED_PARTWOO_INCLUDES_DIR', MYCRED_PARTWOO_ROOT_DIR . 'includes/' );
			$this->define( 'MYCRED_PARTWOO_TEMPLATES_DIR', MYCRED_PARTWOO_ROOT_DIR . 'templates/' );

		}

		/**
		 * Include Plugin Files
		 * @since 1.0
		 * @version 1.2
		 */
		public function includes() {

			$this->file( MYCRED_PARTWOO_INCLUDES_DIR . 'mycred-partial-woo-functions.php' );

			$this->file( MYCRED_PARTWOO_INCLUDES_DIR . 'mycred-partial-woo-checkout.php' );
			$this->file( MYCRED_PARTWOO_INCLUDES_DIR . 'mycred-partial-woo-orders.php' );
			$this->file( MYCRED_PARTWOO_INCLUDES_DIR . 'mycred-partial-woo-settings.php' );
			$this->file( MYCRED_PARTWOO_INCLUDES_DIR . 'mycred-partial-woo-myaccount.php' );

		}

		/**
		 * myCRED
		 * @since 1.0
		 * @version 1.2
		 */
		public function mycred() {

			add_action( 'mycred_init', array( $this, 'start_up' ) );
			add_action( 'mycred_front_enqueue', array( $this, 'enqueue_scripts' ) );
			add_filter( 'mycred_run_this', array( $this, 'reward_adjustment' ) );

		}

		/**
		 * Start
		 * @since 1.0
		 * @version 1.2
		 */
		public function start_up() {

			// Bail if WooCommerce is not installed
			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			global $mycred_partial_payment, $mycred_remove_partial_payment;

			$mycred_partial_payment        = mycred_part_woo_settings();
			$mycred_remove_partial_payment = false;

			mycred_woo_partial_setup_my_account();

		}

		/**
		 * Enqueue Scripts
		 * @since 1.0
		 * @version 1.2
		 */
		public function enqueue_scripts() {

			if ( ! is_user_logged_in() ) {
				return;
			}

			global $mycred_partial_payment;

			wp_register_script(
				'mycred-partial-payment-woo',
				plugins_url( 'assets/js/mycred-partial-payment.js', MYCRED_PARTWOO_THIS ),
				array( 'jquery' ),
				MYCRED_PARTWOO_VERSION,
				true
			);

			if ( function_exists( 'is_checkout' ) && is_checkout() ) {

				$user_id = get_current_user_id();

				$mycred = mycred( $mycred_partial_payment['point_type'] );
				if ( $mycred->exclude_user( $user_id ) ) {
					return;
				}

				$total = mycred_part_woo_get_total();

				$balance = $mycred->get_users_balance( $user_id );
				$max     = $mycred->number( $total / $mycred_partial_payment['exchange'] );
				if ( $balance < $max ) {
					$max = $balance;
				}

				$min    = ( ( $mycred_partial_payment['min'] > 0 ) ? $mycred_partial_payment['min'] : 0 );
				$format = sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), 'COST' );

				wp_localize_script(
					'mycred-partial-payment-woo',
					'myCREDPartial',
					array(
						'ajaxurl'  => get_permalink( get_option( 'woocommerce_checkout_page_id' ) ),
						'token'    => wp_create_nonce( 'mycred-partial-payment-new' ),
						'reload'   => wp_create_nonce( 'mycred-partial-payment-reload' ),
						'rate'     => $mycred_partial_payment['exchange'],
						'max'      => $max,
						'min'      => $min,
						'total'    => $total,
						'step'     => $mycred->number( $mycred_partial_payment['step'] ),
						'decimals' => $mycred->format['decimals'],
						'format'   => $format
					)
				);

				wp_enqueue_script( 'mycred-partial-payment-woo' );

			}

		}

		/**
		 * Reward Adjustments
		 * When you make a partial payment in points AND you set your store to reward
		 * store purchases using points, this partial payment can in certain setups cause
		 * a user to get their points back or get more back due to rewards.
		 * This filter will deduct the amount of points a user made as a partial payment (if they made one)
		 * and deduct this amount from the reward amount to prevent the user to ever gaining more than they paid.
		 * @since 1.0
		 * @version 1.2
		 */
		public function reward_adjustment( $run_this ) {

			// We need WooCommerce for this
			if ( ! function_exists( 'wc_get_order' ) ) {
				return $run_this;
			}

			extract( $run_this );

			$prefs = mycred_part_woo_settings();
			if ( ! array_key_exists( 'rewards', $prefs ) || $prefs['rewards'] != 2 ) {
				return $run_this;
			}

			// Only applicable for store rewards payouts
			if ( apply_filters( 'mycred_woo_reward_reference', 'reward', 0, $type ) == $ref && apply_filters( 'mycred_woo_reward_mycred_payment', false, 0 ) === false ) {

				$order_id = absint( $ref_id );
				$order    = wc_get_order( $order_id );

				$discount = $order->get_cart_discount_total();

				// No discount used = nothing for us to do
				if ( $discount <= 0 ) {
					return $run_this;
				}

				if ( $prefs['exchange'] != 1 ) {
					$discount = $discount / $prefs['exchange'];
				}

				// Stop transaction if the user is getting more than they
				if ( ( $amount - $discount ) <= 0 ) {
					$run_this['amount'] = null;
					$run_this['entry']  = '';
				} // Deduct the amount the user paid from the reward
				else {
					$run_this['amount'] = ( $amount - $discount );
				}

			}

			return $run_this;

		}

		/**
		 * WooCommerce
		 * @since 1.0
		 * @version 1.2
		 */
		public function woocommerce() {

			add_filter( 'woocommerce_available_payment_gateways', array( $this, 'available_gateways' ) );
			add_filter( 'woocommerce_locate_template', array( $this, 'locate_templates' ), 10, 3 );

		}

		/**
		 * Available Gateways
		 * Remove the myCRED gateway if it exists as we will replace it
		 * with our own.
		 * @since 1.0
		 * @version 1.2
		 */
		public function available_gateways( $gateways ) {

			if ( ! isset( $gateways['mycred'] ) ) {
				return $gateways;
			}

			unset( $gateways['mycred'] );

			return $gateways;

		}

		/**
		 * Locate Template
		 * Since we are using WooCommerce functions to locate template files,
		 * we need to make sure we always provide our own default template.
		 * @since 1.0
		 * @version 1.2
		 */
		public function locate_templates( $template, $template_name, $template_path ) {


			if ( str_replace( 'woocommerce/templates/', '', $template ) !== $template && $template_name == 'checkout/mycred-partial-payments.php' ) {


				$default = MYCRED_PARTWOO_TEMPLATES_DIR . 'mycred-partial-payments.php';

				// Check if the theme has a file we should be using instead
				$_template = locate_template( array( $this->slug . '/mycred-partial-payments.php' ) );
				if ( ! $_template && file_exists( $default ) ) {
					$_template = $default;
				}

				return $_template;

			}

			return $template;

		}

		/**
		 * Load Textdomain
		 * @since 1.0
		 * @version 1.2
		 */
		public function load_textdomain() {

			// Load Translation
			$locale = apply_filters( 'plugin_locale', get_locale(), $this->domain );

			load_textdomain( $this->domain, WP_LANG_DIR . '/' . $this->slug . '/' . $this->domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $this->domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

		}

		/**
		 * Activate
		 * @since 1.0
		 * @version 1.2
		 */
		public static function activate_plugin() {

			global $wpdb;

			$message = array();

			// WordPress check
			$wp_version = $GLOBALS['wp_version'];
			if ( version_compare( $wp_version, '4.2', '<' ) ) {
				$message[] = __( 'This myCRED Add-on requires WordPress 4.2 or higher. Version detected:', 'mycredpartwoo' ) . ' ' . $wp_version;
			}

			// PHP check
			$php_version = phpversion();
			if ( version_compare( $php_version, '5.3', '<' ) ) {
				$message[] = __( 'This myCRED Add-on requires PHP 5.3 or higher. Version detected: ', 'mycredpartwoo' ) . ' ' . $php_version;
			}

			// SQL check
			$sql_version = $wpdb->db_version();
			if ( version_compare( $sql_version, '5.0', '<' ) ) {
				$message[] = __( 'This myCRED Add-on requires SQL 5.0 or higher. Version detected: ', 'mycredpartwoo' ) . ' ' . $sql_version;
			}

			// Not empty $message means there are issues
			if ( ! empty( $message ) ) {

				$error_message = implode( "\n", $message );
				die( __( 'Sorry but your WordPress installation does not reach the minimum requirements for running this add-on. The following errors were given:', 'mycredpartwoo' ) . "\n" . $error_message );

			}

			mycred_woo_partial_setup_my_account( true );

		}

		/**
		 * Plugin Update Check
		 * @since 1.0
		 * @version 1.2
		 */
		public function check_for_plugin_update( $checked_data ) {

			global $wp_version;

			if ( empty( $checked_data->checked ) ) {
				return $checked_data;
			}

			$args           = array(
				'slug'    => $this->slug,
				'version' => $this->version,
				'site'    => site_url()
			);
			$request_string = array(
				'body'       => array(
					'action'  => 'version',
					'request' => serialize( $args ),
					'api-key' => md5( get_bloginfo( 'url' ) )
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			);

			// Start checking for an update
			$response = wp_remote_post( $this->update_url, $request_string );

			if ( ! is_wp_error( $response ) ) {

				$result = maybe_unserialize( $response['body'] );

				if ( is_object( $result ) && ! empty( $result ) ) {
						$checked_data->response[ $this->plugin ] = $result;
				}
			}

			return $checked_data;

		}

		/**
		 * Plugin View Info
		 * @since 1.0
		 * @version 1.2
		 */
		public function plugin_view_info( $plugin_meta, $file, $plugin_data ) {

			if ( $file != plugin_basename( MYCRED_PARTWOO_THIS ) ) {
				return $plugin_meta;
			}

			$plugin_meta[] = sprintf( '<a href="%s" class="thickbox" aria-label="%s" data-title="%s">%s</a>',
				esc_url( network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . $this->slug .
				                            '&TB_iframe=true&width=600&height=550' ) ),
				esc_attr( __( 'More information about this plugin', 'mycredpartwoo' ) ),
				esc_attr( 'myCRED Partial Payments - WooCommerce' ),
				__( 'View details', 'mycredpartwoo' )
			);

			$url     = str_replace( array( 'https://', 'http://' ), '', get_bloginfo( 'url' ) );
			$expires = get_option( 'mycred-premium-' . $this->slug . '-expires', '' );
			if ( $expires != '' ) {

				if ( $expires == 'never' ) {
					$plugin_meta[] = 'Unlimited License';
				} elseif ( absint( $expires ) > 0 ) {

					$days = ceil( ( $expires - current_time( 'timestamp' ) ) / DAY_IN_SECONDS );
					if ( $days > 0 ) {
						$plugin_meta[] = sprintf(
							'License Expires in <strong%s>%s</strong>',
							( ( $days < 30 ) ? ' style="color:red;"' : '' ),
							sprintf( _n( '1 day', '%d days', $days ), $days )
						);
					}

					$renew = get_option( 'mycred-premium-' . $this->slug . '-renew', '' );
					if ( $days < 30 && $renew != '' ) {
						$plugin_meta[] = '<a href="' . esc_url( $renew ) . '" target="_blank" class="delete">Renew License</a>';
					}

				}

			} else {
				$plugin_meta[] = '<a href="http://mycred.me/about/terms/#product-licenses" target="_blank">No license found for - ' . $url . '</a>';
			}

			return $plugin_meta;

		}

		/**
		 * Plugin New Version Update
		 * @since 1.0
		 * @version 1.2
		 */
		public function plugin_api_call( $result, $action, $args ) {

			global $wp_version;

			if ( ! isset( $args->slug ) || ( $args->slug != $this->slug ) ) {
				return $result;
			}

			// Get the current version
			$args           = array(
				'slug'    => $this->slug,
				'version' => $this->version,
				'site'    => site_url()
			);
			$request_string = array(
				'body'       => array(
					'action'  => 'info',
					'request' => serialize( $args ),
					'api-key' => md5( get_bloginfo( 'url' ) )
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			);

			$request = wp_remote_post( $this->update_url, $request_string );

			if ( ! is_wp_error( $request ) ) {
				$result = maybe_unserialize( $request['body'] );
			}

			if ( $result->license_expires != '' ) {
				update_option( 'mycred-premium-' . $this->slug . '-expires', $result->license_expires );
			}

			if ( $result->license_renew != '' ) {
				update_option( 'mycred-premium-' . $this->slug . '-renew', $result->license_renew );
			}

			return $result;

		}

	}
endif;

function mycred_woo_partial_payments() {
	return myCRED_WooCommerce_Partial::instance();
}

mycred_woo_partial_payments();
