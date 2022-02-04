<?php

/**
 * @author cmitchell
 */
class JurisdictionModel extends AssureModel {
    
    /**
     * 
     * @param int $jurisdictionId
     * @return array
     */
    public static function getById($jurisdictionId) {
        return DBUtil::getRecord('jurisdiction', $jurisdictionId);
    }
    
}