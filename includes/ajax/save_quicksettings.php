<?php

include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('view_jobs'))
	die();

$myJob = new Job(RequestUtil::get('id'));

$data_array = array(
	"pif_date" => array('jobs', $_GET['pif']),
	"ins_approval" => array('jobs', $_GET['approved']),
	"referral_paid" => array('jobs', $_GET['refpaid']),
	"confirmed" => array('sheets', $_GET['confirmed']),
	"stage_num" => array('jobs', $_GET['stage'])
);

foreach ($data_array as $key => $value)
{
	$update_value = $value[1];
	if($update_value != 'null')
	{
		switch ($value[1])
		{
			case 'false':
				$update_value = 'NULL';
				break;
			case 'true':
				$update_value = 'now()';
				break;
			default:
				$update_value = $value[1];
		}
		$sql = "update " . $value[0] . " set " . $key . "=" . $update_value . " where job_id=" . $myJob->job_id . " limit 1";
		DBUtil::query($sql);
		if($key == 'stage_num' && $update_value != $myJob->stage_num)
		{
			DBUtil::query('update jobs set stage_date=curdate() where job_id=' . $myJob->job_id . ' limit 1') ;
			$sql = "select stage from stages where stage_num='$update_value' and account_id='" . $_SESSION['ao_accountid'] . "'";
            $res = DBUtil::query($sql);
            $stage_name='';
            while ($stage_num = mysqli_fetch_row($res)) {
                $stage_name = $stage_num[0];
            }
            
			JobModel::saveEvent($myJob->job_id, "Moved to stage $stage_name");
			NotifyUtil::notifySubscribersFromTemplate('stage_moved', null, array('job_id' => $myJob->job_id), true);
		}
	}
}
?>