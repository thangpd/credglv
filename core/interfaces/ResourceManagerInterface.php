<?php
/**
 * @project  credglv
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\core\interfaces;


interface ResourceManagerInterface
{
    /**
     * Register a script to Assetmanager
     * @param ScriptInterface $script
     * @return mixed
     */
    public function registerScript(ScriptInterface $script);

    /**
     * @param StyleInterface $style
     * @return mixed
     */
    public function registerStyle(StyleInterface $style);

    /**
     * @return ScriptInterface[]
     */
    public function getRegisteredScripts();

    /**
     * @return StyleInterface[]
     */
    public function getRegisteredStyles();
}