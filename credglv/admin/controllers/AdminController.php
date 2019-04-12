<?php
namespace credglv\admin\controllers;


use credglv\core\interfaces\AdminControllerInterface;


class AdminController extends \credglv\core\Controller implements AdminControllerInterface{
	public function init(){
        $this->viewPath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'views/' . $this->getControllerName();
        parent::init();
	}


	/**
	 * Generate setting tabs page
	 */
	public function generateTabs()
	{

		return $this->render('settings' );

	}
    /**
     * @return array
     */

    public static  function registerAction(){

        return [
	        'pages' => [
		        'admin' => [
			        'credglv-setting-page' => [
				        'title' => 'Cred GLV settings',
				        'capability' => 'activate_plugins',
				        'action' => [self::getInstance(), 'generateTabs'],
				        'menu' => 'credglv-setting-page'
			        ]
		        ]
	        ],
            'ajax' => [
                'credglv_search_user' => [self::getInstance(), 'searchUser'],
                'credglv_search_course' => [self::getInstance(), 'searchCourse'],
                'credglv_search_member' => [self::getInstance(), 'searchInstructor'],
                'credglv_sort' => [self::getInstance(), 'sortData'],
            ],
        ];
    }

}