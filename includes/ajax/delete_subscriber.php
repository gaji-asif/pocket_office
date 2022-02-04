<?php
include '../common_lib.php';
if(!UserModel::loggedIn()) {
	JsonUtil::error('Unauthorized');
	die();
}

//get subscriber by id
$subscriber = JobUtil::getSubscriberById(RequestUtil::get('subscriberid'));

//get job by id
$myJob = new Job(MapUtil::get($subscriber, 'job_id'), FALSE);

if(!$subscriber|| !$myJob->exists()) {
	JsonUtil::error('Invalid reference');
	die();
}
if(!ModuleUtil::checkJobModuleAccess('modify_job_subscribers', $myJob)) {
	JsonUtil::error('Invalid permissions');
	die();
} else {
    	//build and execute query to delete journal
	$sql = "DELETE FROM subscribers WHERE subscriber_id = '{$subscriber['subscriber_id']}'";
	$results = DBUtil::query($sql);

	if(!$results) {
		JsonUtil::error('Operation failed');
	} else {
		JsonUtil::success('Subscriber succesfully removed');
		NotifyUtil::notifyFromTemplate('del_job_subscriber', $subscriber['user_id'], null, array('job_id' => $subscriber['job_id']));
	}
	die();
}