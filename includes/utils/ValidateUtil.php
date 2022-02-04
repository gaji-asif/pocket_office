<?php

/**
 * @author cmitchell
 */
class ValidateUtil extends AssureUtil {
    
    public static function validateEmail($email) {
        return preg_match('/([\w\-]+\@[\w\-]+\.[\w\-]+)/', $email);
    }
    
    public static function validateUSPhone($phone) {
        $phone = StrUtil::formatPhoneToSave($phone);
        return strlen($phone);
    }
    
    public static function validateStateAbbreviation($state) {
        return strlen($state) == 2;
    }
    
    public static function validateUSZipCode($zipCode) {
        return preg_match('/^([0-9]{5})(-[0-9]{4})?$/i', $zipCode);
    }
    
    public static function validateUsername($username) {
        return ctype_alnum($username) && strlen($username) > 5;
    }
}