<?php
include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_documents'))
    die("Insufficient Rights");

$windowHeight = RequestUtil::get('window_height', DEFAULT_WINDOW_HEIGHT);
$_RES_PER_PAGE = calcResultsPerPage($windowHeight);

$limit = (int)RequestUtil::get('limit', 0);
$limit = $limit >= 0 ? $limit : 0;
$searchStr = RequestUtil::get('search');
$id = RequestUtil::get('id');
$action = RequestUtil::get('action');

if(!empty($id) && $action == 'del') {
    $sql = "select filename from documents where document_id='$id' and account_id='{$_SESSION['ao_accountid']}' limit 1";
    $res = DBUtil::query($sql);

    if(mysqli_num_rows($res) != 0) {
        list($filename) = mysqli_fetch_row($res);
        unlink(DOCUMENTS_PATH . '/' . $filename);

        $sql = "delete from documents where document_id='$id' and account_id='{$_SESSION['ao_accountid']}' limit 1";
        DBUtil::query($sql);

        $sql = "delete from document_group_link where document_id='$id'";
        DBUtil::query($sql);
    }
}

//get documents
$documents = DocumentModel::getList($limit, $_RES_PER_PAGE);
$totalDocuments = DBUtil::getLastRowsFound();

if(!empty($searchStr)) {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="5">
	<tr>
		<td colspan="10">
			<b>Searching '<?=$searchStr?>' - <?php echo $totalDocuments ?> result(s) found</b>
		</td>
	</tr>
	<tr>
		<td colspan="10">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<a href="javascript:Request.make('includes/ajax/get_documentlist.php', 'documentscontainer', true, true);" class="basiclink">
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
if(count($documents)) {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">
<?php
	foreach($documents as $document) {
        echo ViewUtil::loadView('document-list-row', array('document' => $document));
    }
?>
</table>
<?php
    $viewData = array(
        'limit' => $limit,
        'query_string_params' => $_GET,
        'results_per_page' => $_RES_PER_PAGE,
        'total_results' => $totalDocuments,
		'script' => 'get_documentlist',
		'destination' => 'documentscontainer'
    );
    echo ViewUtil::loadView('list-pagination', $viewData);
}
else
{
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="5">
	<tr valign="middle">
		<td align="center" colspan="10">
			<b>No Documents Found</b>
		</td>
	</tr>
</table>
<?php
}