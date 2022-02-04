<?php

class Document extends AssureClass {
    
    protected function construct($id, $dieIfNotFound = TRUE) {
        RequestUtil::set('ignore_cache', 1);
        $record = DBUtil::getRecord('documents', $id);
        if (!count($record)) {
            if ($dieIfNotFound) {
                die('Announcement not found');
            } else {
                return FALSE;
            }
        }
        
        foreach($record as $key => $value) {
            $this->$key = $value;
        }
        $this->build($record);
        $this->user = UserModel::getDataForNotification($this->user_id);
        
        return TRUE;
    }

    public function getTooltip() {
        $info = array(
            'Title' => $this->document,
            'File Name' => $this->filename,
            'File Type' => $this->filetype,
            'Description' => UIUtil::cleanOutput($this->get('description'), FALSE),
            'Creator' => "{$this->user['lname']}, {$this->user['fname']}"
        );
            
        echo ViewUtil::loadView('tooltip', array('info' => $info));
    }
    
}