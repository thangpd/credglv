<?php

namespace credglv\admin;

use credglv\admin\controllers\FieldController;
use credglv\core\components\Hook;

use credglv\core\components\RoleManager;
use credglv\core\components\Script;
use credglv\core\components\Style;
use credglv\core\interfaces\ResourceInterface;
use credglv\core\Template;

class AdminTemplate extends Template {
	public function init() {
		parent::init(); // TODO: Change the autogenerated stub
		if ( is_admin() ) {
			$assets = $this->getAssets();
			foreach ( $assets as $type => $resources ) {
				foreach ( $resources as $resource ) {
					/** @var ResourceInterface $static */
					$static = '';
					if ( $type == 'css' ) {
						$static = new Style( $resource );
						wp_enqueue_style( $static->getId(), $static->getUrl(), $static->getDependencies() );
					} else {
						$static = new Script( $resource );
						wp_enqueue_script( $static->getId(), $static->getUrl(), $static->getDependencies() );
					}
				}
			}
		}
	}

	public function getAssets() {
		return [
			'css' => [
				[
					'id'           => 'admin-ui-style',
					'isInline'     => false,
					'url'          => '/assets/admin/css/credglv-admin.css',
					'dependencies' => [ 'credglv-style' ]
				],

			],
			'js'  => [
				[
					'id'           => 'jquery.pjax',
					'isInline'     => false,
					'url'          => '/assets/scripts/jquery.pjax.min.js',
					'dependencies' => [ 'jquery' ]
				],

				[
					'id'           => 'credglv-admin-script',
					'isInline'     => false,
					'url'          => '/assets/admin/js/credglv-admin.js',
					'dependencies' => [ 'jquery' ]
				],
				[
					'id'           => 'credglv-admin-ui-script',
					'isInline'     => false,
					'url'          => '/assets/admin/js/credglv-admin_ui.js',
					'dependencies' => [ 'jquery' ]
				],
			]
		];
	}

	//add list menu item for admin menu
	public function addAdminMenu() {
		$main_menu = $this->getListMenuConfig();
		$main_menu = credglv()->hook->registerFilter( Hook::CREDGLV_HOOK_ADMIN_MENU, $main_menu );
		foreach ( $main_menu as $item ) {
			$this->addMenuPageAdmin( $item );
		}
	}

	//add a menu for menu admin
	public function addMenuPageAdmin( $item ) {
		if ( ! empty( $item['callback'] ) ) {
			add_menu_page( $item['page-title'], $item['menu-title'],
				$item['capability'], $item['slug'], array( $this, $item['callback'] ),
				$item['icon'], $item['position'] );
		} else {
			add_menu_page( $item['page-title'], $item['menu-title'],
				$item['capability'], $item['slug'], '',
				$item['icon'], $item['position'] );
		}

		if ( isset( $item['menu-sub-item'] ) && count( $item['menu-sub-item'] ) > 0 ) {
			foreach ( $item['menu-sub-item'] as $subitem ) {
				if ( ! empty( $subitem['callback'] ) ) {
					add_submenu_page( $item['slug'], $subitem['page-title'], $subitem['menu-title'], $subitem['capability'], $subitem['slug'], $subitem['callback'] );
				} else {
					add_submenu_page( $item['slug'], $subitem['page-title'], $subitem['menu-title'], $subitem['capability'], $subitem['slug'], false );
				}
			}
		}
	}

	/**
	 * Defined list of default admin menu
	 * @return array
	 */
	public function getListMenuConfig() {

		$list_menu = [
			'setting' => array(
				'page-title'    => '',
				'menu-title'    => 'Cred GLV',
				'capability'    => 'activate_plugins',
				'slug'          => 'credglv-setting-page',
				'icon'          => 'dashicons-welcome-learn-more',
				'position'      => 5,
				'callback'      => '',
				'menu-sub-item' => []
			),


		];

		return $list_menu;
	}
}