<?php

/**
 * @author cmitchell
 */
class OriginModel extends AssureModel {
    
    /**
     * 
     * @param int $originId
     * @return array
     */
    public static function getById($originId) {
        return DBUtil::getRecord('origins', $originId);
    }
    
}