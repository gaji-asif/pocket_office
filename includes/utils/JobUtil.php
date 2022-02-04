<?php
/**
 * @author cmitchell
 */
class JobUtil extends AssureUtil {
    
    private static $jobCache = array();
    
    public static function getForMap($latitude, $longitude, $radius, $limit = 300) {
        //get data
        $latitude = RequestUtil::escapeString($latitude);
        $longitude = RequestUtil::escapeString($longitude);
        $radius = (int)$radius;
        $limit = (int)$limit;

        //use a bounding rectangle to limit the number of rows we need to check in the database and speed things up
        $bounds = RequestUtil::escapeArrayVals(JobUtil::getBoundingRectangle($latitude, $longitude, $radius));

        $sql = "SELECT j.job_id, ((ACOS(SIN($latitude * PI() / 180) * SIN(c.lat * PI() / 180) + COS($latitude * PI() / 180) * COS(c.lat * PI() / 180) * COS(($longitude - c.long) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance
                FROM jobs j, customers c
                WHERE j.account_id = '{$_SESSION['ao_accountid']}'
                    AND c.customer_id = j.customer_id
                    AND c.long >= {$bounds['lon_min']}
                    AND c.long <= {$bounds['lon_max']}
                    AND c.lat >= {$bounds['lat_min']}
                    AND c.lat <= {$bounds['lat_max']}
                    AND j.timestamp
                HAVING distance <= $radius
                ORDER BY j.timestamp DESC
                LIMIT $limit";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getMessageHistory($jobId) {
        $jobId = RequestUtil::escapeString($jobId);
        $sql = "SELECT *
                FROM message_history
                WHERE body LIKE '%p=jobs%'
                    AND body LIKE '%id=$jobId%'
                ORDER BY timestamp DESC";
        return DBUtil::queryToArray($sql);
    }
    
    public static function getById($jobId) {
        $jobId = RequestUtil::escapeString($jobId);
        return DBUtil::getRecord('jobs', $jobId);
    }
    
    public static function getSubscriberById($subscriberId) {
        $subscriberId = RequestUtil::escapeString($subscriberId);
        return DBUtil::getRecord('subscribers', $subscriberId);
    }

    public static function getJournalById($journalId) {
        $journalId = RequestUtil::escapeString($journalId);
        return DBUtil::getRecord('journals', $journalId);
    }
    
    public static function getUploadById($uploadId) {
        $uploadId = RequestUtil::escapeString($uploadId);
        return DBUtil::getRecord('uploads', $uploadId);
    }
    
    public static function getMaterialSheetById($sheetId) {
        $sheetId = RequestUtil::escapeString($sheetId);
        return DBUtil::getRecord('sheets', $sheetId);
    }

    public static function getUploadType($fileName) {
        $pieces = explode('.', $fileName);
        $extension = strtoupper(end($pieces));

        switch ($extension) {
            case 'ZIP':
            case 'RAR':
                return "archive";
            case 'JPG':
            case 'JPEG':
            case 'PNG':
            case 'GIF':
            case 'BMP':
                return "image";
            case 'PDF':
                return "pdf";
            case "PPTX":
            case "PPT":
                return "powerpoint";
            case "DOCX":
            case "DOC":
                return  "word";
            case "XLSX":
            case "XLS":
                return "excel";
            default:
                return "unknown";
        }
    }
    
    public static function getUploadEditScript($upload) {
        try {
            $scheduleForm = new ScheduleForm();
            $scheduleForm->initByUploadId(MapUtil::get($upload, 'upload_id'));
            return "schedule_{$scheduleForm->getType()}_job.php?upload_id={$scheduleForm->getUploadId()}";
        } catch (Exception $e) {
            return 'edit_upload.php?id=' . MapUtil::get($upload, 'job_id') . '&upload=' . MapUtil::get($upload, 'upload_id');
        }
    }
    
    public static function getSubscribers($jobId) {
        $jobId = RequestUtil::escapeString($jobId);
        return DBUtil::pluck(DBUtil::getRecords('subscribers', $jobId, 'job_id'), 'user_id');
    }
    
    public static function getSalesmanLink($jobId) {
        return UserUtil::getDisplayName(self::getProperty($jobId, 'salesman'), (RequestUtil::get('csv') || defined('CRON_REQUEST')));
    }
    
    public static function getLink($jobId) {
        $jobNumber = self::getProperty($jobId, 'job_number');
        return (!RequestUtil::get('csv') && !defined('CRON_REQUEST')) ? "<a href=\"/jobs.php?id=$jobId\" data-type=\"job\" data-id=\"$jobId\" tooltip>$jobNumber</a>" : $jobNumber;
    }
    
    public static function getReferralLink($jobId) {
        return UserUtil::getDisplayName(self::getProperty($jobId, 'referral'), (RequestUtil::get('csv') || defined('CRON_REQUEST')));
    }
    
    public static function getCustomerLink($jobId) {
        return CustomerUtil::getDisplayName(self::getProperty($jobId, 'customer_id'), (RequestUtil::get('csv') || defined('CRON_REQUEST')));
    }
    
    public static function getCustomer($jobId) {
        if(isset(self::$jobCache[$jobId])) {
            return MapUtil::get(self::$jobCache[$jobId], 'customer_id');
        }
        
        $job = DBUtil::getRecord('jobs', RequestUtil::escapeString($jobId));
        self::$jobCache[$jobId] = $job;
        
        return MapUtil::get($job, 'customer_id');
    }
    
    public static function getTaskContractor($taskId) {
        $taskId = RequestUtil::escapeString($taskId);
        $sql = "SELECT contractor
                FROM tasks
                WHERE task_id = '$taskId'
                LIMIT 1";
        $results = DBUtil::query($sql);
        
        return $results ? mysqli_result($results, 0) : NULL;
    }
    
    public static function getRepairContractor($repairId) {
        $repairId = RequestUtil::escapeString($repairId);
        $sql = "SELECT contractor
                FROM repairs
                WHERE repair_id = '$repairId'
                LIMIT 1";
        $results = DBUtil::query($sql);
        
        return $results ? mysqli_result($results, 0) : NULL;
    }
    
    public static function getJournalDataForNotification($journalId) {
        $journalId = RequestUtil::escapeString($journalId);
        $sql = "SELECT text
                FROM journals
                WHERE journal_id = '$journalId'
                LIMIT 1";
        return DBUtil::queryToMap($sql);
    }
    
    public static function getDataForNotification($jobId) {
        $jobId = RequestUtil::escapeString($jobId);
        $sql = "SELECT j.job_number, j.job_id, c.fname, c.lname, c.address, c.city,
                c.state, c.zip, c.phone, u.fname as salesman_fname, u.lname as salesman_lname, j.hash, j.stage_num
                FROM jobs j
                LEFT JOIN customers c ON (c.customer_id = j.customer_id)
                LEFT JOIN users u ON (u.user_id = j.salesman)
                WHERE j.job_id = '$jobId'
                LIMIT 1";
        $resultsArray = DBUtil::queryToMap($sql);
        $resultsArray[] = StageModel::getCSVStagesByStageNum(MapUtil::get($resultsArray, 'stage_num'));

        return $resultsArray;
    }
    
    public static function getCSVStages($jobId) {
        $sql = "SELECT s.stage
                FROM stages s, jobs j
                WHERE j.stage_num = s.stage_num
                    AND s.account_id = '{$_SESSION['ao_accountid']}'
                    AND j.job_id = '$jobId'";
        return implode(', ', DBUtil::pluck(DBUtil::query($sql), 'stage'));
    }
    
    public static function getTaskDataForNotification($taskId) {
        $taskId = RequestUtil::escapeString($taskId);
        $sql = "SELECT u.fname, u.lname, tt.task, t.duration, t.note
                FROM tasks t
                LEFT JOIN task_type tt ON (tt.task_type_id = t.task_type)
                LEFT JOIN users u ON (u.user_id = t.contractor)
                WHERE t.task_id = '$taskId'
                LIMIT 1";
        return DBUtil::queryToMap($sql);
    }
    
    public static function getBoundingRectangle($latitude, $longitude, $distance) {
        $latitude=($latitude)?$latitude:0;
        $longitude=($longitude)?$longitude:0;
        
        $latitude = deg2rad($latitude);
        $longitude = deg2rad($longitude);
        $distance = $distance * 1.608; //convert to km

        //angular radius
        $angular_radius = $distance / 6371;
        $lat_min = $latitude - $angular_radius;
        $lat_max = $latitude + $angular_radius;

//        $latitude_t = asin(sin($latitude)/cos($angular_radius));
        $delta_lon = asin(sin($angular_radius)/cos($latitude));

        $lon_min = $longitude - $delta_lon;
        $lon_max = $longitude + $delta_lon;

        return array(
            'lat_min' => rad2deg($lat_min),
            'lat_max' => rad2deg($lat_max),
            'lon_min' => rad2deg($lon_min),
            'lon_max' => rad2deg($lon_max)
        );
    }
    
    public static function isSubscriber($jobId) {
        $jobId = RequestUtil::escapeString($jobId);
        $sql = "SELECT subscriber_id
                FROM subscribers
                WHERE user_id = '{$_SESSION['ao_userid']}'
                    AND job_id = '$jobId'
                LIMIT 1";
        return DBUtil::hasRows(DBUtil::query($sql));
    }
    
    public static function getReportTasks($jobId) {
        $jobId = RequestUtil::escapeString($jobId);
        $sql = "SELECT tt.task, t.completed, t.paid
                FROM tasks t, task_type tt
                WHERE t.job_id = '$jobId'
                    and tt.task_type_id = t.task_type
                ORDER BY t.completed DESC";
        $resultsArray = DBUtil::queryToArray($sql);
        
        foreach($resultsArray as $key => $task) {
            //completed?
            if(!MapUtil::get($task, 'completed')) {
                $resultsArray[$key]['task'] = MapUtil::get($task, 'task') . ' (' . DateUtil::formatDate(MapUtil::get($task, 'completed')) . ')';
            }
            
            //paid?
            if(MapUtil::get($task, 'paid')) {
                $resultsArray[$key]['task'] = '$ ' . MapUtil::get($resultsArray[$key], 'task');
            }
        }
        
        return implode('<br />', MapUtil::pluck($resultsArray, 'task'));
    }
    
    public static function getReportContractors($jobId) {
        $jobId = RequestUtil::escapeString($jobId);
        $sql = "SELECT u.lname
                FROM tasks t, users u
                WHERE t.job_id = '$jobId'
                    AND u.user_id = t.contractor
                ORDER BY u.lname asc";
        return implode('<br />', DBUtil::pluck(DBUtil::query($sql), 'lname'));
    }
    
    public static function getStageList($jobId, $delimiter = ', ') {
        $jobId = RequestUtil::escapeString($jobId);
        $sql = "SELECT s.stage stages s, jobs j
                WHERE j.stage_num = s.stage_num
                    AND s.account_id = '{$_SESSION['ao_accountid']}'
                    AND jobs.job_id = '$jobId'";
        return implode($delimiter, DBUtil::pluck(DBUtil::query($sql), 'stage'));
    }
    
    public static function storeJobViewHistory($jobId) {
        $jobId = RequestUtil::escapeString($jobId);
        $sql = "SELECT job_view_history_id
                FROM job_view_history
                WHERE user_id = '{$_SESSION['ao_userid']}'
                    AND job_id = '$jobId'
                LIMIT 1";
        if(!DBUtil::hasRows(DBUtil::query($sql))) {
            $sql = "INSERT INTO job_view_history (job_id, user_id, timestamp)
                    VALUES('$jobId', '{$_SESSION['ao_userid']}', now())";
            DBUtil::query($sql);
        }
    }
    
    public static function getAllJobTypes() {
        return DBUtil::getAll('job_type', 'job_type');
    }
    
    public static function getAllOrigins() {
        return DBUtil::getAll('origins', 'origin');
    }
    
    public static function getAllProrities() {
        return DBUtil::getAll('priority', 'priority');
    }
    
    /**
     * 
     * @return array
     */
    public static function getAllFailTypes() {
        return DBUtil::getAll('fail_types', 'fail_type');
    }
    
    /**
     * 
     * @param int $jobId
     * @return boolean
     */
    public static function jobIsBookmarked($jobId) {
        $jobId = RequestUtil::escapeString($jobId);
        return DBUtil::getRecord('bookmarks', $jobId, 'job_id');
    }
    
    /**
     * 
     * @return array
     */
    public static function getAllWarranties() {
        return DBUtil::getAll('warranties', 'label', $order = NULL, $key = 'warranty_id');
    }
    
    /**
     * 
     * @param id $jobId
     * @return array
     */
    public static function getContractors($jobId) {
        $jobId = RequestUtil::escapeString($jobId);
        $sql = "SELECT u.user_id, u.lname, u.fname
                FROM users u, jobs j
                LEFT JOIN tasks t ON (t.job_id = j.job_id)
                LEFT JOIN repairs r ON (r.job_id = j.job_id)
                WHERE j.job_id = '$jobId' AND
                (
                    u.user_id = t.contractor
                    OR u.user_id = r.contractor
                )
                GROUP BY u.user_id";
        return DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     * @param int $metaId
     * @return array
     */
    public static function getMetaByMetaId($metaId) {
        $metaId = RequestUtil::escapeString($metaId);
        $sql = "SELECT *
                FROM job_meta
                WHERE meta_id = '$metaId'
                ORDER BY meta_name ASC";
        return DBUtil::queryToArray($sql, 'meta_name');
    }
    
    /**
     * 
     * @param mixed $id
     * @param string $key
     * @param mixed $value
     * @param boolean $deletePrevious
     */
    public static function setJobMeta($id, $key, $value, $deletePrevious = FALSE) {
        if($deletePrevious) {
            self::deleteJobMeta($id, $key);
        }
        
        $sql = "INSERT INTO job_meta (meta_id, meta_name, meta_value)
                VALUES ('$id', '$key', '$value')";
        DBUtil::query($sql);
    }
    
    /**
     * 
     * @param mixed $id
     * @param string $keys
     */
    public static function deleteJobMeta($id, $keys) {
        settype($keys, 'array');

        foreach($keys as $key) {
            $sql = "DELETE FROM job_meta
                    WHERE meta_id = '$id'
                        AND meta_name = '$key'";
            DBUtil::query($sql);
        }
    }
    
    /**
     * 
     * @param int $jobId
     * @return string
     */
    public static function getCacheKey($jobId) {
        return "jobs::{$_SESSION['database_name']}::{$_SESSION['ao_accountid']}::$jobId";
    }
    
    /**
     * 
     * @param int $customerId
     * @return array
     */
    public static function getIdsByCustomerId($customerId) {
        $sql = "SELECT job_id
                FROM jobs
                WHERE customer_id = '$customerId'";
        return DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     * @param int $jobId
     * @return string
     */
    public static function getCSVTasks($jobId) {
        $sql = "SELECT tt.task
                FROM tasks t, task_type tt
                WHERE t.job_id = '$jobId'
                    and tt.task_type_id = t.task_type
                ";
        return implode(', ', DBUtil::pluck(DBUtil::query($sql), 'task'));
    }
    
    public static function getCSVContractors($jobId) {
        $sql = "SELECT u.user_id
                FROM tasks t
                JOIN users u ON t.contractor = u.user_id
                WHERE t.job_id = '$jobId'
                GROUP BY u.user_id
                ORDER BY u.lname ASC";
        $contractorIds = DBUtil::pluck(DBUtil::query($sql), 'user_id');
        
        $contractorLinks = array();
        foreach($contractorIds as $contractorId) {
            $contractorLinks[] = UserUtil::getDisplayName($contractorId);
        }
        
        return implode(', ', $contractorLinks);
    }
    
    /**
     * 
     * @param boolean $activeOnly
     * @param boolean $jobItemsOnly
     * @return array
     */
    public static function getActions($activeOnly = TRUE, $jobItemsOnly = FALSE) {
        $extraSql = $activeOnly ? "AND active = 1\n": '';
        $extraSql .= $jobItemsOnly ? "AND job_item = 1\n" : '';
        
        $sql = "SELECT *
                FROM job_actions
                WHERE 1 = 1
                $extraSql
                ORDER BY action ASC";
        //echo "<pre>";print_r($extraSql);die;
        return DBUtil::queryToArray($sql);
    }
    
    /**
     * 
     * @param int $jobId
     * @return mixed
     */
    public static function get($jobId) {
        if(isset(self::$jobCache[$jobId])) {
            $job = self::$jobCache[$jobId];
        } else {
            $job = DBUtil::getRecord('jobs', RequestUtil::escapeString($jobId));
            self::$jobCache[$jobId] = $job;
        }
        
        return $job;
    }
    
    /**
     * 
     * @param int $jobId
     * @param string $key
     * @return mixed
     */
    public static function getProperty($jobId, $key) {
        return MapUtil::get(self::get($jobId), $key);
    }

}