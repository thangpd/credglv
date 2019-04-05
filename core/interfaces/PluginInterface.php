<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 * @since 1.0
 */


namespace credglv\core\interfaces;


interface PluginInterface
{
    /**
     * Return id of plugin
     * @return mixed
     */
    public function getId();

    /**
     * Start run plugin
     * @return mixed
     */
    public function run();

    /**
     * Get version of plugin
     * @return mixed
     */
    public function getVersion();
}