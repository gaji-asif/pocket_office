<?php
include '../common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');

$type = RequestUtil::get('type');
$id = RequestUtil::get('id');
$global = RequestUtil::get('global') ? 1 : 0;
$subject = RequestUtil::get('subject');
$note = RequestUtil::get('note');

if(!$type || !$id) {
    UIUtil::showModalError('Required information is missing!');
}


$errors = array();
if(RequestUtil::get("submit")) {
    if(empty($subject)) {
        $errors[] = 'Subject cannot be blank';
    }
    if(empty($note)) {
        $errors[] = 'Note cannot be blank';
    }

    if(!count($errors)) {
        $sql = "INSERT INTO notes
                VALUES (NULL, '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$global', '$type', '$id', '$subject', '$note', now())";
        DBUtil::query($sql);

?>

<script>
    Request.makeModal('<?=AJAX_DIR?>/get_notes.php?id=<?=$id?>&type=<?=$type?>', 'notes', false, true, true);
</script>
<?php
    }
}
?>

<form method="post" name="customer" action="?id=<?=$id?>&type=<?=$type?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Add Note</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <input type="hidden" name="type" value="<?=$type?>">
            <input type="hidden" name="id" value="<?=$id?>">
            <b>Subject:</b>&nbsp;<span class="red">*</span>
        </td>
        <td colspan="2" class="listrownoborder">
            <input type="text" name="subject" value="<?=$subject?>">
        </td>
    </tr>
    <tr valign="top">
        <td class="listitem">
            <b>Global Note:</b>
        </td>
        <td class="listrow" colspan="2">
            <input type='checkbox' value="1" name="global">
        </td>
    </tr>
    <tr valign='top'>
        <td class="listitem">
            <b>Note:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow" colspan="2">
            <textarea rows="7" style="width:100%;" name="note"><?=$note?></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="3" align="right" class="listrow">
            <input name="submit" type="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>