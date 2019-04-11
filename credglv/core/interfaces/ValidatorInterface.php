<?php
/**
 * @project  cred
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\core\interfaces;


interface ValidatorInterface
{
    /**
     * Do validate action
     * @return boolean
     */
    public function validate();

    /**
     * Get regex string of validator
     * @return string
     */
    public function getValidatorRegex();

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setTarget($name, $value);
}