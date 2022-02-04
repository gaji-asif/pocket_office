<?php

include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('next_job_stage'))
  die("Insufficient Rights");

$myJob = new Job(RequestUtil::get('id'));

$stage_name='';
if($_GET['action'] == 'select' && $_GET['stage']!='')
{
    $new_stage = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['stage']);
    $sql = "select stage from stages where stage_num='$new_stage' and account_id='" . $_SESSION['ao_accountid'] . "'";
    $res = DBUtil::query($sql);
    while ($stage = mysqli_fetch_row($res)) {
        $stage_name = $stage[0];
    }
}
else
{ 
    $sql = "select order_num from stages where stage_num='$myJob->stage_num' and account_id='" . $_SESSION['ao_accountid'] . "'";
    $res = DBUtil::query($sql);
    $order_num = 0;
    while ($stage = mysqli_fetch_row($res)) {
        $order_num = $stage[0];
    }
    
    $next_order=$order_num+1;
    
    $sql = "select stage_num,stage from stages where order_num='$next_order' and account_id='" . $_SESSION['ao_accountid'] . "'";
    $res = DBUtil::query($sql);
    while ($stage_num = mysqli_fetch_row($res)) {
        $new_stage = $stage_num[0];
        $stage_name = $stage_num[1];
    }
   
}

$sql = "update jobs set stage_num='".$new_stage."', stage_date=curdate() where job_id='".$myJob->job_id."' limit 1";
DBUtil::query($sql);

JobModel::saveEvent($myJob->job_id, "Moved to stage $stage_name");



//NotifyUtil::notifySubscribersFromTemplate('stage_moved', null, array('job_id' => $myJob->job_id), true);

$salesman_id=$myJob->salesman_id;

$sql = "SELECT * FROM `stage_notifications` where `stage_num`= (select stage_num from stages where stage_id=". $myJob->stage_num.") and `user_id`= '".$salesman_id."' LIMIT 1";

$mail_notification_flag=0;
if(mysqli_num_rows($res)==1)

{
	
$mail_notification_flag=1;

}

if(!empty($mail_notification_flag))

{


//NotifyUtil::notifySubscribersFromTemplate('stage_moved', null, array('job_id' => $myJob->job_id), true);


}


?>