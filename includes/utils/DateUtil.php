<?php
/**
 * @author cmitchell
 */
class DateUtil extends AssureUtil {
    
    const DATE = 'M j, Y';
    const SHORT_DATE = 'n/j/y';
    const DATE_TIME = 'M j, Y @ g:i A';
    const TIME = 'g:i A';
    const mysqli_DATE = 'Y-m-d';
    const mysqli_TIMESTAMP = 'Y-m-d H:i:s';
    const mysqli_TIME = 'H:i:s';
    
    /**
     * 
     * @param string $timestamp
     * @return int
     */
    public static function getWeekStart($timestamp = NULL) {
        return $timestamp ? strtotime('previous monday', strtotime($timestamp)) : strtotime('previous monday');
    }
    
    /**
     * 
     * @param string $timestamp
     * @param boolean $short
     * @return type
     */
    public static function getScheduleWeekLink($timestamp, $short = FALSE) {
        $url = self::getScheduleWeekUrl($timestamp);
        $formattedDate = $short ? self::formatShortDate($timestamp) : self::formatDate($timestamp);
        if($formattedDate=='Nov 30, -0001'){
            return  'Not Set';
        }else
            return (!RequestUtil::get('csv') && !defined('CRON_REQUEST')) ? "<a href=\"$url\" title=\"View on schedule\" tooltip>$formattedDate</a>" : $formattedDate;
    }
    
    /**
     * 
     * @param string $timestamp
     * @return type
     */
    public static function getShortScheduleWeekLink($timestamp) {
        return self::getScheduleWeekLink($timestamp, TRUE);
    }
    
    /**
     * 
     * @param string $timestamp
     * @return type
     */
    public static function getScheduleWeekUrl($timestamp) {
        $weekStart = self::getWeekStart($timestamp);
        
        return ROOT_DIR."/schedule.php?view=week&ws=$weekStart";
    }
    
    /**
     * 
     * @param string $timestamp
     * @param boolean $isUnix
     * @return string, int
     */
    public static function formatDate($timestamp = NULL) {
        if(!$timestamp){
            return date(self::DATE);
        }
        
        return self::isUnixTimestamp($timestamp) ? date(self::DATE, $timestamp) : date(self::DATE, strtotime($timestamp));
    }
    
    public static function formatShortDate($timestamp = NULL) {
        if(!$timestamp){
            return date(self::TIME);
        }
        
        return self::isUnixTimestamp($timestamp) ? date(self::SHORT_DATE, $timestamp) : date(self::SHORT_DATE, strtotime($timestamp));
    }
    
    public static function formatDateTime($timestamp = NULL) {
        if(!$timestamp){
            return date(self::DATE_TIME);
        }
        
        return self::isUnixTimestamp($timestamp) ? date(self::DATE_TIME, $timestamp) : date(self::DATE_TIME, strtotime($timestamp));
    }
    
    public static function formatTime($timestamp = NULL) {
        if(!$timestamp){
            return date(self::TIME);
        }
        
        return self::isUnixTimestamp($timestamp) ? date(self::TIME, $timestamp) : date(self::TIME, strtotime($timestamp));
    }
    
    public static function formatMySQLDate($timestamp = NULL) {
        if(!$timestamp){
            return date(self::mysqli_DATE);
        }
        
        return self::isUnixTimestamp($timestamp) ? date(self::mysqli_DATE, $timestamp) : date(self::mysqli_DATE, strtotime($timestamp));
    }
    
    public static function formatMySQLTimestamp($timestamp = NULL) {
        if(!$timestamp){
            return date(self::mysqli_TIMESTAMP);
        }
        
        return self::isUnixTimestamp($timestamp) ? date(self::mysqli_TIMESTAMP, $timestamp) : date(self::mysqli_TIMESTAMP, strtotime($timestamp));
    }
    
    public static function formatMySQLTime($timestamp = NULL) {
        if(!$timestamp){
            return date(self::mysqli_TIME);
        }
        
        return self::isUnixTimestamp($timestamp) ? date(self::mysqli_TIME, $timestamp) : date(self::mysqli_TIME, strtotime($timestamp));
    }
    
    /**
     * 
     * @param mixed $timestamp
     * @return boolean
     */
    public static function isUnixTimestamp($timestamp) {
        return (is_numeric($timestamp) && (int)$timestamp == $timestamp);
    }
    
    /**
     * 
     * @return string
     */
    public static function getOffset() {
        $now = new DateTime();
        $mins = $now->getOffset() / 60;
        $sgn = ($mins < 0 ? -1 : 1);
        $mins = abs($mins);
        $hrs = floor($mins / 60);
        $mins -= $hrs * 60;
        return sprintf('%+d:%02d', $hrs * $sgn, $mins);
    }
}