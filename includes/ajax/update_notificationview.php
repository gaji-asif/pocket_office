<?php 

include '../common_lib.php'; 

if(!UserModel::loggedIn())
  die();

$sql = "insert into view_notifications values(0, '".$_SESSION['ao_userid']."', now())";
DBUtil::query($sql);  
?>