<?php
include '../common_lib.php';
//=======================================
$link = $_POST['links'];
$jid = $_POST['jid'];
if($_POST['links'] != '')
{
	$sql = "INSERT INTO dropbox (link, job_id) VALUES ('$link', '$jid')";
	
	if(DBUtil::query($sql)) {
		echo 'success';
	}
	else
	{
		echo 'failure';
	}
}
else
{
	echo 'Please provide link';
}
?>