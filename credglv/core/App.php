<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */



namespace credglv\core;


use credglv\core\components\ControllerManager;
use credglv\core\components\Cookie;
use credglv\core\components\Hook;
use credglv\core\components\ModelManager;
use credglv\core\components\Page;
use credglv\core\components\ResourceManager;
use credglv\core\components\PluginManager;
use credglv\core\components\RoleManager;
use credglv\core\components\Session;
use credglv\core\components\ShortcodeManager;
use credglv\core\components\WP;
use credglv\helpers\FileHelper;
use credglv\helpers\GeneralHelper;
use credglv\helpers\Helper;
use credglv\helpers\WordpressHelper;


/**
 * @package credglv\core
 * @project  Cred GLV
 *
 *
 * @property Session $session
 * Session manager
 *
 * @property Cookie $cookie
 * Cookie manager
 *
 *
 * @property PluginManager $pluginManager
 * App owner plugin manager
 * Cred GLV may have many children plugins
 * This component can add/remove, enable/disable a plugin
 *
 * @property ShortcodeManager $shortcodeManager
 * Shortcode manager which managed all shortcodes from plugin and themes
 *
 * @property ResourceManager $resourceManager
 * Static resource management like styles and javascripts
 *
 * @property WP $wp
 * Magic class contains magic function __call
 * which used to call wordpress global function
 * don't worry about the message error "Call undefined function ..."
 * all called function will return false if not exists without trigger any errors
 *
 * @property Hook $hook
 * Hook manager
 * Register default hooks and allow other component to register or listen new hook
 *
 * @property Helper $helpers
 * The app supper man
 * It contains all you need to keep you don't waste your time
 * Helper contains :
 * - File helper @see FileHelper which contains all useful functions related to file system
 * - Wordpress helper @see WordpressHelper which contains unseful functions related to wordpress
 * - General helper @see GeneralHelper which contains...any things
 *
 * @property Page $page
 * Manage credglv single page
 *
 *
 * @property ControllerManager $controller
 * Collect and register hook action for all controller classes
 * Each class need to be implemented \lama\core\interfaces\ControllerInterface
 *
 * @property ModelManager $model
 * Like controller manager
 * but model need to implements \credglv\core\interfaces\ModelInterface
 *
 * @property RoleManager $role
 * Role management system
 * Simple role manager which manage who can access to a feature
 *
 *
 * @property Mailer $mailer
 * Cred GLV mailer component
 *
 *
 * @property Logger $logger
 * Cred logger based on Monolog
 *
 *
 *
 *
 *
 *
 */


class App extends BaseObject
{
    /**
     * Singleton
     * Instance of application
     * @var App
     */
    private static $instance = null;

    /** @var  Client */
    public $request;

    /**
     * Current app configs
     * It was passed from config.php in Cred GLV plugin directory
     * @var  Config
     */
    public $config;

    private $ready = false;

    /**
     * App components
     * @var array
     */
    private $_components = [
        'session'           => '\credglv\core\components\Session',
        'cookie'            => '\credglv\core\components\Cookie',
        'wp'                => '\credglv\core\components\WP',
        'helpers'           => '\credglv\helpers\Helper',
        'hook'              => '\credglv\core\components\Hook',
        'page'              => '\credglv\core\components\Page',
        'role'              => '\credglv\core\components\RoleManager',
        'migration'         => '\credglv\core\components\Migration',
        'model'             => '\credglv\core\components\ModelManager',
        'pluginManager'     => '\credglv\core\components\PluginManager',
        'shortcodeManager'  => '\credglv\core\components\ShortcodeManager',
        'resourceManager'   => '\credglv\core\components\ResourceManager',
        'controller'        => '\credglv\core\components\ControllerManager',
    ];



    /**
     * App constructor.
     * @param array $values
     */
    public function __construct(array $values = array())
    {
        /**
         * Register exception handler
         */
        set_exception_handler([$this, 'onException']);
        set_error_handler([$this, 'onError'], E_ERROR | E_CORE_ERROR | E_CORE_WARNING);
        $this->config = new Config($values);
        parent::__construct();
    }

    /**
     * Setup credglv app components
     */
    public function __init()
    {
        do_action('credglv_before_init');
        $this->setupComponents();
        $this->ready = true;
        do_action('credglv_after_init');
        //$this->run();
    }

    /**
     * Setup app components
     *
     * @return void
     */
    protected function setupComponents()
    {
        foreach ($this->_components as $name => $handlerClass) {
            if (empty($this->$name)) {
                $this->$name =$handlerClass::getInstance([
                    'config' => $this->config
                ]);
            }
        }
    }

    /**
     * Start to run this app (plugin)
     */
    public function run()
    {

        credglv()->hook->registerHook(Hook::CREDGLV_RUN, $this);

        //Set .htaccess for credglv (cache, log) folder
        $protectedDirs = ['caches', 'logs'];
        foreach ($protectedDirs as $dir) {
            $dirPath = CREDGLV_WR_DIR . DIRECTORY_SEPARATOR . $dir;
            if (is_dir($dirPath) && !file_exists($dirPath . DIRECTORY_SEPARATOR . '.htaccess')) {
                file_put_contents($dirPath . DIRECTORY_SEPARATOR . '.htaccess', 'deny from all');
            }
        }
        $this->checkVersion();

        /**
         * Unregister exception handler
         */
        restore_error_handler();

        define('CREDGLV_LOADED', true);
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        if (empty($this->logger)) {
            $this->logger = new Logger();
        }
        return $this->logger;
    }
    /**
     * @return Helper
     */
    public function getHelper()
    {
        if (empty($this->helpers)) {
            $this->helpers = new Helper();
        }
        return $this->helpers;
    }

    /**
     * @param $e
     * @throws \Exception
     */
    public function onException($e)
    {
        if ($e instanceof RuntimeException && CREDGLV_DEBUG == false) {
            try {
                $this->getLogger()->error($e->getMessage(),(array) $e->getTrace());
	            $errorViewPath = apply_filters( 'credglv_error_view_path', CREDGLV_PATH . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'error.php' );
	            echo credglv()->getHelper()->getHelper('general')->renderPhpFile( $errorViewPath, array( 'exception' => $e, 'admin' => is_admin() ) );
            } catch (\Exception $e) {
                //throw new \Exception($e);
                die($e->getMessage());
            }
        } else {
           /* if (WP_DEBUG) {
                var_dump($e);
            }
            echo '<div id="message" class="error"><p>' . $e->getMessage() . '</p></div>';*/
           throw new \Exception($e);
        }
    }

    /**
     * @param $no
     * @param $str
     * @param $file
     * @param $line
     * @throws RuntimeException
     */
    public function onError($no, $str, $file, $line)
    {
        throw new RuntimeException("PHP Error : {$str} on file : {$file} at line : {$line}");
    }

    /**
     * This method only run when plugin was activated
     */
    public function activate()
    {
        $this->setupComponents();
        do_action('credglv_active');
	    do_action('credglv_run', $this);
	    $this->migration->up();

    }

    /**
     * Check current version of credglv
     * if new version installed, run migration upgrade
     */
    private function checkVersion()
    {
        if (!defined('DOING_AJAX') && is_admin()) {
            $version = credglv()->config->credglv_version;
            if (empty($version)) {
                credglv()->config->credglv_version = CREDGLV_VERSION;
            } else {
                if (version_compare($version, CREDGLV_VERSION, '<')) {
                    credglv()->migration->upgrade();
                }
            }
        }
    }
    /**
     * This method only run when plugin was deactivated
     */
    public function deactivate()
    {
        $this->migration->down();
    }

    /**
     * Uninstall all components resources
     * Run when Cred GLV was uninstall
     */
    public function uninstall()
    {
        $this->setupComponents();
        $this->migration->uninstall();

    }

    /**
     * Get instance of App (reference)
     *
     * @return App
     */
    public static function getInstance($config = [])
    {
        if (empty(self::$instance)) {
            self::$instance = new App($config);
        }
        $app =  &self::$instance;
        return $app;
    }


    /**
     * @return bool
     */
    public function isReady()
    {
        return $this->ready;
    }


}

