<?php
/**
 * @project  cred
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\core;


use credglv\core\interfaces\ValidatorInterface;

abstract class Validator extends BaseObject implements ValidatorInterface
{
    /**
     * @var bool
     */
    public  $required = false;
    /**
     * @var string
     */
    protected $regex = '/^(.*?)$/';

    /**
     * @var mixed
     */
    protected $content = null;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    public $message = 'Invalid property content';

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * Get regex string of validator
     * @return string
     */
    public function getValidatorRegex()
    {
        return $this->regex;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        // TODO: Implement getSuccessMessage() method.
        return $this->message;
    }



    /**
     * Do validate action
     * @return boolean
     */
    public function validate()
    {
        return preg_match($this->regex, $this->content);
    }

    /**
     * @return bool
     */
    public function requireValidate()
    {
        if ($this->required && empty($this->content)) {
            return false;
        }
        return true;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setTarget($name, $value)
    {
        $this->name = $name;
        $this->content = $value;
        return $this;
    }
}