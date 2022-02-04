<?php

class Email extends AssureClass {

    public $id;
    public $message_id;
    public $thread_id;
    public $label_name;
    public $from_name;
    public $to_mail;
    public $subject;
    public $snippet;
    public $mail_date;
    public $job_id;
    
    public $job_added_date;
    public $deleted_by;
    public $deleted_at;
    public $is_read;
    public $is_shared;
    
    public $delete_status;
    public $create_date;
    public $job_number;
	public $meta_data = array();
    private $account_id;
    
    protected function construct($id, $dieIfNotFound = TRUE) {
       
        RequestUtil::set('ignore_cache', 1);                
        $record = DBUtil::getRecord('gmail_import', $id);        
        
        if (!$record) {
            if($dieIfNotFound) {
                die('Job not found');
            } else {
                return FALSE;
            }
        }
        $this->record = $record;
        //echo "<pre>";print_r($this->record);die;
        //assign data
        list($this->id, $this->message_id, $this->thread_id, $this->label_name, $this->from_name, $this->to_mail, $this->subject, $this->snippet, $this->mail_date, $this->user_id, $this->job_id, $this->$job_added_date,$this->delete_status,$this->deleted_by,$this->deleted_at,$this->is_read,$this->is_shared,$this->create_date) = array_values($record);
        $this->build($record);

        //date of birth
        $this->dob = DateUtil::formatDate($this->timestamp);
        
        //get a whole bunch of related data
        $this->setJobNumber();
        
        return TRUE;
    }



    public function setJobNumber() {
        $sql = "select job_number from jobs where job_id='" . $this->id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->job_number) = mysqli_fetch_row($res);
    }
    

    
}

?>
