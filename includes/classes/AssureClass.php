<?php

/**
 * @author cmitchell
 */
class AssureClass {
    
    private $exists = FALSE;
    private $values;
    
    private function __clone() {}

    public function __construct($id = NULL, $dieIfNotFound = TRUE) {
        $this->exists = $this->construct($id, $dieIfNotFound);
    }
    
    protected function build($record) {
        if(!is_array($record)) {
            throw new Exception(ucfirst(get_called_class()) . ' record not found');
        }
        
        $this->values = $record;
    }
    
//    public function __call($name, $arguments) {
//        
//    }
//
//    public static function __callStatic($name, $arguments) {
//        
//    }
    
    public function exists() {
        return $this->exists;
    }
    
    public function get($prop, $defaultValue = NULL) {
        return MapUtil::get($this->values, $prop, $defaultValue);
    }
    
    public function set($prop, $value) {
        $this->values[$prop] = $value;
    }
    
    /**
     * This assumes that the first item in the values array is the id
     * 
     * @return int
     */
    public function getMyId() {
        return is_array($this->values) ? reset($this->values) : NULL;
    }
    
    /**
     * 
     * @return array
     */
    protected function getVales() {
        return $this->values;
    }
    
    /**
     * Check for user_id on the record and return user name if possible
     * 
     * @param boolean $asLink
     * @return string
     */
    public function getCreatorDisplayName($asLink = FALSE) {
        $userId = $this->get('user_id');
        return $userId ? UserUtil::getDisplayName($this->get('user_id'), $asLink) : '';
    }
}