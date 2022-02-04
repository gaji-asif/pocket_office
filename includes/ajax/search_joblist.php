<?php

include '../common_lib.php';

if(!ModuleUtil::checkAccess('view_jobs'))
  die("Insufficient Rights");

if(moduleOwnership('view_jobs'))
    $ownership = "((subscribers.user_id='".$_SESSION['ao_userid']."' &&  subscribers.job_id=jobs.job_id) || jobs.user_id=".$_SESSION['ao_userid']." || jobs.salesman=".$_SESSION['ao_userid'].") and";

//search jobs
$jobs_array = JobModel::getList();
$num_rows = sizeof($jobs_array);
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="5">
	<tr>
		<td colspan="10">
			<b>Searching '<?=$searchStr?>' - <?php echo $num_rows ?> result(s) found</b>
		</td>
	</tr>
</table>
<?php
if($num_rows != 0)
{
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">
<?php
	foreach($jobs_array as $key => $job_row)
	{
		$class = 'odd';
		if($key%2 == 0)
		{
			$class = 'even';
		}
		$myJob = new Job($job_row['job_id']);

		$view_data = array(
			'myJob' => $myJob,
			'class' => $class,
			'quick_settings' => false,
			'true_job_link' => true
		);
		echo ViewUtil::loadView('job-list-row', $view_data);
	}
?>
</table>
<?php
}