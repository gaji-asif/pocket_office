<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess(array('write_announcements', 'edit_announcements'), TRUE);

$subject = RequestUtil::get('subject');
$text = RequestUtil::get("text");
$level = RequestUtil::get('level');

if(RequestUtil::get("submit")) {
    $errors = array();
	if(empty($subject) || empty($text) || empty($level)) {
		$errors[] = 'Required fields missing';
    }
	
    if(!count($errors)) {
		$sql = "INSERT INTO announcements
                VALUES (NULL, '$subject', '$text', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$level', now())";
		DBUtil::query($sql);

?>

<script>
    Request.makeModal('<?=AJAX_DIR?>/get_announcementlist.php', 'announcements-container', true, true, true);
</script>
<?php
		die();
	}
}

?>
<form action="?" method="post">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Add Announcement</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>Subject:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrownoborder">
            <input type="text" name="subject">
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Min Level:</b>
        </td>
        <td class="listrow">
            <select name="level">
<?php
$userLevels = UserModel::getAllLevels();
foreach($userLevels as $userLevel) {
?>
                <option value="<?=MapUtil::get($userLevel, 'level_id')?>"><?=MapUtil::get($userLevel, 'level')?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr valign="top">
        <td class="listitem">
            <b>Announcement:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <textarea name="text" rows="7"></textarea>
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