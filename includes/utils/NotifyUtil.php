<?php
class NotifyUtil extends AssureUtil {

    private static $accountHooks = array('[>ACCOUNTURL<]', '[>ACCOUNTNAME<]');
    private static $fromHooks = array('[>FROMFNAME<]', '[>FROMLNAME<]', '[>FROMEMAIL<]', '[>FROMUSERNAME<]', '[>FROMPASSWORD<]');
    private static $toHooks = array('[>TOFNAME<]', '[>TOLNAME<]', '[>TOEMAIL<]', '[>TOUSERNAME<]', '[>TOPASSWORD<]');
    private static $jobHooks = array('[>JOBNUMBER<]', '[>JOBID<]', '[>CUSTFNAME<]', '[>CUSTLNAME<]', '[>CUSTADDRESS<]', '[>CUSTCITY<]', '[>CUSTSTATE<]', '[>CUSTZIP<]', '[>CUSTPHONE<]', '[>SALESMANFNAME<]', '[>SALESMANLNAME<]', '[>HASH<]', '[>STAGENUM<]', '[>CSVSTAGES<]');
    private static $taskHooks = array('[>CONTRACTORFNAME<]', '[>CONTRACTORLNAME<]', '[>TASKTYPE<]', '[>DURATION<]', '[>NOTES<]');
    private static $eventHooks = array('[>EVENTTITLE<]', '[>EVENTSTARTDATE<]', '[>EVENTENDDATE<]', '[>EVENTDESCRIPTION<]', '[>EVENTTIME<]');
    private static $journalHooks = array('[>JOURNALTEXT<]');
    private static $systemHooks = array('[>TIMESTAMP<]', '[>QUERY<]', '[>QUERYERROR<]');
    private static $reportHooks = array('[>TIMESTAMP<]', '[>QUERY<]', '[>QUERYERROR<]');

    /**
     * 
     * @param int $userId
     * @return string
     */
    private static function getSMSAddress($userId) {
        if (empty($userId)) {
            return FALSE;
        }
        //build and execute query
        $userId = RequestUtil::escapeString((string) $userId);
        $sql = "SELECT sms.domain, users.phone
				FROM sms, users
				WHERE users.sms_carrier = sms.sms_id AND users.user_id = '$userId'
				LIMIT 1";
        $results = DBUtil::query($sql);
        list($domain, $phone) = DBUtil::fetchAssociativeArray($results);

        //return FALSE if no domain found
        if (empty($domain)) {
            return FALSE;
        }
        return $phone . $domain;
    }

    /**
     * 
     * @param string $hook
     * @param string $table
     * @return array
     */
    private static function getTemplate($hook, $table = 'email_templates') {
        //build and execute query
        $hook = RequestUtil::escapeString($hook);
        $sql = "SELECT subject, text
				FROM $table
				WHERE hook = '$hook' AND account_id = '{$_SESSION['ao_accountid']}' AND is_active = 1
				LIMIT 1";

        $results = DBUtil::query($sql);

        //return FALSE if template not found
        if (DBUtil::hasRows($results) == 0) {

            $sql = "SELECT subject, text
                FROM $table
                WHERE hook = '$hook' AND account_id = '1' AND is_active = 1
                LIMIT 1";            
            $results = DBUtil::query($sql);
            //return FALSE;
        }

        //prepare results
        $template = DBUtil::fetchAssociativeArray($results);
        $subject = prepareText(MapUtil::get($template, 'subject'));
        $message = prepareText(MapUtil::get($template, 'text'));

        return array(
            'subject' => $subject,
            'message' => $message
        );
    }

    /**
     * 
     * @param string $hook
     * @param string $table
     * @return array
     */
    private static function getSystemTemplate($hook, $table = 'system_email_templates') {
        //build and execute query
        $hook = RequestUtil::escapeString($hook);
        $sql = "SELECT subject, text
				FROM $table
				WHERE hook = '$hook' AND is_active = 1
				LIMIT 1";

//		LogUtil::getInstance()->logInfo('getSystemTemplate', $sql);

        $results = DBUtil::query($sql);

        //return FALSE if template not found
        if (DBUtil::hasRows($results) == 0) {
            return FALSE;
        }

        //prepare results
        $template = DBUtil::fetchAssociativeArray($results);
        $subject = prepareText(MapUtil::get($template, 'subject'));
        $message = prepareText(MapUtil::get($template, 'text'));

        return array(
            'subject' => $subject,
            'message' => $message
        );
    }

    /**
     * 
     * @param string $body
     * @param array $toUserData
     * @param array $fromUserData
     * @param array $data
     * @return string
     */
    private static function buildNotificationBody($body, $toUserData, $fromUserData = null, $data = array()) {
        //get data
        $jobData = JobUtil::getDataForNotification(@$data['job_id']);
        $taskData = JobUtil::getTaskDataForNotification(@$data['task_id']);
        $eventData = ScheduleModel::getEventDataForNotification(@$data['event_id']);
        $journalData = JobUtil::getJournalDataForNotification(@$data['journal_id']);
        $systemData = MapUtil::get($data, 'system_data', array());

        //populate message
        $body = str_replace(self::$accountHooks, array(ACCOUNT_URL, $_SESSION['ao_accountname']), $body);
        $body = str_replace(self::$fromHooks, $fromUserData, $body);
        $body = str_replace(self::$toHooks, $toUserData, $body);
        $body = str_replace(self::$jobHooks, $jobData, $body);
        $body = str_replace(self::$taskHooks, $taskData, $body);
        $body = str_replace(self::$eventHooks, $eventData, $body);
        $body = str_replace(self::$journalHooks, $journalData, $body);
        $body = str_replace('[>NOW_DISPLAY_DATE<]', DateUtil::formatDate(), $body);
        $body = str_replace('[>SCHEDULE<]', MapUtil::get($data, 'schedule'), $body);
        $body = str_replace('[>REPORTNAME<]', MapUtil::get($data, 'report_name'), $body);

        return $body;
    }

    /**
     * 
     * @param string $hook
     * @param int $toUserId
     * @param int $fromUserId
     * @param array $data
     * @param boolean $isSystemTemplate
     */
    public static function notifyFromTemplate($hook, $toUserId, $fromUserId = null, $data = array(), $isSystemTemplate = FALSE) {
        self::smsFromTemplate($hook, $toUserId, $fromUserId, $data, $isSystemTemplate);
        self::emailFromTemplate($hook, $toUserId, $fromUserId, $data, $isSystemTemplate);
    }

    /**
     * 
     * @param string $hook
     * @param int $fromUserId
     * @param array $data
     * @param boolean $isSystemTemplate
     * @return boolean
     */
    public static function notifySubscribersFromTemplate($hook, $fromUserId = null, $data = array(), $isSystemTemplate = FALSE) {
        if (empty($data['job_id'])) {
            return FALSE;
        }

        //get subscribers
        $subscribers = JobUtil::getSubscribers($data['job_id']);
        
        //add salesman if exists
        $salesmanId = JobUtil::getProperty($data['job_id'], 'salesman');
        if (!empty($salesmanId)) {
            $subscribers[] = $salesmanId;
        }

        //add task contractor is exists
        if (!empty($data['task_id'])) {
            $contractorId = JobUtil::getTaskContractor($data['task_id']);
            if (!empty($contractorId)) {
                $subscribers[] = $contractorId;
            }
        }

        //add repair contractor is exists
        if (!empty($data['repair_id'])) {
            $contractorId = JobUtil::getRepairContractor($data['repair_id']);
            if (!empty($contractorId)) {
                $subscribers[] = $contractorId;
            }
        }

        //remove duplicates
        $subscribers = array_unique($subscribers);
        
        //echo "<pre>";print_r($subscribers);die;
        
        //iterate
        foreach ($subscribers as $subscriberId) {
            //email
            self::emailFromTemplate($hook, $subscriberId, $fromUserId, $data, $isSystemTemplate);

            //get sms and send if found
            $smsAddress = self::getSMSAddress($subscriberId);
            if (!empty($smsAddress)) {
                self::smsFromTemplate($hook, $subscriberId, $fromUserId, $data, $isSystemTemplate);
            }
        }
    }

    /**
     * 
     * @param string $hook
     * @param int $toUserId
     * @param int $fromUserId
     * @param array $data
     * @param boolean $isSystemTemplate
     * @return boolean
     */
    public static function smsFromTemplate($hook, $toUserId, $fromUserId = null, $data = array(), $isSystemTemplate = FALSE) {
        if(defined('DISABLE_EMAIL')) { return; }
        
        //get sms address and return if FALSE
        $smsAddess = self::getSMSAddress($toUserId);
        if ($smsAddess === FALSE) {
//            LogUtil::getInstance()->logNotice('smsFromTemplate - Invalid SMS address', func_get_args());
            return FALSE;
        }

        //check if to user is deleted and return if true
        if (UserModel::userDeleted($toUserId)) {
            LogUtil::getInstance()->logNotice('smsFromTemplate - User deleted', func_get_args());
            return FALSE;
        }

        //get template data and return if template not found
        if ($isSystemTemplate) {
            $templateData = self::getSystemTemplate($hook, 'system_sms_templates');
        } else {
            $templateData = self::getTemplate($hook, 'sms_templates');
        }
        if ($templateData === FALSE) {
            LogUtil::getInstance()->logNotice("smsFromTemplate - Invalid template: $hook");
            return FALSE;
        }

        //get user data
        $toUserData = UserModel::getDataForNotification($toUserId);
        $fromUserData = UserModel::getDataForNotification($fromUserId);

        //get subject and body
        $templateData['subject'] = stripslashes(APP_NAME . ": {$templateData['subject']}");
        $templateData['message'] = self::buildNotificationBody($templateData['message'], $toUserData, $fromUserData, $data);

        //get new mail object
        $mail = new PHPMailer();
        $mail->IsHTML(FALSE);

        //add from, to, subject, body
        $mail->SetFrom(ALERTS_EMAIL, APP_NAME . " Alerts");
        $mail->AddAddress($smsAddess, "{$toUserData['fname']} {$toUserData['lname']}");
        $mail->Subject = $templateData['subject'];
        $mail->Body = $templateData['message'];

        //send
        if (!$mail->Send()) {
            LogUtil::getInstance()->logNotice('smsFromTemplate - Failed', func_get_args());
            return FALSE;
        } else {
            self::storeMessageHistory('SMS', $toUserData['email'], $templateData['subject'], $templateData['message']);
            return true;
        }
    }
    
    /**
     * 
     * @param string $hook
     * @param int $toUserId
     * @param int $fromUserId
     * @param array $data
     * @param boolean $isSystemTemplate
     * @return boolean
     */
    public static function emailFromTemplate($hook, $toUserId, $fromUserId = null, $data = array(), $isSystemTemplate = FALSE) {


//        if(defined('DISABLE_EMAIL')) { return; }
        //check if to user is deleted and return if true
        if (UserModel::userDeleted($toUserId)) {
            LogUtil::getInstance()->logNotice('emailFromTemplate - User deleted', func_get_args());
            return FALSE;
        }
		
        //get template data and return if template not found
        if ($isSystemTemplate) {
            $templateData = self::getSystemTemplate($hook, 'system_email_templates');
        } else {
            $templateData = self::getTemplate($hook, 'email_templates');
        }

        
        if ($templateData == FALSE) {
            LogUtil::getInstance()->logNotice("emailFromTemplate - Invalid template: $hook");
            return FALSE;
        }
        //get user data
        $toUserData = UserModel::getDataForNotification($toUserId);
        $fromUserData = UserModel::getDataForNotification($fromUserId);


        //get subject and body
        $originalSubject = stripslashes($templateData['subject']);
        $templateData['subject'] = stripslashes(APP_NAME . ": {$templateData['subject']}");
        $templateData['message'] = self::buildNotificationBody($templateData['message'], $toUserData, $fromUserData, $data);
        
        if($hook=='contact_note')
        {
            $templateData['subject'] = 'New Contact Note';
            $templateData['message'] = "Dear ".$toUserData['fname'].'<br><br>'.$data['message'];
        }

        //get body
        $body = stripslashes(ViewUtil::loadView('mail/email-notification', $templateData));
        //echo "<pre>";print_r($body);die;
        //get new mail object
        $mail = new PHPMailer();

        //add from, to, subject, body
        $mail->SetFrom(ALERTS_EMAIL, APP_NAME . " Alerts");
        $mail->AddAddress($toUserData['email'], "{$toUserData['fname']} {$toUserData['lname']}");
        $mail->Subject = $templateData['subject'];
        $mail->MsgHTML($body);


        //attachment
        $attachmentPath = MapUtil::get($data, 'attachment');
        if(file_exists($attachmentPath)) {
            $mail->AddAttachment($attachmentPath);
        }
        //send
        //echo "<pre>";print_r($mail);die;
        if (!$mail->Send()) {
            LogUtil::getInstance()->logNotice("emailFromTemplate - Failed: {$mail->ErrorInfo}", func_get_args());
            return FALSE;
        } else {
            self::storeMessageHistory('Email', $toUserData['email'], $originalSubject, $templateData['message']);
            return true;
        }
    }

    /**
     * 
     * @param string $type
     * @param string $to
     * @param string $subject
     * @param string $body
     */
    public static function storeMessageHistory($type, $to, $subject, $body) {
        //get data
        $type = RequestUtil::escapeString($type);
        $to = RequestUtil::escapeString($to);
        $subject = RequestUtil::escapeString($subject);
        $body = RequestUtil::escapeString($body);

        //build and execute query
        $sql = "INSERT INTO message_history (type, to_email, subject, body, account_id, timestamp)
				VALUES ('$type', '$to', '$subject', '$body', '{$_SESSION['ao_accountid']}', now())";
        DBUtil::query($sql);
    }

}