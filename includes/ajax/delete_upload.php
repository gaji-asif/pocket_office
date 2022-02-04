<?php
include '../common_lib.php';
if(!UserModel::loggedIn()) {
	JsonUtil::error('Unauthorized');
	die();
}

//get upload by id
$upload = JobUtil::getUploadById(RequestUtil::get('uploadid'));

//get job by id
$myJob = new Job(MapUtil::get($upload, 'job_id'));

if(!$upload || !$myJob->exists()) {
	JsonUtil::error('Invalid reference');
	die();
}

if(!ModuleUtil::checkJobModuleAccess('delete_uploads', $myJob)) {
	JsonUtil::error('Invalid permissions');
	die();
} else {
    //build and execute query to delete upload
    $sql = "select filename from uploads WHERE upload_id = '{$upload['upload_id']}'";
    $result = DBUtil::queryToArray($sql,'filename');
    $$filename='';
    if(count($result)>0)
    {
    	foreach($result as $row)
    		$filename=$row['filename'];    

    	$file_path=UPLOADS_PATH.'/'.$filename;  	    	
    	unlink($file_path);

        $sql = "delete from uploads WHERE upload_id = '{$upload['upload_id']}'";
        DBUtil::query($sql);

        LogUtil::getInstance()->logNotice(UserModel::getUserDetailsForLogger() . " deactivated upload ID: {$upload['upload_id']}");
		JsonUtil::success('Upload succesfully removed');

     } else {
		 JsonUtil::error('Operation failed');
     }
}