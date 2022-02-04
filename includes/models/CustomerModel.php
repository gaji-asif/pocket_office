<?php

/**
 * @author cmitchell
 */
class CustomerModel extends AssureModel {
    
    private static $allJurisdictions;
    private static $allCustomers;
    private static $customerCache = array();
    
    /**
     * 
     * @param int, array $customerId
     * @param boolean $asLink
     * @return string
     */
    public static function getDisplayName($customerId = NULL, $useNickname = TRUE) {
        if(!is_array($customerId)) {
            if(isset(self::$customerCache[$customerId])) {
                $customer = self::$customerCache[$customerId];
            } else {
                $customer = DBUtil::getRecord('customers', $customerId);
                self::$customerCache[$customerId] = $customer;
            }
        } else {
            $customer = $customerId;
            $customerId = MapUtil::get($customer, 'customer_id');
        }
        
        
        if(!count($customer)) { return ''; }
        
        $displayName = $useNickname && MapUtil::get($customer, 'nickname') ? MapUtil::get($customer, 'nickname')
                        : MapUtil::get($customer, 'fname') . ' ' . MapUtil::get($customer, 'lname');
        
//        if(!RequestUtil::get('csv') && $asLink) {
//            return "<a href=\"/users.php?id=$customerId\" tooltip>$displayName</a>";
//        }
        
        return $displayName;
    }
    
    public static function getAllJurisdictions() {
        if(self::$allJurisdictions) { return self::$allJurisdictions; }
        
        $results = DBUtil::getAll('jurisdiction', 'location');
        self::$allJurisdictions = $results;

        return $results;
    }
    
    public static function getGPSCoords($address) {
        $encodedAddress = urlencode($address);
        $url = "http://maps.googleapis.com/maps/api/geocode/json?address=$encodedAddress&sensor=false";
        $json = file_get_contents($url);
        $data = json_decode($json);

        return array(
            $data->results[0]->geometry->location->lat,
            $data->results[0]->geometry->location->lng
        );
    }
    
    public static function getAllCustomers($firstLast = FALSE) {
        if(self::$allCustomers) { return self::$allCustomers; }
        
        $concatSql = $firstLast ? "CONCAT(fname, ' ', lname)" : "CONCAT(lname, ', ', fname)";
        $orderSql = $firstLast ? 'ORDER BY fname ASC' : 'ORDER BY lname ASC';
        $sql = "SELECT customer_id, fname, lname, nickname,
                CASE WHEN nickname IS NOT NULL AND nickname <> '' THEN nickname ELSE $concatSql END AS select_label
                FROM customers
                WHERE account_id = '{$_SESSION['ao_accountid']}'
                $orderSql";
        $results = DBUtil::queryToArray($sql);
        self::$allCustomers = $results;
        
        return $results;
    }
    
    /**
     * 
     * @param int $offset
     * @param int $pageSize
     * @return array
     */
    public static function getList($offset = 0, $pageSize = NULL) {
        $offset = $offset >= 0 ? $offset : 0;
        $extraSql = '';
        $searchBys = '';
        $limitStr = '';
        $sort = RequestUtil::get('sort');
        $sort = !empty($sort) ? $sort : 'ORDER BY c.lname ASC';
        $searchStr = RequestUtil::get('search');
        $searchTerms = explode(' ', trim($searchStr));

        if($pageSize) {
            $limitStr = "LIMIT $offset, $pageSize";
        }

        if(ModuleUtil::checkOwnership('view_customers')) {
            $extraSql .= "AND (c.user_id = '{$_SESSION['ao_userid']}' OR (s.user_id = '{$_SESSION['ao_userid']}' AND s.job_id = j.job_id) OR j.user_id={$_SESSION['ao_userid']} OR j.salesman={$_SESSION['ao_userid']} OR j.referral={$_SESSION['ao_userid']}) AND";
        }

        if(!empty($searchStr)) {
            $searchBys = array();
            foreach($searchTerms as $term) {
                $term = trim($term);
                $searchBys[] = "AND (
                                    c.fname LIKE '%$term%'
                                    OR c.lname LIKE '%$term%' 
                                    OR c.address LIKE '%$term%'
                                    OR c.lname LIKE '%$term%'
                                    OR c.nickname LIKE '%$term%'
                                    OR c.zip LIKE '%$term%'
                                )";
            }
            $searchBys = implode(' ', $searchBys);
        }

        $sql = "SELECT SQL_CALC_FOUND_ROWS c.customer_id
                FROM customers c
                JOIN users u ON u.user_id = c.user_id
                WHERE c.account_id = '{$_SESSION['ao_accountid']}'
                    $extraSql
                    $searchBys
                GROUP BY c.customer_id
                $sort
                $limitStr";
        return DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     * @param int $customerId
     * @return array
     */
    public static function getJobs($customerId) {
        return DBUtil::getRecords('jobs', $customerId, 'customer_id');
    }
    
    public static function getTimer($date,$customer) 
    {
        $sql = "SELECT SUM(TIMESTAMPDIFF(MINUTE,t1.start_time,t1.end_time)) as total_time
                FROM job_time_records as t1
                JOIN jobs as t2 on t2.job_id=t1.job_id
                WHERE t2.account_id = '{$_SESSION['ao_accountid']}' AND t2.salesman='{$customer}' AND t1.record_date='{$date}' GROUP BY t2.customer_id,t1.record_date";
        $results = DBUtil::queryToArray($sql);        
        return $results;
    }
    
}