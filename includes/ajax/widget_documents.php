<?php
include '../common_lib.php';
if(!viewWidget('widget_documents')||!ModuleUtil::checkAccess('view_documents')) { die(); }

$extraSql = moduleOwnership('view_documents') ? "AND user_id='{$_SESSION['ao_userid']}'" : '';

$sql = "SELECT document_id, document, timestamp
        FROM documents
        WHERE account_id = '{$_SESSION['ao_accountid']}'
        $extraSql
        ORDER BY timestamp DESC
        LIMIT 10";

$results = DBUtil::query($sql);

if(mysqli_num_rows($results) == 0) {
?>
<h1 class="widget">No Documents Found</h1>
<?php
}
else
{
?>
<table class="table table-condensed table-striped table-hover table-widget">
	<thead>
		<tr>
			<th>Title</th>
			<th>Date</th>
		</tr>
	</thead>
	<tbody>
<?php 
	$i=1;
	while(list($id, $title, $timestamp)=mysqli_fetch_row($results))
	{
		$timestamp = DateUtil::formatDateTime($timestamp);

		$view_data = array(
			'document_id' => $id,
			'title' => $title,
			'timestamp' => $timestamp
		);
		echo ViewUtil::loadView('widgets/document-row', $view_data);
	}
?>
	</tbody>
</table>
<?php
}?>