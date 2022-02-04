<?php
include '../common_lib.php';
//=======================================
$link = $_POST['links'];
$jid = $_POST['jid'];
$hrefs = explode('__',substr($_POST['href'], 0,-1));

//echo '<pre>';print_r($_POST);echo "</pre>";exit;
//echo $href;exit;
if(!empty($_POST['href']) != '')
{
	foreach ($hrefs as $key => $value) {
		$sql = "INSERT INTO dropboxfiles (ref_link, job_id,link) VALUES ('$link', '$jid', '$value')";
		DBUtil::query($sql);
	}
	echo 'success';
}
else
{
	echo 'Please provide link';
}
?>