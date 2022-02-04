<?php 
include '../common_lib.php';
UserModel::isAuthenticated();

$sql = "select user_id from logged_in where account_id='".$_SESSION['ao_accountid']."' and date_sub(now(), interval 1 minute)<last_activity group by user_id";
$res = DBUtil::query($sql);
$num_users_online=mysqli_num_rows($res);

if($num_users_online=='')
  echo '0';
else echo $num_users_online;

?>