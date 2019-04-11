<?php
/**
 * @project  glv
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\helpers;


use credglv\core\BaseObject;
use credglv\core\interfaces\HelperInterface;
use credglv\core\interfaces\ValidatorInterface;

class ValidatorHelper extends BaseObject implements HelperInterface
{
    /**
     * @param $type
     * @return ValidatorInterface
     */
    public function getValidator($type, $params = []) {
        $className = "credglv\\helpers\\validators\\" . ucfirst($type) . 'Validator';
        if (class_exists($className)) {
            return new $className($params);
        }
    }
}