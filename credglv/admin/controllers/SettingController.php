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
use credglv\models\UserModel;


class SettingController extends AdminController implements ControllerInterface {
	public $viewPath = '';

	/**
	 * Default template mails
	 * @var string
	 */
	private $mailTemplateDirectory = CREDGLV_PATH . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'emails';

	public function init() {
		parent::init(); // TODO: Change the autogenerated stub
	}

	/**
	 * Default tabs
	 * @var array
	 */
	private $tabs = [
		'general'  => [
			'label'    => 'General settings',
			'renderer' => [ 'credglv\admin\controllers\SettingController', 'tabGeneral' ]
		],
		'referral' => [
			'label'    => 'Referral',
			'renderer' => [ 'credglv\admin\controllers\SettingController', 'tabReferral' ]
		],

		/*
		'cache'    => [
			'label'    => 'Cache',
			'renderer' => [ 'credglv\admin\controllers\SettingController', 'tabCache' ]
		],

		'payment'  => [
			'label'    => 'Payment',
			'renderer' => [ 'credglv\admin\controllers\SettingController', 'tabPayment' ]
		],
		'frontend' => [
			'label'    => 'Frontend',
			'renderer' => [ 'credglv\admin\controllers\SettingController', 'tabData' ]
		]*/
	];

	/**
	 * Check is current tab
	 *
	 * @param $tab
	 *
	 * @return bool
	 */
	private function isCurrent( $tab ) {
		$currentTab = 'general';

		return isset( $_GET['page'] ) && $_GET['page'] == 'credglv-setting' && ( ( isset( $_GET['tab'] ) && $_GET['tab'] == $tab ) || ( ! isset( $_GET['tab'] ) && $tab == $currentTab ) );
	}

	/**
	 * Generate setting tabs page
	 */
	public function generateTabs() {
		$this->tabs = credglv()->hook->registerFilter( 'credglv_admin_setting_tabs', $this->tabs );
		foreach ( $this->tabs as $name => &$tab ) {
			$tab['active'] = $this->isCurrent( $name );
			if ( $tab['active'] ) {
				$renderer          = $tab['renderer'];
				$tab['tabContent'] = call_user_func( $renderer );
			}
		}

		return $this->render( 'settings', [ 'tabs' => $this->tabs ] );

	}

	/**
	 * For general tab
	 * @return string
	 */
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
	 * For general tab
	 * @return string
	 */
	public function tabData() {
		$data = [];
		if ( isset( $_POST['Pages'] ) ) {
			foreach ( $_POST['Pages'] as $name => $value ) {
				credglv()->config->$name = $value;
			}
			$data['message'] = 'The setting has been saved successfully';
		}
		$pages = credglv()->config->pages;
		$form  = new Form();
		foreach ( $pages as $name => $options ) {
			$value                  = credglv()->config->$name;
			$options['name']        = "Pages[$name]";
			$options['type']        = 'text';
			$options['attributes']  = [ 'class' => 'la-form-control' ];
			$options['template']    = "{label}<div class='config-url'><div class='slug_url'>{input}</div> </div>";
			$options['value']       = empty( $value ) ? $options['slug'] : $value;
			$data['pages'][ $name ] = $form->field( $name, $options );
		}

		if ( defined( 'DOING_AJAX' ) ) {
			$this->render( '_data', $data );
			exit;
		}

		return $this->render( '_data', $data, true );
	}


	/**
	 * For cache tab
	 * @return string
	 */
	public function tabCache() {
		$data            = [
			'caches' => [
				'core_cache'  => __( 'Core caches', 'credglv' ),
				'data_cache'  => __( 'Data caches', 'credglv' ),
				'asset_cache' => __( 'Static asset caches', 'credglv' )
			]
		];
		$options         = apply_filters( 'credglv_cache_options', [] );
		$data['options'] = $options;
		if ( isset( $_POST['all_caches'] ) ) {
			$this->flushCoreCaches();
			$this->flushDataCaches();
			$this->flushAssetsCaches();
			$data['message'] = 'All cache has been flushed successfully';
			$this->render( '_cache', $data );
			exit;

		}
		if ( isset( $_POST['Cache'] ) ) {
			$caches = $_POST['Cache'];
			if ( isset( $caches['core_cache'] ) ) {
				$this->flushCoreCaches();
			}
			if ( isset( $_POST['data_cache'] ) ) {
				$this->flushDataCaches();
			}
			if ( isset( $caches['asset_cache'] ) ) {
				$this->flushAssetsCaches();
			}
			$data['message'] = 'The cache has been flushed successfully';
		}
		if ( isset( $_POST['Options'] ) ) {
			$ops = [];
			foreach ( $options as $name => $label ) {
				$ops[ $name ] = 0;
			}
			$_options = $_POST['Options'];
			$_options = array_merge( $ops, $_options );
			foreach ( $_options as $name => $value ) {
				update_option( $name, $value );
			}
			$data['message'] = 'Your settings has been saved successfully';
		}
		if ( defined( 'DOING_AJAX' ) ) {
			$this->render( '_cache', $data );
			exit;
		}

		return $this->render( '_cache', $data, true );
	}



	/**
	 * Course settings
	 * @return string
	 */
	public function tabGeneral() {
		$data = [];
		/*$credglv_min_transfer = credglv()->config->credglv_min_transfer;

		$credglv_mycred_fee = credglv()->config->credglv_mycred_fee;

		$credglv_comission_level1 = credglv()->config->credglv_comission_level1;

		$credglv_comission_level2 = credglv()->config->credglv_comission_level2;

		$credglv_comission_level3 = credglv()->config->credglv_comission_level3;

		$credglv_comission_level4 = credglv()->config->credglv_comission_level4;

		$credglv_joining_fee = credglv()->config->credglv_joining_fee;*/


		if ( isset( $_POST['Options'] ) ) {
			$share   = 0;
			$options = $_POST['Options'];
			$share   += $options['credglv_comission_level1'];
			$share   += $options['credglv_comission_level2'];
			$share   += $options['credglv_comission_level3'];
			$share   += $options['credglv_comission_level4'];

			if ( $share > $options['credglv_joining_fee'] ) {
				$data['message'] = __( 'Can not save settings, Joining fee is lower than share commission', 'credglv' );
			} else {
				foreach ( $_POST['Options'] as $name => $value ) {
					credglv()->config->$name = $value;
				}
				$data['message'] = __( 'The setting has been saved successfully', 'credglv' );
			}


		}
		if ( defined( 'DOING_AJAX' ) ) {
			$this->render( '_general', $data );
			exit;
		}

		return $this->render( '_general', $data, true );
	}

	/**
	 * Payment settings tab
	 * @return string
	 */
	public function tabPayment() {
		$data            = [];
		$paymentGateways = [];
		$paymentGateways = credglv()->hook->registerFilter( 'credglv_payment_gateways', $paymentGateways );

		if ( isset( $_POST['Payment'] ) ) {
			$payment = $_POST['Payment'];
			if ( isset( $payment['gateways'] ) ) {
				credglv()->config->credglv_payment_gateways = $payment['gateways'];
				$data['message']                            = __( 'Your changes have been saved successfully', 'credglv' );
			}
		}


		$data['gateways'] = $paymentGateways;
		$enabledGateways  = credglv()->config->credglv_payment_gateways;
		if ( empty( $enabledGateways ) ) {
			$enabledGateways = [];
		}
		$data['enabledGateways'] = $enabledGateways;
		if ( isset( $_GET['section'] ) ) {
			$gatewayId = $_GET['section'];
			if ( isset( $paymentGateways[ $gatewayId ] ) ) {
				$data['gateway'] = $paymentGateways[ $gatewayId ];
			}
		}
		if ( defined( 'DOING_AJAX' ) ) {
			$this->render( '_payment', $data );
			exit;
		}

		return $this->render( '_payment', $data, true );
	}

	/**
	 * @param $template
	 *
	 * @return bool|string
	 */
	public function fileGetContent( $template ) {
		return file_get_contents( $this->mailTemplateDirectory . DIRECTORY_SEPARATOR . $template . '.html' );
	}

	/**
	 * Email settings tab
	 * @return string
	 */
	public function tabEmail() {
		$data = [];
		if ( isset( $_POST['edit_email_template'] ) && isset( $_POST['name_template'] ) && isset( $_POST['content_template'] ) ) {
			$name_template = $_POST['name_template'];
			credglv()->mailer->setMailTemplate( $name_template, stripslashes( $_POST['content_template'] ) );
			$data['message'] = __( 'Your change saved successfully', 'credglv' );
		}
		if ( isset( $_POST['reset_template'] ) ) {
			$name_template = $_POST['name_template'];
			credglv()->mailer->restoreDefault( $name_template );
			$data['message'] = __( 'Your template was reset to default', 'credglv' );
		}
		$mailList = credglv()->mailer->getMailingList();

		$list = [
			'' => __( 'Choses template to edit', 'credglv' ),
		];
		foreach ( $mailList as $type => $item ) {
			$list[ $type ] = $item['title'];
		}

		$optionFields = [
			'credglv_email_template' => [
				'type'       => 'select',
				'label'      => __( 'Edit email template: ', 'credglv' ),
				'selected'   => @$_GET['template'],
				'options'    => $list,
				'name'       => 'template',
				'attributes' => [
					'class'           => 'la-form-control',
					'data-select-2'   => true,
					'data-autosubmit' => true,
				]
			],

		];
		$optionFields = credglv()->hook->registerFilter( 'credglv_admin_mail_options', $optionFields );

		$form   = new Form();
		$fields = [];
		foreach ( $optionFields as $name => $optionField ) {
			$fields[] = $form->field( $name, $optionField );
		}
		if ( defined( 'DOING_AJAX' ) ) {
			$this->render( '_general', [
				'fields'  => $fields,
				'message' => __( 'Your change saved successfully', 'credglv' )
			] );
			exit;
		}

		return $this->render( '_email', array_merge( $data, [
			'fields'       => $fields,
			'mailList'     => $mailList,
			'mailTemplate' => ! empty( $_GET['template'] ) ? credglv()->mailer->getMailTemplate( $_GET['template'] ) : ''
		] ), true );
	}

	/**
	 * @param $menu
	 *
	 * @return mixed
	 */
	public function registerAdminMenu( $menu ) {
		$menu['setting']['menu-sub-item'][] = array(
			'page-title' => '',
			'menu-title' => 'Settings',
			'capability' => 'manage_options',
			'slug'       => 'admin.php?page=credglv-setting',
			'parent'     => 'credglv-redeem-point'
		);

		return $menu;
	}

	//save meta post

	public function disableAutosave() {
		wp_enqueue_script( 'credglv-sw-referrer-js', plugins_url( 'assets/js/referrer-sw-js.js', __DIR__ ), array( 'jquery' ), '1.0' );
		wp_enqueue_style( 'credglv-sw-referrer-css', plugins_url( 'assets/css/setting-admin.css', __DIR__ ) );
	}

	/**
	 * Register all actions that controller want to hook
	 * @return mixed
	 */
	public static function registerAction() {
		// TODO: Implement registerAction() method.

		credglv()->hook->listenFilter( Hook::CREDGLV_HOOK_ADMIN_MENU, [ self::getInstance(), 'registerAdminMenu' ] );

		return [
			'pages'   => [
				'admin' => [
					'credglv-setting' => [
						'title'      => 'Credglv settings',
						'capability' => 'activate_plugins',
						'action'     => [ self::getInstance(), 'generateTabs' ],
						'menu'       => 'credglv-setting-menu'
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