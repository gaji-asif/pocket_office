<?php
include '../common_lib.php';
UserModel::isAuthenticated();



$sql = "select messages.subject, messages.body, messages.timestamp, users.fname, users.lname, message_link.timestamp, message_link.delete".
       " from messages, message_link, users".
       " where messages.message_id=message_link.message_id".
       " and message_link.message_link_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."'".
       " and message_link.user_id=users.user_id".
       " limit 1";

$res = DBUtil::query($sql);
$num_rows = mysqli_num_rows($res);

?>

<table width="100%" border="0" class="data-table" cellpadding=5 cellspacing="0">

<?php
if(mysqli_num_rows($res)==0)
{
?>
  <tr><td align="center" colspan=4><b>Message Not Found</b></td></tr>
<?php
}
else
{
  list($subject, $body, $sent, $fname, $lname, $read, $delete)=mysqli_fetch_row($res);

  $body = prepareText($body);

  if($read=='')
    $read_str = " - Unread";
  else
  {
	$read = DateUtil::formatDateTime($read);
	$read_str = " - Read on $read";
  }

  $sent = DateUtil::formatDateTime($sent);
?>

  <tr class='odd' valign='middle'>
    <td width=16>
      <img src="images/icons/right_16.png">
    </td>
    <td class='data-table-cell'>
<?php
    if($delete==0)
    {
?>
      <b><?php echo $subject; ?></b>
<?php
    }
    else
    {
?>
      <s><b><?php echo $subject; ?></b></s>
<?php
    }
?>
      <span class='smallnote'><?php echo $read_str; ?></span>
    </td>
    <td width=200 class='data-table-cell'>
      <?php echo $sent; ?>
    </td>
    <td width=200 class='data-table-cell'>
      <?php echo $lname.", ".$fname; ?>
    </td>
  </tr>
  <tr>
    <td colspan=4>
      <table border="0" width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td class="smalltitle">
            Message:
          </td>
        </tr>
        <tr>
          <td>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" class='listtable'>
              <tr>
                <td width="25%" class="listitemnoborder"><b>Subject:</b></td>
                <td class="listrownoborder"><?php echo $subject; ?></td>
              </tr>
              <tr>
                <td class="listitem"><b>To:</b></td>
                <td class="listrow"><a href="users.php?id=<?=$from_id?>" tooltip><?=$lname?>, <?=$fname?></a></td>
              </tr>
              <tr>
                <td class="listitem"><b>Sent:</b></td>
                <td class="listrow"><?php echo $sent; ?></td>
              </tr>
              <tr>
                <td class="listitem"><b>Read:</b></td>
                <td class="listrow"><?php echo $read; ?></td>
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
    <td colspan=4 class='infofooter'>
      <a class="basiclink" href="javascript:Request.make('includes/ajax/get_sentmessagelist.php', 'messagecontainer', true, true);">
		<i class="icon-double-angle-left"></i>&nbsp;Back
	  </a>
    </td>
  </tr>
<?php
}
?>
</table>

