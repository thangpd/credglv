<?php
/**
 * @project  edu
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\front\controllers;


use credglv\core\Controller;
use credglv\core\interfaces\ControllerInterface;

class FrontController extends Controller
{

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub

    }

    public function getSupportPages()
    {
        return [];
    }

    public function render($view = '', $data = [], $return = true)
    {
        return parent::render($view, $data, $return); // TODO: Change the autogenerated stub
    }
}