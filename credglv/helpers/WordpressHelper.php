<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 * @since 1.0
 */


namespace credglv\helpers;


use credglv\core\interfaces\HelperInterface;

class WordpressHelper implements HelperInterface
{
    public function isLoggedIn()
    {
        if ( !function_exists('is_user_logged_in') ) :
            /**
             * Checks if the current visitor is a logged in user.
             *
             * @since 2.0.0
             *
             * @return bool True if user is logged in, false if not logged in.
             */
            function is_user_logged_in() {
                if (function_exists('wp_get_current_user')) {
                    $user = wp_get_current_user();
                    if ( empty( $user->ID ) )
                        return false;
                    return true;
                }
                return false;

            }
        endif;
            return is_user_logged_in();
    }
    public function activate_plugin( $plugin ) {
        $current = get_option( 'active_plugins' );
        $plugin = plugin_basename( trim( $plugin ) );

        if ( !in_array( $plugin, $current ) ) {
            $current[] = $plugin;
            sort( $current );
            //require_once (WP_PLUGIN_DIR . '/' . $plugin);
            do_action( 'activate_plugin', trim( $plugin ) );
            /*$result = activate_plugin($plugin, '', false, true);
            if ( is_wp_error( $result ) ) {
                return false;
            }*/
            update_option( 'active_plugins', $current );
            do_action( 'activate_' . trim( $plugin ) );
            do_action( 'activated_plugin', trim( $plugin) );
        }

        return true;
    }

    /**
     * @param int $root
     * @param array $cats
     * @return array
     */
    public function getCategoryTree($root = 0, &$cats = [], $defaultQuery = 'taxonomy=cat_course&hide_empty=true&orderby=name&order=ASC&parent=') {
        $next = get_categories($defaultQuery . $root);
        if( $next ) :
            foreach( $next as $cat ) :
                /** @var \WP_Term $cat */
                $_cat = [
                    'term_id' => $cat->term_id,
                    'slug' => $cat->slug,
                    'description' => $cat->description,
                    'parent' => $cat->parent,
                    'count' => $cat->count,
                    'name' => $cat->name,
                    'children' => []
                ];
                $this->getCategoryTree( $cat->term_id, $_cat['children'] );
                $cats[$_cat['term_id']] = (object) $_cat;
            endforeach;
        endif;

        return $cats;
    }



    /**
     * @param $param_value
     * @param string $prefix
     * @return string
     */
    public static function shortcode_custom_css_class( $param_value, $prefix = '' ) {
        $css_class = preg_match( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $param_value ) ? $prefix . preg_replace( '/\s*\.([^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', '$1', $param_value ) : '';

        return $css_class;
    }

    /**
     * @param $content
     * @param null $tagnames
     * @return array
     */
    public function getShortcodeIds($content, $tagnames = null ) {
        global $shortcode_tags;

        if ( empty( $tagnames ) ) {
            $tagnames = array_keys( $shortcode_tags );
        }
        $tagregexp = join( '|', array_map('preg_quote', $tagnames) );

        $regex=
            '\\['                              // Opening bracket
            . '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
            . "(.*?)"                            // 2: Shortcode name
            . '(?![\\w-])'                       // Not followed by word character or hyphen
            . '('                                // 3: Unroll the loop: Inside the opening shortcode tag
            .     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
            .     '(?:'
            .         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
            .         '[^\\]\\/]*'               // Not a closing bracket or forward slash
            .     ')*?'
            . ')'
            . '(?:'
            .     '(\\/)'                        // 4: Self closing tag ...
            .     '\\]'                          // ... and closing bracket
            . '|'
            .     '\\]'                          // Closing bracket
            .     '(?:'
            .         '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
            .             '[^\\[]*+'             // Not an opening bracket
            .             '(?:'
            .                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
            .                 '[^\\[]*+'         // Not an opening bracket
            .             ')*+'
            .         ')'
            .         '\\[\\/\\2\\]'             // Closing shortcode tag
            .     ')?'
            . ')'
            . '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]

        if (preg_match_all("/$regex/", $content, $matches)) {
            if (isset($matches[2])) {
                $tags = [];
                foreach ($matches[2] as $match) {
                    if (!empty($match)) {
                        $tags[] = $match;
                    }
                }
                return $tags;
            }
        }
        return [];
    }
}


if (!function_exists('__')) {
    function __($string, $domain){
        return $string;
    }
}