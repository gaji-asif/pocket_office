<?php
include substr(str_replace(pathinfo(__FILE__, PATHINFO_BASENAME), '', __FILE__), 0, -1) . '/../includes/common_lib.php';

if (defined('STDIN')) {
    $cronKey = $argv[1];
} else { 
    $cronKey = $_GET['key'];
}

if($cronKey != CRON_KEY) { die("\nNot authorized\n\n"); }
if(!DBUtil::connect(CRON_DATABASE, FALSE)) { die('\nConnection failed!\n\n'); }
define('CRON_REQUEST', true);

//email template settings
$hook = 'scheduled_report';
$isSystemTemplate = TRUE;

//get records
$cronReports = DBUtil::getAll('scheduled_reports');

//cached generated reports
$cachedReports = array();

echo "\n\n" . count($cronReports) . " scheduled report(s) found\n\n";

foreach($cronReports as $cronReportNum => $cronReport) {
    //get user and report
    $savedReportId = MapUtil::get($cronReport, 'saved_report_id');
    $userId = MapUtil::get($cronReport, 'user_id');
    $user = DBUtil::getRecord('users', $userId);
    $_SESSION['ao_accountid'] = MapUtil::get($user, 'account_id');
    $savedReport = DBUtil::getRecord('saved_reports', $savedReportId);
    $reportQuery = json_decode(MapUtil::get($savedReport, 'query'), TRUE);
    $reportFullName = ucfirst(MapUtil::get($reportQuery, 'table')) . ' - ' . MapUtil::get($savedReport, 'name');
    $shouldSend = CronUtil::shouldSend($cronReport);
    
    //make sure we have the user and the saved report records
    if(!$user) {
        echo "[$cronReportNum] User record not found\n\n";
        continue;
    } else if(!$savedReport) {
        echo "[$cronReportNum] Saved report record not found\n\n";
        continue;
    } else if(!$shouldSend) {
        echo "[$cronReportNum] Should not send\n\n";
        continue;
    }
    
    echo "[$cronReportNum] Saved Report ID: $savedReportId\n";
    echo "[$cronReportNum] User ID: $userId\n";
    
    //try to get cached report
    $cachedReport = MapUtil::get($cachedReports, $savedReportId);
    if(!$cachedReport) {
        $reportName = MapUtil::get($savedReport, 'name');
        $table = MapUtil::get($reportQuery, 'table');
        $filters = ReportUtil::getFiltersFromRequest($reportQuery);
        $columns = MapUtil::get($reportQuery, 'columns');
        $sql = ReportUtil::buildSQL($table, $columns, $filters, MapUtil::get($user, 'account_id'));
        $rows = ReportUtil::getTableRows(DBUtil::queryToArray($sql), $table, $columns, TRUE);
        $numRows = count($rows);
        array_unshift($rows, ReportUtil::getTableHeaders($table, $columns, TRUE));
        
        if(!$rows) {
            echo "[$cronReportNum] No rows found\n\n";
            continue;
        }
        echo "[$cronReportNum] Row count: $numRows\n";

        //generate and save csv
        $cachedReports[$savedReportId] = CSVUtil::generateAndSave($rows, ucfirst($table) . " - $reportName");
        $cachedReport = $cachedReports[$savedReportId];
    }
    
    $data = array(
        'attachment' => ROOT_PATH . "/cron/$cachedReport",
        'report_name' => $reportFullName
    );
    if(!NotifyUtil::emailFromTemplate($hook, $userId, NULL, $data, $isSystemTemplate)) {
        echo "[$cronReportNum] Failed to send\n\n";        
    } else {
        echo "[$cronReportNum] Notification successfully sent\n\n";
        
        //update last sent timestamp
        CronUtil::updateLastSent('scheduled_reports', $cronReport);
    }
}

//delete saved reports
foreach($cachedReports as $cachedReport) {
    unlink($cachedReport);
}