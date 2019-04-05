<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 * @since 1.0
 *
 *
 * Boot object for credglv plugin
 * Create app instance and global components
 *
 *
 */

namespace credglv\core;




class Bootstrap extends BaseObject
{

    /**
     * Boot application
     */
    public static function boot($config = [])
    {
        $app = App::getInstance($config);
        add_action('after_setup_theme' , [$app, '__init'], 0);
        add_action('wp_loaded' , [$app, 'run']);
    }
}


