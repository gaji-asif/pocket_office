<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_jobs'))
  die();

$sql = "select jobs.job_id, jobs.job_number from jobs left join job_view_history on(job_view_history.job_id=jobs.job_id) where jobs.salesman='".$_SESSION['ao_userid']."' and job_view_history.timestamp is null order by jobs.job_id asc limit 5";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)!=0)
{
?>
<table border="0">
  <tr>
    <td width=95><nobr><b>Your New Jobs:</b></nobr></td>
<?php
  while(list($job_id, $job_num)=mysqli_fetch_row($res))
  {
?>
    <td width=24 align="right" style='border-left: 1px solid #cccccc;'><img src='<?=IMAGES_DIR?>/icons/briefcase_16.png'></td>
    <td><a href='jobs.php?id=<?php echo $job_id; ?>' class='browsinglink' target='main' tooltip><?php echo $job_num; ?></a></td>
<?php
  }
?>
    <td style='border-left: 1px solid #cccccc;'>&nbsp;</td>
  </tr>
</table>
<?php
}
?>