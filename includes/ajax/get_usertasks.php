<?php 

include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('edit_users'))
  die('Insufficient Rights');

$myUser = new User(RequestUtil::get('id'));

if($_GET['action']=='toggle' && $_GET['task']!='')
{
  if($_GET['checked']=='checked')
    $sql = "delete from task_link where task_type_id='".intval($_GET['task'])."' and user_id='".$myUser->getUserID()."' limit 1";
  else $sql = "insert into task_link values('', '".$_SESSION['ao_accountid']."', '".intval($_GET['task'])."', '".$myUser->getUserID()."')";
  DBUtil::query($sql);
}

$sql = "select task_type.task_type_id, task_type.task, task_type.color, task_link.task_link_id". 
       " from task_type". 
       " left join". 
       " task_link".  
       " on". 
       " (task_type.task_type_id=task_link.task_type_id and task_link.user_id='".$myUser->getUserID()."')". 
       " where task_type.account_id='".$_SESSION['ao_accountid']."'". 
       " group by task_type.task_type_id order by task_type.task asc";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
{
?>
<table border="0" width="100%">
  <tr valign='top'>
    <td>
      <b>No Task Types</b>
    </td>
  </tr>
</table>
<?php
}
else
{
?>
<table border="0" width="100%">
  <tr valign='top'>
    <td>
      <table border="0" width="100%"> 
<?php

  while (list($task_type_id, $task_type, $color, $linked) = mysqli_fetch_row($res))
  {
    $checked='';
    if(!empty($linked))
      $checked='checked';
?>
        <tr>
          <td width=20>
            <input type='checkbox' <?php echo $checked; ?> onclick='Request.make("get_usertasks.php?id=<?php echo $myUser->getUserID(); ?>&task=<?php echo $task_type_id; ?>&action=toggle&checked=<?php echo $checked; ?>", "usertaskscontainer", "", "yes");'>
          </td>
          <td style="background-color:<?php echo $color; ?>;">
            <b><?php echo $task_type; ?></b>
          </td>
        </tr>
<?php
  }
?>
      </table>
    </td>
  </tr>
</table>
<?php
}
?>