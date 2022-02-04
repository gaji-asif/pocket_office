<?php 

set_time_limit (0);

if(isset($_POST['session_id']))
{
	session_id($_POST['session_id']);
}

include '../common_lib.php';
ModuleUtil::checkAccess('upload_job_file', TRUE);

$myJob = new Job(RequestUtil::get('id'));                                   

ModuleUtil::checkJobModuleAccess('upload_job_file', $myJob, TRUE);

if(!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$targetFile =  str_replace('//','/',$targetPath) . $_FILES['Filedata']['name'];

	$file_name = $_FILES['Filedata']['name'];
	$title = '';

    $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
            VALUES ('{$myJob->job_id}', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '', '".mysqli_real_escape_string(DBUtil::Dbcont(),$title)."', now())";
         
	DBUtil::query($sql);
	$upload_id = DBUtil::getInsertId();
	
	$filename = 'ul_'.$upload_id.'_'.preg_replace('/[^\da-zA-Z\-\_\.]/', '', $_FILES['Filedata']['name']);
	
	copy($tempFile, $_SERVER['DOCUMENT_ROOT'].'/uploads/'.$filename);
	
	$sql = "UPDATE uploads SET filename='$filename' WHERE upload_id='$upload_id'";
	DBUtil::query($sql);
	

	  JobModel::saveEvent($myJob->job_id, 'Job File(s) Scanned');
	  emailSubscribersFromTemplate('upload_job_file',$_SESSION['ao_userid'],$myJob->job_id, '','');   

	print "Upload successful";
	exit;
}

print "No file to upload";
exit;

?>