<?php
/**
 * @copyright © 2019 by GLV 
 * @project Cred GLV Plugin
 * @since 1.0
 *
 *
 * Base object which contains some useful method
 *
 *
 */



namespace credglv\core;

use credglv\core\components\Hook;
use credglv\core\interfaces\ComponentInterface;

defined('CREDGLV_PATH') or die('Object not allowed access directly');

abstract class BaseObject extends \stdClass
{
    private static $instances;

    protected $components = [];
    /**
     * Returns the fully qualified name of this class.
     * @return string the fully qualified name of this class.
     */
    public static function className()
    {
        return get_called_class();
    }

    /**
     * Constructor.
     * The default implementation does two things:
     *
     * - Initializes the object with the given configuration `$config`.
     * - Call [[init()]].
     *
     * If this method is overridden in a child class, it is recommended that
     *
     * - the last parameter of the constructor is a configuration array, like `$config` here.
     * - call the parent implementation at the end of the constructor.
     *
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct($config = [])
    {
        if (!empty($config) && is_array($config)) {
            foreach ($config as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
        }
        if (defined('CREDGLV_LOADED')) {
            $this->init();
        } else {
            add_action(Hook::CREDGLV_RUN, [$this, 'init']);
        }
        //$this->init();
        self::$instances[self::className()] = &$this;

    }

    /**
     * Get instance of app
     * @return App
     */
    public function getApp(){
        if (empty( $this->app )) {
            $this->app = App::getInstance();
        }
        return $this->app;
    }

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {

    }

    /**
     * Returns the value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$value = $object->property;`.
     * @param string $name the property name
     * @return mixed the property value
     * @throws RuntimeException if the property is not defined
     * @throws RuntimeException if the property is write-only
     * @see __set()
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->components)) {
            return $this->components[$name];
        }
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . $name)) {
            throw new RuntimeException('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new RuntimeException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * Sets value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$object->property = $value;`.
     * @param string $name the property name or the event name
     * @param mixed $value the property value
     * @throws RuntimeException if the property is not defined
     * @throws RuntimeException if the property is read-only
     * @see __get()
     */
    public function __set($name, $value)
    {
        if ($value instanceof ComponentInterface) {
            $this->components[$name] = $value;
        } else {
            $setter = 'set' . $name;
            if (method_exists($this, $setter)) {
                $this->$setter($value);
            } elseif (method_exists($this, 'get' . $name)) {
                throw new RuntimeException('Setting read-only property: ' . get_class($this) . '::' . $name);
            } else {
                throw new RuntimeException('Setting unknown property: ' . get_class($this) . '::' . $name);
            }
        }

    }

    /**
     * Checks if a property is set, i.e. defined and not null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `isset($object->property)`.
     *
     * Note that if the property is not defined, false will be returned.
     * @param string $name the property name or the event name
     * @return bool whether the named property is set (not null).
     * @see http://php.net/manual/en/function.isset.php
     */
    public function __isset($name)
    {
        if (array_key_exists($name, $this->components)) {
            return is_object($this->components[$name]);
        }
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } else {
            return false;
        }
    }

    /**
     * Sets an object property to null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `unset($object->property)`.
     *
     * Note that if the property is not defined, this method will do nothing.
     * If the property is read-only, it will throw an exception.
     * @param string $name the property name
     * @throws RuntimeException if the property is read only.
     * @see http://php.net/manual/en/function.unset.php
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new RuntimeException('Unsetting read-only property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * Calls the named method which is not a class method.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when an unknown method is being invoked.
     * @param string $name the method name
     * @param array $params method parameters
     * @throws RuntimeException when calling unknown method
     * @return mixed the method return value
     */
    public function __call($name, $params)
    {
        throw new RuntimeException('Calling unknown method: ' . get_class($this) . "::$name()");
    }

    /**
     * Returns a value indicating whether a property is defined.
     * A property is defined if:
     *
     * - the class has a getter or setter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param string $name the property name
     * @param bool $checkVars whether to treat member variables as properties
     * @return bool whether the property is defined
     * @see canGetProperty()
     * @see canSetProperty()
     */
    public function hasProperty($name, $checkVars = true)
    {
        return $this->canGetProperty($name, $checkVars) || $this->canSetProperty($name, false);
    }

    /**
     * Returns a value indicating whether a property can be read.
     * A property is readable if:
     *
     * - the class has a getter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param string $name the property name
     * @param bool $checkVars whether to treat member variables as properties
     * @return bool whether the property can be read
     * @see canSetProperty()
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return method_exists($this, 'get' . $name) || $checkVars && property_exists($this, $name);
    }

    /**
     * Returns a value indicating whether a property can be set.
     * A property is writable if:
     *
     * - the class has a setter method associated with the specified name
     *   (in this case, property name is case-insensitive);
     * - the class has a member variable with the specified name (when `$checkVars` is true);
     *
     * @param string $name the property name
     * @param bool $checkVars whether to treat member variables as properties
     * @return bool whether the property can be written
     * @see canGetProperty()
     */
    public function canSetProperty($name, $checkVars = true)
    {
        return method_exists($this, 'set' . $name) || $checkVars && property_exists($this, $name);
    }

    /**
     * Returns a value indicating whether a method is defined.
     *
     * The default implementation is a call to php function `method_exists()`.
     * You may override this method when you implemented the php magic method `__call()`.
     * @param string $name the method name
     * @return bool whether the method is defined
     */
    public function hasMethod($name)
    {
        return method_exists($this, $name);
    }


    /**
     * Singleton
     * Keep class instance in memory
     * @param array $config
     * @return BaseObject
     */
    public static function getInstance($config = [])
    {
        $class = self::className();
        if (empty(self::$instances[$class])) {
            self::$instances[$class] = new $class($config);
        }
        $app =  self::$instances[$class];
        return $app;
    }

    /**
     * Magic for static call
     * @param $name
     * @param $args
     * @return mixed
     */
    public static function __callStatic($name, $args = [])
    {
        if (preg_match('/^__(.*?)$/', $name)) {
            $name = str_replace('__', '', $name);
        }
        $className = self::className();
       /* if (method_exists($className, $name)) {
            return self::$name($args);
        }*/
        $class = new \ReflectionClass($className);
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($method->name == $name) {
                $obj = self::getInstance();
                return $obj->$name($args);
            }
        }
        return false;
    }

    /**
     * Get cache key supported multisite
     * @param string $key
     * @return string
     */
    public static function cacheKey($key) {
        if (is_multisite()) {
            $siteId = get_site()->id;
            $key = "site{$siteId}_{$key}";
        }
        return $key;
    }

}

