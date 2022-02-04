<?php
/**
 * @author cmitchell
 */
class LogUtil extends AssureUtil {
    
    private static $logger = NULL;
    
    private function __construct() {}
    private function __clone() {}
    
    public static function getInstance() {
        if(!self::$logger) {
            self::init();
        }
        
        return self::$logger;
    }
    
    private static function init() {
        self::$logger = KLogger::instance(LOG_PATH);
    }
}