<?php

/**
 * @author cmitchell
 */
class ScheduleModel extends AssureModel {
    
    public static function setEventMetaValue($metaId, $key, $value) {
        $sql = "INSERT INTO event_meta (event_id, meta_name, meta_value)
                VALUES('$metaId', '$key', '$value')";
        return DBUtil::query($sql);
    }
    
    public static function getEventMetaValue($metaId, $key) {
        $sql = "SELECT meta_value
                FROM event_meta
                WHERE meta_id = '$metaId'
                    AND meta_name = '$key'
                LIMIT 1";
        return DBUtil::queryToScalar($sql);
    }
    
    public static function getEventUserGroups($eventId) {
        $sql = "SELECT em.meta_value as usergroup_id, ug.label
                FROM event_meta em,
                    usergroups ug
                WHERE em.event_id = '$eventId'
                    AND em.meta_name = 'group'
                    AND em.meta_value = ug.usergroup_id
                LIMIT 1";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getEventDataForNotification($eventId) {

        $sql = "SELECT title, date, end_date, text, all_day
                FROM events
                WHERE event_id = '$eventId'
                LIMIT 1";
        $results = DBUtil::queryToMap($sql);

        //add extras
        $results['date'] = DateUtil::formatDate($results['date']);

        if(isset($results['end_date']))
         $results['end_date'] = DateUtil::formatDate($results['end_date']);

        $results['time'] = DateUtil::formatTime($results['date']);
        if (isset($results['all_day']) && $results['all_day'] == '1') {
            $results['time'] = 'All Day';
        }
        unset($results['all_day']);

        return $results;
    }
    
}