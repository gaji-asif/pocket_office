<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('upload_document', TRUE);

$action = RequestUtil::get('action');
$id = RequestUtil::get('id');
$title = RequestUtil::get('title');
$editTitle = RequestUtil::get('edittitle');

$errors = array();
if($action == 'del') {
    $documentGroupLinks = DBUtil::getRecord('document_group_link', $id, 'document_group_id');
    if(count($documentGroupLinks)) {
        $errors[] = 'Documents currently associated - cannot remove';
    }
    
    if(!count($errors)) {
        $sql = "DELETE FROM document_groups
                WHERE document_group_id = '$id'
                    AND account_id = '{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
        $info[] = 'Document group successfully deleted';
    }
}

if(RequestUtil::get('submit-new')) {
    if(!$title) {
        $errors[] = 'Title cannot be empty';
    }
    
    if(!count($errors)) {
        $sql = "INSERT INTO document_groups (label, account_id)
                VALUES ('$title', '{$_SESSION['ao_accountid']}')";
        DBUtil::query($sql);
        $info[] = "Document group '$title' successfully added";
    }
}

if(RequestUtil::get('submit-edit')) {
    if(!$editTitle) {
        $errors[] = 'Title cannot be empty';
    }
    
    if(!count($errors)) {
        $sql = "UPDATE document_groups SET label = '$editTitle'
                WHERE document_group_id = '$id'
                    AND account_id = '{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
        $info[] = 'Document group successfully modified';
    }
}
?>
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Modify Document Groups</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal reload-parent"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<?=AlertUtil::generate($info, 'info', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitem">
            <b>Add Group:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <form action="?" method="post">
            <input type="text" name="title">
            <input name="submit-new" type="submit" value="Add">
            </form>
        </td>
    </tr>
    <tr valign="top">
        <td class="listitem"><b>Current Groups:</b></td>
        <td class="listrow">
<?php
$documentGroups = DocumentModel::getAllDocumentGroups();
foreach($documentGroups as $documentGroup) {
?>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                    <td width="20">
                        <a href="" onclick="if(confirm('Are you sure?')){window.location = '?action=del&id=<?=$documentGroup['document_group_id']?>';} return false;">
                            <img src="<?=IMAGES_DIR?>/icons/delete.png">
                        </a>
                    </td>
                    <td>
                        <form method="post" action="?">
                            <input type="text" name="edittitle" value='<?=$documentGroup['label']?>'>
                            <input type="hidden" name="id" value="<?=$documentGroup['document_group_id']?>">
                            <input name="submit-edit" type="submit" value="Edit">
                        </form>
                    </td>
                </tr>
            </table>
<?php
}
?>
        </tr>
    <td>
</table>
</body>
</html>