<?php
/**
 * @project  eduall
 * @copyright © 2017 by Chuke Hill Co.,LTD
 * @author ivoglent
 * @time  11/14/17.
 */


namespace credglv\helpers\form;


class RadioListElement extends FormElement
{
    public $options = [];
    public $value = null;
    public function render()
    {
        $name = $this->control->getAttribute('name');
        $input = '';
        foreach ($this->options as $option) {
            $checked = '';
            if ($option == trim($this->value)) {
                $checked = ' checked ';
            }
            $input .= ("<label><input type='radio' {$checked} name='{$name}' value='{$option}' />{$option}</label>");
        }
        $html = str_replace('{label}', $this->label, $this->template);
        $html = str_replace('{input}', $input, $html);
        $html = str_replace('{description}', !empty( $this->description ) ? '<p>'. $this->description .'</p>' : '' , $html);
        return $html;
    }
}