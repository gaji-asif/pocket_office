<?php

/**
 * @author cmitchell
 */
class AuthModel extends AssureModel {
    
    public static function processLogin() {
        $account = RequestUtil::get('account');
        $username = RequestUtil::get('username');
        $password = RequestUtil::get('password');
        $databases = DBUtil::getDatabases();
       
        //print '<pre>';print_r($databases);die;
        if (is_array($databases)) {
            foreach($databases as $databaseName => $database) {
                LogUtil::getInstance()->logNotice("Try database - $databaseName - {$_SERVER['SERVER_NAME']}");
                //print_r($databaseName);
                //echo $database['database'];
                $link = DBUtil::connect($databaseName);
                //echo $username.' '.$password.' '. $account;
                if ($link) {
                    $results = self::attemptToLogin($username, $password, $account);
                    /*--------------------------------*/
                    
                    /*------------------------------------*/
                   //echo "<pre>";print_r($results);die(); 
                    if ($results) {
                        $_SESSION = $results;
                        $_SESSION['database_name'] = $databaseName;
                        
                        //set cookies for easy login
                        self::setLoginCookies();

                        return TRUE;
                    }
                }
            }
        } else {
            LogUtil::getInstance()->logNotice('Failed login - empty database array');
        }
        return FALSE;
    }
    
    public static function attemptToLogin($username, $password, $account) {
	if($account!=''){
        $sql = "SELECT users.user_id AS ao_userid, users.username AS ao_username, users.fname AS ao_fname, users.lname AS ao_lname, users.dba AS ao_dba,
                DATE_FORMAT(access.timestamp, '%c/%e %k:%i') AS ao_lastvisit, users.level AS ao_level, users.is_active, users.is_deleted,
                accounts.account_name AS ao_accountname, users.account_id AS ao_accountid, users.founder AS ao_founder, settings.num_results AS ao_numresults,
                settings.browsing_results AS ao_browsingresults, settings.refresh AS ao_refresh, settings.widget_today AS ao_widget_today,
                settings.widget_announcements AS ao_widget_announcements, settings.widget_documents AS ao_widget_documents,
                settings.widget_bookmarks AS ao_widget_bookmarks, settings.widget_urgent AS ao_widget_urgent, settings.widget_inbox AS ao_widget_inbox,
                settings.widget_journals AS ao_widget_journals, accounts.logo AS ao_logo, accounts.job_unit AS ao_jobunit, accounts.is_active AS account_is_active,
                users.office_id AS ao_officeid, levels.level as ao_levelname
                FROM accounts, levels, users
                LEFT JOIN settings ON users.user_id = settings.user_id
                LEFT JOIN access ON access.user_id = users.user_id
                WHERE users.username = '$username'
                        AND users.password = '$password'
                        AND accounts.account_name ='$account'
                        AND accounts.account_id = users.account_id
                        AND levels.level_id = users.level
                ORDER BY access.access_id DESC LIMIT 1";
		}
		else{
			$sql = "SELECT users.user_id AS ao_userid, users.username AS ao_username, users.fname AS ao_fname, users.lname AS ao_lname, users.dba AS ao_dba,
					DATE_FORMAT(access.timestamp, '%c/%e %k:%i') AS ao_lastvisit, users.level AS ao_level, users.is_active, users.is_deleted,
					accounts.account_name AS ao_accountname, users.account_id AS ao_accountid, users.founder AS ao_founder, settings.num_results AS ao_numresults,
					settings.browsing_results AS ao_browsingresults, settings.refresh AS ao_refresh, settings.widget_today AS ao_widget_today,
					settings.widget_announcements AS ao_widget_announcements, settings.widget_documents AS ao_widget_documents,
					settings.widget_bookmarks AS ao_widget_bookmarks, settings.widget_urgent AS ao_widget_urgent, settings.widget_inbox AS ao_widget_inbox,
					settings.widget_journals AS ao_widget_journals, accounts.logo AS ao_logo, accounts.job_unit AS ao_jobunit, accounts.is_active AS account_is_active,
					users.office_id AS ao_officeid, levels.level as ao_levelname
					FROM accounts, levels, users
					LEFT JOIN settings ON users.user_id = settings.user_id
					LEFT JOIN access ON access.user_id = users.user_id
					WHERE users.username = '$username'
							AND users.password = '$password'
						 	AND accounts.account_id = users.account_id
							AND levels.level_id = users.level
					ORDER BY access.access_id DESC LIMIT 1";
		}

        $rs=DBUtil::query($sql);
        $result=DBUtil::fetchAssociativeArray($rs);
        
        return $result;

        //return DBUtil::fetchAssociativeArray(DBUtil::query($sql));
    }
    
    public static function setSystemUserAccess() {
        $_SESSION['ao_system_user'] = FALSE;
        if (UserModel::isSystemUser()) {
            $_SESSION['ao_system_user'] = TRUE;

            //set true user data to remember for switch back
            $_SESSION['ao_true_user_data']['user_id'] = $_SESSION['ao_userid'];
            $_SESSION['ao_true_user_data']['username'] = $_SESSION['ao_username'];
            $_SESSION['ao_true_user_data']['fname'] = $_SESSION['ao_fname'];
            $_SESSION['ao_true_user_data']['lname'] = $_SESSION['ao_lname'];
        }
    }
    
    public static function processForgotPassword($email, $account) {
        if (is_array($GLOBALS['databases'])) {
            foreach ($GLOBALS['databases'] as $databaseName => $database) {
                $link = DBUtil::connect($databaseName);
                    LogUtil::getInstance()->logNotice("Attempting to connect to database '$databaseName'");
                if ($link) {
                    LogUtil::getInstance()->logNotice("Connected to database '$databaseName'");
                    $results = self::attemptToRetrieveCredentials($email, $account);
                    if($results) { return $results; }
                }
            }

        }
        LogUtil::getInstance()->logNotice('Failed password recovery - empty database array');
        return FALSE;
    }
    
    public static function attemptToRetrieveCredentials($email, $account) {
        $sql = "SELECT users.user_id, users.username, users.password as pw, users.fname, users.lname, accounts.account_name as account
                FROM users, accounts
                WHERE users.email='$email'
                    AND accounts.account_id = users.account_id
                    AND users.is_active = 1
                    AND users.is_deleted <> 1
                LIMIT 1";
        $results = DBUtil::query($sql);
        if (!DBUtil::hasRows($results)) {
            LogUtil::getInstance()->logNotice("Attempt to retrieve creds failed (" . DBUtil::numRows($results) . "): $sql");
            return FALSE;
        }
        return $results;
    }
    
    public static function setLoginCookies() {
         
        //set account cookie for 30 days
        setcookie('ao_accountname', $_SESSION['ao_accountname'], time() + (86400 * 30));

        //set username cookie for 30 days
        setcookie('ao_username', $_SESSION['ao_username'], time() + (86400 * 30));
    }

    public static function getSessionLength() {
        
        return AccountModel::getmetaValue('user_session_timeout', time() + time());
        // $length = 86400 * 30;
        // return $length;
    }
    
    public static function setSessionStart() {
        $_SESSION['ao_sessionstart'] = time();
    }
    
    public static function sessionHasExpired() {
        

        //if session var isn't set, set it now.
        if(!isset($_SESSION['ao_sessionstart'])) { self::setSessionStart(); }
        
        $hasTimedOut = isset($_SESSION['ao_sessionstart']) ? ($_SESSION['ao_sessionstart'] + self::getSessionLength() < time()) : false;
        
        if($hasTimedOut) {
            logout(TRUE);
        }
        return $hasTimedOut;
    }
    
}