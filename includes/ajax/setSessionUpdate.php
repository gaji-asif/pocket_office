<?php 
include '../common_lib.php';

$status = RequestUtil::get('status');
$session_status = AuthModel::sessionHasExpired();
	

echo $session_status;exit;