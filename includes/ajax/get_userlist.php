<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_users'))
  die("Insufficient Rights");

$limit = (int)RequestUtil::get('limit', 0);
$limit = $limit >= 0 ? $limit : 0;
$sort = RequestUtil::get('sort') ?: 'ORDER BY lname ASC';
$searchStr = RequestUtil::get('search');
$windowHeight = RequestUtil::get('window_height', DEFAULT_WINDOW_HEIGHT);
$_RES_PER_PAGE = calcResultsPerPage($windowHeight);
$limitStr = "LIMIT $limit, $_RES_PER_PAGE";

if($searchStr) {
    $sql = "SELECT SQL_CALC_FOUND_ROWS user_id
            FROM users
            WHERE (fname LIKE '%$searchStr%' || lname LIKE '%$searchStr%' || dba LIKE '%$searchStr%')
                AND account_id = '{$_SESSION['ao_accountid']}'
            $sort
            $limitStr";
} else {
    $sql = "SELECT SQL_CALC_FOUND_ROWS user_id
            FROM users
            WHERE account_id = '{$_SESSION['ao_accountid']}'
            $sort
            $limitStr";
}
$users = DBUtil::queryToArray($sql);
$totalUsers = DBUtil::getLastRowsFound();
?>

<table width="100%" border="0" class="data-table" cellpadding=5 cellspacing="0">

<?php
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
						<a href="javascript:Request.make('includes/ajax/get_joblist.php', 'jobscontainer', true, true);" class='basiclink'>
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
?>

<?php
if(count($users)) {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">
<?php
    foreach($users as $user) {
        $myUser = new User($user['user_id']);
        echo ViewUtil::loadView('user-list-row', array('myUser' => $myUser));
    }
?>
</table>
<?php
    $viewData = array(
        'limit' => $limit,
        'query_string_params' => $_GET,
        'results_per_page' => $_RES_PER_PAGE,
        'total_results' => $totalUsers,
        'script' => 'get_userlist',
        'destination' => 'userscontainer'
    );
    echo ViewUtil::loadView('list-pagination', $viewData);
} else {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="5">
	<tr valign="middle">
		<td align="center" colspan="10">
			<b>No Users Found</b>
		</td>
	</tr>
</table>
<?php
}
