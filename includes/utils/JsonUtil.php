<?php

/**
 * @author cmitchell
 */
class JsonUtil extends AssureUtil {
    
    public static function out($array) {
        if(!headers_sent()) { 
            header('Content-Type: application/json');
        }
        echo json_encode($array);
    }

    public static function error($message) {
        self::out(array(
            'error' => $message
        ));
    }

    public static function success($message) {
        self::out(array(
            'success' => $message
        ));
    }
    
}