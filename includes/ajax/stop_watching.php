<?php

include '../common_lib.php';

//get data
$conversation_id = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['conversationid']);
$type = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['type']);

if(empty($conversation_id) || empty($type))
{
	JsonUtil::error('Invalid reference');
	die();
}

//build and execute query to delete journal
$sql = "DELETE FROM watching
		WHERE conversation_id = '$conversation_id'
			AND type = '$type'
		LIMIT 1";
$results = DBUtil::query($sql);

if($results === false)
{
	JsonUtil::error('Operation failed');
}
else
{
	JsonUtil::success('Watch removed');
}
die();