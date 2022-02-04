<?php
include '../common_lib.php';

$assureJson = new AssureJson();

//check access
if(!UserModel::checkNavAccess('reports.php')) {
    $assureJson->addError('You do not have permission to save reports')->out();
}

//get and parse
$queryArr = array();
$savedReportId = RequestUtil::get('saved_report_id');
parse_str(urldecode($_SERVER['QUERY_STRING']), $queryArr);

//remove saved report id and copy from query
unset($queryArr['copy']);
unset($queryArr['saved_report_id']);

//json encode
$jsonQuery = json_encode($queryArr);

//get existing
$existingReport = DBUtil::getRecord('saved_reports', $savedReportId);

//check name
$name = RequestUtil::get('report_name');
if(!$name) {
    $assureJson->addError('You must enter a name')->out();
}

$insert = !$existingReport || RequestUtil::get('copy');

if($insert) {
    //save
    $sql = "INSERT INTO saved_reports (account_id, user_id, name, query)
            VALUES ('{$_SESSION['ao_accountid']}', '{$_SESSION['ao_userid']}', '$name', '$jsonQuery')";
} else {
    //update
    $sql = "UPDATE saved_reports
    SET name = '$name', query = '$jsonQuery'
    WHERE saved_report_id = '$savedReportId'
    LIMIT 1";
}
if(!DBUtil::query($sql)) {
    $assureJson->addError('Unable to save - please try again')->out();
}

$queryArr['saved_report_id'] = $insert ? DBUtil::getInsertId() : $savedReportId;
$assureJson->addResult('query', $queryArr);

if($insert) {
    $assureJson->addSuccess("New report '$name' saved")->out();
} else {
    $assureJson->addSuccess("Existing report '$name' saved")->out();
}