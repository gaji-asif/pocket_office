<?php 
include '../common_lib.php';
UserModel::isAuthenticated();

if(isset($_GET['action']) && $_GET['action']=='toggle' && $_GET['num']!='')
{
  if($_GET['checked']=='checked')
    $sql = "delete from stage_notifications where stage_num='".$_GET['num']."' and user_id='".$_SESSION['ao_userid']."' limit 1";
  else $sql = "insert into stage_notifications values('', '".$_GET['num']."', '".$_SESSION['ao_userid']."')";
  
  DBUtil::query($sql);
}

if(isset($_GET['action']) && $_GET['action']=='togglesms' && $_GET['num']!='')
{
  if($_GET['checked']=='checked')
    $sql = "delete from stage_notifications_sms where stage_num='".$_GET['num']."' and user_id='".$_SESSION['ao_userid']."' limit 1";
  else $sql = "insert into stage_notifications_sms values('', '".$_GET['num']."', '".$_SESSION['ao_userid']."')";
  
  DBUtil::query($sql);
}

$sql = "select stages.stage_num, stage_notifications.stage_notification_id, stage_notifications_sms.stage_notification_sms_id". 
       " from stages". 
       " left join". 
       " stage_notifications".  
       " on". 
       " (stages.stage_num=stage_notifications.stage_num and stage_notifications.user_id='".$_SESSION['ao_userid']."')". 
       " left join".
       " stage_notifications_sms".
       " on".
       " (stages.stage_num=stage_notifications_sms.stage_num and stage_notifications_sms.user_id='".$_SESSION['ao_userid']."')".
       " where stages.account_id='".$_SESSION['ao_accountid']."'". 
       " group by stages.stage_num order by stages.stage_num asc";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
{
?>
<table border="0" width="100%">
  <tr valign='top'>
    <td>
      <b>No Stages</b>
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
        <tr valign='top'>
          <td align="center"><img src='<?=IMAGES_DIR?>/icons/info_16.png'></td>
          <td colspan=3 class='smallnote'>
            Stage notifications apply only to subscribed jobs
            <br />E = Email Notification
            <br />T = SMS Text Notification
          </td>
        </tr>
        <tr><td colspan=4>&nbsp;</td></tr>
        <tr>
          <td align="center"><b>E</b></td>
          <td align="center"><b>T</b></td>
          <td></td>
          <td></td>
        </tr>  
<?php

  while (list($stage_num, $notification, $notification_sms) = mysqli_fetch_row($res))
  {
    $checked='';
    if($notification!='')
      $checked='checked';
    $checked_sms='';
    if($notification_sms!='')
      $checked_sms='checked';
?>
        <tr>
          <td width=20>
            <input type='checkbox' <?php echo $checked; ?> onclick='Request.make("includes/ajax/get_stagenotifications.php?num=<?php echo $stage_num; ?>&action=toggle&checked=<?php echo $checked; ?>", "stagenotificationscontainer", "", "yes");'>
          </td>
          <td width=20>
            <input type='checkbox' <?php echo $checked_sms; ?> onclick='Request.make("includes/ajax/get_stagenotifications.php?num=<?php echo $stage_num; ?>&action=togglesms&checked=<?php echo $checked_sms; ?>", "stagenotificationscontainer", "", "yes");'>
          </td>
          <td align="right" width=20>
            <b><?php echo $stage_num; ?>.</b>
          </td>
          <td>
<?php  
    $sql = "select stage from stages where stage_num=".$stage_num." order by stage_num asc";
    $res_inner = DBUtil::query($sql);
    
    $rows = mysqli_num_rows($res_inner);
    $i=1;
    while(list($stage) = mysqli_fetch_row($res_inner))
    {
      echo $stage;
      if($i!=$rows)
        echo ", ";
      $i++;
    }
?>
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