<?php

class AssureJson {
    private $errors = array();
    private $info = array();
    private $results = array();
    private $success = array();
    
    public function __construct() {}
    
    /**
    * Description
    * 
    * @param
    * @return
    */
    public function addResult($key, $value) {
        $this->results[$key] = $value;
        
        return $this;
    }
    
    /**
    * Description
    * 
    * @param
    * @return
    */
    public function addError($error) {
        if (!is_array($error)) {
            $this->errors[] = $error;
            return $this;
        }
        
        foreach($error as $val) {
            $this->addError($val);
        }
        
        return $this;
    }
    
    /**
    * Description
    * 
    * @param
    * @return
    */
    public function addInfo($info) {
        if (!is_array($info)) {
            $this->info[] = $info;
            return $this;
        }
        
        foreach($info as $val) {
            $this->addInfo($val);
        }
        
        return $this;
    }
    
    /**
    * Description
    * 
    * @param
    * @return
    */
    public function addSuccess($success) {
        if (!is_array($success)) {
            $this->success[] = $success;
            return $this;
        }
        
        foreach($success as $val) {
            $this->addSuccess($val);
        }
        
        return $this;
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function deleteResult($key) {
        if (isset($this->results[$key])) {
            unset($this->results[$key]);
        }
        
        return $this;
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function clearResults() {
        $this->results = array();
        
        return $this;
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function clearInfo() {
        $this->info = array();
        
        return $this;
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function clearSuccess() {
        $this->success = array();
        
        return $this;
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function clearErrors() {
        
        return $this;
        $this->errors = array();
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function reset() {
        $this->clearErrors();
        $this->clearInfo();
        $this->clearResults();
        $this->clearSuccess();
        
        return $this;
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function success() {
        return empty($this->errors) ? TRUE : FALSE;
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function getAsArray() {
        return array(
            'errors' => $this->errors,
            'info' => $this->info,
            'results' => $this->results,
            'success' => $this->success,
            'timestamp' => DateUtil::formatMySQLTimestamp()
        );
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function out() {
        $this->header();
        echo $this->json($this->getAsArray());
        die();
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public static function json($data) {
        return json_encode($data);
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public static function header() {
        if(!headers_sent()) {
            header('Content-Type: application/json');
        }
    }
}