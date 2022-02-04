<?php
/**
 * @author cmitchell
 */
class ReportUtil extends AssureUtil {
    
    /**
     * 
     * @return array
     */
    public static function getSavedReports() {
        $sql = "SELECT *
                FROM saved_reports sr
                WHERE sr.account_id = '{$_SESSION['ao_accountid']}'
                AND active = 1";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getTableMap($table = NULL) {
        $table = $table ?: RequestUtil::get('table');
        return MapUtil::get(self::getMap(), $table);
    }
    
    /**
     * 
     * @return array
     */
    public static function getResultsFromRequest() {
        $table = RequestUtil::get('table');
        $sql = ReportUtil::buildSQL($table);
        return DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     * @return type
     */
    public static function getFiltersFromRequest ($allRequestVars = NULL) {
        $allRequestVars = $allRequestVars ?: RequestUtil::getAll();
        
        $filters = array();
        foreach($allRequestVars as $key => $value) {
            if(!StrUtil::startsWith($key, 'filter_')) { continue; }
            
            $filters[str_replace('filter_', '', $key)] = $value;
        }
        
        return $filters;
    }
    
    /**
     * 
     * @param string $table
     * @param array $columns
     * @param array $filters
     * @param string $sort
     * @return string
     */
    public static function buildSQL($table = NULL, $columns = NULL, $filters = NULL, $accountId = NULL) {
        $table = $table ?: RequestUtil::get('table');
        $map = self::getTableMap($table);
        $columns = $columns ?: RequestUtil::get('columns');
        $filters = $filters ?: self::getFiltersFromRequest();
        $accountId = $accountId ?: $_SESSION['ao_accountid'];
        
        if(!$map || !$columns) { return NULL; }
        
        $alias = MapUtil::get($map, 'alias', array());
        $columnSql = self::buildColumnSQL($map, $columns);
        $joinSql = self::buildJoinSQL($map);
        $leftJoinSql = self::buildJoinSQL($map, 'left');
        $filterSql = self::buildFilterSql($map, $filters);
        $filterSql = $filterSql ? "AND $filterSql " : '';
        $idField = MapUtil::get($map, 'id_field');
        
        return "SELECT $columnSql
            FROM $table $alias
            $joinSql
            $leftJoinSql
            WHERE $alias.account_id = '$accountId'
            $filterSql
            GROUP BY $idField";
    }
    
    /**
     * 
     * @param string $table
     * @return array
     */
    public static function buildFiltersMap($table) {
        $map = self::getTableMap($table);
        if(!$map) { return array(); }
        $filtersMap = MapUtil::get($map, 'filters');
        
        $filters = array();
        foreach($filtersMap as $label => $filter) {
            $filters[$label] = ViewUtil::loadView('filters/' . MapUtil::get($filter, 'control'), array('name' => $label));
        }
        
        return $filters;
    }
    
    /**
     * 
     * @param string $table
     * @return array
     */
    public static function buildColumnsMap($table = NULL) {
        $table = $table ?: RequestUtil::get('table');
        $map = self::getTableMap($table);
        if(!$map) { return array(); }
        return MapUtil::get($map, 'columns');
    }
    
    /**
     * 
     * @param type $table
     * @return type
     */
    public static function getTableHeaders($table = NULL, $columns = NULL, $asArray = FALSE) {
        $table = $table ?: RequestUtil::get('table');
        $columns = $columns ?: RequestUtil::get('columns');
        $columnMap = self::buildColumnsMap($table);
        $headers = $asArray ? array() : '';
        
        foreach($columns as $columnLabel) {
            if(!isset($columnMap[$columnLabel])) { continue; }
            $columnLabel = StrUtil::humanizeCamelCase($columnLabel);
            if($asArray) {
                $headers[] = trim($columnLabel);
            } else {
                $headers .= "<th>$columnLabel</th>";
            }
        }
        
        return $headers;
    }
    
    public static function getTableRows($resultRows, $table = NULL, $columns = NULL, $asArray = FALSE) {
        $table = $table ?: RequestUtil::get('table');
        $columns = $columns ?: RequestUtil::get('columns');
        $map = self::getTableMap($table);
        $allColumnsMap = MapUtil::get($map, 'columns');
        $rows = $asArray ? array() : '';
        
        foreach($resultRows as $resultRow) {
            //get rid of id and save the row id
            $rowId = array_shift($resultRow);
            
            if($asArray) {
                $rows[] = self::buildRow($rowId, $resultRow, $allColumnsMap, $columns, $asArray);
            } else {
                $rows .= self::buildRow($rowId, $resultRow, $allColumnsMap, $columns, $asArray);
            }
        }
        
        return $rows;
    }
    
    public static function buildRow($rowId, $resultRow, $allColumnsMap, $columns, $asArray = FALSE) {
        $row = $asArray ? array() : '';
        //iterate over cols
        foreach($columns as $column) {
            $columnMap = MapUtil::get($allColumnsMap, $column);
            if(!$columnMap) { continue; }
            
            //use raw value or getter
            $method = MapUtil::get($columnMap, 'getter');
            $value = MapUtil::get($resultRow, $column);
            $value = $method ? call_user_func($method, $value ?: $rowId) : $value;
            
            //formatting
            $formatter = MapUtil::get($columnMap, 'formatter');
            $value = $formatter && !empty($value) ? call_user_func($formatter, $value) : $value;
            
            if($asArray) {
                $row[] = trim($value);
            } else {
                $row .= "<td>$value</td>";
            }
        }
        
        return $asArray ? $row : "<tr class=\"report-row-id-$rowId\">$row</td>";
    }
    
    /**
     * 
     * @param array $map
     * @return string
     */
    private static function buildColumnSQL($map, $reportColumns = NULL) {
        $columnMap = MapUtil::get($map, 'columns', array());
        $idField = MapUtil::get($map, 'id_field');
        $columns = $idField ? array($idField) : array();
        
        foreach($reportColumns as $reportColumn) {
            $column = MapUtil::get($columnMap, $reportColumn);  
            $field = MapUtil::get($column, 'field');
            if(!$field) { continue; }
            $columns[] = "$field AS $reportColumn";
            
        }
        
        return implode(",\r\n", $columns);
    }
    
    /**
     * 
     * @param array $map
     * @return string
     */
    private static function buildJoinSQL($map, $joinType = NULL) {
        $joinMap = MapUtil::get($map, $joinType ? "{$joinType}_joins" : 'joins', array());
        $joinSql = $joinType ? strtoupper($joinType) . ' JOIN ' : 'JOIN ';
        $joins = array();
        
        foreach($joinMap as $join) {
            $conditions = implode(' AND ', MapUtil::get($join, 'on'));
            $joins[] = $joinSql . MapUtil::get($join, 'table')
                    . ' ' . MapUtil::get($join, 'alias')
                    . ' ON ' . $conditions;
        }
        return implode("\r\n", $joins);
    }
    
    private static function buildFilterSql($map, $reportFilters = NULL) {
        $filterMap = MapUtil::get($map, 'filters', array());
        $filters = array();
        
        foreach($reportFilters as $reportFilter => $value) {
            $filter = MapUtil::get($filterMap, str_replace('_', ' ', $reportFilter));
            if(empty($filter) || empty($value)) { continue; }

            $value = is_array($value) ? implode(",", $value) : $value;
            $filters[] = str_replace('::value', $value, MapUtil::get($filter, 'condition'));
        }
        
        return implode("\r\nAND ", $filters);
    }
    
    public static function getMap() {
        return array(
            /**
             * Jobs
             */
            'jobs' => array(
                'alias' => 'j',
                'id_field' => 'j.job_id',
                'columns' => array(
                    'Number' => array(
                        'getter' => 'JobUtil::getLink'
                    ),
                    'Origin' => array(
                        'field' => 'o.origin',
                    ),
                    'Type' => array(
                        'field' => 'jt.job_type',
                    ),
                    'InvoiceBalance' => array(
                        'getter' => 'JobModel::getInvoiceBalance'
                    ),
                    'Paid' => array(
                        'field' => 'j.pif_date',
                        'formatter' => 'DateUtil::formatShortDate'
                    ),
                    'Customer' => array(
                        'getter' => 'CustomerUtil::getDisplayName',
                        'field' => 'j.customer_id'
                    ),
                    'Address' => array(
                        'field' => "CONCAT(c.address, ', ', c.city, ', ', c.state, ' ', c.zip)",
                        'formatter' => 'MapUtil::getLinkToInternalMap'
                    ),
                    'Stage' => array(
                        'getter' => 'StageModel::getCSVStagesByStageNum',
                        'field' => 'j.stage_num'
                    ),
                    'Jurisdiction' => array(
                        'field' => 'jur.location',
                    ),
                    'Midroof' => array(
                        'field' => 'jur.midroof_timing',
                    ),
                    'Permit' => array(
                        'field' => 'permits.number',
                    ),
                    'Provider' => array(
                        'field' => 'ins.insurance',
                    ),
                    'Claim' => array(
                        'field' => 'j.claim',
                    ),
                    'Salesman' => array(
                        'getter' => 'UserUtil::getDisplayName',
                        'field' => 'j.salesman'
                    ),
                    'Canvasser' => array(
                        'getter' => 'UserUtil::getDisplayName',
                        'field' => 'cv.user_id'
                    ),
                    'Referral' => array(
                        'getter' => 'UserUtil::getDisplayName',
                        'field' => 'j.referral'
                    ),
                    'ReferralPaid' => array(
                        'field' => 'j.referral_paid',
                        'formatter' => 'DateUtil::formatShortDate'
                    ),
                    'Stage' => array(
                        'field' => 'stg.stage',
                    ),
                    'Tasks' => array(
                        'getter' => 'JobUtil::getCSVTasks',
                    ),
                    'Contractors' => array(
                        'getter' => 'JobUtil::getCSVContractors',
                    )
                ),
                'joins' => array(
                    array(
                        'table' => 'customers',
                        'alias' => 'c',
                        'on' => array('c.customer_id = j.customer_id')
                    ),
                    array(
                        'table' => 'stages',
                        'alias' => 'stg',
                        'on' => array('stg.stage_num = j.stage_num')
                    ),
                ),
                'left_joins' => array(
                    array(
                        'table' => 'jurisdiction',
                        'alias' => 'jur',
                        'on' => array('jur.jurisdiction_id = j.jurisdiction')
                    ),
                    array(
                        'table' => 'permits',
                        'on' => array('permits.job_id = j.job_id')
                    ),
                    array(
                        'table' => 'users',
                        'alias' => 'u_salesman',
                        'on' => array('u_salesman.user_id = j.salesman')
                    ),
                    array(
                        'table' => 'users',
                        'alias' => 'u_referral',
                        'on' => array('u_referral.user_id = j.referral')
                    ),
                    array(
                        'table' => 'origins',
                        'alias' => 'o',
                        'on' => array('o.origin_id = j.origin')
                    ),
                    array(
                        'table' => 'job_type',
                        'alias' => 'jt',
                        'on' => array('jt.job_type_id = j.job_type')
                    ),
                    array(
                        'table' => 'insurance',
                        'alias' => 'ins',
                        'on' => array('ins.insurance_id = j.insurance_id')
                    ),
                    array(
                        'table' => 'canvassers',
                        'alias' => 'cv',
                        'on' => array('cv.job_id = j.job_id')
                    ),
                    array(
                        'table' => 'status_holds',
                        'alias' => 'sh',
                        'on' => array('sh.job_id = j.job_id')
                    )
                ),
                'filters' => array(
                    'Since' => array(
                        'control' => 'date',
                        'condition' => "j.timestamp >= '::value'"
                    ),
                    'Thru' => array(
                        'control' => 'date',
                        'condition' => "j.timestamp <= '::value'"
                    ),
                    'Paid' => array(
                        'control' => 'binary',
                        'condition' => 'j.pif_date IS NOT NULL'
                    ),
                    'HideHolds' => array(
                        'control' => 'binary',
                        'condition' => '(sh.status_hold_id IS NULL OR sh.timestamp <= NOW())'
                    ),
                    'UnpaidReferral' => array(
                        'control' => 'binary',
                        'condition' => 'j.referral_paid IS NULL'
                    ),
                    'Stage' => array(
                        'control' => 'stages-number',
                        'condition' => "j.stage_num IN(::value)"
                    ),
                    'Customer' => array(
                        'control' => 'customers',
                        'condition' => "j.customer_id IN(::value)"
                    ),
                    'User' => array(
                        'control' => 'users',
                        'condition' => "j.user_id IN(::value)"
                    ),
                    'Salesman' => array(
                        'control' => 'users',
                        'condition' => "j.salesman IN(::value)"
                    ),
                    'Canvasser' => array(
                        'control' => 'users',
                        'condition' => "cv.user_id IN(::value)"
                    ),
                    'Referral' => array(
                        'control' => 'users',
                        'condition' => "j.referral IN(::value)"
                    ),
                    'Origin' => array(
                        'control' => 'origins',
                        'condition' => "j.origin IN(::value)"
                    ),
                    'Type' => array(
                        'control' => 'job-types',
                        'condition' => "j.job_type IN(::value)"
                    ),
                    'Jurisdiction' => array(
                        'control' => 'jurisdictions',
                        'condition' => "j.jurisdiction IN(::value)"
                    ),
                ),
                'sorts' => array()
            ),
            /**
             * Tasks
             */
            'tasks' => array(
                'alias' => 't',
                'id_field' => 't.task_id',
                'columns' => array(
                    'Type' => array(
                        'field' => 'tt.task',
                    ),
                    'JobNumber' => array(
                        'getter' => 'JobUtil::getLink',
                        'field' => 't.job_id'
                    ),
                    'JobInvoiceBalance' => array(
                        'getter' => 'JobModel::getInvoiceBalance',
                        'field' => 't.job_id'
                    ),
                    'StartDate' => array(
                        'field' => 't.start_date',
                        'formatter' => 'DateUtil::getShortScheduleWeekLink'
                    ),
                    'Completed' => array(
                        'field' => 't.completed',
                        'formatter' => 'DateUtil::formatShortDate'
                    ),
                    'Paid' => array(
                        'field' => 't.paid',
                        'formatter' => 'DateUtil::formatShortDate'
                    ),
                    'Customer' => array(
                        'getter' => 'CustomerUtil::getDisplayName',
                        'field' => 'j.customer_id'
                    ),
                    'Address' => array(
                        'field' => "CONCAT(c.address, ', ', c.city, ', ', c.state, ' ', c.zip)",
                        'formatter' => 'MapUtil::getLinkToInternalMap'
                    ),
                    'Jurisdiction' => array(
                        'field' => 'jur.location',
                    ),
                    'TaskStage' => array(
                        'getter' => 'StageModel::getStageNameById',
                        'field' => 't.stage_id',
                    ),
                    'JobStage' => array(
                        'getter' => 'StageModel::getCSVStagesByStageNum',
                        'field' => 'j.stage_num'
                    ),
                    'Midroof' => array(
                        'field' => 'jur.midroof_timing',
                    ),
                    'Permit' => array(
                        'field' => 'permits.number',
                    ),
                    'Provider' => array(
                        'field' => 'ins.insurance',
                    ),
                    'Claim' => array(
                        'field' => 'j.claim',
                    ),
                    'Contractor' => array(
                        'getter' => 'UserUtil::getDbaOrDisplayName',
                        'field' => 't.contractor'
                    ),
                    'Salesman' => array(
                        'getter' => 'UserUtil::getDisplayName',
                        'field' => 'j.salesman'
                    ),
                    'Notes' => array(
                        'field' => 't.note'
                    ),
                ),
                'joins' => array(
                    array(
                        'table' => 'jobs',
                        'alias' => 'j',
                        'on' => array(
                            't.job_id = j.job_id'
                        )
                    ),
                    array(
                        'table' => 'customers',
                        'alias' => 'c',
                        'on' => array(
                            'c.customer_id = j.customer_id'
                        )
                    ),
                    array(
                        'table' => 'task_type',
                        'alias' => 'tt',
                        'on' => array('tt.task_type_id = t.task_type')
                    ),
                    
                ),
                'left_joins' => array(
                    array(
                        'table' => 'jurisdiction',
                        'alias' => 'jur',
                        'on' => array(
                            'jur.jurisdiction_id = j.jurisdiction'
                        )
                    ),
                    array(
                        'table' => 'permits',
                        'on' => array(
                            'permits.job_id = j.job_id'
                        )
                    ),
                    array(
                        'table' => 'users',
                        'alias' => 'u_salesman',
                        'on' => array(
                            'u_salesman.user_id = j.salesman'
                        )
                    ),
                    array(
                        'table' => 'users',
                        'alias' => 'u_contractor',
                        'on' => array(
                            'u_contractor.user_id = t.user_id'
                        )
                    ),
                    array(
                        'table' => 'insurance',
                        'alias' => 'ins',
                        'on' => array(
                            'ins.insurance_id = j.insurance_id'
                        )
                    )
                ),
                'filters' => array(
                    'StartDateSince' => array(
                        'control' => 'date',
                        'condition' => "(t.start_date IS NOT NULL AND t.start_date >= '::value')"
                    ),
                    'StartDateThru' => array(
                        'control' => 'date',
                        'condition' => "(t.start_date IS NOT NULL AND  t.start_date <= '::value')"
                    ),
                    'CreatedDateSince' => array(
                        'control' => 'date',
                        'condition' => "(t.timestamp >= '::value')"
                    ),
                    'CreatedDateThru' => array(
                        'control' => 'date',
                        'condition' => "(t.timestamp <= '::value')"
                    ),
                    'Incomplete' => array(
                        'control' => 'binary',
                        'condition' => 't.completed IS NULL'
                    ),
                    'Type' => array(
                        'control' => 'task-types',
                        'condition' => "t.task_type IN(::value)"
                    ),
                    'TaskStage' => array(
                        'control' => 'stages-id',
                        'condition' => "t.stage_id IN(::value)"
                    ),
                    'JobStage' => array(
                        'control' => 'stages-number',
                        'condition' => "j.stage_num IN(::value)"
                    ),
                    'Customer' => array(
                        'control' => 'customers',
                        'condition' => "j.customer_id IN(::value)"
                    ),
                    'User' => array(
                        'control' => 'users',
                        'condition' => "j.user_id IN(::value)"
                    ),
                    'Salesman' => array(
                        'control' => 'users',
                        'condition' => "j.salesman IN(::value)"
                    ),
                    'Referral' => array(
                        'control' => 'users',
                        'condition' => "j.referral IN(::value)"
                    ),
                    'Origin' => array(
                        'control' => 'origins',
                        'condition' => "j.origin IN(::value)"
                    ),
                    'JobType' => array(
                        'control' => 'job-types',
                        'condition' => "j.job_type IN(::value)"
                    ),
                    'Jurisdiction' => array(
                        'control' => 'jurisdictions',
                        'condition' => "j.jurisdiction IN(::value)"
                    ),
                ),
                'sorts' => array()
            ),
            /**
             * Repairs
             */
            'repairs' => array(
                'alias' => 'r',
                'id_field' => 'r.repair_id',
                'columns' => array(
                    'Type' => array(
                        'field' => 'ft.fail_type',
                    ),
                    'JobNumber' => array(
                        'getter' => 'JobUtil::getLink',
                        'field' => 'r.job_id'
                    ),
                    'StartDate' => array(
                        'field' => 'r.startdate',
                        'formatter' => 'DateUtil::getShortScheduleWeekLink'
                    ),
                    'Completed' => array(
                        'field' => 'r.completed',
                        'formatter' => 'DateUtil::formatShortDate'
                    ),
                    'Customer' => array(
                        'getter' => 'CustomerUtil::getDisplayName',
                        'field' => 'j.customer_id'
                    ),
                    'Address' => array(
                        'field' => "CONCAT(c.address, ', ', c.city, ', ', c.state, ' ', c.zip)",
                        'formatter' => 'MapUtil::getLinkToInternalMap'
                    ),
                    'Jurisdiction' => array(
                        'field' => 'jur.location',
                    ),
                    'Midroof' => array(
                        'field' => 'jur.midroof_timing',
                    ),
                    'Permit' => array(
                        'field' => 'permits.number',
                    ),
                    'Provider' => array(
                        'field' => 'ins.insurance',
                    ),
                    'Claim' => array(
                        'field' => 'j.claim',
                    ),
                    'Contractor' => array(
                        'getter' => 'UserUtil::getDbaOrDisplayName',
                        'field' => 'r.contractor'
                    ),
                    'Salesman' => array(
                        'getter' => 'UserUtil::getDisplayName',
                        'field' => 'j.salesman'
                    ),
                    'Notes' => array(
                        'field' => 'r.notes'
                    ),
                ),
                'joins' => array(
                    array(
                        'table' => 'jobs',
                        'alias' => 'j',
                        'on' => array(
                            'r.job_id = j.job_id'
                        )
                    ),
                    array(
                        'table' => 'customers',
                        'alias' => 'c',
                        'on' => array(
                            'c.customer_id = j.customer_id'
                        )
                    ),
                ),
                'left_joins' => array(
                    array(
                        'table' => 'fail_types',
                        'alias' => 'ft',
                        'on' => array(
                            'ft.fail_type_id = r.fail_type'
                        )
                    ),
                    array(
                        'table' => 'jurisdiction',
                        'alias' => 'jur',
                        'on' => array(
                            'jur.jurisdiction_id = j.jurisdiction'
                        )
                    ),
                    array(
                        'table' => 'permits',
                        'on' => array(
                            'permits.job_id = j.job_id'
                        )
                    ),
                    array(
                        'table' => 'users',
                        'alias' => 'u_salesman',
                        'on' => array(
                            'u_salesman.user_id = j.salesman'
                        )
                    ),
                    array(
                        'table' => 'users',
                        'alias' => 'u_contractor',
                        'on' => array(
                            'u_contractor.user_id = r.user_id'
                        )
                    ),
                    array(
                        'table' => 'insurance',
                        'alias' => 'ins',
                        'on' => array(
                            'ins.insurance_id = j.insurance_id'
                        )
                    )
                ),
                'filters' => array(
                    'Start Date Since' => array(
                        'control' => 'date',
                        'condition' => "(t.start_date IS NOT NULL AND t.start_date >= '::value')"
                    ),
                    'Start Date Thru' => array(
                        'control' => 'date',
                        'condition' => "(t.start_date IS NOT NULL AND  t.start_date <= '::value')"
                    ),
                    'Incomplete' => array(
                        'control' => 'binary',
                        'condition' => 'r.completed IS NULL'
                    ),
                    'Type' => array(
                        'control' => 'fail-types',
                        'condition' => "r.fail_type IN(::value)"
                    ),
                    'Customer' => array(
                        'control' => 'customers',
                        'condition' => "j.customer_id IN(::value)"
                    ),
                    'User' => array(
                        'control' => 'users',
                        'condition' => "j.user_id IN(::value)"
                    ),
                    'Salesman' => array(
                        'control' => 'users',
                        'condition' => "j.salesman IN(::value)"
                    ),
                    'Referral' => array(
                        'control' => 'users',
                        'condition' => "j.referral IN(::value)"
                    ),
                    'Origin' => array(
                        'control' => 'origins',
                        'condition' => "j.origin IN(::value)"
                    ),
                    'JobType' => array(
                        'control' => 'job-types',
                        'condition' => "j.job_type IN(::value)"
                    ),
                    'Jurisdiction' => array(
                        'control' => 'jurisdictions',
                        'condition' => "j.jurisdiction IN(::value)"
                    ),
                ),
                'sorts' => array()
            )
        );
    }
    
}