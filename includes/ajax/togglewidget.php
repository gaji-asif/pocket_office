<?php 
include '../common_lib.php';
UserModel::isAuthenticated();

$widget = $_GET['widget'];
$state = $_GET['state'];

$sql = "update settings set ".mysqli_real_escape_string(DBUtil::Dbcont(),$widget)."='".mysqli_real_escape_string(DBUtil::Dbcont(),$state)."' limit 1";
DBUtil::query($sql);

$_SESSION['ao_'.$widget]=$state;

?>