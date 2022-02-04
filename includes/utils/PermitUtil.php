<?php
/**
 * @author cmitchell
 */
class PermitUtil extends AssureUtil {
    
    public static function getPermitList() {
        $sql = "SELECT p.*, j.*, c.*, ju.*, p.timestamp + INTERVAL ju.permit_days DAY AS permit_expires
                FROM permits p, jobs j
                LEFT JOIN customers c ON c.customer_id = j.customer_id 
                LEFT JOIN jurisdiction ju ON ju.jurisdiction_id = j.jurisdiction
                WHERE j.job_id = p.job_id
                    AND j.account_id = '{$_SESSION['ao_accountid']}'
                ";
        return DBUtil::queryToArray($sql);
    }
    
}