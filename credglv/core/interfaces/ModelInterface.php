<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 * @since 1.0
 */


namespace credglv\core\interfaces;


interface ModelInterface
{
    /**
     * Get object/table name
     * @return string
     */
    public function getName();
    /**
     * Save object properties to database
     * @return boolean
     */
    public function save();


    /**
     * @param $postId
     * @param null $post
     * @param bool $update
     * @return mixed
     */
    public function afterSave($postId,$post = null, $update = false);

    /**
     * Find and object in database by id (by primary key)
     * @param $id
     * @return ModelInterface
     */
    public static function findOne($id);

    /**
     * Find a list of object
     * @param mixed $conds
     * @return ModelInterface[]
     *
     * if $conds is an array those are where condition
     * if $conds is string, it's a query string
     */
    public static function find($conds, $tableName);

    /**
     * Delete a object by primary key
     * @param $id
     * @return boolean
     */
    public function delete();

    /**
     * Get object attributes
     * @return array
     */
    public function getAttributes();

    /**
     * @return mixed
     */
    public static function getPosttypeConfig();

}