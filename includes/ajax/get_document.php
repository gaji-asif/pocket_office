<?php
include '../common_lib.php';
ModuleUtil::checkAccess('view_documents', TRUE);

//get data
$document_id = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id']);

$sql = "select documents.stage_num, documents.document, documents.filetype, documents.description, documents.filename, documents.timestamp, users.fname, users.lname, documents.user_id, dg.label".
       " from users, documents".
       " left join document_group_link dgl on (dgl.document_id = documents.document_id)".
       " left join document_groups dg on (dgl.document_group_id = dg.document_group_id)".
       " where documents.document_id='$document_id' and documents.account_id='".$_SESSION['ao_accountid']."' and documents.user_id=users.user_id".
       " order by timestamp asc";
$res = DBUtil::query($sql);

?>

<table width="100%" border="0" class="data-table" cellpadding=5 cellspacing="0">

<?php
if(mysqli_num_rows($res)==0)
{
?>
	<tr><td align="center" colspan=5><b>Document Not Found</b></td></tr>
<?php
}
else
{
	list($stage_num, $document, $filetype, $description, $filename, $date, $fname, $lname, $user_id, $group)=mysqli_fetch_row($res);

	if(mysqli_num_rows($res)!=0)
	  UserModel::storeBrowsingHistory($document, $filetype, 'documents.php', mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id']));

    
    if(ModuleUtil::checkOwnership('view_documents') && $user_id != $_SESSION['ao_userid']) {
        ModuleUtil::showInsufficientRightsAlert('view_documents', TRUE);
    }

	$date = DateUtil::formatDateTime($date);

	$icon='<img src="images/icons/'.$filetype.'.png">';
?>

	<tr valign='middle' class='odd'>
		<td width=16><?php echo $icon; ?></td>
		<td class='data-table-cell smalltitle'><?php echo $document; ?></td>
		<td class='data-table-cell' width="15%"><?php echo $group; ?>&nbsp;</td>
		<td width="15%" class='data-table-cell'><?php echo $date; ?></td>
		<td width="20%" class='data-table-cell'><?php echo $lname.", ".$fname; ?></td>
	</tr>
<?php
	$edit_documents = false;
	if((ModuleUtil::checkAccess('modify_documents') && !moduleOwnership('modify_documents')) || (moduleOwnership('modify_documents') && $user_id == $_SESSION['ao_userid'])) {
		$edit_documents = true;
	}
	$delete_documents = false;
	if((ModuleUtil::checkAccess('delete_documents') && !moduleOwnership('delete_documents')) || (moduleOwnership('delete_documents') && $user_id == $_SESSION['ao_userid'])) {
		$delete_documents = true;
	}
	if($edit_documents || $delete_documents)
	{
?>
    <tr>
        <td colspan="5">
            <div class="btn-group pull-right">
<?php
        if($edit_documents) {
?>
                <div rel="open-modal" data-script="edit_document.php?id=<?=$document_id?>" class="btn btn-small" title="Edit Document" tooltip>
                    <i class="icon-pencil"></i>
                </div>
<?php
        }
        if($delete_documents) {
?>
                <div rel="make-request" data-action="<?=AJAX_DIR?>/get_documentlist.php?action=del&id=<?=$document_id?>" data-destination="documentscontainer" data-confirm="Delete <?=$document?>?" class="btn btn-small" title="Delete Document" tooltip>
                    <i class="icon-remove"></i>
                </div>
<?php
        }
?>
            </div>
        </td>
    </tr>
<?php
	}
?>
	<tr>
		<td colspan=5>
			<b>Document Info:</b>
			<table border="0" width="100%" class='listtable' cellpadding="0" cellspacing="0">
				<tr>
					<td width=150 class="listitemnoborder"><b>Description:</b></td>
					<td class="listrownoborder"><?php echo $description; ?></td>
				</tr>
				<tr>
					<td class="listitem"><b>File Type:</b></td>
					<td class="listrow"><?=$filetype?></td>
				</tr>
<?php
    $csv_stages = StageModel::getCSVStagesByStageNum($stage_num);
    $file_size = '0';
    if(file_exists(DOCUMENTS_PATH. '/' . $filename))
    {
        $file_size = ceil(filesize(DOCUMENTS_PATH. '/' . $filename) / 1000);
    }
?>
				<tr>
					<td class="listitem"><b>Stage:</b></td>
					<td class="listrow"><?php echo $csv_stages; ?></td>
				</tr>
				<tr>
					<td class="listitem"><b>File Size:</b></td>
					<td class="listrow"><?=$file_size?> kb</td>
				</tr>
				<tr>
                    <td class="listitem"><b>View/Download:</b></td>
					<td class="listrow">
                        <a href="<?=($filetype != 'image' && $filetype != 'pdf') ? 'http://docs.google.com/viewer?url=' : ''?><?=ACCOUNT_URL?>/docs/<?=$filename?>" target="blank">
                            <?=$filename?>
                        </a>
                    </td>
				</tr>
			</table>
		</td>
	</tr>
    <tr><td colspan="10">&nbsp;</td></tr>
	<tr>
		<td colspan=10 class='infofooter'>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>
						<a href="javascript:Request.make('includes/ajax/get_documentlist.php', 'documentscontainer', true, true);" class='basiclink'>
							<i class="icon-double-angle-left"></i>&nbsp;Back
						</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
<?php
}
?>
</table>
