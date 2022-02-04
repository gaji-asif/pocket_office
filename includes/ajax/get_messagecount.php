<?php 

include '../common_lib.php'; 

if(!UserModel::loggedIn())
  die();

$sql = "select count(message_link_id) from message_link where user_id='".$_SESSION['ao_userid']."' and message_link.delete='0' and timestamp is NULL";
$res = DBUtil::query($sql);
list($total_messages) = mysqli_fetch_row($res);

$total_messages_print = $total_messages;
if($total_messages==0)
  $total_messages_print = '';

echo $total_messages_print;