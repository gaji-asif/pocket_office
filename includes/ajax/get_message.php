<?php
include '../common_lib.php';
UserModel::isAuthenticated();

$sql = "select messages.subject, messages.body, messages.timestamp, users.fname, users.lname, message_link.timestamp, messages.user_id".
       " from messages, message_link, users".
       " where message_link.delete=0 and messages.message_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."'".
       " and message_link.message_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."'".
       " and messages.user_id=users.user_id".
       " limit 1";

$res = DBUtil::query($sql);
$num_rows = mysqli_num_rows($res);

?>

<table width="100%" border="0" class="data-table" cellpadding="5" cellspacing="0">
<?php
if(mysqli_num_rows($res)==0)
{
?>
  <tr><td align="center" colspan=4><b>Message Not Found</b></td></tr>
<?php
}
else
{
  list($subject, $body, $sent, $fname, $lname, $read, $from_id)=mysqli_fetch_row($res);
  UserModel::storeBrowsingHistory($subject, 'email_16', 'messaging.php', mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id']));

  $reply_body = "\n\r\n\r\n\rORIGINAL MESSAGE:\n&quot;".stripslashes($body)."&quot;";
  $body = prepareText($body);

  if($read=='')
  {
    $sql = "update message_link set timestamp=now() where message_id='".$_GET['id']."' and user_id='".$_SESSION['ao_userid']."' limit 1";
    DBUtil::query($sql);

  }
  else $read = DateUtil::formatDateTime($read);

  $sent = DateUtil::formatDateTime($sent);
?>

  <tr class='odd' valign='middle'>
    <td width=16>
      <img src="images/icons/bubble_16_grey.png">
    </td>
    <td class='data-table-cell smalltitle'><?php echo $subject; ?></td>
    <td width="15%" class='data-table-cell'>
      <?php echo $sent; ?>
    </td>
    <td width="20%" class='data-table-cell'>
      <?php echo $lname.", ".$fname; ?>
    </td>
  </tr>
  <tr>
    <td colspan=4>
      <table border="0" width="100%" cellpadding="0" cellspacing="0">
        <tr valign='bottom'>
          <td class="smalltitle">
            Message:
          </td>
          <td align="right">
            <input type='button' value='Mark Unread' onclick='markUnread("<?php echo $_GET['id']; ?>")'>
            <input type='button' value='Delete' onclick='deleteMessage("<?php echo $_GET['id']; ?>")'>
          </td>
        </tr>
        <tr>
          <td colspan=2>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class='listtable'>
              <tr>
                <td width="25%" class="listitemnoborder"><b>Subject:</b></td>
                <td class="listrownoborder"><?php echo $subject; ?></td>
              </tr>
              <tr>
                <td class="listitem"><b>From:</b></td>
                <td class="listrow"><a href="users.php?id=<?=$from_id?>" tooltip><?php echo $lname.", ".$fname; ?></a></td>
              </tr>
              <tr>
                <td class="listitem"><b>Sent:</b></td>
                <td class="listrow"><?php echo $sent; ?></td>
              </tr>
              <tr valign='top'>
                <td class="listitem"><b>Message:</b></td>
                <td class="listrow"><?php echo $body; ?></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan=4>
      <table border="0" width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td class="smalltitle">
            Send Reply:
          </td>
        </tr>
        <tr>
          <td>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class='listtable'>
              <tr>
                <td width="25%" class="listitemnoborder"><b>Subject:</b></td>
                <td class="listrownoborder">
                  <form name='reply' method="post" action='messaging.php' style='margin:0;' onsubmit='return checkReply();'>
                  RE: <?php echo $subject; ?>
                </td>
              </tr>
              <tr>
                <td class="listitem"><b>To:</b></td>
                <td class="listrow"><?php echo $lname.", ".$fname; ?></td>
              </tr>
              <tr valign='top'>
                <td class="listitem"><b>Message:</b></td>
                <td class="listrow">
                  <input type='hidden' name='to' value='<?php echo $from_id; ?>'>
                  <input type='hidden' name='subject' value='RE: <?php echo $subject; ?>'>
                  <textarea name='body' id='body' style='width:100%;' rows=10><?php echo $reply_body; ?></textarea>
                </td>
              </tr>
              <tr valign='top'>
                <td align="right" colspan=2 class="listrow">
                  <input type="submit" value='Reply'>
                  </form>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td colspan=10 class='infofooter'>
		<a class="basiclink" href="javascript:Request.make('includes/ajax/get_messagelist.php', 'messagecontainer', true, true);">
		  <i class="icon-double-angle-left"></i>&nbsp;Back
		</a>
    </td>
  </tr>
<?php
}
?>
</table>

