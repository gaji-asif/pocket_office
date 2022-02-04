<?php

include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('view_jobs'))
  die("Insufficient Rights");

$myJob = new Job(RequestUtil::get('id'));

if(moduleOwnership('view_jobs') && (!JobUtil::isSubscriber($myJob->job_id) && $myJob->salesman_id!=$_SESSION['ao_userid'] && $myJob->user_id!=$_SESSION['ao_userid']))
  die("Insufficient Rights");

echo ViewUtil::loadView('job-list-row', array('myJob' => $myJob, 'quick_settings' => false, 'complete_row' => false));