<?php

class Customer extends AssureClass {

    protected function construct($id, $dieIfNotFound = TRUE) {
        RequestUtil::set('ignore_cache', 1);
        $record = DBUtil::getRecord('customers', $id);
        if (!count($record)) {
            if ($dieIfNotFound) {
                die('Customer not found');
            } else {
                return FALSE;
            }
        }
        
        $this->build($record);
        $this->setUserNames();
        
        return TRUE;
    }

    public function setUserNames() {
        $user = DBUtil::getRecord('users', $this->get('user_id'));
        $this->set('user_fname', MapUtil::get($user, 'fname'));
        $this->set('user_lname', MapUtil::get($user, 'lname'));
    }
    
    public function getAddresses() {
        if(!$this->addresses) {
            $this->addresses = DBUtil::getRecords('customer_addresses', $this->getMyId(), 'customer_id');
        }
        
        return $this->addresses;
    }

    public function getTooltip() {
        $info = array(
            'Name' => $this->getDisplayName(),
            'Nickname' => $this->get('nickname'),
            'Address' => $this->getFullAddress(),
            'Email' => $this->get('email'),
            'Phone' => UIUtil::formatPhone($this->get('phone')) . ($this->get('phone2') ? ', ' . UIUtil::formatPhone($this->get('phone2')) : '')
        );
            
        echo ViewUtil::loadView('tooltip', array('info' => $info));
    }
    
    public function hasOwnership($userId = NULL) {
        $userId = $userId ?: $_SESSION['ao_userid'];
        
        return ModuleUtil::checkIsFounder() || $this->get('user_id') == $userId;
    }
    
    public function getDisplayName($useNickname = FALSE) {
        return $useNickname && $this->get('nickname') ? $this->get('nickname') : ("{$this->get('fname')} {$this->get('lname')}");
    }
    
    public function getFullAddress() {
        return "{$this->get('address')}, {$this->get('city')}, {$this->get('state')} {$this->get('zip')}";
    }

}