<?php
/**
 * @project  Cred GLV Plugin
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\core;


use credglv\core\interfaces\PluginInterface;
use credglv\extensions\manager\ManagerExtension;

abstract class Plugin extends BaseObject implements PluginInterface
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        add_action('admin_notices', [$this, 'checkUpdate']);
    }


    /**
     * Check plugin update
     */
    public function checkUpdate(){
        $pluginId = $this->getId();

        if (!empty($updateCheck)) {
            $result = $updateCheck;
        } else {
            /** @var ManagerExtension $extManager */
            $extManager = credglv()->pluginManager->getExtension('manager');
            $result =  $extManager->checkPluginUpdate($pluginId, $this->getVersion());//print_r($result);exit;

        }

        if ($result) {
            switch ($result->status) {
                case 'license-required' :
                    $message = isset($result->message) ? $result->message : sprintf(__('You are using a copy of %s plugin. Please enable your license to access full features of upgrade and support', 'credglv'), $pluginId);
                    $class   = 'notice notice-warning';
                    printf( '<div class="%s"><p style="font-weight: bold">%s <a href="%s" >Activate now!</a> </p></div>', esc_attr( $class ), esc_html( $message ), $result->activation_link );
                    break;
                case 'update-required' :
                    $message = isset($result->message) ? $result->message : sprintf(__('New version of %s is available.', 'credglv'), $pluginId);
                    $class   = 'notice notice-success';
                    printf( '<div class="%s"><p style="font-weight: bold">%s <a href="%s" >Update now!</a> </p></div>', esc_attr( $class ), esc_html( $message ), $result->update_link );
                    break;
            }


        }
    }
}