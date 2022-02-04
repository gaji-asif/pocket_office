<?php

class Announcement extends AssureClass {

    private $isRead = NULL;

    protected function construct($id, $dieIfNotFound = TRUE) {
        RequestUtil::set('ignore_cache', 1);
        $record = DBUtil::getRecord('announcements', $id);
        if (!count($record)) {
            if ($dieIfNotFound) {
                die('Announcement not found');
            } else {
                return FALSE;
            }
        }
        
        $this->build($record);
        
        return TRUE;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isRead() {
        if(!is_null($this->isRead)) { return $this->isRead; }
        
        $sql = "SELECT timestamp
                FROM read_announcements
                WHERE announcement_id = '{$this->getMyId()}'
                    AND user_id = '{$_SESSION['ao_userid']}'
                LIMIT 1";
        $this->isRead = DBUtil::hasRows(DBUtil::query($sql));
        return $this->isRead;
    }
    
    /**
     * 
     * @return Result
     */
    public function markRead() {
        if($this->isRead()) { return; }
        
        $sql = "INSERT INTO read_announcements (announcement_id, user_id, account_id, timestamp)
                VALUES ('{$this->getMyId()}', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', NOW())";
        if(DBUtil::query($sql)) {
            $this->isRead = TRUE;
        }
    }

}