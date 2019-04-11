<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 * @since 1.0
 */


namespace credglv\core\interfaces;


interface ComponentInterface
{
    /**
     * @param array $config
     * @return $this
     */
    public static function getInstance($config = []);
}