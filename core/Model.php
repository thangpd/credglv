<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 * @since 1.0
 *
 *
 * Base Model class
 */

namespace credglv\core;


use credglv\core\interfaces\ModelInterface;
use credglv\core\interfaces\ValidatorInterface;

abstract class Model extends BaseObject
{
    /**
     * @var \WP_Post
     */
    public $post;
    /**
     * Object data
     * Used for magics function
     * @var array
     */
    public $__data = [];
    /**
     * Object attribute
     * @var array
     */
    public $attributes = [];


    /**
     * @return mixed
     * example :
     * return [
     *    'name' => [
     *        'label' => 'Name',
     *        'validate' => ['text', ['length' => 200, 'required' => true, 'message' => 'Please enter a valid name']]
     *    ],
     *    'age' => [
     *        'label' => 'Age',
     *        'validate' => ['number', ['max' => 100, 'min' => 0, 'message' => 'Please enter a valid age']]
     *    ]
     * ]
     */
    public abstract function getAttributes();

    /**
     * Abstract function get name of table/model
     * @return mixed
     */
    public abstract function getName();

    /**
     * Mark this model is a new record.
     * That mean db need to insert instead update
     * @var bool
     */
    public $isNew   = true;


    /**
     * Init object
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
        if (empty($this->getName())) {
            throw new RuntimeException(__('No config name for class : ' . self::className(), 'credglv'));
        }
    }

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $this->attributes = $this->getAttributes();
        $this->attributes = apply_filters(CREDGLV_NAMESPACE . '_' . $this->getName() . '_attributes', $this->attributes);
        foreach ($this->attributes as $key => $params) {
            $this->__data[$key] = null;
        }

        do_action('credglv_model_created', $this);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        if (!is_array($this->__data)) {
            $this->__data = [];
        }
        if (array_key_exists($name, $this->__data)) {
            return $this->__data[$name];
        } else {
            if (!empty($this->post) && property_exists($this->post, $name)) {
                return $this->post->$name;
            }
        }

        return null; // TODO: Change the autogenerated stub
    }

    /**
     * Magic function
     * Set meta attribute if it exists
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->__data)) {
            $this->__data[$name] = $value;
        } else {
            parent::__set($name, $value); // TODO: Change the autogenerated stub
        }
    }

    /**
     * Find a post by ID
     * Retrieve data in post table and also get meta data for other attributes
     * @param $post
     * @return bool|Model
     * @throws RuntimeException
     */
    public  static function  findOne($post)
    {
        if (!is_object($post)) {
            $post = credglv()->wp->get_post($post);
        }

        if (!empty($post)) {

            $modelClass = self::className();
            /** @var Model $model */
            $model = new $modelClass();
            if ($post->post_type != $model->getName()) {
                throw new RuntimeException(__("Can not cast post type from {$post->post_type} to {$model->getName()}", 'credglv'));
            }
            $model->post = $post;
            $model->isNew = false;
            $model->getData();
            return $model;
        }
        return false;
    }

    /**
     * Load wordpress post that belong to this model
     * @param $post
     * @return $this|bool
     * @throws RuntimeException
     */
    public function loadPost($post) {
        if (!is_object($post)) {
            $post = credglv()->wp->get_post($post);
        }

        if (!empty($post)) {
            if ($post->post_type != $this->getName()) {
                throw new RuntimeException(__("Can not cast post type from {$post->post_type} to {$this->getName()}", 'credglv'));
            }
            $this->post = $post;
            $this->isNew = false;
            $this->getData();
            return $this;
        }
        return false;
    }
    /**
     * Validate model attribute if validate configured by getAttribute function
     * @see getAttributes()
     * @param $name
     * @param $value
     * @param $validateParams
     * @return boolean
     */
    public function validateAttribute($name, $value, $validateParams) {
        if (!empty($validateParams)) {
            $validator = credglv()->helpers->validator->getValidator($validateParams[0], $validateParams[1]);
            if (!empty($validator)) {
                /** @var ValidatorInterface $validator */
                $validator->setTarget($name, $value);
                return $validator->validate();
            }
        }
        return true;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        if (property_exists($this, $name)) {
            return true;
        }
        if (array_key_exists($name, $this->attributes)) {
            return true;
        }
        return parent::__isset($name);
    }


    /**
     * After save post, update post's meta data
     * @param $postId
     * @return  boolean
     */
    public function afterSave($postId, $post = null, $update = false) {
        $attrs = $this->attributes;
        $postType = credglv()->helpers->general->camelClassName($this->getName());
        foreach ($attrs as $name => $attr) {
            if (isset($_POST[$postType][$name])) {
                $attrValue = $_POST[$postType][$name];
            } else {
                continue;
            }
            if (!empty($attr) && isset($attr['validate'])) {
                if ($this->validateAttribute($name, $attrValue, $attr['validate'])) {
                    //Validate success. Save meta post
                    if (isset($attr['single']) && $attr['single'] == false && is_array($attrValue)) {
                        foreach ($attrValue as $itemValue) {
                            credglv()->wp->update_post_meta($postId, $name, $itemValue);
                        }
                    } else {
                        credglv()->wp->update_post_meta($postId, $name, $attrValue);
                    }

                } else {
                    //Trigger validation error
                }
            } else {
                if (isset($attr['single']) && $attr['single'] == false && is_array($attrValue)) {
                    foreach ($attrValue as $itemValue) {
                        credglv()->wp->update_post_meta($postId, $name, $itemValue);
                    }
                } else {
                    credglv()->wp->update_post_meta($postId, $name, $attrValue);
                }
            }
        }
        $this->isNew = false;
        return true;
    }

    /**
     * @param $attr
     * @return string
     */
    public function getAttributeLabel($attr)
    {
        if (isset($this->attributes[$attr])) {
            return $this->attributes[$attr]['label'];
        }
        return '';
    }

    /**
     * Return model data
     * Model data is attribute data received from post meta table
     * @return object
     */
    public function getData($attrs  = [])
    {
        if (!empty($this->post)) {
            if (empty($attrs)) {
                $attrs = $this->attributes;
            }
            foreach ($attrs as $attr => $params) {
                $this->__data[$attr] = get_post_meta($this->post->ID, $attr, isset($params['single']) ? $params['single'] : true);
            }
        }
        return (object) $this->__data;
    }
    /**
     * Find a list of object
     * @param mixed $conds
     * @param string $tableName
     * @return ModelInterface[]
     *
     * if $conds is an array those are where condition
     * if $conds is string, it's a query string
     */
    public static function find($conds , $tableName)
    {
        global $wpdb;
        if (empty($tableName)) {
            $tableName = $wpdb->prefix . 'posts';
        }
        $where = "SELECT * FROM {$tableName} WHERE ";
        $whereQuery = [];
        if (is_array($conds)) {
            $_conds = [];
            foreach ($conds as $key => $value) {
                $_conds[] = (is_numeric($value) ?  $value : "'{$value}'");
                $whereQuery[] = ("{$key} = " . (is_numeric($value) ? "%d " : "%s " ));
            }
        }
        $where .= implode(' AND ', $whereQuery);
        $results = $wpdb->get_results($wpdb->prepare($where, $conds));
        $models = [];
        foreach ($results as &$result) {
            $class = get_called_class();
            $model = new $class;
            /** @var ModelInterface $model */
            $model->isNew = false;
            $attributes = $model->getAttributes();
            foreach (array_keys($attributes) as $key)
            {
                $model->{$key} = $result->{$key};
            }
            $models[] = $model;
        }
        return $models;
    }
    /**
     * @param $attr
     * @return mixed|null
     */
    public function getAttribtueValue($attr)
    {   
        $list_attr_define = ['post_title','post_content','post_parent','post_ID','post_type'];
        
        if ( in_array($attr, $list_attr_define) && !empty($this->post) ){
            return $this->post->$attr;
        }
        if (isset($this->__data[$attr])) {
            return $this->__data[$attr];
        }
        return  null;
    }

    /**
     * Get model ID based post's ID
     * @return int
     */
    public function getId()
    {
        if (!empty($this->post)) {
            return $this->post->ID;
        }
        return 0;
    }
}