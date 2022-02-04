<?php

/**
 * @author cmitchell
 */
class CronUtil {    
    
    /**
     * 
     * @param array $cronRecord
     * @return boolean
     */
    public static function shouldSend($cronRecord, $time = NULL) {
        $time = $time ?: time();
        $daysToday = unixtojd($time);
        $weekDayToday = date('N', $time); //ISO-8601
        $dayLastSent = MapUtil::get($cronRecord, 'last_sent');
        $dayLastSent = $dayLastSent ? unixtojd(strtotime($dayLastSent)) : NULL;
        $daysSinceLastSent = $dayLastSent ? ($daysToday - $dayLastSent) : 0;
        $weekDay = MapUtil::get($cronRecord, 'week_day');
        $weekFreqency = MapUtil::get($cronRecord, 'week_frequency');
        $dayFreqency = MapUtil::get($cronRecord, 'day_frequency', ($weekFreqency * 7));
        
        return (!$dayLastSent && ($weekDayToday == $weekDay))
                || ($daysSinceLastSent && ($daysSinceLastSent % $dayFreqency === 0));
    }
    
    /**
     * 
     * @param string $table
     * @param array $cronRecord
     * @return Resource
     */
    public static function updateLastSent($table, $cronRecord) {
        $arrayKeys = array_keys($cronRecord);
        $cronRecordId = reset($cronRecord);
        $cronRecordIdKey = reset($arrayKeys);
        
        $sql = "UPDATE $table
                SET last_sent = NOW()
                WHERE $cronRecordIdKey = '$cronRecordId'
                LIMIT 1";
        return DBUtil::query($sql);
    }
    
}