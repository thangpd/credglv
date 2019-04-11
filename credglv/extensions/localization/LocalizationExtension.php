<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */



namespace credglv\extensions\localization;


use credglv\core\Extension;
use credglv\core\interfaces\ExtensionInterface;


class LocalizationExtension extends Extension implements ExtensionInterface
{
    const DATA_DIR          = 'data';
    const VERSION           = '1.0.1';
    const EXTENSION_ID      = 'localization';
    /**
     * Start Cred GLV extension
     * @return mixed
     */
    public function run()
    {
        // TODO: Implement run() method.
        credglv()->hook->listenFilter('credglv_currencies_list', [$this, 'listCurrency']);
        credglv()->hook->listenFilter('credglv_languages_list', [$this, 'listLanguage']);
        credglv()->hook->listenFilter('credglv_countries_list', [$this, 'listCountry']);
        credglv()->hook->listenFilter('credglv_locales_list', [$this, 'listLocales']);
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        // TODO: Implement isEnabled() method.
    }

    /**
     * @param $type
     * @return array|mixed|object
     */
    private function getFileContent($type)
    {
        $dataPath = dirname(__FILE__) . '/' . self::DATA_DIR . "/$type.json";
        $dataPath = credglv()->helpers->general->fixPath($dataPath);
        if (file_exists($dataPath)) {
            return json_decode(file_get_contents($dataPath), true);
        }
        return [];
    }

    /**
     * Get list email template
     * @param  [type] $list [description]
     * @return [type]       [description]
     */
    public function listCurrency($list){

        if (!is_array($list)) {
            $list = [];
        }
        $data = $this->getFileContent('currency');
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $list[$key] = "{$value} ({$key})";
            }
        }
        return $list;
    }

    /**
     * @param $list
     * @return array
     */
    public function listLocales($list) {
        if (!is_array($list)) {
            $list = [];
        }
        $data = $this->getFileContent('locales');
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $list[$key] = "{$value} ({$key})";
            }
        }
        return $list;
    }


    /**
     * @param $list
     * @return array
     */
    public function listLanguage($list) {
        if (!is_array($list)) {
            $list = [];
        }
        $data = $this->getFileContent('language');
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $list[$key] = "{$value} ({$key})";
            }
        }
        return $list;
    }

    /**
     * @param $list
     * @return array
     */
    public function listCountry($list) {
        if (!is_array($list)) {
            $list = [];
        }
        $data = $this->getFileContent('country');
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $list[$key] = "{$value} ({$key})";
            }
        }
        return $list;
    }

    /**
     * Get current version of extension
     * @return mixed
     */
    public function getVersion()
    {
        return self::VERSION;
    }

    /**
     * Automatic check update version
     * @return mixed
     */
    public function checkVersion()
    {
        // TODO: Implement checkVersion() method.
    }

    /**
     * Run this function when plugin was activated
     * We need create something like data table, data roles, caps etc..
     * @return mixed
     */
    public function onActivate()
    {
        // TODO: Implement onActivate() method.
    }

    /**
     * Run this function when plugin was deactivated
     * We need clear all things when we leave.
     * Please be a polite man!
     * @return mixed
     */
    public function onDeactivate()
    {
        // TODO: Implement onDeactivate() method.
    }

    /**
     * Run if current version need to be upgraded
     * @param string $currentVersion
     * @return mixed
     */
    public function onUpgrade($currentVersion)
    {
        // TODO: Implement onUpgrade() method.
    }

    /**
     * Run when credcoin glv was uninstalled
     * @return mixed
     */
    public function onUninstall()
    {
        // TODO: Implement onUninstall() method.
    }

    /**
     * @return string
     */
    public function getId()
    {
        return self::EXTENSION_ID;
    }
}