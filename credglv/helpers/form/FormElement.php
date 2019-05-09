<?php
/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 */



namespace credglv\helpers\form;


use AdamWathan\Form\Elements\FormControl;
use credglv\core\BaseObject;

class FormElement extends BaseObject
{


    /** @var  FormControl */
    public $control;
    /**
     * @var string
     */
    protected $template = '<div class="la-form-group"><label>{label}</label>{input}{description}</div>';

    /**
     * @var string
     */
    protected $label = '';

	/**
	 * @var string
	 */
    protected $description = '';

    /**
     * FormElement constructor.
     * @param FormControl $control
     */
    public function __construct(FormControl $control, $configs = [])
    {
        parent::__construct($configs);
        $this->control = $control;
    }

    /**
     * @return  string
     */
    public function render()
    {
        $input = $this->control->__toString();
        $html = str_replace('{label}', $this->label, $this->template);
	    $html = str_replace('{input}', $input, $html);
	    $html = str_replace('{description}', !empty( $this->description ) ? '<p>'. $this->description .'</p>' : '' , $html);
        return $html;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}