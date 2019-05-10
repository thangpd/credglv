<?php

namespace credglv\admin\controllers;


use credglv\core\interfaces\AdminControllerInterface;


class AdminController extends \credglv\core\Controller implements AdminControllerInterface {
	public function init() {
		$this->viewPath = dirname( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'views/' . $this->getControllerName();
		parent::init();
	}


	/**
	 * Generate setting tabs page
	 */
	public function generateTabs() {


		return $this->render( 'admin' );
	}

	/**
	 * @return array
	 */

	public static function registerAction() {

		return [
			'pages' => [
				'admin' => [
					'credglv-redeem-point' => [
						'title'      => 'Cred GLV settings',
						'capability' => 'activate_plugins',
						'action'     => [ self::getInstance(), 'generateTabs' ],
						'menu'       => 'credglv-redeem-point'
					]
				]
			],
			'ajax'  => [

				'credglv_sort' => [ self::getInstance(), 'sortData' ],
			],
		];
	}

}