<?php

class Task extends AssureClass {

    public $task_id;
    public $task_type_id;
    public $task_type;
    public $task_color;
    public $job_id;
    public $stage_name;
    public $user_fname;
    public $user_lname;
    public $contractor_id;
    public $contractor_fname;
    public $contractor_lname;
    public $contractor_dba;
    public $start_date;
    public $duration;
    public $notes;
    public $timestamp;
    public $stage_id;
    public $dob;
    public $completed;
    public $account_id;
    public $total_length;
    public $stage_num;
    public $paid;
    public $user_id;	
   public $start_time;
    public $end_date;
    public $end_time;

	

    protected function construct($id, $dieIfNotFound = TRUE) {

	     RequestUtil::set('ignore_cache', 1);
        $record = DBUtil::getRecord('tasks', $id);
         
        if (!count($record)) {
            if ($dieIfNotFound) {
                die('Task not found');
            } else {
                return FALSE;
            }
        }
	 
      list($this->task_id, $this->task_type_id, $this->job_id, $this->stage_id, $this->user_id, $this->account_id, $this->contractor_id, $this->start_date, $this->notes, $this->duration, $this->completed, $this->paid, $this->timestamp, $this->start_time, $this->end_date, $this->end_time) = array_values($record);
	  //   list($this->task_id, $this->task_type_id, $this->job_id, $this->stage_id, $this->user_id, $this->account_id, $this->contractor_id, $this->start_date, $this->notes, $this->duration, $this->completed, $this->paid, $this->timestamp) = array_values($record);
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
        if ($this->paid != '') {
            $this->paid = DateUtil::formatDate($this->paid);
        }
        $this->setContractorNames();
        $this->setUserNames();
        $this->setTaskType();
        $this->setStageDetails();
        //$this->notes = prepareText($this->notes);
        
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

    function setTaskType() {
        $sql = "select task, color from task_type where task_type_id='" . $this->task_type_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->task_type, $this->task_color) = mysqli_fetch_row($res);
    }

    function setStageDetails() {
        $sql = "select stage, stage_num from stages where stage_id='" . $this->stage_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->stage_name, $this->stage_num) = mysqli_fetch_row($res);
    }

    public function getTooltip() {
        $contractor = !empty($this->contractor_lname) ? ("{$this->contractor_lname}, {$this->contractor_fname}" . (!empty($this->contractor_dba) ? " ({$this->contractor_dba})" : '')) : '';
        $time='';
        $time_arr=explode(':',$this->start_time);
        if($this->start_time!='00:00:00')
        {
            if($time_arr[0]>11)
            {            
                $tm=$time_arr[0]-12;    
                if($time_arr[0]!='00')                
                    $time=ltrim($tm.':'.$time_arr[1].'pm','0');   
                else
                    $time=$tm.':'.$time_arr[1].'pm';             
            }
            else
            {
                if($time_arr[0]!='00')
                    $time=ltrim($time_arr[0].':'.$time_arr[1].'am','0');
                else
                    $time=$tm.':'.$time_arr[1].'pm';
            }
        }

        $info = array(
            'Task Type' => $this->task_type,
            'Creator' => "{$this->user_lname}, {$this->user_fname}",
            'Completed' => !empty($this->paid) ? DateUtil::formatDate($this->paid) : '',
            'DOB' => DateUtil::formatDate($this->timestamp),
            'Associated Stage' => $this->stage_name,
            'Contractor' => $contractor,
            'Allotted Duration' => "{$this->duration} day(s)",
            'Start Date' => !empty($this->start_date) ? DateUtil::formatDate($this->start_date) : '',
            'Start Time' => !empty($time) ? $time : ''           
        );
            
        echo ViewUtil::loadView('tooltip', array('info' => $info));
    }

}