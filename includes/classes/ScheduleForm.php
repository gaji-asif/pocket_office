<?php

/**
 * @author cmitchell
 */
class ScheduleForm {

    private $metaData;
    
    public $exists = FALSE;
    
    public function __construct($id = NULL) {
        if(!$id) { return; }
        
        $results = $this->fetch($id);
        
        if(!count($results)) {
            throw new Exception("ScheduleForm with ID '$id' not found");
        }
        
        $this->setValues($results);
    }
    
    public function initByUploadId($uploadId) {
        RequestUtil::set('ignore_cache', 1);
        $results = $this->fetch($uploadId, 'upload_id');
        
        if(!count($results)) {
            throw new Exception("ScheduleForm with Upload Id '$uploadId' not found");
        }
        
        $this->setValues($results);
    }
    
    /**
     * 
     * @return array
     */
    public function getMetaData($key = NULL, $default = NULL) {
        if(!$this->metaData) {
            $this->metaData = JobModel::getAllMeta($this->getMyMetaId());
        }
        
        if($key) {
            $array = MapUtil::get($this->metaData, $key);
            return is_array($array) ? MapUtil::get($array, 'meta_value') : $default;
        }
        
        return $this->metaData;
    }
    
    /**
     * 
     * @return string
     */
    public function getMyMetaId() {
        return $this->getType() . "-job-{$this->getJobId()}-{$this->getId()}";
    }
    
    /**
     * 
     * @return int
     */
    public function getJobId() {
        return $this->job_id;
    }
    
    /**
     * 
     * @return int
     */
    public function getId() {
        return $this->schedule_form_id;
    }
    
    /**
     * 
     * @return string
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * 
     * @return int
     */
    public function getUploadId() {
        return $this->upload_id;
    }
    
    /**
     * 
     * @return int
     */
    public function getUploadTitle() {
        if(!$this->upload_id) {
            return 'Schedule ' . ucfirst($this->getType()) . ' Job';
        }
        
        if(!$this->upload) {
            $this->upload = JobUtil::getUploadById($this->upload_id);
        }
        return MapUtil::get($this->upload, 'title');
    }
    
    /**
     * 
     * @return int
     */
    public function getAccountId() {
        return $this->account_id;
    }
    
    /**
     * 
     * @return int
     */
    public function getUserId() {
        return $this->user_id;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isActive() {
        return $this->active ? TRUE : FALSE;
    }
    
    /**
     * 
     * @param array $results
     */
    private function setValues($results) {
        $this->exists = TRUE;
        foreach($results as $key => $value) {
            $this->$key = $value;
        }
    }
    
    /**
     * 
     * @param int $jobId
     */
    public function setJobId($jobId) {
        $this->job_id = $jobId;
        
        return $this;
    }
    
    /**
     * 
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
        
        return $this;
    }
    
    /**
     * 
     * @param int $accountId
     */
    public function setAccountId($accountId) {
        $this->account_id = $accountId;
        
        return $this;
    }
    
    /**
     * 
     * @param int $userId
     */
    public function setUserId($userId) {
        $this->user_id = $userId;
        
        return $this;
    }
    
    /**
     * 
     * @param int $uploadId
     */
    public function setUploadId($uploadId) {
        $this->upload_id = $uploadId;
        
        return $this;
    }
    
    /**
     * 
     */
    public function deactivate() {
        $this->active = 0;
        
        return $this;
    }
    
    /**
     * 
     */
    public function activate() {
        $this->active = 1;
        
        return $this;
    }
    
    /**
     * 
     */
    public function store() {
        if(empty($this->account_id)) {
            $this->setAccountId($_SESSION['ao_accountid']);
        }
        if(empty($this->user_id)) {
            $this->setUserId($_SESSION['ao_userid']);
        }
        
        $this->validate();
        
        if($this->exists) {
            $this->update();
        } else {
            $this->insert();
        }
    }
    
    /**
     * 
     * @throws Exception
     */
    private function validate() {
        if(empty($this->type)) {
            throw new Exception("ScheduleForm must have a type");
        }
        
        if(empty($this->job_id)) {
            throw new Exception("ScheduleForm must have a Job ID");
        }
    }
    
    /**
     * 
     */
    private function insert() {
        $sql = "INSERT INTO schedule_forms (account_id, user_id, job_id, type, upload_id)
                VALUES ('{$this->getAccountId()}', '{$this->getUserId()}', '{$this->getJobId()}', '{$this->getType()}', '{$this->getUploadId()}')";
        DBUtil::query($sql);
        $this->setValues($this->fetch(DBUtil::getInsertId()));
    }
    
    /**
     * 
     */
    private function update() {
        $sql = "UPDATE schedule_forms
                SET job_id = '{$this->getJobId()}',
                    type = '{$this->getType()}',
                    active = '{$this->active}',
                    upload_id = '{$this->getUploadId()}',
                    account_id = '{$this->getAccountId()}',
                    user_id = '{$this->getUserId()}'
                WHERE schedule_form_id = '{$this->getId()}'
                LIMIT 1";
        DBUtil::query($sql);
    }
    
    /**
     * 
     * @param int $id
     * @return array
     */
    private function fetch($id = NULL, $column = 'schedule_form_id') {
        $id = $id ?: $this->getId();
                
        $sql = "SELECT *
                FROM schedule_forms
                WHERE $column = '$id'
                    AND account_id = '{$_SESSION['ao_accountid']}'
                LIMIT 1";
        return DBUtil::fetchAssociativeArray(DBUtil::query($sql));
    }
}