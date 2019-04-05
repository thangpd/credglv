<?php
/**
 * @project  edu
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\front\controllers;


use credglv\core\components\Script;
use credglv\core\interfaces\FrontControllerInterface;

class HomeController extends FrontController implements FrontControllerInterface
{

    /**
     * Cred home page
     * @return string
     */
    public function credglvHome()
    {
        global $post;
        credglv()->wp->wp_enqueue_script('credglv');
        credglv()->wp->wp_enqueue_script('credglv.ui');
        return $this->render('index', [
            'page' => $post,
            'isHome' => $this->checkHomepage($post)
        ]);
    }

    /**
     * Set /credglv-home as homepage of this site
     */
    public function setHomepage()
    {
        $pages = get_posts( ['name' => 'credglv-home' , 'post_type' => 'page', 'post_status' => 'publish'] );
        if (!empty($pages)) {
            $homepage = array_shift($pages);
            update_option( 'page_on_front', $homepage->ID );
            update_option( 'show_on_front', 'page' );
            $this->responseJson([
                'code' => 200,
                'data' => __('Set your home page completed successfully. Please reload your page to see the change.', 'credglv')
            ]);
        }
    }

    /**
     * Check if credglv homepage is current home page
     * if not show an option to help user set this page is site home page
     * @param WP_POST $page
     * @return bool
     */
    public function checkHomepage($page)
    {
        if (!empty($page) && get_option('page_on_front') == $page->ID && get_option('show_on_front') == 'page') {
            return true;
        }
        return false;
    }
    /**
     * Register all actions that controller want to hook
     * @return mixed
     */
    public static function registerAction()
    {
        // TODO: Implement registerAction() method.
        return [
            'pages' => [
                'front' => [
                    credglv()->config->getUrlConfigs('credglv_home') => ['credglvHome', [
                        'db' => true,
                        'title' => 'Cred Home'
                    ]]
                ]
            ],
            'ajax' => [
                'set_credglv_homepage' => [self::getInstance(), 'setHomepage']
            ]
        ];
    }
}