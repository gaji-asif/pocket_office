<?php

class FlowForm {
    private $defaultOptions = array(
        'action' => '?',
        'name' => '',
        'method' => 'POST',
        'attrs' => '',
        'reset_btn' => TRUE,
        'submit_btn' => TRUE,
        'reset_btn_label' => 'Reset',
        'submit_btn_label' => 'Save',
        'errors' => array(),
        'info_message' => '',
        'success_message' => '',
        'submit_check_name' => 'flow_form'
    );
    private $options = array();
    private $output = '';
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function __construct($options = array()) {
        //merge options
        $this->options = array_merge($this->defaultOptions, $options);
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public static function clearPostVars() {
        $_POST = array();
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function addText($label, $name = NULL, $value = '', $id = NULL, $class = NULL, $attrs = NULL, $helper_text = NULL) {
        $view_data = array(
            'label' => $label,
            'name' => $name,
            'value' => $value,
            'id' => $id,
            'class' => $class,
            'attrs' => $attrs,
            'helper_text' => $helper_text,
            'type' => 'text'
        );
        $this->add('form/text', $view_data);
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function addPassword($label, $name = NULL, $value = '', $id = NULL, $class = NULL, $attrs = NULL, $helper_text = NULL) {
        $view_data = array(
            'label' => $label,
            'name' => $name,
            'value' => $value,
            'id' => $id,
            'class' => $class,
            'attrs' => $attrs,
            'helper_text' => $helper_text,
            'type' => 'password'
        );
        $this->add('form/text', $view_data);
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function addHidden($label, $name = NULL, $value = '', $id = NULL, $class = NULL, $attrs = NULL, $helper_text = NULL) {
        $view_data = array(
            'label' => $label,
            'name' => $name,
            'value' => $value,
            'id' => $id,
            'class' => $class,
            'attrs' => $attrs,
            'helper_text' => $helper_text,
            'type' => 'hidden'
        );
        $this->add('form/text', $view_data);
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function addMultiSelect() {
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function addSelect($label, $values = array(), $keys = array(0, 1), $name = NULL, $selected = NULL, $id = NULL, $class = NULL, $attrs = NULL, $helper_text = NULL) {
        $view_data = array(
            'label' => $label,
            'values' => $values,
            'keys' => $keys,
            'name' => $name,
            'selected' => $selected,
            'id' => $id,
            'class' => $class,
            'attrs' => $attrs,
            'helper_text' => $helper_text
        );
        $this->add('form/select', $view_data);
    }
    
    /**
     * Description
     * 
     * @param
     * @return
     */
    public function addTextarea() {}
    
    /**
    * Description
    * 
    * @param
    * @return
    */
    private function addSubmitCheck() {
        $this->addHidden(NULL, $this->options['submit_check_name']);
    }
    
    /**
    * Description
    * 
    * @param
    * @return
    */
    public function render() {
        //add hidden text field to identify form submit
        $this->addSubmitCheck();
        
        $view_data = array(
            'output' => $this->output,
            'options' => $this->options
        );
        echo ViewUtil::loadView('form/wrapper', $view_data);
    }
    
    /**
    * Description
    * 
    * @param
    * @return
    */
    public function clear() {}
    
    /**
    * Description
    * 
    * @param
    * @return
    */
    private function add($view, $view_data = array()) {
        //populate with submitted value if it exists
        if(isset($view_data['selected'])) {
            $view_data['selected'] = isset($_POST[$view_data['name']]) ? $_POST[$view_data['name']] : $view_data['selected'];
        } else if(isset($view_data['value'])) {
            $view_data['value'] = isset($_POST[$view_data['name']]) ? $_POST[$view_data['name']] : $view_data['value'];
        }
        
        $this->output .= ViewUtil::loadView($view, $view_data);
    }
}