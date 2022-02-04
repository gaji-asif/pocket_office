<?php

/**
 * @author cmitchell
 */
class Account extends AssureClass {
    
    protected function construct($id, $dieIfNotFound = TRUE) {
        RequestUtil::set('ignore_cache', 1);
        $record = DBUtil::getRecord('accounts', $id);
        $this->record = $record;

        if (!count($record)) {
            if($dieIfNotFound) {
                die('Office not found');
            } else {
                return FALSE;
            }
        }
        
        $this->build($record);
        
        return TRUE;
    }
    
    public function getFullAddress() {
        return "{$this->get('address')}, {$this->get('city')}, {$this->get('state')} {$this->get('zip')}";
    }
    
}