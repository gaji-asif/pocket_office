<?php
include '../common_lib.php';
//=======================================
$jid = $_POST['job_id'];
$folderlink = $_POST['folderlink'];
$drop_id = $_POST['drop_id'];
if(isset($jid) and $jid !='')
{

	/*
	$sql = "update dropbox set is_delete = 1 where drop_id = ".$drop_id;
	DBUtil::query($sql);

	//$sql = "delete from dropboxfiles where job_id = $jid and ref_link = '".$folderlink."'";
	//DBUtil::query($sql);
	echo 'success';
	*/

	$sql = "delete from dropbox where drop_id = ".$drop_id;
	DBUtil::query($sql);

	$sql = "delete from dropboxfiles where job_id = $jid and ref_link = '".$folderlink."'";
	DBUtil::query($sql);
	echo 'success';
}
else
{
	echo 'Please provide link';
}
?>