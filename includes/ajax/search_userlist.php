<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_users'))
  die("Insufficient Rights");

$sql = "select count(user_id)".
       " from users".
       " where (fname like '%".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search'])."%' || lname like '%".$_GET['search']."%' || dba like '%".$_GET['search']."%') and account_id='".$_SESSION['ao_accountid']."'";
$res = DBUtil::query($sql);
list($total_res)=mysqli_fetch_row($res);

$sql = "select user_id, fname, lname, reg_date, is_active, is_deleted, dba".
       " from users".
       " where (fname like '%".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search'])."%' || lname like '%".$_GET['search']."%' || dba like '%".$_GET['search']."%') and account_id='".$_SESSION['ao_accountid']."'".
       " ".mysqli_real_escape_string(DBUtil::Dbcont(),$sort)." ".mysqli_real_escape_string(DBUtil::Dbcont(),$limit_str);

$res = DBUtil::query($sql);
$num_rows = mysqli_num_rows($res);

?>

<table width="100%" border="0" class="data-table" cellpadding=5 cellspacing="0">
  <tr>
    <td colspan=4>
      <b>Searching '<?php echo $_GET['search']; ?>' - <?php echo $total_res ?> result(s) found</b>
    </td>
  </tr>

<?php

$i=1;
while(list($user_id, $fname, $lname, $date, $active, $deleted, $dba)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';

  $date = DateUtil::formatDate($date);
  $icon='<img src="images/icons/'.$filetype.'.png">';

  $active_str = '';
  if($active==0)
    $active_str = ' - <font color="red">INACTIVE</font>';

  $name_str = $lname.", ".$fname;
  if($deleted==1)
    $name_str = "<s>".$name_str."</s>";
?>

  <tr class='<?php echo $class; ?>' valign='middle' onclick="window.location='users.php?id=<?php echo $user_id; ?>';" onmouseover='hoverRow(this);' onmouseout='hoverRowOut(this, "<?php echo $class; ?>");'>
    <td class='data-table-cell'>
      <b><?php echo $name_str.$active_str; ?></b>
    </td>
    <td width=244 class='data-table-cell'>
      <?php echo $dba; ?>
    </td>
    <td width=200 class='data-table-cell'>
      <?php echo $date; ?>
    </td>
  </tr>

<?php
  $i++;
}
?>
</table>