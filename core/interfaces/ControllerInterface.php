<?php
/**
 * @project  cred
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\core\interfaces;


interface ControllerInterface
{

    /**
     * Register all actions that controller want to hook
     * @return mixed
     */
    public static function registerAction();
}