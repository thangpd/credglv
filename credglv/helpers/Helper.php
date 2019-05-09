<?php
/**
 * @project  edu
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\helpers;


use credglv\core\BaseObject;
use credglv\core\interfaces\ComponentInterface;
use credglv\models\BundleModel;
use credglv\models\OrderItemModel;
use credglv\models\OrderModel;
use credglv\models\CourseModel;
use credglv\models\Student;

class Helper extends BaseObject implements ComponentInterface {
	/**
	 * @var GeneralHelper
	 */
	public $general;
	/**
	 * @var FileHelper
	 */
	public $file;

	/**
	 * @var WordpressHelper
	 */
	public $wp;

	/**
	 * @var ValidatorHelper
	 */
	public $validator;

	/** @var  FormHelper */
	public $form;

	/** @var SecurityHelper */
	public $security;

	public function __construct( array $config = [] ) {
		parent::__construct( $config );
		$this->general   = new GeneralHelper();
		$this->file      = new FileHelper();
		$this->wp        = new WordpressHelper();
		$this->validator = new ValidatorHelper();
		$this->form      = new FormHelper();

	}

	/**
	 * @param $_name
	 *
	 * @return mixed
	 */
	public function getHelper( $_name ) {
		$name = ucfirst( $_name ) . 'Helper';
		if ( empty( $this->$_name ) ) {
			$this->$_name = new $name();
		}

		return $this->$_name;
	}


	/**
	 * Inserts any number of scalars or arrays at the point
	 * in the haystack immediately after the search key ($needle) was found,
	 * or at the end if the needle is not found or not supplied.
	 * Modifies $haystack in place.
	 *
	 * @param array &$haystack the associative array to search. This will be modified by the function
	 * @param string $needle the key to search for
	 * @param mixed $stuff one or more arrays or scalars to be inserted into $haystack
	 *
	 * @return int the index at which $needle was found
	 */
	public function arrayInsertAfter( &$haystack, $needle = '', $stuff ) {
		if ( ! is_array( $haystack ) ) {
			return $haystack;
		}

		$new_array = array();
		for ( $i = 2; $i < func_num_args(); ++ $i ) {
			$arg = func_get_arg( $i );
			if ( is_array( $arg ) ) {
				$new_array = array_merge( $new_array, $arg );
			} else {
				$new_array[] = $arg;
			}
		}

		$i = 0;
		foreach ( $haystack as $key => $value ) {
			++ $i;
			if ( $key == $needle ) {
				break;
			}
		}

		$haystack = array_merge( array_slice( $haystack, 0, $i, true ), $new_array, array_slice( $haystack, $i, null, true ) );

		return $i;
	}


}