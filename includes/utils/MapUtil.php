<?php
/**
 * @author cmitchell
 */
class MapUtil extends AssureUtil {
    
    /**
     * 
     * @param array $array
     * @param mixed $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public static function get($array, $key, $defaultValue = NULL) {
        if(!is_array($array)) {
            return $array;
        }
        
        return isset($array[$key]) ? $array[$key] : $defaultValue;
    }
    
    /**
     * 
     * @param array $array
     * @param mixed $key
     * @return mixed
     */
    public static function pluck($array, $key) {
        $array = is_array($array) ? $array : array();
        reset($array);
        $pluckedValues = array_map(function($value) use ($key) {
            return MapUtil::get($value, $key);
        }, $array);
        
        return array_values($pluckedValues);
    }
    
    /**
     * 
     * @param array $array
     * @param string $key
     * @param string $delimeter
     * @return types
     */
    public static function join($array, $key, $delimeter = ', ') {
        return implode($delimeter, MapUtil::pluck($array, $key));
    }
    
    /**
     * 
     * @param array $array
     * @param string $key
     * @return array
     */
    public static function mapTo($array, $key) {
        if(!is_array($array) || !count($array)) {
            return $array;
        }
        
        //make sure key exists
        $firstRow = reset($array);
        if(!isset($firstRow[$key])) {
            return $array;
        }
        
        $mappedArray = array();
        foreach ($array as $row){
            $mappedArray[$row[$key]] = $row;
        }
        
        return $mappedArray;
    }
    
    /**
     * 
     * @param array $point1
     * @param array $point2
     * @param string $uom
     * @return float
     */
    public static function calculateDistanceFromLatLong($point1, $point2, $uom = 'miles')  {
        switch (strtolower($uom)) {
            case 'km'	:
                $earthMeanRadius = 6371.009; // km
                break;
            case 'm'	:
                $earthMeanRadius = 6371.009 * 1000; // km
                break;
            case 'miles'	:
                $earthMeanRadius = 3958.761; // miles
                break;
            case 'yards'	:
            case 'yds'	:
                $earthMeanRadius = 3958.761 * 1760; // miles
                break;
            case 'feet'	:
            case 'ft'	:
                $earthMeanRadius = 3958.761 * 1760 * 3; // miles
                break;
            case 'nm'	:
                $earthMeanRadius = 3440.069; // miles
                break;
        }
        $deltaLatitude = deg2rad($point2['latitude'] - $point1['latitude']);
        $deltaLongitude = deg2rad($point2['longitude'] - $point1['longitude']);
        $a = sin($deltaLatitude / 2) * sin($deltaLatitude / 2) +
             cos(deg2rad($point1['latitude'])) * cos(deg2rad($point2['latitude'])) *
             sin($deltaLongitude / 2) * sin($deltaLongitude / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthMeanRadius * $c;
        
        return $distance;
    }
    
    /**
     * 
     * @param string $address
     * @return string
     */
    public static function getLinkToExternalMap ($address) {
        return "<a href=\"https://www.google.com/maps/?q=$address\" target=\"_blank\" title=\"View in Google Maps\" tooltip>$address</a>";
    }
    
    /**
     * 
     * @param string $address
     * @return string
     */
    public static function getUrlToInternalMap($address) {
        return "/maps.php?radius=5&address=$address";
    }
    
    /**
     * 
     * @param string $address
     * @param string $text
     * @return string
     */
    public static function getLinkToInternalMap($address, $text = NULL) {
        $text = $text ?: $address;
        $url = self::getUrlToInternalMap($address);
        return (RequestUtil::get('csv') || defined('CRON_REQUEST')) ? $text : "<a href=\"$url\" title=\"View proximity map\" tooltip>$text</a>";
    }
    
}