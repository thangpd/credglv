<?php
/**
 * @project  credglv
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\core\interfaces;


interface ExtensionInterface extends MigrableInterface
{
    /**
     * @return string
     */
    public function getId();
    /**
     * Start Cred GLV extension
     * @return mixed
     */
    public function run();

    /**
     * @return boolean
     */
    public function isEnabled();

    /**
     * Get current version of extension
     * @return mixed
     */
    public function getVersion();

    /**
     * Automatic check update version
     * @return mixed
     */
    public function checkVersion();
}