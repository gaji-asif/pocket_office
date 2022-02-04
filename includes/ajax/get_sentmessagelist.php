<?php
include '../common_lib.php';
UserModel::isAuthenticated();

$windowHeight = RequestUtil::get('window_height', DEFAULT_WINDOW_HEIGHT);
$_RES_PER_PAGE = calcResultsPerPage($windowHeight);

if($_GET['limit']=='')
  $_GET['limit'] = 0;

$limit_str = "limit ".$_GET['limit'].", ".$_RES_PER_PAGE;

if($_GET['search'] != '')
{
  $sql = "select count(messages.message_id)".
         " from messages, users, message_link".
         " where (messages.subject like '%".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search'])."%' || messages.body like '%".$_GET['search']."%')".
         " and messages.user_id='".$_SESSION['ao_userid']."'".
         " and messages.message_id=message_link.message_id".
         " and message_link.user_id=users.user_id";
  $res = DBUtil::query($sql);
  list($total_res)=mysqli_fetch_row($res);

  $sql = $sql = "select message_link.message_link_id, messages.subject, messages.timestamp, users.fname, users.lname, message_link.timestamp, message_link.delete".
         " from messages, users, message_link".
         " where (messages.subject like '%".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search'])."%' || messages.body like '%".$_GET['search']."%')".
         " and messages.user_id='".$_SESSION['ao_userid']."'".
         " and messages.message_id=message_link.message_id".
         " and message_link.user_id=users.user_id".
         " order by messages.timestamp desc";
         " ".mysqli_real_escape_string(DBUtil::Dbcont(),$limit_str);
}
else
{
  $sql = "select count(messages.message_id)".
         " from messages, users, message_link".
         " where messages.user_id='".$_SESSION['ao_userid']."'".
         " and messages.message_id=message_link.message_id".
         " and message_link.user_id=users.user_id";
  $res = DBUtil::query($sql);
  list($total_res)=mysqli_fetch_row($res);

  $sql = $sql = "select message_link.message_link_id, messages.subject, messages.timestamp, users.fname, users.lname, message_link.timestamp, message_link.delete".
         " from messages, users, message_link".
         " where messages.user_id='".$_SESSION['ao_userid']."'".
         " and messages.message_id=message_link.message_id".
         " and message_link.user_id=users.user_id".
         " order by messages.timestamp desc".
         " ".mysqli_real_escape_string(DBUtil::Dbcont(),$limit_str);
}
$res = DBUtil::query($sql);
$num_rows = mysqli_num_rows($res);


?>

<table width="100%" border="0" class="data-table" cellpadding=5 cellspacing="0">
<?php

if($_GET['search'] != '')
{
?>
  <tr>
    <td colspan=10>
      <b>Searching '<?php echo $_GET['search']; ?>' - <?php echo $num_rows ?> result(s) found</b>
    </td>
  </tr>
  <tr>
    <td colspan=10>
		<a href="javascript:Request.make('includes/ajax/get_sentmessagelist.php', 'messagecontainer', true, true);" class='basiclink'>
			<i class="icon-double-angle-left"></i>&nbsp;Back
		</a>
    </td>
  </tr>
<?php
}

if($_GET['id']=='')
{
  $i=1;
  while(list($message_link_id, $subject, $sent, $fname, $lname, $read, $delete)=mysqli_fetch_row($res))
  {
    $class='odd';
    if($i%2==0)
      $class='even';

    $sent = DateUtil::formatDateTime($sent);

    $link_class='basiclink';
    $icon='<img src="images/icons/right_16.png">';

    if($read=='')
      $read_str = " - Unread";
    else
	{
		$read = DateUtil::formatDateTime($read);
		$read_str = " - Read on $read";
	}

    $subject = prepareText($subject);
?>

  <tr class='<?php echo $class; ?>' valign='middle' onclick="Request.make('<?=AJAX_DIR?>/get_sentmessage.php?id=<?php echo $message_link_id; ?>', 'messagecontainer', true, true);" onmouseover='hoverRow(this);' onmouseout='hoverRowOut(this, "<?php echo $class; ?>");'>
    <td width=16>
      <?php echo $icon; ?>
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

<?php
    $i++;
  }
  if($num_rows==0 && $_GET['search']=='')
  {
?>
  <tr valign='middle'>
    <td align="center" colspan=10>
      <b>No Sent Messages Found</b>
    </td>
  </tr>
<?php
  }
}
?>
</table>

<?php
if(($i+$limit)<$total_res)
{
?>
<table border="0" width="100%">
  <tr>
    <td align="left" width='250'>
<?php
  if($_GET['limit']>1)
  {
?>
    <a href='javascript:Request.make("includes/ajax/get_sentmessagelist.php?limit=<?php echo ($_GET['limit']-$_RES_PER_PAGE); ?>&search=<?php echo $_GET['search']; ?>", "messagecontainer", "yes", "yes");' class='basiclink'>&lt;&lt;Prev <?php echo $_RES_PER_PAGE; ?></a>
<?php
  }
?>
    </td>
    <td align="center" width=200>
<?php
  if(($_GET['limit']+$_RES_PER_PAGE)>$total_res)
    echo "<b>Showing: ".($_GET['limit']+1)." - ".$total_res." of ".$total_res."</b>";
  else echo "<b>Showing: ".($_GET['limit']+1)." - ".($_GET['limit']+$_RES_PER_PAGE)." of ".$total_res."</b>";
?>
    </td>
    <td align="right" width='250'>
<?php
  if($_GET['limit']+$_RES_PER_PAGE>=$total_res)
  {
  }
  else
  {
?>
      <a href='javascript:Request.make("includes/ajax/get_sentmessagelist.php?limit=<?php echo ($_GET['limit']+$_RES_PER_PAGE); ?>&search=<?php echo $_GET['search']; ?>", "messagecontainer", "yes", "yes");' class='basiclink'>Next <?php echo $_RES_PER_PAGE; ?> &gt;&gt;</a>
<?php
  }
?>
    </td>
  </tr>
</table>
<?php
}
?>

