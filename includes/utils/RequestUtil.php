<?php
/**
 * @author cmitchell
 */
class RequestUtil extends AssureUtil {
    
    public static function get($key, $defaultValue = NULL) {
        
        if($_REQUEST && isset($_REQUEST[$key]) && (is_array($_REQUEST[$key]) || trim($_REQUEST[$key]) != '')) {
            return $_REQUEST[$key];
        }
        if($_POST && isset($_POST[$key]) && (is_array($_POST[$key]) || trim($_POST[$key]) != '')) {
            return $_POST[$key];
        }
        if($_GET && isset($_GET[$key]) && (is_array($_GET[$key]) || trim($_GET[$key]) != '')) {
            return $_GET[$key];
        }
        return $defaultValue;
    }
    
    /**
     * 
     */
    public static function cleanseRequest() {
        self::makeFilesSafe();
        self::makeGetSafe();
        self::makePostSafe();
        self::makeRequestSafe();
    }
    
    /**
     * 
     */
    public static function makeGetSafe() {
        $_GET = self::escapeArrayVals($_GET);
    }
    
    /**
     * 
     */
    public static function makePostSafe() {
        $_POST = self::escapeArrayVals($_POST);
    }
    
    /**
     * 
     */
    public static function makeRequestSafe() {
        $_REQUEST = self::escapeArrayVals($_REQUEST);
    }
    
    /**
     * 
     */
    public static function makeFilesSafe() {
        $_FILES = self::escapeArrayVals($_FILES);
    }
    
    /**
     * 
     * @param mixed $array
     * @return mixed
     */
    public static function escapeArrayVals($array) {
        if(!is_array($array)) {
            return self::escapeString($array);
        }
        
        foreach($array as $key => $value) {
            $array[$key] = self::escapeArrayVals($value);
        }
        
        return $array;
    }
    
    /**
     * 
     * @param string $string
     * @return string
     */
    public static function escapeString($string) {
        $string = stripslashes($string);
        
        //supress in case connection hasn't be made
        $escapedString = @mysqli_real_escape_string(DBUtil::Dbcont(),$string);
        
        if($escapedString === FALSE) {
//            LogUtil::getInstance()->logNotice('Cannot escape request var - DB connection not established');
            $escapedString = $string;
        }
        
        return $escapedString;
    }
    
    /**
     * 
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value) {
        self::delete($key);
        $_POST[$key] = $value;
    }
    
    /**
     * 
     * @param array $values
     */
    public static function add($values) {
        $_POST = array_merge($_POST, is_array($values) ? $values : array());
    }
    
    /**
     * 
     * @param string $key
     */
    public static function delete($key) {
        if($_REQUEST) {
            unset($_REQUEST[$key]);
        }
        if($_POST) {
            unset($_POST[$key]);
        }
        if($_GET) {
            unset($_GET[$key]);
        }
    }
    
    /**
     * 
     * @return array
     */
    public static function getAll() {
        return array_merge($_GET, $_POST, $_REQUEST, $_FILES);
    }
}
