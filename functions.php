<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 * Global functions in LearnMaster plugin
 */


/**
 * Get instance of LearnMaster App plugin
 * @return \credglv\core\App
 */
function credglv()
{
    return \credglv\core\App::getInstance();
}


/**
 * @param $str
 */
function credglv_do_shortcode($str)
{
    if (credglv_is_ready()) {
        return credglv()->shortcodeManager->doShortcode($str);
    }
    return false;

}


/**
 * Minify Css files
 * @param $files
 * @param $name
 * @return bool|string
 */
function credglv_minify_css($files, $name)
{
    if (credglv_is_ready()) {
        return credglv()->resourceManager->releaseStyle($files, $name);
    }
    return false;

}

/**
 * Minify script files
 * @param $files
 * @param $name
 * @return bool|string
 */
function credglv_minify_js($files, $name)
{
    if (credglv_is_ready()) {
        return credglv()->resourceManager->releaseScript($files, $name);
    }
    return false;

}

/**
 * @param $html
 * @return mixed
 */
function credglv_minify_html($html)
{
    if (credglv_is_ready()) {
        return credglv()->helpers->general->minifyHtml($html);
    }
    return false;

}

    add_filter('show_admin_bar', '__return_false');

/**
 * @return bool
 */
function credglv_is_ready()
{
    return credglv()->isReady();
}


add_action('plugins_loaded', function () {
    load_plugin_textdomain('credglv', false, basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'languages');
}, 9);
