<?php

/**
 * @author cmitchell
 */
class SavedReport extends AssureClass {
    
    protected function construct($id, $dieIfNotFound = TRUE) {
        RequestUtil::set('ignore_cache', 1);
        $record = DBUtil::getRecord('saved_reports', $id);
        if (!count($record)) {
            if ($dieIfNotFound) {
                die('Saved report not found');
            } else {
                return FALSE;
            }
        }
        
        $this->build($record);
        $this->set('decodedQuery', json_decode($this->get('query'), TRUE));
        
        return TRUE;
    }
    
    /**
     * 
     * @return string
     */
    public function getDisplayName() {
        $decodedQuery = $this->get('decodedQuery');
        $table = ucfirst(MapUtil::get($decodedQuery, 'table'));
        
        return "$table - {$this->get('name')}";
    }
    
}