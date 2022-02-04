<?php
include '../common_lib.php';

$assureJson = new AssureJson();

//check access
if(!UserModel::checkNavAccess('reports.php')) {
    $assureJson->addError('You do not have permission to delete reports')->out();
}

$savedReportId = RequestUtil::get('saved_report_id');
$existingReport = DBUtil::getRecord('saved_reports', $savedReportId);

if(!$existingReport) {
    $assureJson->addError('Report not found - unable to delete')->out();
}

$sqlSavedReport = "UPDATE saved_reports
        SET active = 0
        WHERE saved_report_id = '$savedReportId'
        LIMIT 1";
$sqlScheduledReports = "UPDATE scheduled_reports
        SET active = 0
        WHERE saved_report_id = '$savedReportId'";
if(!DBUtil::query($sqlSavedReport)) {
    $assureJson->addError('Unable to delete - please try again')->out();
}
DBUtil::query($sqlScheduledReports);
$assureJson->addSuccess('Report successfully deleted')->out();