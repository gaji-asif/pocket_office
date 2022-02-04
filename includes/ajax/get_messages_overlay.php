<?php 

include '../common_lib.php'; 

if(!UserModel::loggedIn())
  die();

$sql = "select message_link.message_id, users.fname, users.lname, messages.subject, messages.body, messages.timestamp, message_link.timestamp ".
       "from message_link, messages, users ".
       "where message_link.user_id='".$_SESSION['ao_userid']."' and message_link.delete = 0 and message_link.message_id=messages.message_id and users.user_id=messages.user_id limit 8";
$res = DBUtil::query($sql);
?>
<table width="250" cellpadding="0" cellspacing="0" border="0">
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" class="overlay_title">
        <tr>
          <td>Messages</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table width="100%" cellpadding="0" cellspacing="0" border="0" class="infocontainernopadding">
        <tr>
          <td>
<?php

if(mysqli_num_rows($res)==0)
  echo "<br /><center><b>No New Messages</b></center>&nbsp;";

$i=0;
while(list($message_id, $fname, $lname, $subject, $body, $timestamp, $timestamp_read)=mysqli_fetch_row($res))
{
    $subject = prepareText($subject);
    $body = str_replace(array("\n","<br />","<br>"), " ", $body);
    $body = prepareText($body);
		$body = substr(trim($body), 0, 40)."...";
		
		$icon = 'bubble_32';
		if(!empty($timestamp_read))
			$icon = 'bubble_32_grey';
?>
            <div class="notification_wrapper" onclick="window.location='messaging.php?id=<?php echo $message_id; ?>';">
              <table width="250" cellpadding="0" cellspacing="0" border="0">
                <tr valign='top'> 
                  <td style="padding:5px;" width="32">
                    <img src="<?php echo ACCOUNT_URL; ?>/images/icons/<?php echo $icon; ?>.png" />      
                  </td>
                  <td class='notification_cell'>
                    <b><?php echo $subject; ?></b>
                    <br /><?php echo $body; ?>
                    <br /><font color="#0086CC"><?php echo $fname[0].". ".$lname; ?></font>
                    <span class="smallnote">(<?php echo DateUtil::formatDate($timestamp); ?> @ <?php echo DateUtil::formatTime($timestamp); ?>)</span>
                  </td>
                </tr>
              </table>
            </div>
<?php
  $i++;
}
?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

