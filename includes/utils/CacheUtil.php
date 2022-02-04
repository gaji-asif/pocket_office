<?php

/**
 * @author cmitchell
 */
class CacheUtil extends AssureUtil {
    
    /**
     * 
     * @return string
     */
    public static function getGenericCacheKey () {
        $backtrace = debug_backtrace();
        $calledMap = MapUtil::get($backtrace, '1');
        $args = implode(':', MapUtil::get($calledMap, 'args'));
        $class = MapUtil::get($calledMap, 'class');
        $type = MapUtil::get($calledMap, 'type');
        $method = MapUtil::get($calledMap, 'function');
        $method = $class && $type ? "$class$type$method" : $methodName;
        
        return "ACCT{$_SESSION['ao_accountid']}:$method:$args";
    }
    
    /**
     * 
     * @return string
     */
    public static function getCacheKey() {
        $args = func_get_args();
        $key = array();
        $backtrace = debug_backtrace();
        $calledMap = MapUtil::get($backtrace, '1');
        $class = MapUtil::get($calledMap, 'class');
        $type = MapUtil::get($calledMap, 'type');
        $method = MapUtil::get($calledMap, 'function');
        $method = $class && $type ? "$class$type$method" : $methodName;
        
        foreach ($args as $val) {
            if (is_null($val)) break; // quit at first NULL value
            
            $key[] = $val;
        }
        
        return "$method:" . implode(':', $key);
    }
    
}