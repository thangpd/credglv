<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\core;


use credglv\core\interfaces\ExtensionInterface;

abstract class Extension extends BaseObject
{
    /**
     * Do not allow constructor call from child
     * Extension constructor.
     * @param array $config
     */
    public final function __construct($config = [])
    {
        parent::__construct($config);
    }

    /**
     * @param $view
     * @param array $data
     * @param bool $return
     * @return string | mixed
     * @throws RuntimeException
     */
    public function render($view, $data = [], $return = false)
    {
        $viewFile = file_exists($view) ? $view : $this->currentPath() . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $view . '.php';
        if (!file_exists($viewFile)) {
            throw new RuntimeException(__('Can not find view of extension : ' . $viewFile, 'credglv'));
        }
        $result = credglv()->helpers->general->renderPhpFile($viewFile, $data);
        if (!$result) {
            echo $result;
        } else {
            return $result;
        }

    }

    /**
     * Get current path of this extension
     * @return string
     */
    protected function currentPath($allowSymlink = false)
    {
        $reflector = new \ReflectionClass(get_called_class());
        $fn = $reflector->getFileName();
        if ($allowSymlink) {
            $path = dirname($fn);
            if (strpos($path, ABSPATH) === false) {
                //This is symlink
                $path = ABSPATH . 'wp-content/plugins/' . CREDGLV_NAME .'/'    . substr($path, strpos($path, 'extensions'));
                $path = credglv()->helpers->general->fixPath($path);
            }
            return credglv()->helpers->general->fixPath($path);
        }
        return dirname($fn);
    }
    /**
     * No child extension can auto call init after it was created
     */
    public final function init()
    {

    }

    /**
     * @param string $file
     * @return string
     */
    public function getExtensionUrl($file = '')
    {
        $extPath = $this->currentPath(true);
        $relativePath = substr($extPath, strpos($extPath, credglv()->helpers->general->fixPath(ABSPATH)));
        $extBaseUrl = credglv()->wp->site_url() . '/' . str_replace("\\",'/',  str_replace( credglv()->helpers->general->fixPath(ABSPATH), '', $extPath)) . '/';

        if (!empty($file)) {
            return $extBaseUrl . '/' . $file;
        }
        return $extBaseUrl;
    }
}