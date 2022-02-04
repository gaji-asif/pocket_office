<?php

/**
 * @author cmitchell
 */
class UserModel extends AssureModel {
    
    private static $me;
    
    public static function getAllUserGroups() {
        return DBUtil::getAll('usergroups', 'label');
    }
    
    public static function get($userId) {
        return DBUtil::getRecord('users', $userId);
    }
    
    public static function getBrowsingHistory($limit = 20) {
        $limitStr = $limit ? "LIMIT $limit" : '';

        $sql = "SELECT browsing_id, title, icon, script, item_id, timestamp
                FROM browsing
                WHERE user_id = '{$_SESSION['ao_userid']}'
                GROUP BY CONCAT(script, item_id)
                ORDER BY MAX(timestamp) DESC
                $limitStr";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getNewJobs($limit = 20) {
        $sql = "SELECT j.job_id, j.job_number
                FROM jobs j
                LEFT JOIN job_view_history jvh on (jvh.job_id = j.job_id)
                WHERE j.salesman = '{$_SESSION['ao_userid']}'
                    AND jvh.timestamp IS NULL
                ORDER BY j.job_id ASC
                LIMIT $limit";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getSubscriptions($userId = NULL) {
        $userId = $userId ?: $_SESSION['ao_userid'];
        
        $sql = "SELECT *
                FROM subscribers
                WHERE user_id = '$userId'";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getConversationsBeingWatchedByType($type, $userId = NULL) {
        $userId = $userId ?: $_SESSION['ao_userid'];

        $sql = "SELECT *
                FROM watching
                WHERE type = '$type'
                    AND user_id = '$userId'";
        return DBUtil::queryToArray($sql);
    }
    
    public static function isWatchingConversation($conversationId, $type, $userId = NULL) {
        $userId = $userId ?: $_SESSION['ao_userid'];

        $sql = "SELECT *
                FROM watching
                WHERE type = '$type'
                    AND user_id = '$userId'
                    AND conversation_id = '$conversationId'
                LIMIT 1";
        return DBUtil::hasRows(DBUtil::query($sql));
    }
    
    public static function startWatchingConversation($conversationId, $type, $userId = NULL) {
        if (UserModel::isWatchingConversation($conversationId, $type, $userId)) {
            return TRUE;
        }

        $userId = $userId ?: $_SESSION['ao_userid'];
        $sql = "INSERT INTO watching (user_id, conversation_id, type)
                VALUES ($userId, '$conversationId', '$type')";
        return DBUtil::query($sql);
    }
    
    public static function stopWatchingConversation($conversationId, $type, $userId = NULL) {
        $userId = $userId ?: $_SESSION['ao_userid'];

        $sql = "DELETE FROM watching
                WHERE user_id = '$userId'
                    AND conversation_id = '$conversationId'
                    AND type = '$type'";
        return DBUtil::query($sql);
    }
    
    public static function getJournals($limit = 10, $userId = NULL) {
        $userId = $userId ?: $_SESSION['ao_userid'];

        $sql = "SELECT j.*, jobs.job_number, u.fname, u.lname
                FROM journals j, users u, jobs
                LEFT JOIN watching w ON w.conversation_id = jobs.job_id and w.type = 'job'
                LEFT JOIN subscribers s on s.job_id = jobs.job_id
                WHERE j.job_id = jobs.job_id
                    AND u.user_id = j.user_id
                    AND (
                            w.user_id = '$userId'
                            OR s.user_id = '$userId'
                        )
                GROUP BY j.journal_id
                ORDER BY j.timestamp DESC
                LIMIT $limit";
        return DBUtil::queryToArray($sql, 'journal_id');
    }
    
    public static function getDataForNotification($userId = NULL) {
        $userId = $userId ?: $_SESSION['ao_userid'];
        
        $sql = "SELECT fname, lname, email, username, password
                FROM users
                WHERE user_id = '$userId' limit 1";
        return DBUtil::fetchAssociativeArray(DBUtil::query($sql));
    }
    
    public static function updateSession($userId = NULL) {
        $userId = $userId ?: $_SESSION['ao_userid'];
        
        $sql = "DELETE FROM logged_in WHERE user_id = '$userId'";
        DBUtil::query($sql);

        $sql = "INSERT INTO logged_in (account_id, user_id, last_activity) 
                VALUES ('{$_SESSION['ao_accountid'] }', '$userId', NOW())";
        DBUtil::query($sql);
    }
    
    public static function userDeleted($userId = NULL) {
        $userId = $userId ?: $_SESSION['ao_userid'];
        
        $sql = "SELECT user_id
                FROM users
                WHERE user_id = '$userId'
                    AND is_deleted = 1";
        return DBUtil::hasRows(DBUtil::query($sql));
    }
    
    public static function numCurrentUsers() {
        $sql = "SELECT count(user_id)
                FROM users
                WHERE account_id = '{$_SESSION['ao_accountid']}'
                    AND is_deleted = 0";
        return DBUtil::queryToScalar($sql);
    }
    
    public static function licenseLimit() {
        $sql = "SELECT license_limit
                FROM accounts
                WHERE account_id = '{$_SESSION['ao_accountid']}'";
        return DBUtil::queryToScalar($sql);
    }
    
    public static function historyAccess($script) {
        $sql = "SELECT na.navaccess_id
                FROM nav_access na, navigation n
                WHERE n.source = '$script'
                    AND n.navigation_id = na.navigation_id
                    AND na.account_id='{$_SESSION['ao_accountid']}'
                    AND na.level='{$_SESSION['ao_level']}'
                LIMIT 1";
        return DBUtil::hasRows(DBUtil::query($sql));
    }
    
    public static function storeBrowsingHistory($title, $icon, $script, $itemId) {
        $title = RequestUtil::escapeString($title);
        $sql = "INSERT INTO browsing (user_id, title, icon, script, item_id, timestamp)
                VALUES ('{$_SESSION['ao_userid']}', '{$title}', '{$icon}', '{$script}', '{$itemId}', now())";
        return DBUtil::query($sql);
    }
    
    public static function emailExists($email) {
        $sql = "SELECT user_id
                FROM users
                WHERE email = '$email'
                LIMIT 1";
        return DBUtil::hasRows(DBUtil::query($sql));
    }
    
    public static function usernameExists($username) {
        $sql = "SELECT user_id
                FROM users
                WHERE username = '$username'
                LIMIT 1";
        return DBUtil::hasRows(DBUtil::query($sql));
    }

    public static function phoneExists($phone) {
        $sql = "SELECT user_id
                FROM users
                WHERE phone = '$phone'
                LIMIT 1";
        return DBUtil::hasRows(DBUtil::query($sql));
    }
    
    /**
     * 
     * @param int $userId
     * @param string $key
     * @return mixed
     */
    public static function getProperty($userId, $key) {
        return MapUtil::get(self::get($userId), $key);
    }
    
    public static function logAccess($userId) {
        $sql = "INSERT INTO access (user_id, account_id, timestamp, ip_address)
                VALUES ('$userId', '{$_SESSION['ao_accountid']}', now(), '{$_SERVER['REMOTE_ADDR']}')";
        return DBUtil::query($sql);
    }
    
    public static function loggedIn() {
        $sql = "SELECT username
                FROM users
                WHERE user_id = '{$_SESSION['ao_userid']}'
                    AND username = '{$_SESSION['ao_username']}'
                LIMIT 1";
        
        return (!AuthModel::sessionHasExpired() && DBUtil::hasRows(DBUtil::query($sql)));
    }
    
    public static function getAllByLevel($level, $returnInactive = FALSE, $firstLast = FALSE) {
        $concatSql = $firstLast ? "concat(fname, ' ', lname)" : "concat(lname, ', ', fname)";
        $extraSql = $returnInactive ? '' : 'AND is_active = 1 AND is_deleted = 0';
        $orderSql = $firstLast ? 'ORDER BY fname,lname ASC' : 'ORDER BY lname,fname ASC';

        if (is_array($level)) {
            $level = implode(',', $level);
        }

        $sql = "SELECT user_id, fname, lname, dba, is_active, is_deleted, $concatSql as select_label
                FROM users WHERE 1=1";

        if(!empty($level))
            $sql .= " AND level in ($level)";

        $sql .= " AND account_id = '{$_SESSION['ao_accountid']}'
                    $extraSql
                $orderSql";
        //echo $sql;die;
        return DBUtil::queryToArray($sql, 'user_id');
    }
    
    public static function getAll($returnInactive = FALSE, $firstLast = FALSE) {
        $concatSql = $firstLast ? "CONCAT(fname, ' ', lname)" : "CONCAT(lname, ', ', fname)";
        $extraSql = $returnInactive ? '' : 'AND is_active = 1 AND is_deleted = 0';
        $orderSql = $firstLast ? 'ORDER BY fname, lname ASC' : 'ORDER BY lname, fname ASC';
        $sql = "SELECT user_id, fname, lname, dba, is_active, is_deleted, $concatSql AS select_label
                FROM users
                WHERE account_id = '{$_SESSION['ao_accountid']}'
                    $extraSql
                $orderSql";
        return DBUtil::queryToArray($sql, 'user_id');
    }
    
    public static function getjobID($returnInactive = FALSE, $firstLast = FALSE, $job_id) {
        $concatSql = $firstLast ? "CONCAT(fname, ' ', lname)" : "CONCAT(lname, ', ', fname)";
        $extraSql = $returnInactive ? '' : 'AND is_active = 1 AND is_deleted = 0';
        $orderSql = $firstLast ? 'ORDER BY fname ASC' : 'ORDER BY lname ASC';
        $subscribeuser = UserModel::getuserID($job_id);
        
        foreach($subscribeuser as $sub) {
            $subcriber[] = $sub['user_id'];
        }
        
        $lastsub = $subcriber ? 'OR user_id IN('.implode(',',$subcriber).')' : '';
        $sql = "SELECT user_id, fname, lname, dba, is_active, is_deleted, $concatSql AS select_label
                FROM users
                WHERE account_id = '{$_SESSION['ao_accountid']}' AND journal=1 $lastsub
                    $extraSql
                $orderSql";
        return DBUtil::queryToArray($sql, 'user_id');
    }
    
    public static function getuserID($job_id) {
         
      $sql = "SELECT user_id
                FROM subscribers
                WHERE job_id =$job_id ";
        return DBUtil::queryToArray($sql);
    }

    public static function getAllLevels() {
        return DBUtil::getAll('levels');
    }
    
    public static function fetchNavAccess() {
        $sql = "SELECT n.source
                FROM nav_access na, navigation n
                WHERE na.account_id = '{$_SESSION['ao_accountid']}'
                    AND na.level = '{$_SESSION['ao_level']}'
                    AND na.navigation_id = n.navigation_id";
        $modules = DBUtil::queryToArray($sql);

        $_SESSION['ao_nav_access'] = array();
        foreach($modules as $module) {
            $_SESSION['ao_nav_access'][] = $module['source'];
        }
    }
    
    /**
     * 
     * @param string $source
     * @param boolean $showAlert
     * @return boolean
     */
    public static function checkNavAccess($source, $showAlert = FALSE) {
        $sql = "SELECT * 
                FROM nav_access na, navigation n
                WHERE na.account_id = '{$_SESSION['ao_accountid']}'
                    AND n.source = '$source'
                    AND na.level = '{$_SESSION['ao_level']}'
                    AND na.navigation_id = n.navigation_id
                LIMIT 1";
        $hasAccess = self::isAuthenticated(FALSE) && DBUtil::hasRows(DBUtil::query($sql));
        
        if($showAlert && !$hasAccess) {
            echo AlertUtil::generate(array('You do not have access to this.'));
        }

        return $hasAccess;
    }
    
    /**
     * 
     * @param boolean $redirectToLogin
     * @return boolean
     */
    public static function isAuthenticated($redirectToLogin = TRUE) {
        $sql = "SELECT username
                FROM users
                WHERE user_id = '{$_SESSION['ao_userid']}'
                    AND username = '{$_SESSION['ao_username']}'
                LIMIT 1";
        $results = DBUtil::query($sql);
      
        $authenticated = !(!$results || !DBUtil::hasRows($results) || AuthModel::sessionHasExpired());
        
        if(!$redirectToLogin) {
            return $authenticated;
        } else if (!$authenticated && $redirectToLogin) {
            echo ViewUtil::loadView('js/top-redirect', array('url' => '/'));
            die();
        }
        
    }
    
    public static function fetchWidgetAccess() {
        if (!ModuleUtil::checkAccess('view_schedule')) {
            $_SESSION['ao_widget_today'] = '0';
        }
        if (!ModuleUtil::checkAccess('view_announcements')) {
            $_SESSION['ao_widget_announcements'] = '0';
        }
        if (!ModuleUtil::checkAccess('view_documents')) {
            $_SESSION['ao_widget_documents'] = '0';
        }
        if (!ModuleUtil::checkAccess('view_jobs')) {
            $_SESSION['ao_widget_urgent'] = '0';
            $_SESSION['ao_widget_bookmarks'] = '0';
            $_SESSION['ao_widget_journals'] = '0';
        }
    }
    
    public static function getStageAdvancementByLevel($levelId, $ignoreCache = FALSE) {
        if($ignoreCache) {
            RequestUtil::set('ignore_cache', 1);
        }
        return DBUtil::getRecords('stage_access', $levelId, 'level_id');
    }
    
    public static function getStageAdvancementByUser($userId, $ignoreCache = FALSE) {
        if($ignoreCache) {
            RequestUtil::set('ignore_cache', 1);
        }
        return DBUtil::getRecords('user_stage_access', $userId, 'user_id');
    }
    
    public static function getEmailStageNotificationByUser($userId) {
        return DBUtil::getRecords('stage_notifications', $userId, 'user_id');
    }
    
    public static function getSmsStageNotificationByUser($userId) {
        return DBUtil::getRecords('stage_notifications_sms', $userId, 'user_id');
    }
    
    public static function getUserDetailsForLogger() {
        return "{$_SESSION['ao_fname']} {$_SESSION['ao_lname']} (ID: {$_SESSION['ao_userid']})";
    }
    
    public static function getMe() {
        if(!self::$me) {
            self::$me = new User($_SESSION['ao_userid'], FALSE);
        }
        return self::$me;
    }
    
    /**
     * 
     * @param string $string
     * @return array
     */
    public static function getMentions($string) {
        preg_match_all("/@(\w+)/i", $string, $matches);
        $users = UserModel::getByUsernames(MapUtil::get($matches, 0), TRUE);
        return $users;
    }
    
    /**
     * 
     * @param array $usernames
     * @param boolean $removeAt
     * @return array
     */
    public static function getByUsernames($usernames, $removeAt = FALSE) {
        $usernames = !$removeAt ? $usernames : StrUtil::remoteAtsFromArray($usernames);
        $usernames = "'" . implode("','", $usernames) . "'";
        $sql = "SELECT *
                FROM users
                WHERE account_id = '{$_SESSION['ao_accountid']}'
                    AND username IN($usernames)";
        return DBUtil::queryToArray($sql, 'username');
    }
    
    //------------------------------------------------------------------------
    //------------------------------------------------------------------------
    public static function isSystemUser() {
        //echo "<pre>"; print_r($_SESSION['ao_level']); echo "</pre>";
        if($_SESSION['ao_level'] == 1) {
        if($_SESSION['ao_system_user']) {
            return TRUE;
        }
        return count(DBUtil::getRecord('users', $_SESSION['ao_userid']));
        }
    }
    
    public static function systemUserCheck() {
        if(!self::isSystemUser()) {
            header('Location: ' . ROOT_DIR);
        }
    }

    public static function getAllInSystem() {
        $sql = "SELECT *
                FROM users u
                LEFT JOIN accounts a ON a.account_id = u.account_id
                LEFT JOIN levels l ON l.level_id = u.level";
        return DBUtil::queryToArray($sql, 'user_id');
    }


    /* Newly Added
        Get All accounts
    */
    public static function getAllAccounts($where='') {
       
        $sql = "SELECT * FROM accounts ".$where;
        
        return DBUtil::queryToArray($sql, 'account_name');
    }

     /* Newly Added
        Get All Level
    */
    public static function getAllLevel() {
       
        $sql = "SELECT * FROM levels";
        
        return DBUtil::queryToArray($sql, 'level');
    }

    /*
        Get User per account Sofikul
    */
    public static function getUserByAccount($accountid,$level) 
    {        
        $sql = "SELECT *
                FROM users u
                LEFT JOIN accounts a ON a.account_id = u.account_id
                LEFT JOIN levels l ON l.level_id = u.level ";
        
        $where='';
        if(!empty($accountid))
            $where = " WHERE u.account_id = '$accountid'";
        if(!empty($level))
            $where = " WHERE u.level = '$level'";
        if(!empty($accountid) && !empty($level))
            $where = "WHERE u.account_id = '$accountid' AND u.level = '$level'";

        $sql .=$where;

        return DBUtil::queryToArray($sql, 'username');
    }

     /*
        Get User per account Sofikul
    */
    public static function getCompanyByUser($user_id) 
    {        
        $sql = "SELECT *
                FROM company_user c
                LEFT JOIN accounts a ON a.account_id = c.account_id";
        
        $where='';
        if(!empty($user_id))
            $where = " WHERE c.user_id = '$user_id'";        

        $sql .=$where;

        return DBUtil::queryToArray($sql, 'account_id');
    }

    /*
        Get User per account Sofikul
    */
    public static function getUserByLevel($level) 
    {        
        $sql = "SELECT *
                FROM users u
                LEFT JOIN accounts a ON a.account_id = u.account_id
                LEFT JOIN levels l ON l.level_id = u.level ";
        
        $where='';        
        if(!empty($level))
            $where = " WHERE u.level = '$level'";        

        $sql .=$where;

        return DBUtil::queryToArray($sql, 'username');
    }

    /*
        Get Company per user Sofikul
    */
    public static function getAssComp($where='') {
       
        $sql = "SELECT * FROM company_user ".$where;

        $acc_arr=array();
        $ass_comp=DBUtil::queryToArray($sql, '');
        foreach($ass_comp as $row)
        {
            $acc_arr[]=$row['account_id'];
        }        
        return $acc_arr;
    }


    public static function getMeasurmentList($job_id)
    {
        $sql = "SELECT *
                FROM measurment
                WHERE job_id = $job_id
                ORDER BY created_at desc";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getContactsHeader()
    {
        $account_id = $_SESSION['ao_accountid'];
        $sql = "SELECT * FROM contacts
                WHERE account_id = $account_id
                ORDER BY contact_name asc";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getContactsList($job_id)
    {
        $sql = "SELECT job_contacts.*,contact_name as contact_header,fname,lname
                FROM job_contacts
                JOIN contacts ON contacts.contact_header_id = job_contacts.contact_header_id
                LEFT JOIN users ON users.user_id = job_contacts.created_by
                WHERE job_id = $job_id
                ORDER BY created_at desc";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getInvoiceList($job_id)
    {
        $sql = "SELECT *
                FROM invoices
                WHERE job_id = $job_id
                ORDER BY invoice_id desc";
        return DBUtil::queryToArray($sql);
    }
    
    public static function gnerateInvoice($job_id)
    {
        $sql="SELECT invoice_id FROM invoices ORDER BY invoice_id DESC limit 1";
        return DBUtil::queryToArray($sql);
    }
    public static function getSalesmanDetails($user_id)
    {
        $sql = "SELECT * FROM  user_invoice_header WHERE user_id='{$user_id}'";
        return DBUtil::queryToArray($sql);
    }
    
    
}