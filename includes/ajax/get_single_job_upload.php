<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_jobs'))
die("Insufficient Rights");

$myJob = new Job(RequestUtil::get('id'));
$upload_id = $_GET['upload_id'];


//if(moduleOwnership('view_jobs') && (!JobUtil::isSubscriber($myJob->job_id) && $myJob->salesman_id!=$_SESSION['ao_userid'] && $myJob->user_id!=$_SESSION['ao_userid']))
if(!ModuleUtil::checkJobModuleAccess('view_jobs', $myJob) || !isset($myJob->uploads_array[$upload_id]))
{
    die();
}

$view_data = array(
    'upload_id' => $upload_id,
    'upload' => $myJob->uploads_array[$upload_id],
    'myJob' => $myJob,
);
echo ViewUtil::loadView('job-upload-container', $view_data);