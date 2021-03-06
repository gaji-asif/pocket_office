<?php

class User extends AssureClass {

    public $username;
    public $fname;
    public $lname;
    public $dba;
    public $email;
    public $phone;
    public $sms_carrier;
    public $sms_name;
    public $reg_date;
    public $account_name;
    public $level_title;
    public $dob;
    public $notes;
    public $office_id;
    public $office;
    private $user_id;
    private $password;
    private $level_id;
    private $is_active;
    private $is_deleted;
    private $account_id;
    private $founder;
	private $journal;
    private $generalinsbox;
    private $generalins;
    private $workerinsbox;
    private $workerins;
    private $meta;

    protected function construct($id, $dieIfNotFound = TRUE) {
        RequestUtil::set('ignore_cache', 1);
        $record = DBUtil::getRecord('users', $id);
        if (!count($record)) {
            if ($dieIfNotFound) {
                die('User not found');
            } else {
                return FALSE;
            }
        }
        
        list($this->user_id, $this->username, $this->fname, $this->lname, $this->password, $this->dba, $this->email, $this->phone, $this->sms_carrier, $this->level_id, $this->is_active, $this->is_deleted, $this->reg_date, $this->account_id, $this->founder, $this->notes, $this->office_id, $this->journal, $this->generalinsbox, $this->generalins, $this->workerinsbox, $this->workerins) = array_values($record);
        $this->build($record);
        
        $this->dob = DateUtil::formatDate($this->dob);
        $this->notes = stripslashes($this->notes);
        $this->setAcountName();
        $this->setLevelTitle();
        $this->setSMSName();
        if ($this->office_id != '') {
            $this->setOffice();
        }
        else {
            $this->office = 'Default';
        }
        
        return TRUE;
    }

    public function setOffice() {
        $sql = "select title from offices where office_id='" . $this->office_id . "'";
        $res = DBUtil::query($sql);
        list($this->office) = mysqli_fetch_row($res);
    }

    public function setAcountName() {
        $sql = "select account_name from accounts where account_id='" . $this->account_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->account_name) = mysqli_fetch_row($res);
    }

    public function setLevelTitle() {
        $sql = "SELECT level
                FROM levels
                WHERE level_id = '{$this->level_id}'
                LIMIT 1";
        $res = DBUtil::query($sql);
        list($this->level_title) = mysqli_fetch_row($res);
    }

    public function setSMSName() {
        $sql = "select carrier from sms where sms_id='" . $this->sms_carrier . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->sms_name) = mysqli_fetch_row($res);
    }

    public function updateBasicInfo($dba, $email, $phone, $sms) {
        $sql = "update users set dba='" . $dba . "', email='" . $email . "', phone='" . $phone . "', sms_carrier='" . $sms . "' where user_id='" . $this->user_id . "' limit 1";
        DBUtil::query($sql);

        $this->username = $username;
        $this->dba = $dba;
        $this->email = $email;
        $this->phone = $phone;
        $this->sms_carrier = $sms;

        $_SESSION['ao_dba'] = $dba;
    }

    public function updatePassword($new_password) {
        $sql = "update users set password='" . $new_password . "' where user_id='" . $this->user_id . "' limit 1";
        DBUtil::query($sql);
    }

    public function getUserID() {
        return $this->user_id;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getLevel() {
        return $this->level_id;
    }

    public function getIsActive() {
        return $this->is_active;
    }

    public function isDeleted() {
        if ($this->is_deleted == 1)
            return true;
        return false;
    }

    public function getAccountID() {
        return $this->account_id;
    }

    public function getFounder() {
        return $this->founder;
    }
	
	public function getJournal() {
        return $this->journal;
    }

    public function getGeneralinsbox() {
        return $this->generalinsbox;
    }

    public function getGeneralins() {
        return $this->generalins;
    }

    public function getWorkerinsbox() {
        return $this->workerinsbox;
    }

    public function getWorkerins() {
        return $this->workerins;
    }
    
    public function fetchMeta() {
        $this->meta = MapUtil::mapTo(DBUtil::getRecords('user_meta', $this->user_id, 'user_id'), 'meta_key');
    }
    
    public function getMeta($key = NULL) {
        if(!$this->meta) {
            $this->fetchMeta();
        }
        
        if(!$key) {
            return $this->meta;
        }
        
        return MetaUtil::get($this->meta, $key);
    }
    
    public function setMeta($key, $value) {
        $this->deleteMeta($key);
        
        $sql = "INSERT INTO user_meta (user_id, meta_key, meta_value)
                VALUES('{$this->user_id}', '$key', '$value')";
        $results = DBUtil::query($sql);
        
        if($results) {
            $this->fetchMeta();
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function deleteMeta($key) {
        $sql = "DELETE FROM user_meta
                WHERE user_id = '{$this->user_id}'
                    AND meta_key = '$key'";
        DBUtil::query($sql);
    }

    public function getTooltip() {
        $info = array(
            'Name' => "{$this->lname}, $this->fname",
            'DBA' => $this->dba,
            'Email' => $this->email,
            'Phone' => UIUtil::formatPhone($this->phone),
            'Access Level' => $this->level_title
        );
            
        echo ViewUtil::loadView('tooltip', array('info' => $info));
    }
    
    /**
     * 
     * @param boolean $firstLast
     * @return string
    */
	
    public function getDisplayName($useDba = TRUE) {
        return $this->get('dba') && $useDba ? $this->get('dba') : 
            (UIUtil::getFirstLast() ? "{$this->get('fname')} {$this->get('lname')}" : "{$this->get('lname')}, {$this->get('fname')}");
    }
}
?>