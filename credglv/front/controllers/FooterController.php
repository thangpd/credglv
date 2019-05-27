<?php
/**
 * @project  edu
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\front\controllers;


use credglv\core\components\Script;
use credglv\core\interfaces\FrontControllerInterface;

class FooterController extends FrontController implements FrontControllerInterface
{

    /**
     * Cred home page
     * @return string
     */
	public function credglv_add_script_to_footer(){
		echo '<script type="text/javascript">
            
        PullToRefresh.init({
            mainElement: \'body\',
            onRefresh: function(){ window.location.reload(); }
        });
        </script>';
	}
    /**
     * Register all actions that controller want to hook
     * @return mixed
     */
    public static function registerAction()
    {
        // TODO: Implement registerAction() method.
        return [
	        'actions' => [
		        'wp_footer' => [ self::getInstance(), 'credglv_add_script_to_footer' ],

	        ],
	        'ajax'    => [


	        ],
	        'assets'  => [
		        'css' => [
			        [
				        'id'           => 'credglv-user-register',
				        'isInline'     => false,
				        'url'          => '/front/assets/css/register.css',
				        'dependencies' => [ 'credglv-style', 'select2' ]
			        ],
		        ],
		        'js'  => [
			        [
				        'id'       => 'credglv-main-js',
				        'isInline' => false,
				        'url'      => '/front/assets/js/main.js',
			        ]
		        ]
	        ]
        ];
    }
}