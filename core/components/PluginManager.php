<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */



namespace credglv\core\components;



use credglv\core\BaseObject;
use credglv\core\interfaces\ComponentInterface;
use credglv\core\interfaces\ExtensionInterface;
use credglv\core\interfaces\PluginInterface;


class PluginManager extends BaseObject implements ComponentInterface
{
    /**
     * List of built-in extensions
     * @var array
     */
    private $requiredExts = [
            'manager' => 'Extension manager',
            'localization' => 'Localization data',
            'support' => 'Support',
    ];

    /**
     * @var ExtensionInterface
     */
    private $activatedExtensions = [];

    public function __construct($config = [])
    {
        parent::__construct($config);
       /* if (credglv()->wp->is_admin()) {
            credglv()->hook->listenHook('admin_init', [$this, 'checkRequiredExtensions']);
        } else {

        }*/
        $this->checkRequiredExtensions();
    }

    /**
     * @param ExtensionInterface $extension
     * @return mixed|string
     */
    public function getUrl($extension) {
        $url = CREDGLV_PATH_PLUGIN . '/extensions/' . $extension->getId();
        $url = credglv()->hook->registerFilter('credglv_extension_url_' . $extension->getId(), $url);
        return $url;
    }

    /**
     * Check and active required extension
     */
    public function checkRequiredExtensions()
    {
        $this->requiredExts = credglv()->hook->registerFilter('credglv_builtin_exts', $this->requiredExts);
        foreach ($this->requiredExts as $ext => $label) {
           $this->activateExtension($ext);
        }
    }

    /**
     * Activate an extension
     * @param mixed $ext extension object or extension' name
     * @return bool|mixed
     */
    public function activateExtension($ext)
    {
        /** @var ExtensionInterface $extension */
        $extension = null;
        if (is_object($ext) && $ext instanceof ExtensionInterface) {
            $extension = $ext;
        } else {
            try {
                $extName = "\\credglv\\extensions\\$ext\\" . credglv()->helpers->general->camelClassName($ext) . 'Extension';
                if (class_exists($extName)) {
                    $extension = new $extName();
                }
            } catch (\Exception $e) {
                //Error -> logs
            }
        }
        if (!empty($extension) && $extension instanceof ExtensionInterface) {
            $this->activatedExtensions[] = $extension;
            return $extension->run();
        }
        return false;
    }

    /**
     * @param $id
     * @return bool|ExtensionInterface
     */
    public function getExtension($id) {
        foreach ($this->activatedExtensions as $extension) {
            /** @var ExtensionInterface $extension */
            if ($extension->getId() == $id) {
                return $extension;
            }
        }
        return  false;
    }
}
