<?php
/**
 * @author cmitchell
 */
class SettingsUtil extends AssureUtil {
    
    /**
     * 
     * @param string $key
     * @return mixed
     */
    public static function get($key = NULL) {
        $me = UserModel::getMe();
        
        return $key ? MetaUtil::get($me->getMeta(), $key) : $me->getMeta();
    }
    
    /**
     * 
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
    public static function set($key, $value) {
        $me = UserModel::getMe();
        
        return $me->setMeta($key, $value);
    }
    
}