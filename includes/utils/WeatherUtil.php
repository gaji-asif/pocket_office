<?php

/**
 * @author cmitchell
 */
class WeatherUtil extends AssureUtil {
    
    private static $apiUri = 'http://api.openweathermap.org/data/2.5/forecast/daily';
    
    /**
     * 
     * @param string $city
     * @param string $state
     * @param int $days
     * @return object
     */
    public static function fetchForcast($city, $state, $days = 1) {
        $url = self::$apiUri . "?q=$city,$state&units=imperial&cnt=$days&APPID=5c74418a34fc96b28a2b89b8bebfb473";
        $weathearData = json_decode(file_get_contents($url));
        
        print_r($weathearData);
        return $weathearData;
    }
    
}