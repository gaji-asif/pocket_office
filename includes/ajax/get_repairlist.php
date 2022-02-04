<?php

include '../common_lib.php';

$windowHeight = RequestUtil::get('window_height', DEFAULT_WINDOW_HEIGHT);
$_RES_PER_PAGE = calcResultsPerPage($windowHeight);

if(!ModuleUtil::checkAccess('view_repairs'))
  die("Insufficient Rights");

if($_GET['limit']=='')
  $_GET['limit'] = 0;

$limit_str = "limit ".$_GET['limit'].", ".$_RES_PER_PAGE;

$sort = "order by repairs.timestamp desc";
if($_GET['sort']!='')
  $sort = $_GET['sort'];

if($_GET['search']=='Search...')
  $_GET['search']='';


if(moduleOwnership('view_repairs'))
    $ownership = "(repairs.user_id=".$_SESSION['ao_userid']." || repairs.salesman=".$_SESSION['ao_userid'].") and";
if($_GET['search']!='')
{
  $sql = "select count(repairs.repair_id)".
         " from repairs, jobs, customers".
         " where ".$ownership." (jobs.job_number like '%".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search'])."%' || customers.address like '%".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search'])."%' || customers.fname like '%".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search'])."%' || customers.lname like '%".$_GET['search']."%')".
         " and jobs.customer_id=customers.customer_id  and jobs.job_id=repairs.repair_id and repairs.account_id='".$_SESSION['ao_accountid']."'";
  $res = DBUtil::query($sql);
  list($total_res)=mysqli_fetch_row($res);

  $sql = "select repairs.repair_id, jobs.job_number, customers.fname, customers.lname, datediff(curdate(), repairs.timestamp), priority.priority, repair_status.status, fail_types.fail_type".
         " from fail_types, customers, repairs, priority, repair_status, jobs".
         " where ".$ownership." (jobs.job_number like '%".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search'])."%' || customers.address like '%".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search'])."%' || customers.fname like '%".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search'])."%' || customers.lname like '%".$_GET['search']."%')".
         " and repairs.fail_type=fail_types.fail_type_id and jobs.customer_id=customers.customer_id and repairs.account_id='".$_SESSION['ao_accountid']."'".
         " and repairs.priority=priority.priority_id and repairs.status=repair_status.status_id and jobs.job_id=repairs.job_id".
         " group by repairs.repair_id ".mysqli_real_escape_string(DBUtil::Dbcont(),$sort)." ".mysqli_real_escape_string(DBUtil::Dbcont(),$limit_str);
}
else
{
  $sql = "select count(repairs.repair_id)".
         " from repairs".
         " where ".$ownership." repairs.account_id='".$_SESSION['ao_accountid']."'";
  $res = DBUtil::query($sql);
  list($total_res)=mysqli_fetch_row($res);

  $sql = "select repairs.repair_id, jobs.job_number, customers.fname, customers.lname, datediff(curdate(), repairs.timestamp), priority.priority, repair_status.status, fail_types.fail_type".
         " from fail_types, customers, repairs, priority, repair_status, jobs".
         " where ".$ownership." jobs.customer_id=customers.customer_id and repairs.account_id='".$_SESSION['ao_accountid']."'".
         " and repairs.fail_type=fail_types.fail_type_id and repairs.priority=priority.priority_id and repairs.status=repair_status.status_id and jobs.job_id=repairs.job_id".
         " group by repairs.repair_id ".mysqli_real_escape_string(DBUtil::Dbcont(),$sort)." ".mysqli_real_escape_string(DBUtil::Dbcont(),$limit_str);
}
$res = DBUtil::query($sql);
$num_rows = mysqli_num_rows($res);
?>

<table width="100%" border="0" class="data-table" cellpadding=5 cellspacing="0">

<?php
if($_GET['search']!='')
{
?>
  <tr>
    <td colspan=10>
      <b>Searching '<?php echo $_GET['search']; ?>' - <?php echo $total_res ?> result(s) found</b>
    </td>
  </tr>
  <tr>
    <td colspan=10>
		<a href="javascript:Request.make('includes/ajax/get_repairlist.php', 'repairscontainer', true, true);" class='basiclink'>
			<i class="icon-double-angle-left"></i>&nbsp;Back
		</a>
    </td>
  </tr>
<?php
}
else if($num_rows==0)
{
?>
  <tr valign='middle'>
    <td align="center" colspan=10>
      <b>No Repairs Found</b>
    </td>
  </tr>
<?php
}
?>

<?php

$i=1;
while(list($repair_id, $job_number, $cust_fname, $cust_lname, $age, $priority, $status, $fail_type)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';

  $diff = $stage_age-$duration;

  $icon='<img src="images/icons/'.$filetype.'.png">';

  $stages = JobUtil::getCSVStages($job_id);

  if($duration=='9999' || empty($duration))
    $duration = "No Limit";

?>

  <tr class='<?php echo $class; ?>' valign='middle' onclick="Request.make('<?=AJAX_DIR?>/get_repair.php?id=<?php echo $repair_id; ?>', 'repairscontainer', true, true);" onmouseover='hoverRow(this);' onmouseout='hoverRowOut(this, "<?php echo $class; ?>");'>
    <td width=147 class='data-table-cell'>
      <b><?php echo $job_number; ?></b>
    </td>
    <td class='data-table-cell'>
      <?php echo $cust_lname.", ".$cust_fname; ?>
    </td>
    <td width=175 class='data-table-cell'>
      <?php echo $fail_type; ?>
    </td>
    <td width=150 class='data-table-cell'>
      <?php echo $status; ?>
    </td>
    <td width=154 class='data-table-cell'>
      <?php echo $priority; ?>
    </td>
    <td width="25%" align="center" align="center">
      <?php echo $age; ?>
    </td>
  </tr>

<?php
  $i++;
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
    <a href='javascript:Request.make("includes/ajax/get_repairlist.php?limit=<?php echo ($_GET['limit']-$_RES_PER_PAGE); ?>&filter=<?php echo $_GET['filter']; ?>&sort=<?php echo $_GET['sort']; ?>&search=<?php echo $_GET['search']; ?>", "repairscontainer", "yes", "yes");' class='basiclink'>&lt;&lt;Prev <?php echo $_RES_PER_PAGE; ?></a>
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
  $next_number = $total_res-($_GET['limit']+$_RES_PER_PAGE);
  if($next_number>$_RES_PER_PAGE)
    $next_number = $_RES_PER_PAGE;
  if($_GET['limit']+$_RES_PER_PAGE>=$total_res)
  {
  }
  else
  {
?>
      <a href='javascript:Request.make("includes/ajax/get_repairlist.php?limit=<?php echo ($_GET['limit']+$_RES_PER_PAGE); ?>&filter=<?php echo $_GET['filter']; ?>&sort=<?php echo $_GET['sort']; ?>&search=<?php echo $_GET['search']; ?>", "repairscontainer", "yes", "yes");' class='basiclink'>Next <?php echo $next_number; ?> &gt;&gt;</a>
<?php
  }
?>
    </td>
  </tr>
</table>
<?php
}
?>