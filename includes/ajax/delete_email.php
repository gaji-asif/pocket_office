<?php

include '../common_lib.php';

if(!UserModel::loggedIn()) {

	JsonUtil::error('Unauthorized');

	die();

}



//get journal by id

$journal = JobUtil::getEmailById(RequestUtil::get('emailid'));



//get job by id

$myJob = new Job(MapUtil::get($journal, 'job_id'), FALSE);



if(!$journal || !$myJob->exists()) {

	JsonUtil::error('Invalid reference');

	die();

}



if(!ModuleUtil::checkJobModuleAccess('delete_journals', $myJob)) {

	JsonUtil::error('Invalid permissions');

	die();

} else {

	//build and execute query to delete journal

	$sql = "UPDATE job_email

			SET `is_deleted` = 2

			WHERE `email_id` = '{$journal['email_id']}'

			LIMIT 1";

	if(DBUtil::query($sql)){

		JsonUtil::success('Email succesfully removed');

	} else {

		JsonUtil::error('Operation failed');

	}

	die();

}