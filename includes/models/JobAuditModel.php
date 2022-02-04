<?php

/**
 * @author cmitchell
 */
class JobAuditModel extends AssureModel {
    
    private $job;
    private $auditChanges = array();
    private $jobAudits = array();
    private $history = array();
    private $fieldsToCompare = array(
        'customer_id' => array(
            'label' => 'customer',
            'getter' => 'CustomerModel::getDisplayName'
        ),
        'jurisdiction' => array(
            'label' => 'jurisdiction',
            'getter' => 'JurisdictionModel::getById',
            'formatter' => 'MapUtil::get',
            'formatter_param' => 'location'
        ),
        'referral' => array(
            'label' => 'referral',
            'getter' => 'CustomerModel::getDisplayName'
        ),
        'salesman' => array(
            'label' => 'salesman',
            'getter' => 'CustomerModel::getDisplayName'
        ),
        'origin' => array(
            'label' => 'origin',
            'getter' => 'OriginModel::getById',
            'formatter' => 'MapUtil::get',
            'formatter_param' => 'origin'
        ),
        'insurance_id' => array(
            'label' => 'insurance provider',
            'getter' => 'InsuranceModel::getProviderById',
            'formatter' => 'MapUtil::get',
            'formatter_param' => 'insurance'
        ),
        'job_type' => array(
            'label' => 'job type',
            'getter' => 'JobModel::getTypeById',
            'formatter' => 'MapUtil::get',
            'formatter_param' => 'job_type'
        ),
        'job_number' => array(
            'label' => 'job number'
        ),
        'claim' => array(
            'label' => 'claim number'
        ),
        'pif_date' => array(
            'label' => 'paid date'
        ),
        'job_type_note' => array(
            'label' => 'job type note'
        ),
        'canvasser' => array(
            'label' => 'canvasser',
            'getter' => 'UserUtil::getDisplayName',
            'getter_params' => array(FALSE, TRUE) //asLink, firstLast
        )
    );
    
    /**
     * 
     * @param int $jobId
     * @return boolean
     */
    public function __construct($jobId) {
        $this->job = new Job($jobId, FALSE);
        return $this->job ? TRUE : FALSE;
    }
    
    /**
     * 
     * @return array
     */
    public function generate() {
        $this->fetchAudits();
        $this->processRecords();
        $this->createHistory();
        
        return $this->history;
    }
    
    /**
     * 
     * @return string
     */
    public function render() {
        return ViewUtil::loadView('job-audits', array('history' => $this->generate()));
    }
    
    /**
     * 
     */
    private function fetchAudits() {
        $sql = "SELECT *
                FROM job_audits
                WHERE job_id = '{$this->job->getMyId()}'
                ORDER BY audit_timestamp ASC";
        $this->jobAudits = DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     */
    private function processRecords() {
        $previousJobAudit = NULL;
        
        foreach($this->jobAudits as $jobAudit) {
            //first set of changes...
            if(!$previousJobAudit) {
                $previousJobAudit = $jobAudit;
                continue;
            }
            
            //check for changes
            $changes = array();
            foreach($this->fieldsToCompare as $field => $fieldData) {
                if(MapUtil::get($previousJobAudit, $field) == MapUtil::get($jobAudit, $field)) { continue; }

                //getter and formatter
                $getter = MapUtil::get($fieldData, 'getter');
                $getterParams = MapUtil::get($fieldData, 'getter_params', array());
                $formatter = MapUtil::get($fieldData, 'formatter');
                $formatterParam = MapUtil::get($fieldData, 'formatter_param');

                //fetch old value
                $oldValue = MapUtil::get($previousJobAudit, $field);
                $oldValue = $getter ? call_user_func_array($getter, array_merge(array($oldValue), $getterParams)) : $oldValue;
                $oldValue = $formatter ? call_user_func_array($formatter, array($oldValue, $formatterParam)) : $oldValue;

                //fetch new value
                $newValue = MapUtil::get($jobAudit, $field);
                $newValue = $getter ? call_user_func_array($getter, array_merge(array($newValue), $getterParams)) : $newValue;
                $newValue = $formatter ? call_user_func_array($formatter, array($newValue, $formatterParam)) : $newValue;

                $changes[] = array(
                    'label' => MapUtil::get($fieldData, 'label'),
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                    'audit_user_id' => MapUtil::get($jobAudit, 'audit_user_id'),
                    'audit_timestamp' => MapUtil::get($jobAudit, 'audit_timestamp')
                );
            }
            reset($this->fieldsToCompare);
            if(!count($changes)) { continue; }
            $previousJobAudit = $jobAudit;
            $this->auditChanges = array_merge($this->auditChanges, $changes);
        }
    }
    
    /**
     * 
     */
    private function createHistory() {
        //reverse
        $this->auditChanges = array_reverse($this->auditChanges);

        foreach($this->auditChanges as $auditChange) {
            $label = MapUtil::get($auditChange, 'label');
            $oldValue = MapUtil::get($auditChange, 'old_value');
            $newValue = MapUtil::get($auditChange, 'new_value');

            $change = array(
                'timestamp' => MapUtil::get($auditChange, 'audit_timestamp'),
                'user_id' => MapUtil::get($auditChange, 'audit_user_id')
            );

            if(empty($oldValue)) {
                $change['action' ] = "set $label to $newValue.";
            } else if(empty($newValue)) {
                $change['action' ] = "cleared the $label value (was $oldValue).";
            } else {
                $change['action' ] = "changed $label from $oldValue to $newValue.";
            }

            $this->history[] = $change;
        }
    }
    
}