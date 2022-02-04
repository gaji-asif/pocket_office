<?php

/**
 * @author cmitchell
 */
class MetaUtil extends AssureUtil {
    
    /**
     * 
     * @param array $metaData
     * @param string $key
     * @return mixed
     */
    public static function get($metaData, $key, $defaultValue = NULL) {
        return MapUtil::get(MapUtil::get($metaData, $key, array()), 'meta_value', $defaultValue);
    }
    
    /**
     * 
     * @param string $class
     * @param int $id
     * @param string $key
     * @return array
     */
    public static function fetch($class, $id, $key) {
        $sql = "SELECT *
                FROM meta
                WHERE class = '$class'
                    AND class_id = '$id'
                    AND key = '$name'";
        return DBUtil::queryToArray($sql);
    }
    
    public static function save($class, $id, $key, $value) {
        
    }
    
    public static function delete($class, $id, $key) {
        
    }
    
}