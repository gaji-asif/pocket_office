<?php
include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('edit_job_task'))
  die("Insufficient Rights");

$date = RequestUtil::get('d');
$contractor_id = RequestUtil::get('id');

if(empty($date)) { return; }

$php_date = strtotime($date);
$weekday = intval(date('w', $php_date) + 6 ) % 7;

$cur_date = $php_date - $weekday * 24 * 3600;
$cur_day_num = date('j', $cur_date);
//$prev_monday+24*3600;
//echo 'Monday of this week: '.date( 'l j F Y', $prev_monday );
//echo '<br />Next: '.date( 'l j F Y', $next_date );

?>
<table border="0" class='schedulecontainer' style='border-top: 1px solid #cccccc;'>
  <tr>
    <td width=100 align="center" style='font-weight: bold;'>Monday</td>
    <td width=100 align="center" style='font-weight: bold;'>Tuesday</td>
    <td width=100 align="center" style='font-weight: bold;'>Wednesday</td>
    <td width=100 align="center" style='font-weight: bold;'>Thursday</td>
    <td width=100 align="center" style='font-weight: bold;'>Friday</td>
    <td width=100 align="center" style='font-weight: bold;'>Saturday</td>
    <td width=100 align="center" style='font-weight: bold;'>Sunday</td>
  </tr>
  <tr valign='top' height=100>
<?php
for($i=$cur_day_num; $i<($cur_day_num+7); $i++)
{
  $cur_month = date('M',$cur_date);
  $cur_month_num = date('n',$cur_date)-1;
  $cur_day = date('j',$cur_date);
  $cur_year= date('Y',$cur_date);

  $cur_date_format = date('Y-m-d',$cur_date);

  $border = 'border: 1px solid #999999; background-color: #ffffff;';
  $background_hover = '#EDEDED';
  $background_out = '#ffffff';
  if($cur_date_format==$date)
    $border = 'border: 2px solid #0086CC; background-color: #ffffff;';
?>
    <td style='<?php echo $border; ?>' onmouseover='this.style.cursor="pointer"; this.style.backgroundColor="<?php echo $background_hover; ?>";' onmouseout='this.style.backgroundColor="<?php echo $background_out; ?>";' onclick='setScheduleFromProx("<?php echo $cur_day; ?>","<?php echo $cur_month_num; ?>","<?php echo $cur_year; ?>");'>
      <table border="0" width="100%" cellspacing="0" cellpadding="0">
        <tr>
          <td class='smallnote' align="right" width="100%" style='background-color: #ededed; border-bottom: 1px solid #cccccc;'>
            <b><?php echo $cur_month." ".intval($cur_day); ?></b>
          </td>
        </tr>
        <tr>
          <td>
            <table border="0" width="100%" cellpadding="0">
<?php
  $sql_repairs = "select jobs.job_number, fail_types.fail_type, repairs.completed".
                 " from users, repairs, jobs, fail_types".
                 " where users.user_id='".$contractor_id."' and repairs.account_id='".$_SESSION['ao_accountid']."' and repairs.contractor=users.user_id and jobs.job_id=repairs.job_id and fail_types.fail_type_id=repairs.fail_type and repairs.startdate='".$cur_date_format."' group by repairs.repair_id";
  $res_repairs = DBUtil::query($sql_repairs);

  while(list($job_number, $fail_type, $completed)=mysqli_fetch_row($res_repairs))
  {
?>
              <tr>
                <td>
                  <table border="0" style='border: 1px solid red; background: #ffffff; font-size: 11px;' width="100%" cellspacing="0" cellpadding=1>
                    <tr valign='top'>
                      <td>
<?php
    if($completed=='')
    {
?>
                        <b><?php echo $fail_type; ?></b>
<?php
    }
    else
    {
?>
                        <s><b><?php echo $fail_type; ?></b></s>
<?php
    }
?>
                        <br>
                        <span class='smallnote'>(<?php echo $job_number; ?>)</span>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
<?php
  }
  $sql = "select tasks.duration, jobs.job_number, task_type.task, tasks.completed, task_type.color".
         " from tasks, jobs, task_type".
         " where tasks.contractor='".$contractor_id."' and tasks.task_type=task_type.task_type_id and jobs.account_id='".$_SESSION['ao_accountid']."' and tasks.job_id=jobs.job_id and tasks.start_date<='".$cur_date_format."' and date_add(tasks.start_date, interval (tasks.duration-1) day)>='".$cur_date_format."' group by tasks.task_id";

  $res = DBUtil::query($sql);
  while(list($duration, $job_num, $type, $completed, $color)=mysqli_fetch_row($res))
  {
?>
              <tr>
                <td>
                  <table border="0" style='border: 1px solid #999999; background: <?php echo $color; ?>; font-size: 11px;' width="100%" cellspacing="0" cellpadding=1>
                    <tr valign='top'>
                      <td>
<?php
    if($completed=='')
    {
?>
                        <b><?php echo $type; ?></b>
<?php
    }
    else
    {
?>
                        <b><s><?php echo $type; ?></s></b>
<?php
    }
?>
                        <br>
                        <span class='smallnote'><?php echo $duration; ?> Day(s)
                        <br>
                        <span class='smallnote'>(<?php echo $job_num; ?>)
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
<?php
  }
?>
            </table>
          </td>
        </tr>
      </table>
    </td>
<?php

  $cur_date = $cur_date+24*3600;
}
?>
  </tr>
</table>