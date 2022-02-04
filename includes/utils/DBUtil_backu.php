<?php

/**
 * @author cmitchell
*/

class DBUtil extends AssureUtil 
{    

    private static $link;

    private static $databases;

    private static $schemaCache = array();

    private static $cache = array();    

    public static function connect($databaseName, $log = TRUE) 
    {       
        $databaseCreds = MapUtil::get(self::$databases, $databaseName);
        self::$link = @mysqli_connect($databaseCreds['host'], $databaseCreds['user'], $databaseCreds['password']);

        if(!self::$link) 
        {
            if($log) 
            {
                LogUtil::getInstance()->logNotice("Failed database connection - Invalid connection variables: {$databaseCreds['host']}, {$databaseCreds['user']}, {$databaseCreds['password']}");
            }
            return self::$link;
        }

        //select db
        mysqli_select_db($databaseName, self::$link);        

        //rb
        //R::setup("mysql:host={$databaseCreds['host']};dbname=$databaseName", $databaseCreds['user'], $databaseCreds['password']);
      
        return self::$link;
    }

    

    /**
     * 
     * @param string $sql
     * @return Resource
     */
    public static function query($sql) 
    {
        //custom DB Conection 
        $databaseCreds = MapUtil::get(self::$databases, 'workflow_performance');
        self::$link = @mysqli_connect($databaseCreds['host'], $databaseCreds['user'], $databaseCreds['password']);
        mysqli_select_db($databaseName, self::$link);     

        if(!self::$link) {
            return FALSE;
        }        
      
        //set timezone based on offset
        self::setDbTimeZone();

        /*----Set The global Database----------------*/
        $gdb=$GLOBALS['databases'];

        //DBUtil::connect($_SESSION['database_name']);

        

        if(is_array($gdb))
        {
                $db_name=$gdb['workflow_performance']['database'];
                mysqli_select_db($db_name); 
        }
        
        // echo "<pre>";print_r($gdb);die();

        /*-----------------------------------------*/       
        $results = mysqli_query($sql, self::$link) or mysqli_error();

               /* if (!empty($results)) { 

                    die('Invalid query: ' . mysqli_error());

                }*/
      
       if(!$results) {

          self::sendQueryErrorNotification($sql, mysqli_error());

           LogUtil::getInstance()->logNotice("Failed database query - {$_SESSION['database_name']} - " . mysqli_error() . ' - ' . $sql);

        }
        
        return $results;

    }
    

    /**
     * 
     * @param Resource $results
     * @param mixed $key
     * @return array
     */
    public static function convertResultsToArray($results, $key = NULL) 
    {
        $resultsArray = array();

        if(!$results) 
        {
            return $resultsArray;
        }

        while ($row = self::fetchAssociativeArray($results)) 
        {
            if(!$row) 
            {
                continue;
            }
           
            if(!isset($row[$key])) 
            {
                $resultsArray[] = $row;
            }
            else 
            {
                $resultsArray[$row[$key]] = $row;
            }
        }

        return $resultsArray;

    }
    

    /**
     * 
     * @param Resource $results
     * @param mixed $key
     * @return array
     */
    public static function pluck($results, $key) 
    {
        $resultsArray = self::convertResultsToArray($results);        
        return MapUtil::pluck($resultsArray, $key);
    } 


    /**
     * 
     * @param Resource $results
     * @param mixed $defaultValue
     * @return mixed
     */
    public static function fetchAssociativeArray($results, $defaultValue = NULL) {

        if(!$results || !@mysqli_num_rows($results)) 
        {
            return $defaultValue;
        }        

        return mysqli_fetch_assoc($results);

    }  


    /**
     * 
     * @param Resource $results
     * @return boolean
     */
    public static function hasRows($results) 
    {
        return !$results ? FALSE : (!@mysqli_num_rows($results) ? FALSE : TRUE);
    }    

    /**
     * 
     * @param Resource $results
     * @return int
     */
    public static function numRows($results) 
    {
        return !$results ? 0 : @mysqli_num_rows($results);
    }


    /**
     * 
     * @param string $sql
     */
    public static function handleQueryError($sql) 
    {
        //get new mail object
        $mail = new PHPMailer();

        //add from, to, subject, body
        $mail->SetFrom(ALERTS_EMAIL, APP_NAME . " Alerts");

        $mail->AddAddress("cbm3384@gmail.com", "Christopher Mitchell");

        $mail->Subject = APP_NAME . " Query Error";

        $mail->MsgHTML("Query Error<br><br>$sql<br><br>" . mysqli_errno(self::$link) . " : " . mysqli_error(self::$link) . "<br><br>" . DateUtil::formatDateTime());

        //send

        $mail->Send();
    }    

    /**
     * 
     * @param string $table
     * @return array
     */
    public static function getTableFields ($table) 
    {
        $cachedData = MapUtil::get(self::$schemaCache, $table);

        if($cachedData) { return $cachedData; }        

        self::$schemaCache[$table] = DBUtil::queryToArray("SHOW COLUMNS FROM $table");

        return self::$schemaCache[$table];
    }
    

    /**
     * 
     * @param string $table
     * @param int $id
     * @param string $field
     * @return array
     */
    public static function getRecords($table, $id = NULL, $field = NULL) 
    {
        return self::getRecord($table, $id, $field, $onlyOne = FALSE);
    }
    

    /**
     * 
     * @param string $table
     * @param int $id
     * @param string $field
     * @param boolean $onlyOne
     * @return array
     */
    public static function getRecord($table, $id = NULL, $field = NULL, $onlyOne = TRUE) 
    {
        $tableFields = MapUtil::pluck(DBUtil::getTableFields($table), 'Field');

        $id = $id ?: RequestUtil::get('id');

        $field = $field ?: $tableFields[0];

        //get cache key from method args

        $cacheKey = CacheUtil::getCacheKey($table, $id, $field, $onlyOne);        

        //should we cache?

        if(!RequestUtil::get('ignore_cache')) 
        {
            $cachedData = MapUtil::get(self::$cache, $cacheKey);
            if($cachedData) { return $cachedData; }
        } 
        else 
        {
            RequestUtil::delete('ignore_cache');
        }        

        $accountSql = in_array('account_id', $tableFields) && !empty($_SESSION['ao_accountid'])

                        ? "AND account_id = '{$_SESSION['ao_accountid']}'" : '';

        $limitStr = $onlyOne ? 'LIMIT 1' : '';

        $activeStr = in_array('active', $tableFields) ? "AND active = '1'" : '';

        $sql = "SELECT *

                FROM $table

                WHERE $field = '$id'

                    $accountSql

                    $activeStr

                $limitStr";

        self::$cache[$cacheKey] = $onlyOne ? self::fetchAssociativeArray(DBUtil::query($sql), array())

                        : DBUtil::queryToArray($sql);        

        return self::$cache[$cacheKey];

    }

    

    /**
     * 
     * @param string $table
     * @param int $id
     * @param string $field
     * @param boolean $onlyOne
     * @return Result
     */
    public static function deleteRecord($table, $id = NULL, $field = NULL, $onlyOne = TRUE) 
    {
        $tableFields = MapUtil::pluck(DBUtil::getTableFields($table), 'Field');

        $id = $id ?: RequestUtil::get('id');

        $field = $field ?: $tableFields[0];

        $accountSql = in_array('account_id', $tableFields) && !empty($_SESSION['ao_accountid'])

                        ? "AND account_id = '{$_SESSION['ao_accountid']}'" : '';

        $limitStr = $onlyOne ? 'LIMIT 1' : '';

        $sql = "DELETE FROM $table

                WHERE $field = '$id'

                    $accountSql

                $limitStr";

        return DBUtil::query($sql);

    }
    

    /**
     * 
     * @param string $table
     * @param string $orderColumn
     * @param string $order
     * @param string $key
     * @return array
     */
    public static function getAll($table, $orderColumn = NULL, $order = NULL, $key = NULL) 
    {
        //get cache key from method args
        $cacheKey = CacheUtil::getCacheKey($table, $orderColumn, $order, $key);        

        //should we cache?

        if(!RequestUtil::get('ignore_cache')) 
        {
            $cachedData = MapUtil::get(self::$cache, $cacheKey);
            if($cachedData) { return $cachedData; }
        } 
        else 
        {
            RequestUtil::delete('ignore_cache');
        }        

        $tableFields = MapUtil::pluck(DBUtil::getTableFields($table), 'Field');

        $accountSql = in_array('account_id', $tableFields) && !empty($_SESSION['ao_accountid'])

                        ? "AND account_id = '{$_SESSION['ao_accountid']}'" : '';

        $orderSql = $orderColumn && in_array($orderColumn, $tableFields)

                    ? "ORDER BY $orderColumn " . ($order ?: '') : '';

        $activeStr = in_array('active', $tableFields) ? "AND active = '1'" : '';

        $sql = "SELECT *

                FROM $table

                WHERE 1 = 1

                    $accountSql

                    $activeStr

                $orderSql";

        $results = DBUtil::queryToArray($sql, $key);        

        //cache and return

        self::$cache[$cacheKey] = $results;

        return $results;
    }
    

    /**
     * 
     * @param Resource $link
     */
    public static function setLink($link) 
    {
        self::$link = $link;
    }    

    /**
     * 
     * @return Resource
     */
    public static function getLink() 
    {      
        return self::$link;
    }    

    /**
     * 
     */
    public static function setDbTimeZone() 
    {
        if (!isset($_SESSION['ao_offset'])) 
        {
            $_SESSION['ao_offset'] = DateUtil::getOffset();
        }
       //print_r($_SESSION);
        //set
        $sql = "SET time_zone = '{$_SESSION['ao_offset']}'";
        mysqli_query($sql, self::$link);
    }

    

    /**
     * 
     * @return int
     */
    public static function getInsertId() 
    {
        return DBUtil::queryToScalar('SELECT LAST_INSERT_ID()');
        //return mysqli_insert_id(self::$link);
    }
    

    /**
     * 
     * @param Resource $results
     * @return mixed
     */
    public static function fetchScalar($results, $defaultValue = NULL) 
    {
        $arr = self::fetchAssociativeArray($results);
		return is_array($arr) && count($arr) ? array_shift($arr) : $defaultValue;
    }
    

    /**
     * 
     * @param array $databases
     */
    public static function setDatabases($databases) 
    {
        self::$databases = $databases;
    }    

    /**
     * 
     * @return array
     */
    public static function getDatabases() 
    {
        return self::$databases;
    }


    /**
     * 
     * @param string $sql
     * @param string $error
     */
    public static function sendQueryErrorNotification($sql, $error) 
    {
        if(defined('DISABLE_EMAIL')) { return; }

        //get new mail object

        $mail = new PHPMailer();        

        $date = date('r');

        $body = "

            Failed database query:<br /><br />

            Username: {$_SESSION['ao_username']} <br />

            First Name: {$_SESSION['ao_fname']} <br />

            Username: {$_SESSION['ao_lname']} <br /><br />

            Date: $date<br /><br />

            $error<br /><br />

            <pre>$sql</pre><br /><br />

            Stack Trace: <br /><pre>" . var_export(debug_backtrace(), TRUE) . '</pre>';

        //add from, to, subject, body

        $mail->SetFrom(ALERTS_EMAIL, APP_NAME . " Alerts");

        $mail->AddAddress("cbm3384@gmail.com", "Christopher Mitchell");

        $mail->Subject = APP_NAME . " Alerts: Failed Database Query";

        $mail->MsgHTML($body);

        //send
        $mail->Send();

    }    

    /**
     * 
     * @param string $sql
     * @param string $key
     * @return array
     */
    public static function queryToArray($sql, $key = NULL) 
    {
        return self::convertResultsToArray(self::query($sql), $key);
    }    

    /**
     * 
     * @param string $sql
     * @param string $key
     * @return array
     */
    public static function queryToMap($sql, $key = NULL) 
    {
        return self::fetchAssociativeArray(self::query($sql), $key);
    }    

    /**
     * 
     * @param string $sql
     * @return mixed
     */
    public static function queryToScalar($sql, $defaultValue = NULL) 
    {
        return self::fetchScalar(self::query($sql), $defaultValue);
    }    

    /**
    * 
    * @return int
    */
    public static function getLastRowsFound() 
    {
        return self::queryToScalar('SELECT FOUND_ROWS() as count');
    }

}