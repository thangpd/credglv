<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 * @since 1.0
 */


namespace credglv\core\interfaces;


interface ShortcodeInterface
{
    /**
     * Get shortcode dependencies
     * The dependency shortcode need to be registered before this shortcode
     *
     * @return ShortcodeInterface[]
     */
    public function getDependencies();

    /**
     * Get static resources of shortcode
     *
     * @return [];
     *
     * example :
     * return [
                    [
                        'type'          => 'script'
                        'id'            => 'hello-script',
                        'isInline'      => false,
                        'url'           => 'hello/assets/scripts/hello.js',
                        'dependencies'  => ['jquery']
                    ],
                    [
                        'type'          => 'style'
                        'id'            => 'hello-style',
                        'isInline'      => false,
                        'url'           => 'hello/assets/css/hello.css',
                        'dependencies'  => ['bootstrap']
                    ]
              ]
     */
    public function getStatic();

    /**
     * Get shortcode layout
     * @return string
     */
    public function getLayout();

    /**
     * Get full content of this shortcode
     * @param mixed $data optional
     * @return string
     */
    public function getShortcodeContent($data = []);

    /**
     * Get Id of shortcode
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getPath();


    /**
     * Array of default value of all shortcode options
     * @return array
     */
    public function getAttributes();

}