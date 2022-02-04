<?php
include '../common_lib.php';
ModuleUtil::checkAccess('view_jobs', TRUE, TRUE);
$limit = (int)RequestUtil::get('limit', 0);
$limit = $limit >= 0 ? $limit : 0;
$searchStr = RequestUtil::get('emailsearch');
$windowHeight = RequestUtil::get('window_height', DEFAULT_WINDOW_HEIGHT);
$_RES_PER_PAGE = calcResultsPerPage($windowHeight);
$_SESSION['ao_full_joblist_query_string'] = http_build_query($_GET);

//search jobs
$emails = EmailModel::getList($limit, $_RES_PER_PAGE);

$totalJobs = DBUtil::getLastRowsFound();
//echo "<pre>";print_r($totalJobs);die;

$email_folder = RequestUtil::get('email_folder');

$prev_folder = RequestUtil::get('prev_folder');

if(!empty($searchStr) && $prev_folder == $email_folder) {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="5">
	<tr>
		<td colspan="10">
			<b> <?= $email_folder ?> - Searching '<?=$searchStr?>' - <?=$totalJobs?> result(s) found</b>
		</td>
	</tr>
	<tr>
		<td colspan="10">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<a href="" rel="make-request" data-action="includes/ajax/get_emaillist.php" data-destination="emailscontainer">
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
if(count($emails)) {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">
<?php
	
    foreach($emails as $mail) {
        $myEmail = new Email($mail['id']);
        //echo "<pre>";print_r($myEmail);die;
        echo ViewUtil::loadView('email-list-row', array('myEmail' => $myEmail));
    }
?>
</table>
<?php
    $viewData = array(
        'limit' => $limit,
        'query_string_params' => $_GET,
        'results_per_page' => $_RES_PER_PAGE,
        'total_results' => $totalJobs,
		'script' => 'get_emaillist',
		'destination' => 'emailscontainer'
    );
    echo ViewUtil::loadView('list-pagination', $viewData);
} else {
?>
<table width="100%" border="0" class="data-table" style="border-top:1px solid #e1e1e1" cellpadding="0" cellspacing="5" height="50">
	<tr valign="middle">
		<td align="center" colspan="10">
			<b>No Emails Found</b>
		</td>
	</tr>
</table>
<?php
}