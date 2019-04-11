<?php
/**
 * @project  Cred GLV Plugin
 * @copyright © 2019 by thomas
 * @author thomas
 */


namespace credglv\core;


abstract class CustomModel extends Model
{
    protected static $primaryKey              = 'id';
    /**
     * @return string
     */
    public static function getTableName() {
        return '';
    }
    /**
     * @param $id
     * @return array|null|CustomModel
     */
    public static function findOne($id)
    {
        global $wpdb;
        $tableName = self::getTableName();
        $idKey = self::$primaryKey;
        $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$tableName} WHERE {$idKey} = %d", $id));
        if (!empty($result)) {
            $result = array_shift($result);
            $className = self::className();
            $model = new $className();
            $model->isNew = false;
            $attributes = $model->getAttributes();
            foreach (array_keys($attributes) as $key)
            {
                $model->{$key} = $result->{$key};
            }
            return $model;
        }
        return null;
    }

    /**
     * Save object properties to database
     * @return boolean
     */
    public function save()
    {
        global $wpdb;
        // TODO: Implement save() method.
        try {
            $idKey = self::$primaryKey;
            $attributes = array_keys($this->getAttributes());
            $data = [];
            foreach ($attributes as $attribute)
            {
                $data[$attribute] = $this->{$attribute};
            }
            if ($this->isNew) {
                $wpdb->insert($this->getName(), $data);
            } else {
                $wpdb->update($this->getName(), $data, [
                    "{$idKey}" => $this->id
                ]);
            }
            return true;
        } catch (\Exception $e) {

        }
        return false;

    }

    /**
     * Find a list of object
     * @param mixed $conds
     * @return ModelInterface[]
     *
     * if $conds is an array those are where condition
     * if $conds is string, it's a query string
     */
    public static function find($conds, $page = 1, $limit = 25)
    {
        global $wpdb;
        $tableName = self::getTableName();
        $results = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$tableName} WHERE %s LIMIT %d OFFSET %d", $conds, $limit, ($page -1) * $limit));
        return $results;
    }
}