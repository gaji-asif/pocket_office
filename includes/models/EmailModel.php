<?php

/**
 * @author cmitchell
 */
class EmailModel extends AssureModel {
    
    

    /**
     * 
     * @param int $offset
     * @param int $pageSize
     * @return array
     */
    public static function getList($offset = 0, $pageSize = NULL) 
    {
        $offset = $offset >= 0 ? $offset : 0;
        $sort = RequestUtil::get('folder', '');
        $sort = !empty($sort) ? $sort : 'gi.mail_date DESC';
        $sort = "ORDER BY $sort";
        $searchStr = RequestUtil::get('emailsearch');
        $searchTerms = explode(' ', trim($searchStr));
        
        
        $folder = RequestUtil::get('email_folder', '');
        
        $searchBys = '';
        $limitStr = '';
        

        $advance_search = RequestUtil::get('advance_search');
        $search_type = RequestUtil::get('search_type', '');
        $advance_sort = RequestUtil::get('advance_sort', '');
        if((!empty($advance_search) && !empty($search_type)) || !empty($advance_sort)) 
        {
            $ret_val = self::getAdvanceList($offset,$pageSize);
            return $ret_val;
        }
        
        //echo $_SESSION['ao_accountid'];die;
       
        $defaultwhere = "(gi.user_id = '{$_SESSION['ao_userid']}') OR (gi.is_shared='s' AND usr.account_id='{$_SESSION['ao_accountid']}')";
        if($folder=='Draft')
        {
            $defaultwhere .= " AND gi.delete_status=0 AND gi.label_name = 'DRAFT'";
        }
        else if($folder=='Outbox')
        {
            $defaultwhere .= " AND gi.delete_status=0 AND gi.label_name = 'OUTBOX'";
        }
        else if($folder=='Sent')
        {
            $defaultwhere .= " AND gi.delete_status=0 AND gi.label_name = 'SENT'";
        }
        else if($folder=='Archive')
        {
            $defaultwhere .= " AND gi.delete_status = 2";
        }
        else
        {
            $defaultwhere .= " AND gi.delete_status=0 AND gi.label_name != 'SENT' AND gi.label_name != 'DRAFT'";
        }
        
        if(!empty($searchStr)) 
        {
            $searchBys = array();
            foreach($searchTerms as $term) {
                $term = trim($term);
                $searchBys[] = "AND (
                                    gi.from_name LIKE '%$term%'
                                    OR gi.subject LIKE '%$term%'
                                    OR gi.to_mail LIKE '%$term%'
                                    OR j.job_number LIKE '%$term%'
                                    OR c.address LIKE '%$term%' 
                                    OR c.fname LIKE '%$term%'
                                    OR c.lname LIKE '%$term%'
                                    OR c.nickname LIKE '%$term%'
                                    OR c.zip LIKE '%$term%'
                                )";
            }
            $searchBys = implode(' ', $searchBys);
        }
            
        $searchBys = $defaultwhere . $searchBys;

        if($pageSize) {
            $limitStr = "LIMIT $offset, $pageSize";
        }
        

        $sql = "SELECT SQL_CALC_FOUND_ROWS gi.id
                FROM gmail_import gi
                JOIN users usr ON usr.user_id=gi.user_id
                LEFT JOIN jobs j ON j.job_id=gi.job_id
                LEFT JOIN customers c ON c.customer_id=j.customer_id
                LEFT JOIN job_type jt ON j.job_type = jt.job_type_id
                LEFT JOIN jurisdiction jur ON j.jurisdiction = jur.jurisdiction_id
                LEFT JOIN users u ON u.user_id = j.salesman
                $extraJoins
                WHERE 
                    $searchBys $extraSql $salesman $canvasser $referral $stage $creator $type $age $jurisdiction $taskType $warranty $insuranceProvider
                GROUP BY gi.id
                $closd_sql
                $sort
                $limitStr";
		//echo $sql;die;
        return DBUtil::queryToArray($sql);
    }

    public static function getEmailList()
    {
        $sql = "SELECT email from users WHERE is_active=1 AND account_id = '{$_SESSION['ao_accountid']}' ORDER BY email ASC";
        return DBUtil::queryToArray($sql);
    }

    public static function getLeadList()
    {
        $myUser = new User(RequestUtil::get('id'), FALSE);
        $sql = "SELECT jobs.job_id, jobs.job_number, customers.fname, customers.lname FROM `jobs`
LEFT JOIN customers ON jobs.customer_id = customers.customer_id";
        return DBUtil::queryToArray($sql);
    }

     public static function fromEmailDetails($from_email)
    {
       $sql = "SELECT * FROM `users` WHERE email='{$from_email}' LIMIT 1";
        return DBUtil::queryToArray($sql);
     

//         $result = mysql_query("SELECT * FROM `users` WHERE email='{$from_email}' LIMIT 1");
//         return $result;
// $row = mysql_fetch_assoc($result);
// return $row['fname'];
    }

    

    /**
     * 
     * @param int $offset
     * @param int $pageSize
     * @return array
     */
    public static function getAdvanceList($offset = 0, $pageSize = NULL) 
    {

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
    
    
}