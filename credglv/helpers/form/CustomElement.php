<?php
/**
 * @project  edu
 * @copyright © 2017 by ivoglent
 * @author ivoglent
 * @time  7/28/17.
 */


namespace credglv\helpers\form;


use AdamWathan\Form\Elements\FormControl;

use credglv\core\RuntimeException;

class CustomElement extends FormElement
{
    /**
     * @var callable|null
     */
    protected $renderer = null;

    /**
     * @var array
     */
    public $params = [];
    /**
     * CustomElement constructor.
     * @param FormControl $control
     * @param mixed $callable
     * @param array $configs
     */
    public function __construct(FormControl $control, $callable , $configs = [])
    {
        parent::__construct($control, []);
        if ((is_string($callable) && function_exists($callable)) || is_callable($callable)) {
            $this->renderer = $callable;
        } else {
            throw new RuntimeException(__('Invalid render callback for form custom field' , 'credglv'));
        }
        $this->params = $configs;
    }

    public function __get($name)
    {
        // TODO: Implement __get() method.
        if (array_key_exists($name, $this->params)) {
            return $this->params[$name];
        }
        return null; // TODO: Change the autogenerated stub
    }

    public function __isset($name)
    {
        if (property_exists($this, $name)) {
            return true;
        }
        if (array_key_exists($name, $this->params)) {
            return true;
        }
        return parent::__isset($name);
    }

    /**
     * Custom render a field
     * @return  string
     */
    public function render()
    {
        $html = '';
        $renderer = $this->renderer;

        if (is_string($renderer)) {
            if (function_exists($renderer)) {
                $html =$renderer($this);
            } else {
                $html = credglv()->hook->registerFilter($renderer, $this, $html);
            }
        } else  {
            $html = call_user_func($renderer, $this);
        }
        if (is_null($html)) {
            $html = '';
        }
        return $html;
    }
}