<?php
include '../common_lib.php';
ModuleUtil::checkAccess('view_jobs', TRUE, TRUE);
$limit = (int)RequestUtil::get('limit', 0);
$limit = $limit >= 0 ? $limit : 0;
$searchStr = RequestUtil::get('search');
$windowHeight = RequestUtil::get('window_height', DEFAULT_WINDOW_HEIGHT);
$_RES_PER_PAGE = calcResultsPerPage($windowHeight);
$_SESSION['ao_full_joblist_query_string'] = http_build_query($_GET);

//search jobs
$jobs = JobModel::getList($limit, $_RES_PER_PAGE);

$totalJobs = DBUtil::getLastRowsFound();


if(!empty($searchStr)) {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="5">
	<tr>
		<td colspan="10">
			<b>Searching '<?=$searchStr?>' - <?=$totalJobs?> result(s) found</b>
		</td>
	</tr>
	<tr>
		<td colspan="10">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<a href="" rel="make-request" data-action="includes/ajax/get_joblist.php" data-destination="jobscontainer">
							<i class="icon-double-angle-left"></i>&nbsp;Back
						</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
}
if(count($jobs)) {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">
<?php
	
    foreach($jobs as $job) {
        $myJob = new Job($job['job_id']);
        echo ViewUtil::loadView('job-list-row', array('myJob' => $myJob));
    }
?>
</table>
<?php
    $viewData = array(
        'limit' => $limit,
        'query_string_params' => $_GET,
        'results_per_page' => $_RES_PER_PAGE,
        'total_results' => $totalJobs,
		'script' => 'get_joblist',
		'destination' => 'jobscontainer'
    );
    echo ViewUtil::loadView('list-pagination', $viewData);
} else {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="5">
	<tr valign="middle">
		<td align="center" colspan="10">
			<b>No Jobs Found</b>
		</td>
	</tr>
</table>
<?php
}