<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 * @since 1.0
 *
 */
/**
 * Version of CREDGLV
 */

/**
 * Name of credcoin glv plugin
 */
defined( 'CREDGLV_NAME' ) or define( 'CREDGLV_NAME', 'credglv' );
/**
 * Namespace of credcoin glv plugin
 */
defined( 'CREDGLV_NAMESPACE' ) or define( 'CREDGLV_NAMESPACE', 'credglv' );

define( 'CREDGLV_HOME', 'http://thangpd.info/' );
define( 'CREDGLV_SUPPORT_EMAIL', 'admin@thangpd.info' );


define( 'NAME_PLUGIN', 'credglv' );
define( 'CREDGLV_PATH_PLUGIN', plugins_url() . DIRECTORY_SEPARATOR . NAME_PLUGIN );

defined( 'CREDGLV_PATH' ) or define( 'CREDGLV_PATH', ABSPATH . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . NAME_PLUGIN );
defined( 'PUBLIC_PATH' ) or define( 'PUBLIC_PATH', dirname( CREDGLV_PATH ) . DIRECTORY_SEPARATOR . 'public' );
defined( 'CREDGLV_DEBUG' ) or define( 'CREDGLV_DEBUG', defined( 'WP_DEBUG' ) ? WP_DEBUG : true );
defined( 'CREDGLV_WR_DIR' ) or define( 'CREDGLV_WR_DIR', ABSPATH . 'wp-content' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'credglv' );
defined( 'CREDGLV_QR_CODE' ) or define( 'CREDGLV_QR_CODE', ABSPATH . 'wp-content' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'credglv' . DIRECTORY_SEPARATOR . 'qrcode' );
$glv_upload_path = wp_upload_dir();
defined( 'CREDGLV_QR_CODE_URI' ) or define( 'CREDGLV_QR_CODE_URI', $glv_upload_path ['baseurl'] . '/credglv/qrcode/' );
define( 'CREDGLV_PATH_TEMPLATE', ABSPATH . '/wp-content/plugins/' . NAME_PLUGIN . '/admin/templates/' );

return [
	'lazyShortcode' => false,
	'roleManager'   => [
		'roles'        => [

			\credglv\core\components\RoleManager::CREDGLV_ROLE_ADMIN   => [
				'label'        => __( 'CredGLV ADMIN', 'credglv' ),
				'capabilities' => array(
					'read'          => true,  // true allows this capability
					'edit_posts'    => true,
					'delete_posts'  => false, // Use false to explicitly deny
					'upload_files'  => true,
					'publish_posts' => false,
				)
			],
			\credglv\core\components\RoleManager::CREDGLV_MEMBER_1     => [
				'label'        => __( 'Member Level 1', 'credglv' ),
				'capabilities' => array(
					'read' => true
				)
			],
			\credglv\core\components\RoleManager::CREDGLV_MEMBER_2     => [
				'label'        => __( 'Member Level 2', 'credglv' ),
				'capabilities' => array(
					'read' => true
				)
			],
			\credglv\core\components\RoleManager::CREDGLV_MEMBER_3     => [
				'label'        => __( 'Member Level 3', 'credglv' ),
				'capabilities' => array(
					'read' => true
				)
			],
			\credglv\core\components\RoleManager::CREDGLV_ROLE_DEFAULT => [
				'label'        => __( 'CredGLV Default', 'credglv' ),
				'capabilities' => array(
					'read' => true
				)
			],
		],
		'capabilities' => [
		],
	],
	'resource'      => [
		'handler' => '',
		'assets'  => [
			'scripts' => [
				[
					'id'           => 'credglv',
					'url'          => plugins_url( 'credglv/assets/scripts/credglv.js' ),
					'dependencies' => [
						'backbone',
						'underscore'
					],
				],
				[
					'id'           => 'pulltorefresh',
					'url'          => plugins_url( 'credglv/assets/libs/pulltorefresh/pulltorefresh.js' ),
					'dependencies' => [
						'jquery',
					],
				],
				[
					'id'           => 'credglv-toaster',
					'url'          => plugins_url( 'credglv/assets/scripts/credglv-toaster.js' ),
					'dependencies' => [
						'jquery',
						'boostrap-notify'
					],
				],
				[
					'id'           => 'boostrap-notify',
					'url'          => plugins_url( 'credglv/assets/libs/bootstrap-notify.js' ),
					'dependencies' => [
						'jquery',

					],
				],
				[
					'id'  => 'credglv.shortcode',
					'url' => plugins_url( 'credglv/assets/scripts/credglv.shortcode.js' )
				],
				[
					'id'           => 'credglv.ui',
					'url'          => plugins_url( 'credglv/assets/scripts/credglv.ui.js' ),
					'dependencies' => [
						'select2'
					],
				],
				[
					'id'           => 'select2',
					'url'          => plugins_url( 'credglv/assets/libs/select2/js/select2.min.js' ),
					'dependencies' => [
						'jquery'
					],
				],
				[
					'id'           => 'jquery-validate',
					'url'          => plugins_url( 'credglv/assets/libs/jquery-validation-1.19.0/dist/jquery.validate.js' ),
					'dependencies' => [
						'jquery'
					],
				],
				[
					'id'           => 'jquery-ui',
					'url'          => plugins_url( 'credglv/assets/libs/jquery-ui/jquery-ui.js' ),
					'dependencies' => [
						'jquery'
					],
				],
				[
					'id'           => 'spinning',
					'url'          => plugins_url( 'credglv/assets/libs/spinning/spinning.js' ),
					'dependencies' => [
						'jquery'
					],
				],

			],
			'styles'  => [
				[
					'id'       => 'loading',
					'isInline' => false,
					'url'      => plugins_url( 'credglv/assets/libs/loading-btn/loading.css' ),
				],
				[
					'id'       => 'loading-btn',
					'isInline' => false,
					'url'      => plugins_url( 'credglv/assets/libs/loading-btn/loading-btn.css' ),
				],
				[
					'id'       => 'font-awesome',
					'isInline' => false,
					'url'      => plugins_url( 'credglv/assets/libs/font-awesome/css/all.css' ),
				],
				[
					'id'       => 'font-flaticon',
					'isInline' => false,
					'url'      => plugins_url( 'credglv/assets/libs/font-flaticon/flaticon.css' ),
				],
				[
					'id'           => 'credglv-style',
					'url'          => plugins_url( 'credglv/assets/styles/credglv.css' ),
					'dependencies' => [ 'font-awesome' ]
				],
				[
					'id'           => 'select2',
					'url'          => plugins_url( 'credglv/assets/libs/select2/css/select2.min.css' ),
					'dependencies' => []
				],
				[
					'id'           => 'jquery-ui',
					'url'          => plugins_url( 'credglv/assets/libs/jquery-ui/jquery-ui.css' ),
					'dependencies' => []
				],
				[
					'id'           => 'spinning',
					'url'          => 'https://spin.js.org/spin.css',
					'dependencies' => []
				],
				[
					'id'           => 'boostrap-notify',
					'url'          => plugins_url( 'credglv/assets/styles/animate.css' ),
					'dependencies' => []
				],
			]

		]
	],
	'pages'         => [
		'credglv_search'              => [
			'slug'  => 'credglv-search',
			'label' => __( 'Search page', 'credglv' )
		],
		'credglv_checkout'            => [
			'slug'  => 'credglv-checkout',
			'label' => __( 'Checkout page', 'lame' )
		],
		'credglv_dashboard'           => [
			'slug'  => 'dashboard',
			'label' => __( 'Course dashboard page' )
		],
		'credglv_user_profile'        => [
			'slug'  => 'credglv-user-profile',
			'label' => 'User page'
		],
		'credglv_user_edit_profile'   => [
			'slug'  => 'credglv-user-edit-profile',
			'label' => 'User page'
		],
		'credglv_member_edit_profile' => [
			'slug'  => 'credglv-member-edit-profile',
			'label' => 'User page'
		],
		'credglv_login'               => [
			'slug'  => 'credglv-login',
			'label' => 'Login page'
		],
		'credglv_register'            => [
			'slug'  => 'register',
			'label' => 'Register page'
		]
	],
];