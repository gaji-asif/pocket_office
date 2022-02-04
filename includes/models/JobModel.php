<?php

/**
 * @author cmitchell
 */
class JobModel extends AssureModel {
    
    /**
     * 
     * @return array
     */
    public static function getAllStatuses() {
        return DBUtil::getAll('status', 'status');
    }
    
    /**
     * 
     * @param mixed $metaId
     * @return array
     */
    public static function getAllMeta($metaId) {
        $results = DBUtil::getRecords('job_meta', $metaId, 'meta_id');
		 
        return MapUtil::mapTo($results, 'meta_name');
    }
    
    /**
     * 
     * @param mixed $metaId
     * @param string $key
     * @return mixed
     */
    public static function getMetaValue($metaId, $key) {
        $sql = "SELECT meta_value
                FROM job_meta
                WHERE meta_id = '$metaId'
                    AND meta_name = '$key'
                LIMIT 1";
        return DBUtil::queryToScalar($sql);
    }
    
    /**
     * 
     * @param mixed $metaId
     * @param string $key
     * @param string $value
     * @return MySQL Result
     */
    public static function setMetaValue($metaId, $key, $value) {
        self::deleteMetaValue($metaId, $key);
        
        $sql = "INSERT INTO job_meta (meta_id, meta_name, meta_value)
                VALUES ('$metaId', '$key', '$value')";
        return DBUtil::query($sql);
    }
    
    /**
     * 
     * @param mixed $metaId
     * @param string $key
     * @return MySQL Result
     */
    public static function deleteMetaValue($metaId, $key) {
        $sql = "DELETE FROM job_meta
                WHERE meta_id = '$metaId'
                    AND meta_name = '$key'";
        return DBUtil::query($sql);
    }
    
    /**
     * 
     * @param int $jobId
     * @param string $action
     * @return mixed
     */
    public static function saveEvent($jobId, $action) {
        $sql = "INSERT INTO history (user_id, account_id, job_id, action, timestamp) 
                VALUES ('{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$jobId', '$action', NOW())";
        return DBUtil::query($sql);
    }
    
    /**
     * 
     * @param int $jobId
     * @return array
     */
    public static function getActionHistory($jobId) {
        $concatSql = UIUtil::getFirstLast() ? "CONCAT(u.fname, ' ', u.lname)" : "CONCAT(u.lname, ', ', u.fname)";
        $sql = "SELECT h.timestamp, h.action, $concatSql AS display_name, h.user_id
                FROM history h
                JOIN users u ON u.user_id = h.user_id
                WHERE h.job_id = '$jobId'
                ORDER BY h.history_id DESC";
        return DBUtil::queryToArray($sql);
    }

    /**
     * 
     * @param int $offset
     * @param int $pageSize
     * @return array
     */
    public static function getList($offset = 0, $pageSize = NULL) {
        $offset = $offset >= 0 ? $offset : 0;
        $salesman = RequestUtil::get('salesman', '');
        $referral = RequestUtil::get('referral', '');
        $canvasser = RequestUtil::get('canvasser', '');
        $stage = RequestUtil::get('stage', '');
        $creator = RequestUtil::get('creator', '');
        $type = RequestUtil::get('type', '');
        $sort = RequestUtil::get('sort', '');
        $sort = !empty($sort) ? $sort : 'j.timestamp DESC';
        $sort = "ORDER BY order_key ASC, $sort";
        $age = RequestUtil::get('age', '');
        $jurisdiction = RequestUtil::get('jurisdiction', '');
        $taskType = RequestUtil::get('task_type', '');
        $searchStr = RequestUtil::get('search');
        $searchTerms = explode(' ', trim($searchStr));
        $permitExpires = RequestUtil::get('permit_expires');
        $warranty = RequestUtil::get('warranty', '');
        $insuranceProvider = RequestUtil::get('insurance_provider', '');
        $extraJoins = '';
        $extraSql = '';
        $searchBys = '';
        $limitStr = '';

        $closed_job = RequestUtil::get('hidden_closed_job', '');
        $closd_sql="";
        if(empty($closed_job))
        {
            $closd_sql = " HAVING  order_key = 0 ";
        }
        

        $advance_search = RequestUtil::get('advance_search');
        $search_type = RequestUtil::get('search_type', '');
        $advance_sort = RequestUtil::get('advance_sort', '');
        if((!empty($advance_search) && !empty($search_type)) || !empty($advance_sort)) 
        {
            $ret_val = self::getAdvanceList($offset,$pageSize);
            return $ret_val;
        }

        if($warranty) {
            $extraJoins .= "LEFT JOIN job_meta jm ON (jm.meta_id = j.job_id AND jm.meta_name = 'job_warranty')";
        }
        
        if(ModuleUtil::checkOwnership('view_jobs')) {
            $extraSql .= "AND ((sb.user_id = '{$_SESSION['ao_userid']}' AND sb.job_id = j.job_id) OR j.user_id = {$_SESSION['ao_userid']} OR j.salesman = {$_SESSION['ao_userid']} OR j.referral = {$_SESSION['ao_userid']})\r\n";
        }

        if(!empty($searchStr)) {
            $searchBys = array();
            foreach($searchTerms as $term) {
                $term = trim($term);
                $searchBys[] = "AND (
                                    j.job_number LIKE '%$term%'
                                    OR c.address LIKE '%$term%' 
                                    OR c.fname LIKE '%$term%'
                                    OR c.lname LIKE '%$term%'
                                    OR c.nickname LIKE '%$term%'
                                    OR c.zip LIKE '%$term%'
                                    OR j.job_type_note LIKE '%$term%'
                                    OR p.number LIKE '%$term%'
                                )";
            }
            $searchBys = implode(' ', $searchBys);
        }
        
        if($permitExpires !== NULL) {
            $extraSql .= "AND (
                            p.permit_id IS NOT NULL
                            AND CURDATE() <= DATE_ADD(p.timestamp, INTERVAL jur.permit_days DAY)
                            AND DATEDIFF(DATE_ADD(p.timestamp, INTERVAL jur.permit_days DAY), CURDATE()) <= $permitExpires
                        )\r\n";
        }

        if($pageSize) {
            $limitStr = "LIMIT $offset, $pageSize";
        }
        
        $finalStageNum = StageModel::getLastStageNum();

		$hidehold = RequestUtil::get('hidehold', '');
		 
		if($hidehold !== NULL && !empty($hidehold))
		{
		
		 $sql = "SELECT SQL_CALC_FOUND_ROWS job_id from (Select j.job_id,
                    CASE WHEN sh.status_id IS NOT NULL AND (sh.expires IS NULL OR sh.expires > CURDATE()) THEN 1 
                        ELSE 0
                    END AS order_key
                FROM customers c, jobs j
                LEFT JOIN job_type jt ON (j.job_type = jt.job_type_id)
                LEFT JOIN jurisdiction jur ON (j.jurisdiction = jur.jurisdiction_id)
                LEFT JOIN permits p ON (j.job_id = p.job_id)
                LEFT JOIN status_holds sh ON (sh.job_id = j.job_id)
                LEFT JOIN status s ON (s.status_id = sh.status_id)
                LEFT JOIN stages st ON (st.stage_num = j.stage_num)
                LEFT JOIN repairs r ON (r.job_id = j.job_id AND r.completed IS NULL)
                LEFT JOIN subscribers sb ON (sb.job_id = j.job_id)
                LEFT JOIN users u ON (u.user_id = j.salesman)
                LEFT JOIN tasks t ON (t.job_id = j.job_id)
                LEFT JOIN task_type tt ON (tt.task_type_id = t.task_type)
                LEFT JOIN canvassers cv ON (cv.job_id = j.job_id)
                $extraJoins
                WHERE j.customer_id = c.customer_id
                    AND j.account_id = '{$_SESSION['ao_accountid']}'
                    $searchBys $extraSql $salesman $canvasser $referral $stage $creator $type $age $jurisdiction $taskType $warranty $insuranceProvider
                GROUP BY j.job_id
                $closd_sql
                $sort
                ) i where i.order_key=0 $limitStr";
		}
		else
		{

        $sql = "SELECT SQL_CALC_FOUND_ROWS j.job_id,
                    CASE 
                        WHEN j.stage_num = '$finalStageNum' THEN 1 
                        WHEN sh.status_id IS NOT NULL AND (sh.expires IS NULL OR sh.expires > CURDATE()) THEN 1 
                        ELSE 0
                    END AS order_key
                FROM customers c, jobs j
                LEFT JOIN job_type jt ON (j.job_type = jt.job_type_id)
                LEFT JOIN jurisdiction jur ON (j.jurisdiction = jur.jurisdiction_id)
                LEFT JOIN permits p ON (j.job_id = p.job_id)
                LEFT JOIN status_holds sh ON (sh.job_id = j.job_id)
                LEFT JOIN status s ON (s.status_id = sh.status_id)
                LEFT JOIN stages st ON (st.stage_num = j.stage_num)
                LEFT JOIN repairs r ON (r.job_id = j.job_id AND r.completed IS NULL)
                LEFT JOIN subscribers sb ON (sb.job_id = j.job_id)
                LEFT JOIN users u ON (u.user_id = j.salesman)
                LEFT JOIN tasks t ON (t.job_id = j.job_id)
                LEFT JOIN task_type tt ON (tt.task_type_id = t.task_type)
                LEFT JOIN canvassers cv ON (cv.job_id = j.job_id)
                $extraJoins
                WHERE j.customer_id = c.customer_id
                    AND j.account_id = '{$_SESSION['ao_accountid']}'
                    $searchBys $extraSql $salesman $canvasser $referral $stage $creator $type $age $jurisdiction $taskType $warranty $insuranceProvider
                GROUP BY j.job_id
                $closd_sql
                $sort
                $limitStr";
		}
//        LogUtil::getInstance()->logNotice($sql);
        return DBUtil::queryToArray($sql);
    }

    /**
     * 
     * @param int $offset
     * @param int $pageSize
     * @return array
     */
    public static function getAdvanceList($offset = 0, $pageSize = NULL) {

        $offset = $offset >= 0 ? $offset : 0;
        $type = RequestUtil::get('search_type', '');
        $sort = RequestUtil::get('advance_sort', '');
        $sort = !empty($sort) ? $sort : 'j.timestamp DESC';
        $sort = "ORDER BY order_key ASC, $sort";
        $searchStr = RequestUtil::get('advance_search');
        $extraJoins = '';
        $extraSql = '';
        $searchBys = '';
        $limitStr = '';
        $closd_sql="";

        $closed_job = RequestUtil::get('hidden_closed_job', '');
        if(empty($closed_job))
        {
            $closd_sql = " HAVING  order_key = 0 ";
        }
        
        if(ModuleUtil::checkOwnership('view_jobs')) {
            $extraSql .= "AND ((sb.user_id = '{$_SESSION['ao_userid']}' AND sb.job_id = j.job_id) OR j.user_id = {$_SESSION['ao_userid']} OR j.salesman = {$_SESSION['ao_userid']} OR j.referral = {$_SESSION['ao_userid']})\r\n";
        }
        if(!empty($searchStr) && $type=='in') 
        {               
            $searchBys = "  AND (
                                c.address LIKE '%$searchStr%' 
                                OR CONCAT(c.fname,' ',c.lname) LIKE '%$searchStr%'
                                OR c.nickname LIKE '%$searchStr%'
                            )";
        }         
        if(!empty($searchStr) && $type=='cn') 
        {               
            $searchBys = " AND j.claim LIKE '%$searchStr%'";
        }   
        if(!empty($searchStr) && $type=='pn') 
        {    
            $extraJoins .= "LEFT JOIN job_meta jm ON (jm.meta_id = j.job_id AND jm.meta_name = 'insurance_policy')";
            $searchBys = " AND jm.meta_value LIKE '%$searchStr%'";
        } 
        if(!empty($searchStr) && $type=='jn') 
        {               
            $searchBys = " AND j.job_number LIKE '%$searchStr%'";
        }  
        if(!empty($searchStr) && $type=='phn') 
        {               
            $searchBys = " AND c.phone LIKE '%$searchStr%'";
        }     

        if($pageSize) {
            $limitStr = "LIMIT $offset, $pageSize";
        }
        
        $finalStageNum = StageModel::getLastStageNum();


        $sql = "SELECT SQL_CALC_FOUND_ROWS j.job_id,
                CASE 
                    WHEN j.stage_num = '$finalStageNum' THEN 1 
                    WHEN sh.status_id IS NOT NULL AND (sh.expires IS NULL OR sh.expires > CURDATE()) THEN 1 
                    ELSE 0
                END AS order_key
                FROM customers c, jobs j
                LEFT JOIN subscribers sb ON sb.job_id = j.job_id
                LEFT JOIN tasks t ON t.job_id = j.job_id
                LEFT JOIN status_holds sh ON (sh.job_id = j.job_id)
                $extraJoins
                WHERE j.customer_id = c.customer_id
                    AND j.account_id = '{$_SESSION['ao_accountid']}'                   
                    $searchBys $extraSql
                GROUP BY j.job_id
                $closd_sql
                $sort
                $limitStr";
        //echo $sql;die;
        return DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     * @param int $jobId
     * @return string
     */
    public static function getInvoiceBalance($jobId, $withCurrencySign = TRUE) {
        $job = new Job($jobId, FALSE);
        return ($withCurrencySign ? '$' : '') . ($job ? $job->getInvoiceBalance() : CurrencyUtil::formatUSD(0));
    }
    
    /**
     * 
     * @param int $jobTypeId
     * @return array
     */
    public static function getTypeById($jobTypeId) {
        return DBUtil::getRecord('job_type', $jobTypeId);
    }
    
}