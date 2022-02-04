<?php
/**
 * @author cmitchell
 */
class StrUtil extends AssureUtil {
    
    /**
     * 
     * @param string $haystack
     * @param string $needle
     * @return boolean
     */
    public static function startsWith($haystack, $needle) {
        return $needle === '' || strpos($haystack, $needle) === 0;
    }
    
    /**
     * 
     * @param string $string
     * @return string
     */
    public static function humanizeCamelCase($string) {
        return ltrim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $string));
    }
    
    /**
     * 
     * @param array $arr
     * @return array
     */
    public static function remoteAtsFromArray($arr) {
        return array_map(function($val) {
            return ltrim($val, '@');
        }, $arr);
    }
    
    /**
     * 
     * @param string $string
     * @return string
     */
    public static function convertMentionsToLinks($string) {
        $mentions = UserModel::getMentions($string);
        foreach($mentions as $username => $mention) {
            $userLink = UserUtil::getUsernameLink($mention);
            $string = str_replace("@$username", $userLink, $string);
        }
        
        return $string;
    }
    
    /**
     * 
     * @param string $phone
     * @return string
     */
    public static function formatPhoneToSave($phone) {
        return preg_replace('/[^0-9]/', '', $phone);
    }
    
    /**
     * 
     * @param string $string
     * @return string
     */
    public static function makeStringMachineSafe($string) {
        return strtolower(preg_replace("/[^a-zA-Z]+/", "_", $string));
    }
}