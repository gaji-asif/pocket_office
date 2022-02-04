<?php
include '../common_lib.php';
ModuleUtil::checkAccess('view_announcements', TRUE, TRUE);

$id = RequestUtil::get('id');
$action = RequestUtil::get('action');
$limit = RequestUtil::get('limit') ?: 0;
$searchStr = RequestUtil::get('search');
$windowHeight = RequestUtil::get('window_height') ?: DEFAULT_WINDOW_HEIGHT;
$_RES_PER_PAGE = calcResultsPerPage($windowHeight);

if($id && $action == 'del') {
  $sql = "delete from announcements where announcement_id='$id' and account_id='{$_SESSION['ao_accountid']}' limit 1";
  DBUtil::query($sql);

  $sql = "delete from read_announcements where announcement_id='$id' and account_id='{$_SESSION['ao_accountid']}'";
  DBUtil::query($sql);
}

//search announcements
$announcements = AnnouncementModel::getList($limit, $_RES_PER_PAGE);
$totalAnnouncements = DBUtil::getLastRowsFound();

if(!empty($searchStr)) {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="5">
	<tr>
		<td colspan="10">
			<b>Searching '<?=$searchStr?>' - <?=$totalAnnouncements?> result(s) found</b>
		</td>
	</tr>
	<tr>
		<td colspan="10">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
                        <a href="" rel="make-request" data-action="includes/ajax/get_announcementlist.php" data-destination="announcements-container">
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
if(count($announcements)) {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">
<?php
    foreach($announcements as $announcement) {
        $myAnnouncement = new Announcement($announcement['announcement_id']);
        echo ViewUtil::loadView('announcement-list-row', array('myAnnouncement' => $myAnnouncement));
    }
?>
</table>
<?php
    $viewData = array(
        'limit' => $limit,
        'query_string_params' => $_GET,
        'results_per_page' => $_RES_PER_PAGE,
        'total_results' => $totalAnnouncements,
		'script' => 'get_announcementlist',
		'destination' => 'announcementscontainer'
    );
    echo ViewUtil::loadView('list-pagination', $viewData);
} else {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="5">
	<tr valign="middle">
		<td align="center" colspan="10">
			<b>No Announcements Found</b>
		</td>
	</tr>
</table>
<?php
}

