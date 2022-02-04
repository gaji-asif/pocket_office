<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('modify_documents', TRUE);

//get document
$document = DBUtil::getRecord('documents');
$myDocumentGroup = DocumentUtil::getDocumentGroup($document['document_id']);
if(!count($document)) {
    UIUtil::showModalError('Document not found!');
}

//edit document
$errors = array();
if(RequestUtil::get('submit')) {
    $title = RequestUtil::get('document');
    $description = RequestUtil::get('description');
    $group = RequestUtil::get('document_group');
    
	if(empty($title) || empty($description)) {
		$errors[] = 'Required fields missing';
	}
    
	if(!count($errors)) {
        FormUtil::update('documents');

		//change group if applicable
		if($group != MapUtil::get($myDocumentGroup, 'document_group_id')) {
            $sql = "DELETE FROM document_group_link
						WHERE document_id = '{$document['document_id']}'
						LIMIT 1";
			DBUtil::query($sql);
			if(!empty($group)) {
				$sql = "INSERT INTO document_group_link (document_id, document_group_id)
						VALUES ('{$document['document_id']}', '$group')";
                DBUtil::query($sql);
			}
		}
?>

<script>
	Request.makeModal('<?=AJAX_DIR?>/get_document.php?id=<?=$document['document_id']?>', 'documentscontainer', true, true, true);
</script>
<?php
	die();
	}
}
?>
<form method="post" action="?id=<?=RequestUtil::get('id')?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            Edit Document
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" width="100%" align="left" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td class="listitem">
            <b>Title:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" name="document" value="<?=$document['document']?>">
        </td>
    </tr>
<?php
$documentGroups = DocumentModel::getAllDocumentGroups();
if(!empty($documentGroups)) {
?>
    <tr>
        <td width="25%" class="listitem"><b>Group:</b></td>
        <td class="listrow">
            <select name="document_group">
                <option value=""></option>
<?php
    foreach($documentGroups as $group) {
?>
                <option value="<?=$group['document_group_id']?>" <?=$group['document_group_id'] == MapUtil::get($myDocumentGroup, 'document_group_id') ? 'selected' : ''?>><?=$group['label']?></option>
<?php
    }
?>
            </select>
        </td>
    </tr>
<?php
}
?>
    <tr>
        <td class="listitem"><b>Stage:</b></td>
        <td class="listrow">
            <select name="stage_num">
                <option value=""></option>
<?php
$stages = StageModel::getAllStages();
foreach($stages as $stage) {
?>
                <option value="<?=$stage['stage_num']?>" <?=$stage['stage_num'] == $document['stage_num'] ? 'selected' : ''?>><?=$stage['stage']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr valign="top">
        <td class="listitem">
            <b>Description:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <textarea name="description" rows="7"><?=$document['description']?></textarea>
        </td>
    </tr>
    <tr>
        <td align="right" colspan="2" class="listrow">
            <input type="submit" name="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>