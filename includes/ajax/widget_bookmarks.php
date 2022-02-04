<?php

include '../common_lib.php';
if(!viewWidget('widget_bookmarks')||!ModuleUtil::checkAccess('view_jobs')) { die(); }

$sql = "select bookmarks.job_id, customers.fname, customers.lname, jobs.job_number".
       " from bookmarks, customers, jobs".
       " where bookmarks.user_id='".$_SESSION['ao_userid']."' and bookmarks.job_id=jobs.job_id".
       " and customers.customer_id=jobs.customer_id".
       " order by bookmarks.timestamp desc";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
{
?>
<h1 class="widget">No Bookmarks Found</h1>
<?php
}
else
{
?>
<table class="table table-condensed table-striped table-hover table-widget">
	<thead>
		<tr>
			<th>Job #</th>
			<th>Customer</th>
		</tr>
	</thead>
	<tbody>
<?php
	while(list($job_id, $fname, $lname, $job_num)=mysqli_fetch_row($res))
	{
		$view_data = array(
			'job_id' => $job_id,
			'job_num' => $job_num,
			'fname' => $fname,
			'lname' => $lname,
		);
		echo ViewUtil::loadView('widgets/bookmark-row', $view_data);
	}
?>
	</tbody>
</table>
<?
}