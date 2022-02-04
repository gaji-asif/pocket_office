<?php

/**
 * @author cmitchell
 */
class InsuranceModel extends AssureModel {
    
    /**
     * 
     * @return array
     */
    public static function getAllProviders() {
        return DBUtil::getAll('insurance', 'insurance');
    }
    
    /**
     * 
     * @param int $providerId
     * @return array
     */
    public static function getProviderById($providerId) {
        return DBUtil::getRecord('insurance', $providerId);
    }
    
}