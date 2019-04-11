<?php
/**
 * @project  cred
 * @copyright © 2019 by Chuke Hill 
 * @author thomas
 */


namespace credglv\admin\controllers;


use credglv\admin\controllers\AdminController;
use credglv\core\interfaces\AdminControllerInterface;
use credglv\models\FieldModel;

class FieldController extends AdminController implements AdminControllerInterface
{
    public function customFieldPage(){
        $data = [];
        $data['field'] = FieldModel::$fieldAttributes;
        if (isset($_POST['Field']) && !empty($_POST['Field'])) {
            $field = $_POST['Field'];
            //Validate
            if (!empty($field['label'])) {
                if (empty($field['name'])) {
                    $i = 0;
                    $name = '';
                    do {
                        $name = credglv()->helpers->general->rewriteUrl($field['label']) . ($i > 0 ? '-' . $i : '');
                        $i++;
                    } while(FieldModel::getField($name));
                    $field['name'] = $name;
                }
                if (isset($_POST['deleteField'])) {
                    FieldModel::deleteField($field['name']);
                } else {
                    //Update
                    FieldModel::addField($field['name'], $field['label'], $field['type'], trim($field['default']), isset($field['primary']) ?  true : false, isset($field['filterable']) ?  true : false);
                }
            }


        }
        $fields = FieldModel::getAllFields();
        if (isset($_GET['name'])) {
            $name = $_GET['name'];
            if (array_key_exists($name, $fields)) {
                $field = $fields[$name];
                switch ($field['type']) {
                    case 'list' :
                    case 'select' :
                    case 'radiolist' :
                    case 'checklist' :
                        $field['default'] = implode("\n", $field['default']);
                        break;
                }
                $data['field'] = $field;
            }
        }

        $data['fields'] =  $fields;

        //print_r($data);exit;
        return $this->render('index', $data);
    }

    /**
     * @return string
     */
    public function selectCustomField()
    {
        return $this->render('select', [], true);
    }

    /**
     * @return string
     */
    public function renderPopup(){
        $data = [];
        $data['fields'] = FieldModel::getAllFields();
        $data['field'] = FieldModel::$fieldAttributes;
        return $this->render('custom', $data);
    }
    public static function registerAction()
    {
        return parent::registerAction();
    }
}