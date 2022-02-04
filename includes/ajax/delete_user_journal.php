<?php
include '../common_lib.php';
if(!UserModel::loggedIn()) {
	JsonUtil::error('Unauthorized');
	die();
}

//get journal by id


$journal = $_REQUEST['userid']	;
//print_r($_REQUEST);
//get job by id
/*$myJob = new Job(MapUtil::get($journal, 'job_id'), FALSE);

if(!$journal || !$myJob->exists()) {
	JsonUtil::error('Invalid reference');
	die();
}

if(!ModuleUtil::checkJobModuleAccess('delete_journals', $myJob)) {
	JsonUtil::error('Invalid permissions');
	die();
} else { */
	//build and execute query to delete journal
	$sql = "UPDATE users SET journal=0 WHERE user_id = '{$journal}' LIMIT 1";
	/*$sql = "DELETE FROM journals
			WHERE journal_id = '{$journal['journal_id']}'
			LIMIT 1"; */
	if(DBUtil::query($sql)){
		JsonUtil::success('Journal succesfully removed');
	} else {
		JsonUtil::error('Operation failed');
	}
	die();
//}