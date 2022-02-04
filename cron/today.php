<?php
//die();
include substr(str_replace(pathinfo(__FILE__, PATHINFO_BASENAME), '', __FILE__), 0, -1) . '/../includes/common_lib.php';

if (defined('STDIN')) {
    $cronKey = $argv[1];
} else { 
    $cronKey = $_GET['key'];
}

if($cronKey != CRON_KEY) { die("\nNot authorized\n\n"); }
if(!DBUtil::connect(CRON_DATABASE, FALSE)) { die('\nConnection failed!\n\n'); }

//email template settings
$hook = 'today';
$isSystemTemplate = TRUE;

$sql = "SELECT * 
        FROM users u
        JOIN user_meta um ON um.user_id = u.user_id
        WHERE um.meta_key = 'daily_schedule'
            AND meta_value = '1'";
$users = DBUtil::queryToArray($sql);

echo "\n\n" . count($users) . " user(s)\n\n";

foreach($users as $userNum => $user) {
    $userId = MapUtil::get($user, 'user_id');
    
    echo "[$userNum] User ID: $userId\n";
    
    $accountId = MapUtil::get($user, 'account_id');
    $viewData = array(
        'repairs' => ScheduleUtil::getRepairs($date = NULL, $userId, $accountId),
        'tasks' => ScheduleUtil::getTasks($date = NULL, $userId, $taskTypeId = NULL, $accountId),
        'events' => ScheduleUtil::getEvents($date = NULL, $userId, $accountId),
        'appointments' => ScheduleUtil::getAppointments($date = NULL, $userId, $accountId),
        'deliveries' => ScheduleUtil::getDeliveries($date = NULL, $userId, $accountId),
        'expiringHolds' => ScheduleUtil::getExpiringHolds($date = NULL, $userId, $accountId)
    );
    
    echo "[$userNum] " . count($viewData['repairs']) . " repairs found\n";
    echo "[$userNum] " . count($viewData['tasks']) . " tasks found\n";
    echo "[$userNum] " . count($viewData['events']) . " events found\n";
    echo "[$userNum] " . count($viewData['appointments']) . " appointments found\n";
    echo "[$userNum] " . count($viewData['deliveries']) . " deliveries found\n";
    echo "[$userNum] " . count($viewData['expiringHolds']) . " expiring holds found\n";
    
    if(empty($viewData['repairs']) && empty($viewData['tasks']) && empty($viewData['events'])
            && empty($viewData['appointments']) && empty($viewData['deliveries']) && empty($viewData['expiringHolds'])) {
        echo "[$userNum] Nothing to report\n\n";
        continue;
    }
    
    $data = array(
        'schedule' => ViewUtil::loadView('mail/today', $viewData)
    );

    if(!NotifyUtil::emailFromTemplate($hook, $userId, NULL, $data, $isSystemTemplate)) {
        echo "[$userNum] Failed to send\n\n";
    } else {
        echo "[$userNum] Notification successfully sent\n\n";
    }
}