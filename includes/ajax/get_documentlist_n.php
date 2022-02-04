<?php
include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_documents'))
    die("Insufficient Rights");

$windowHeight = RequestUtil::get('window_height', DEFAULT_WINDOW_HEIGHT);
$_RES_PER_PAGE = calcResultsPerPage($windowHeight);

$limit = $_GET['limit'];
if($limit == '')
    $limit = 0;

if($_GET['search'] == 'Search...')
{
	$_GET['search'] = '';
}

if($_GET['id'] != '' && $_GET['action'] == 'del')
{
    $sql = "select filename from documents where document_id='" . mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id']) . "' and account_id='" . $_SESSION['ao_accountid'] . "' limit 1";
    $res = DBUtil::query($sql);

    if(mysqli_num_rows($res) != 0)
    {
        list($filename) = mysqli_fetch_row($res);
        unlink(DOCUMENTS_PATH . '/' . $filename);

        $sql = "delete from documents where document_id='" . mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id']) . "' and account_id='" . $_SESSION['ao_accountid'] . "' limit 1";
        DBUtil::query($sql);

        $sql = "delete from document_group_link where document_id='" . mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id']) . "'";
        DBUtil::query($sql);
    }
}

if(moduleOwnership('view_documents'))
    $ownership = "(documents.user_id=" . $_SESSION['ao_userid'] . ") and";

$searchStr = '';
if($_GET['search'] != '')
{
    $search_term = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search']);
    $searchStr = "(documents.document like '%$search_term%' || documents.description like '%$search_term%') and";
}

if(!empty($_GET['document_group_id']))
{
    $grouping = "dgl.document_group_id = " . intval($_GET['document_group_id']) . " and";
}

$sql = "select documents.document_id, documents.document, documents.filetype, documents.timestamp, concat(users.lname, ', ', users.fname) as owner, documents.user_id, dg.label
        from users, documents
        left join document_group_link dgl on (dgl.document_id = documents.document_id)
        left join document_groups dg on (dgl.document_group_id = dg.document_group_id)
        where $ownership $searchStr $grouping documents.account_id={$_SESSION['ao_accountid']} and documents.user_id=users.user_id
        order by documents.document asc";
$res = DBUtil::query($sql);
$num_rows = mysqli_num_rows($res);


?>
<table class="table table-bordered table-condensed table-hover table-striped">
<?=ViewUtil::loadView('document-header-n')?>
<?php



//limit results
$documents_array = array();
$limited_documents_array = array();
while($documents_array[] = mysqli_fetch_assoc($res)){}
$limited_documents_array = array_slice($documents_array, $limit, $_RES_PER_PAGE);

foreach($limited_documents_array as $key => $document)
{
	if(empty($document))
	{
		continue;
	}

	$view_array = array(
		'document' => $document
	);
	echo ViewUtil::loadView('document-list-row', $view_array);
}
?>
</table>