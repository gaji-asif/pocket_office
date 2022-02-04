<?php 
include '../common_lib.php';
UserModel::isAuthenticated();

if($_GET['id']!='')
{
  $sql = "select count(job_id) from jobs where date_format(timestamp, '%Y')='".date('Y')."' and salesman='".intval($_GET['id'])."'";
  $res = DBUtil::query($sql)or die(myql_error());
  
  list($num_jobs)=mysqli_fetch_row($res);
  
  
  echo "<b>".$num_jobs."</b> YTD job(s)";
}
else echo '';

?>