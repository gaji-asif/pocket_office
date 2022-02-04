<?php

/**
 * @author cmitchell
 */
class ContactHeaderModel extends AssureModel {
    
    /**
     * 
     * @return array
     */
    public static function getAllContactHeaders() {
        return DBUtil::getAll('contacts', 'contact_name','ASC');
    }
    
    /**
     * 
     * @param int $providerId
     * @return array
     */
    public static function getContactHeadersById($id) {
        return DBUtil::getRecord('contacts', $id);
    }
    
}