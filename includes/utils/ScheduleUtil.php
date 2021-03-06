<?php
/**
 * @author cmitchell
 */
class ScheduleUtil extends AssureUtil {
    
    /**
     * 
     * @param string $date
     * @param int $userId
     * @param int $accountId
     * @return array
     */
    public static function getRepairs($date = NULL, $userId = NULL, $accountId = NULL, $customer = NULL, $provider = NULL) {
        $queryDate = DateUtil::formatMySQLDate($date ?: DateUtil::formatMySQLDate());
        $accountId = $accountId ?: $_SESSION['ao_accountid'];
        $userId = $userId ?: $_SESSION['ao_userid'];
        
        $extraSql = '';
        if (ModuleUtil::checkOwnership('view_schedule', $userId, $accountId)) {
            $extraSql = "AND (
                            (r.user_id='$userId' AND r.job_id=j.job_id)
                            OR r.user_id='$userId'
                            OR r.contractor = '$userId'
                            OR j.user_id = '$userId'
                            OR j.referral='$userId'
                        )";
        }

        if(!empty($customer))
        {
            $extraSql .= $customer;
        }

        if(!empty($provider))
        {
            $extraSql .= $provider;
        }

        $sql = "SELECT j.job_id, c.fname AS customer_fname, c.lname AS customer_lname, c.address, c.city,
                        j.job_id, r.repair_id, j.job_number, u.fname, u.lname, r.contractor,
                        u.lname AS contractor_lname, u.fname AS contractor_fname,
                        u.dba, ft.fail_type, u_s.fname AS salesman_fname, u_s.lname AS salesman_lname,
                        j.salesman, r.completed
                FROM jobs j
                JOIN repairs r ON r.job_id = j.job_id
                JOIN fail_types ft ON ft.fail_type_id = r.fail_type
                JOIN customers c ON c.customer_id = j.customer_id
                LEFT JOIN users u ON r.contractor = u.user_id
                LEFT JOIN users u_s ON u_s.user_id = j.salesman
                WHERE j.account_id='$accountId'
                    AND r.startdate = '$queryDate'
                    $extraSql 
                GROUP BY r.repair_id";
        //echo $sql;
        return DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     * @param string $date
     * @param int $userId
     * @param int $taskTypeId
     * @param int $accountId
     * @return array
     */
    public static function getTasks($date = NULL, $userId = NULL, $taskTypeId = NULL, $accountId = NULL, $customer = NULL, $provider = NULL) {
        $queryDate = DateUtil::formatMySQLDate($date ?: DateUtil::formatMySQLDate());
        $accountId = $accountId ?: $_SESSION['ao_accountid'];
        $userId = $userId ?: $_SESSION['ao_userid'];

        $extraSql = '';
        $extraSql .= !empty($taskTypeId) ? "AND t.task_type = '$taskTypeId'" : '';
        if (ModuleUtil::checkOwnership('view_schedule', $userId, $accountId)) {
            $extraSql .= "AND (
                            t.user_id = '$userId'
                            OR t.contractor = '$userId'
                            OR j.user_id = '$userId'
                            OR j.referral = '$userId'
                        )";
        }

        if(!empty($customer))
        {
            $extraSql .= $customer;
        }

        if(!empty($provider))
        {
            $extraSql .= $provider;
        }

        $sql = "SELECT j.job_id, c.fname AS customer_fname, c.lname AS customer_lname, c.address, c.city,
                        j.salesman, u_s.fname AS salesman_fname, u_s.lname AS salesman_lname,
                        t.duration, j.job_id, j.job_number, tt.task,
                        t.task_id, tt.color, u.fname AS contractor_fname, u.lname AS contractor_lname,
                        t.contractor, u.dba, jur.midroof, t.completed, t.paid, jur.midroof_timing, jur.location
                FROM jobs j
                JOIN tasks t ON t.job_id = j.job_id
                JOIN task_type tt ON tt.task_type_id = t.task_type
                JOIN customers c ON c.customer_id = j.customer_id
                LEFT JOIN jurisdiction jur ON jur.jurisdiction_id = j.jurisdiction
                LEFT JOIN users u ON t.contractor = u.user_id
                LEFT JOIN users u_s ON u_s.user_id = j.salesman
                WHERE j.account_id = '$accountId'
                    AND t.start_date <= '$queryDate'
                    AND DATE_ADD(t.start_date, INTERVAL (t.duration - 1) day) >= '$queryDate'
                   $extraSql
                GROUP BY t.task_id";
        return DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     * @param string $date
     * @param int $userId
     * @param int $accountId
     * @return array
     */
    public static function getEvents($date = NULL, $userId = NULL, $accountId = NULL, $customer = NULL, $provider = NULL) {
        $queryDate = DateUtil::formatMySQLDate($date ?: DateUtil::formatMySQLDate());
        $accountId = $accountId ?: $_SESSION['ao_accountid'];
        $userId = $userId ?: $_SESSION['ao_userid'];

        $extraSql = '';
        if (ModuleUtil::checkOwnership('view_schedule', $userId, $accountId)) {
            $extraSql = "AND ((e.global = 0 AND e.user_id = '$userId') OR e.global = 1)";
        }
        $sql = "SELECT e.event_id, e.date, e.title, e.all_day
                FROM events e
                WHERE '$queryDate' BETWEEN CAST(e.date AS date) AND e.end_date
                    AND e.account_id = '$accountId'
                    $extraSql
                ORDER BY CAST(e.date AS time) ASC";
        return DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     * @param string $date
     * @param int $userId
     * @param int $accountId
     * @return array
     */
    public static function getAppointments($date = NULL, $userId = NULL, $accountId = NULL, $customer = NULL, $provider = NULL) {
        $queryDate = DateUtil::formatMySQLDate($date ?: DateUtil::formatMySQLDate());
        $accountId = $accountId ?: $_SESSION['ao_accountid'];
        $userId = $userId ?: $_SESSION['ao_userid'];
        
        $extraSql = '';
        if (ModuleUtil::checkOwnership('view_schedule', $userId, $accountId)) {
            $extraSql = "AND (
                            j.salesman = '$userId'
                            OR j.referral = '$userId'
                            OR a.user_id = '$userId'
                            OR j.user_id = '$userId'
                        )";
        }
        if(!empty($customer))
        {
            $extraSql .= $customer;
        }

        if(!empty($provider))
        {
            $extraSql .= $provider;
        }
        $sql = "SELECT j.job_id, c.fname AS customer_fname, c.lname AS customer_lname, c.address, c.city,
                        a.appointment_id, a.title, a.datetime, j.job_id, j.job_number,
                        j.salesman, u_s.fname AS salesman_fname, u_s.lname AS salesman_lname
                FROM customers c, appointments a, jobs j
                LEFT JOIN users u_s ON (u_s.user_id = j.salesman)
                WHERE CAST(a.datetime AS date)  = '$queryDate'
                    AND j.job_id = a.job_id
                    AND j.account_id = '$accountId'
                    AND c.customer_id = j.customer_id
                    $extraSql
                GROUP BY a.appointment_id
                ORDER BY datetime ASC";
        return DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     * @param string $date
     * @param int $userId
     * @param int $accountId
     * @return array
     */
    public static function getDeliveries($date = NULL, $userId = NULL, $accountId = NULL, $customer = NULL, $provider = NULL) {
        $queryDate = DateUtil::formatMySQLDate($date ?: DateUtil::formatMySQLDate());
        $accountId = $accountId ?: $_SESSION['ao_accountid'];
        $userId = $userId ?: $_SESSION['ao_userid'];
        
        $extraSql = '';
        if (ModuleUtil::checkOwnership('view_schedule', $userId, $accountId)) {
            $extraSql = "AND (
                            sts.user_id = '$userId'
                            OR j.user_id = '$userId'
                            OR j.referral = '$userId'
                        )";
        }
        if(!empty($customer))
        {
            $extraSql .= $customer;
        }

        if(!empty($provider))
        {
            $extraSql .= $provider;
        }
        $sql = "SELECT j.job_id, c.fname AS customer_fname, c.lname AS customer_lname, c.address, c.city,
                        sts.job_id, j.job_number, u.fname, u.lname, sts.confirmed, j.salesman, sts.label,
                        u_s.fname AS salesman_fname, u_s.lname AS salesman_lname
                FROM customers c, sheets sts, users u, jobs j
                LEFT JOIN users u_s ON (u_s.user_id = j.salesman)
                WHERE j.job_id = sts.job_id
                    AND sts.account_id = '$accountId'
                    AND sts.delivery_date = '$queryDate'
                    AND u.user_id = j.salesman
                    AND c.customer_id = j.customer_id
                    $extraSql
                GROUP BY sts.sheet_id";
        return DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     * @param string $date
     * @param int $userId
     * @param int $accountId
     * @return array
     */
    public static function getExpiringHolds($date = NULL, $userId = NULL, $accountId = NULL, $customer = NULL, $provider = NULL) {
        $queryDate = DateUtil::formatMySQLDate($date ?: DateUtil::formatMySQLDate());
        $accountId = $accountId ?: $_SESSION['ao_accountid'];
        $userId = $userId ?: $_SESSION['ao_userid'];$extraSql = '';
        
        if (ModuleUtil::checkOwnership('view_jobs', $userId, $accountId)) {
            $extraSql = "AND (
                            j.user_id = '$userId'
                            OR j.user_id = '$userId'
                            OR j.referral = '$userId'
                        )";
        }

        if(!empty($customer))
        {
            $extraSql .= $customer;
        }

        if(!empty($provider))
        {
            $extraSql .= $provider;
        }
        $sql = "SELECT j.job_id, j.job_number, c.fname AS customer_fname, c.lname AS customer_lname, c.address, c.city,
                j.job_number, s.status, u_s.fname AS salesman_fname, u_s.lname AS salesman_lname
                FROM customers c, status_holds sh, status s, jobs j
                LEFT JOIN users u_s ON (u_s.user_id = j.salesman)
                WHERE j.job_id = sh.job_id
                    AND c.customer_id = j.customer_id
                    AND sh.expires = '$queryDate'
                    AND sh.account_id = '$accountId'
                    AND s.status_id = sh.status_id
                    $extraSql";
        return DBUtil::queryToArray($sql);
    }
	
	public static function getInsuracenotification()
	{
		$sql = "SELECT user_id, fname, lname, email, level, generalins, workerins FROM users";
		return DBUtil::queryToArray($sql);
	}

	public static function getInsuracenotification2()
	{
		$sql1 = "SELECT email FROM users WHERE level=1";
		return DBUtil::queryToArray($sql1);
	}
    
    /**
     * 
     * @param string $date
     * @param int $userId
     * @param int $taskTypeId
     * @param int $accountId
     * @return array
     */
    public static function getTodolist($date = NULL, $userId = NULL, $taskTypeId = NULL, $customer = NULL, $provider = NULL) {
        $queryDate = DateUtil::formatMySQLDate($date ?$date: DateUtil::formatMySQLDate());
        $accountId =  $_SESSION['ao_accountid'];
        $userId =  $_SESSION['ao_userid'];

        $extraSql = '';
        $extraSql .= !empty($taskTypeId) ? "AND t.task_type = '$taskTypeId'" : '';
        if (ModuleUtil::checkOwnership('view_schedule', $userId, $accountId)) {
            $extraSql .= "AND (
                            t.user_id = '$userId'
                            OR j.salesman = '$userId'
                            OR j.user_id = '$userId'
                            OR j.referral = '$userId'
                        )";
        }

        if(!empty($customer)){
            $extraSql .= $customer;
        }        

        if(!empty($provider)){
            $extraSql .= $provider;
        }

        $sql = "SELECT j.job_id, c.fname AS customer_fname, c.lname AS customer_lname, c.address, c.city,
                        j.salesman, u_s.fname AS salesman_fname, u_s.lname AS salesman_lname, j.job_number,
                        t.completed,tdj.tbl_todolist_job_id,tdj.todolist_id, tdj.name as todolist_job,t.date_of_complete,tdj.color
                FROM jobs j
                JOIN todolist_job_status t ON t.job_id = j.job_id
                JOIN tbl_todolist_job as tdj ON t.todolist_job_id=tdj.tbl_todolist_job_id
                JOIN customers c ON c.customer_id = j.customer_id
                LEFT JOIN users u_s ON u_s.user_id = j.salesman
                WHERE j.account_id = '$accountId'
                    AND t.date_of_complete = '$queryDate'
                   $extraSql
                GROUP BY t.id";
                
        return DBUtil::queryToArray($sql);
    }
}