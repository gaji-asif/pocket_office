<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_announcements'))
  die("Insufficient Rights");

$sql = "select announcements.announcement_id, announcements.subject, announcements.timestamp, users.fname, users.lname".
         " from announcements, users".
         " where (announcements.subject like '%".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search'])."%' || announcements.body like '%".$_GET['search']."%') and announcements.user_id=users.user_id and announcements.account_id='".$_SESSION['ao_accountid']."' and announcements.min_level>='".$_SESSION['ao_level']."'".
         " order by announcements.timestamp desc";

$res = DBUtil::query($sql);
$num_rows = mysqli_num_rows($res);


?>

<table width="100%" border="0" class="data-table" cellpadding=5 cellspacing="0">
<tr>
  <td colspan=4>
    <b>Searching '<?php echo $_GET['search']; ?>' - <?php echo $num_rows ?> result(s) found</b>
  </td>
</tr>
<?php
$i=1;
while(list($announcement_id, $subject, $date, $fname, $lname)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';

  $date = DateUtil::formatDateTime($date);

  $link_class='basiclink';
  $icon='<img src="images/icons/bubble_16_grey.png">';
  if(!AnnouncementModel::isRead($announcement_id))
  {
    $link_class='boldlink';
    $icon='<img src="images/icons/bubble_16.png">';
  }
?>

  <tr class='<?php echo $class; ?>' valign='middle' onclick="window.location='announcements.php?id=<?php echo $announcement_id; ?>';" onmouseover='hoverRow(this);' onmouseout='hoverRowOut(this, "<?php echo $class; ?>");'>
    <td width=16>
      <?php echo $icon; ?>
    </td>
    <td class='data-table-cell'>
      <b><?php echo $subject; ?></b>
    </td>
    <td width=200 class='data-table-cell'>
      <?php echo $date; ?>
    </td>
    <td width=200 class='data-table-cell'>
      <?php echo $lname.", ".$fname; ?>
    </td>
  </tr>

<?php
  $i++;
}
?>
</table>

