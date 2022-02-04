<?php

class Repair extends AssureClass {

    public $repair_id;
    public $job_id;
    public $contractor_fname;
    public $contractor_lname;
    public $contractor_dba;
    public $user_fname;
    public $user_lname;
    public $priority_id;
    public $priority;
    public $fail_type_id;
    public $fail_type;
    public $notes;
    public $timestamp;
    public $account_id;
    public $dob;
    public $completed;
    public $total_length;
    public $contractor_id;
    public $start_date;
    public $user_id;

    protected function construct($id, $dieIfNotFound = TRUE) {
        RequestUtil::set('ignore_cache', 1);
        $record = DBUtil::getRecord('repairs', $id);
        
        if (!count($record)) {
            if ($dieIfNotFound) {
                die('Repair not found');
            } else {
                return FALSE;
            }
        }
        
        list($this->repair_id, $this->job_id, $this->account_id, $this->user_id, $this->contractor_id, $this->priority_id, $this->fail_type_id, $this->notes, $this->start_date, $this->timestamp, $this->completed) = array_values($record);
        $this->build($record);
        
        $this->dob = DateUtil::formatDate($this->timestamp);
        if ($this->completed != '') {
            if ($this->start_date != '')
                $diff = abs(strtotime($this->completed) - strtotime($this->start_date));
            else
                $diff = abs(strtotime($this->completed) - strtotime($this->timestamp));
            $this->total_length = ceil($diff / (60 * 60 * 24));

            $this->completed = DateUtil::formatDate($this->completed);
        }
        $this->setContractorNames();
        $this->setUserNames();
        $this->setPriority();
        $this->setFailType();
        
        return TRUE;
    }

    function setContractorNames() {
        $sql = "select fname, lname, dba from users where user_id='" . $this->contractor_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->contractor_fname, $this->contractor_lname, $this->contractor_dba) = mysqli_fetch_row($res);
    }

    function setUserNames() {
        $sql = "select fname, lname from users where user_id='" . $this->user_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->user_fname, $this->user_lname) = mysqli_fetch_row($res);
    }

    function setPriority() {
        $sql = "select priority.priority from priority where priority.priority_id='" . $this->priority_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->priority) = mysqli_fetch_row($res);
    }

    function setFailType() {
        $sql = "select fail_type from fail_types where fail_type_id='" . $this->fail_type_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->fail_type) = mysqli_fetch_row($res);
    }

}

?>