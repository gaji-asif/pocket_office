<?php

set_time_limit (0);

include '../common_lib.php';

//get session ID
//$session_id = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, MCRYPT_SECRET_KEY, $_GET['session_info'], MCRYPT_MODE_ECB);

list($rand_session_code, $upload_session_id) = explode('|', $_GET['session_info']);

//hack to fix session/database issue
$_SESSION['database_name'] = $_GET['dbname'];
DBUtil::connect($_GET['dbname']);

//print "Rand session code: $rand_session_code<hr>Rand session ID: $rand_session_id<hr>";

//print 'select session_id from upload_sessions where id='.intval($upload_session_id).' and rand_session_code='.intval($rand_session_code);
$query = DBUtil::query('select session_id, account_id, user_id from upload_sessions where id='.intval($upload_session_id).' and rand_session_code='.intval($rand_session_code)) ;
$result = mysqli_fetch_assoc($query);
$session_id = $result['session_id'];

if(!$session_id)
{
	print 'Invalid session ('.$upload_session_id.')';
	//print_r($result);
	exit;
}

//print "Switching to session #$session_id";
//session_start();
//session_id($session_id);
$_SESSION['ao_accountid'] = $result['account_id'];
$_SESSION['ao_userid'] = $result['user_id'];
/*
	ob_start();
	print_r($_REQUEST);
	$contents = ob_get_contents();
	ob_end_clean();

	mail('plsoucy@crealabs.com', 'Scan upload', $contents, 'From: Assure2 <assure@crealabs.com>');
*/

  /*
if(!ModuleUtil::checkAccess('upload_job_file'))
  die('Insufficient Rights');

print "Rights were sufficient\n";*/
$myJob = new Job(RequestUtil::get('id'));
/*
if(moduleOwnership('upload_job_file') && (!JobUtil::isSubscriber($myJob->job_id) && $myJob->salesman_id!=$_SESSION['ao_userid'] && $myJob->user_id!=$_SESSION['ao_userid']))
  die("Insufficient Rights");*/


	/*ob_start();
	print_r($_REQUEST);
	print_r($_FILES);
	$contents = ob_get_contents();
	ob_end_clean();

	mail('plsoucy@crealabs.com', 'Scan upload', $contents, 'From: Assure2 <assure@crealabs.com>');
	exit;*/


if(isset($_FILES['RemoteFile']))
{
	$file_extension = strtolower(substr($_FILES['RemoteFile']['name'], -4));
	if(!in_array($file_extension, array('.pdf', '.jpg', '.png')))
	{
		print 'Invalid file extension';
		exit;
	}

	$title = isset($_GET['title']) ? $_GET['title'] : '';

    $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
            VALUES ('{$myJob->job_id}', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '', '" . mysqli_real_escape_string(DBUtil::Dbcont(),$title) . "', now())";
                    
	DBUtil::query($sql);
	$upload_id = DBUtil::getInsertId();

	$filename = 'ul_' . $upload_id . '_' . preg_replace('/[^\da-zA-Z\-\_\.]/', '', $_FILES['RemoteFile']['name']);

	copy($_FILES['RemoteFile']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $filename);

	$sql = "update uploads set filename='$filename' where upload_id='$upload_id'";
	DBUtil::query($sql);


	JobModel::saveEvent($myJob->job_id, "Job File(s) Scanned");
	NotifyUtil::notifySubscribersFromTemplate('upload_job_file', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id));
	//print "File uploaded successfully";
}


//print 'No file to upload';


?>