<?php
/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 */


namespace credglv\core\components;


use AdamWathan\Form\Elements\Date;
use AdamWathan\Form\Elements\FormControl;
use Faker\Provider\DateTime;
use credglv\helpers\form\CheckListElement;
use credglv\helpers\form\CustomElement;
use credglv\helpers\form\EditorElement;
use credglv\helpers\form\FormElement;
use credglv\helpers\form\RadioListElement;

class Form extends \AdamWathan\Form\FormBuilder {
	/**
	 * @param string $type
	 *
	 * @return $this
	 */
	public function begin( $type = 'POST' ) {
		if ( $type == 'POST' ) {
			$this->open();
		} else {
			$this->open()->get();
		}

		return $this;
	}

	/**
	 * Close form tag
	 */
	public function end() {
		$this->close();
	}

	/**
	 * @param $name
	 * @param array $options
	 *
	 * @return FormElement
	 */
	public function field( $name, $options = [] ) {
		/** @var FormControl $field */
		$field = null;
		switch ( $options['type'] ) {
			case 'text' :
				$field = $this->text( isset( $options['name'] ) ? $options['name'] : $name );
				break;
			case 'textarea' :
				$field = $this->textarea( isset( $options['name'] ) ? $options['name'] : $name )->cols( isset( $options['cols'] ) ? $options['cols'] : 5 )->rows( isset( $options['rows'] ) ? $options['rows'] : 5 );
				break;
			case 'select' :
				$field = $this->select( isset( $options['name'] ) ? $options['name'] : $name, $options['options'] );
				break;
			case 'checkbox' :
				$field = $this->checkbox( isset( $options['name'] ) ? $options['name'] : $name, $options['value'] );
				if ( isset( $options['checked'] ) && $options['checked'] ) {
					$field->check();
				}
				break;
			case 'radio' :
				$field = $this->radio( isset( $options['name'] ) ? $options['name'] : $name, $options['value'] );
				if ( isset( $options['checked'] ) && $options['checked'] ) {
					$field->check();
				}
				break;
			case 'checklist' :
				$field = $this->text( isset( $options['name'] ) ? $options['name'] : $name );

				return new CheckListElement( $field, $options );
				break;
			case 'radiolist' :
				$field = $this->text( isset( $options['name'] ) ? $options['name'] : $name );
				return new RadioListElement( $field, $options );
				break;
			case 'button' :
				$field = $this->button( isset( $options['label'] ) ? $options['label'] : '' );
				break;
			case 'hidden' :
				$field = $this->hidden( isset( $options['name'] ) ? $options['name'] : '' );
				break;
			case 'list' :
				$field = $this->text( isset( $options['name'] ) ? $options['name'] : '' )->attribute( 'class', 'textlist' );
				break;
			case 'editor' :
				$field = $this->textarea( isset( $options['name'] ) ? $options['name'] : $name );
				break;
			case 'date' :
				$field            = $this->date( isset( $options['name'] ) ? $options['name'] : $name );
				if(!empty($options['value'])){
				$options['value'] = new \DateTime($options['value']);
				}
				break;
			case 'custom' :
				$field = $this->text( isset( $options['name'] ) ? $options['name'] : $name );

				return new CustomElement( $field, isset( $options['renderer'] ) ? $options['renderer'] : null, $options );
				break;

		}
		//value
		if ( ! empty( $field ) && isset( $options['value'] ) ) {
			$field->value( $options['value'] );
		}
		//placeholder
		if ( ! empty( $field ) && isset( $options['placeholder'] ) ) {
			$field->placeholder( $options['placeholder'] );
		}
		//class
		if ( ! empty( $field ) && isset( $options['class'] ) ) {
			$field->addClass( $options['class'] );
		}
		//selected
		if ( ! empty( $field ) && isset( $options['selected'] ) ) {
			$field->select( $options['selected'] );
		}
		//id
		if ( ! empty( $field ) && isset( $options['id'] ) ) {
			$field->id( $options['id'] );
		}
		//required
		if ( ! empty( $field ) && isset( $options['required'] ) ) {
			$field->required( $options['required'] );
		}
		//data
		if ( ! empty( $field ) && isset( $options['data'] ) ) {
			$field->data( $options['data'] );
		}
		//attributes
		if ( ! empty( $field ) && isset( $options['attributes'] ) ) {
			foreach ( $options['attributes'] as $key => $value ) {
				$field->attribute( $key, $value );
			}
		}
		if ( $options['type'] == 'editor' ) {
			return new EditorElement( $field, $options );
		}

		return new FormElement( $field, $options );
	}
}