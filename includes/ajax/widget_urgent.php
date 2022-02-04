<?php

include '../common_lib.php';
if(!viewWidget('widget_urgent') || !ModuleUtil::checkAccess('view_jobs')) { die(); }

//if(1 == 1) {
//if($results == NULL) {
    $extraSql = moduleOwnership('view_jobs') ? "AND ((subscribers.user_id = '{$_SESSION['ao_userid']}' &&  subscribers.job_id = jobs.job_id) || jobs.user_id = '{$_SESSION['ao_userid']}' || jobs.salesman = '{$_SESSION['ao_userid']}' || jobs.referral = '{$_SESSION['ao_userid']}')" : '';
    $sql = "SELECT jobs.job_id,
                jobs.job_number,
                customers.fname,
                customers.lname,
                jobs.stage_num,
                (datediff(curdate(), jobs.stage_date) - stages.duration) AS days_past,
                datediff(curdate(), jobs.stage_date),
                stages.duration,
                repairs.repair_id,
                jobs.pif_date,
                jobs.ins_approval,
                jobs.referral_paid
            FROM customers, jobs
                LEFT JOIN stages ON (stages.stage_num = jobs.stage_num)
                LEFT JOIN subscribers ON (subscribers.job_id = jobs.job_id)
                LEFT JOIN repairs ON (repairs.job_id = jobs.job_id AND repairs.completed IS NULL)
                LEFT JOIN status_holds ON (status_holds.job_id = jobs.job_id)
            WHERE jobs.customer_id = customers.customer_id
                AND jobs.account_id = '{$_SESSION['ao_accountid']}'
                AND (datediff(curdate(), jobs.stage_date) - stages.duration) > 0
                AND duration <> 9999
                AND duration IS NOT NULL
                AND duration <> ''
                AND status_holds.status_hold_id IS NULL
                $extraSql
            GROUP BY jobs.job_id
            ORDER BY datediff(curdate(), jobs.stage_date) - (stages.duration)
            DESC LIMIT 10";
    $results = DBUtil::query($sql);
//}

?>

<?php
//var_dump($results);

if(!$results || mysqli_num_rows($results)==0) {
?>
<h1 class="widget">No Urgent Jobs Found</h1>
<?php
} else {
?>
<table class="table table-condensed table-striped table-hover table-widget">
	<thead>
		<tr>
			<th>Job #</th>
			<th>Customer</th>
			<th>Stage</th>
			<th>DAS / Limit</th>
		</tr>
	</thead>
	<tbody>
<?php
	while(list($job_id, $job_num, $fname, $lname, $stage_num, $days_past, $das, $duration, $repair_id, $paid, $ins_approval, $ref_paid)=mysqli_fetch_row($results)) {
		$label_class = 'label-warning';
		if($days_past > 5) {
			$label_class = 'label-important';
		}

		$stages = JobUtil::getCSVStages($job_id);

		$repair_str = '';
		if(!empty($repair_id))
		{
			$repair_str = ', <span class="red">REPAIR</span>';
		}

		$view_data = array(
			'job_id' => $job_id,
			'job_num' => $job_num,
			'lname' => $lname,
			'fname' => $fname,
			'stage_num' => $stage_num,
			'stages' => $stages,
			'repair_str' => $repair_str,
			'label_class' => $label_class,
			'das' => $das,
			'duration' => $duration
		);
		echo ViewUtil::loadView('widgets/urgent-row', $view_data);
	}
?>
	</tbody>
</table>
<?php 
  }?>