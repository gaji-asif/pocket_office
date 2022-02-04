<?php

include '../common_lib.php';
if(!viewWidget('widget_urgent')||!ModuleUtil::checkAccess('view_jobs')) { die(); }

echo ViewUtil::loadView('doc-head');

if(moduleOwnership('view_jobs'))
    $ownership = "((subscribers.user_id='".$_SESSION['ao_userid']."' &&  subscribers.job_id=jobs.job_id) || jobs.user_id=".$_SESSION['ao_userid']." || jobs.salesman=".$_SESSION['ao_userid']." || jobs.referral='".$_SESSION['ao_userid']."') and";


$sql = "select jobs.job_id, jobs.job_number, customers.fname, customers.lname, jobs.stage_num, (datediff(curdate(), jobs.stage_date)-(stages.duration)), datediff(curdate(), jobs.stage_date), stages.duration, repairs.repair_id, jobs.pif_date, jobs.ins_approval, jobs.referral_paid".
       " from customers, jobs".
       " left join stages on (stages.stage_num=jobs.stage_num)".
       " left join subscribers on (subscribers.job_id=jobs.job_id)".
       " left join repairs on (repairs.job_id=jobs.job_id and repairs.completed is null)".
       " left join status_holds on (status_holds.job_id=jobs.job_id)".
       " where ".$ownership." jobs.customer_id=customers.customer_id".
       " and jobs.account_id='".$_SESSION['ao_accountid']."'".
       " and stages.duration>-1".
       " and (datediff(curdate(), jobs.stage_date)-stages.duration)>-3".
       " and duration <> 9999 and duration is not null and duration <> ''".
       " and status_holds.status_hold_id is null".
       " group by jobs.job_id".
       " order by datediff(curdate(), jobs.stage_date)-(stages.duration) desc";
       //echo $sql;

$res = DBUtil::query($sql);

?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                Urgent Jobs
              </td>
              <td align="right">
              <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="infocontainernopadding">
          <table width="100%" border="0" cellpadding=5 cellspacing="0">
<?php

if(mysqli_num_rows($res)==0)
{
?>
          <tr valign='top'>
            <td style='font-weight: bold;' align="center"><b>No Urgent Jobs Found</b></td>
          </tr>
<?php
}
else
{
?>
          <tr valign='top'>
            <td colspan=6 class='widgetheader'>
              <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                  <td width=28>&nbsp;</td>
                  <td width=28>&nbsp;</td>
                  <td width=146><b>ID Number</b></td>
                  <td><b>Customer Name</b></td>
                  <td width=330><b>Stage</b></td>
                  <td width=90 align="center"><b>DAS</b></td>
                </tr>
              </table>
            </td>
          </tr>
<?php
}

$i=1;
while(list($job_id, $job_num, $fname, $lname, $stage_num, $days_past, $das, $duration, $repair_id, $paid, $ins_approval, $ref_paid)=mysqli_fetch_row($res))
{
  if($days_past<0)
    $color = "yellow";
  else if($days_past<6)
    $color = "orange";
  else if($days_past>5)
    $color = "red";

  $class='odd';
  if($i%2==0)
    $class='even';

  $date = DateUtil::formatDate($date);

  $stages = JobUtil::getCSVStages($job_id);

  $repair_str = '';
  if($repair_id!='')
    $repair_str = ", <span style='color: red; font-weight: bold;'>REPAIR</span>";

  $paid_icon = 'dollar_grey_16';
  $paid_alt = 'Job Not Paid in Full';
  if($paid!='')
  {
    $paid_icon = 'dollar_16';
    $paid_alt = 'Job Paid in Full on '.$paid;
  }

  $approve_icon = "shield";
  $approve_alt = "Claim Not Approved";
  if($ins_approval!='')
  {
    $approve_icon = "yellow-tick-shield";
    $approve_alt = "Claim Approved on ".DateUtil::formatDate($ins_approval)." @ ".DateUtil::formatTime($ins_approval);
  }
  if($ref_paid!='')
  {
    $approve_icon = "tick-shield";
    $approve_alt = "Referral Paid on ".DateUtil::formatDate($ref_paid)." @ ".DateUtil::formatTime($ref_paid);
  }

?>
          <tr valign='top' class='<?php echo $class; ?>' onclick='parent.location="<?=ROOT_DIR?>/jobs.php?id=<?php echo $job_id; ?>";' onmouseover='hoverRow(this);' onmouseout='hoverRowOut(this, "<?php echo $class; ?>");'>
            <td width=16><img title='<?php echo $paid_alt; ?>' src='<?=IMAGES_DIR?>/icons/<?php echo $paid_icon; ?>.png' tooltip></td>
            <td width=16><img title='<?php echo $approve_alt; ?>' src='<?=IMAGES_DIR?>/icons/<?php echo $approve_icon; ?>.png' tooltip></td>
            <td width=137><b><?php echo $job_num; ?></b></td>
            <td><?php echo prepareText($lname).", ".prepareText($fname); ?></td>
            <td width=325 style='font-size: 10px;'><b>#<?php echo $stage_num;?>:</b> <?php echo $stages.$repair_str; ?></td>
            <td width=82 align="center" style='background-color:<?php echo $color; ?>;'>
              <b><?php echo $das; ?></b>
              / <?php echo $duration; ?>
            </td>
          </tr>
<?php
    $i++;
  }
?>
        </td>
      </tr>
    </table>
  </body>
</html>