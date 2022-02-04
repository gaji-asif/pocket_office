<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('add_job_journal', TRUE);
$firstLast = UIUtil::getFirstLast();

$journal = @mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('journal'));
$recipientIds = RequestUtil::get('recipients', array());
$myJob = new Job(RequestUtil::get('id'));

ModuleUtil::checkJobModuleAccess('add_job_journal', $myJob, TRUE);

$errors = array();
if(RequestUtil::get('submit')) {
	if(empty($journal)) {
		$errors[] = 'Journal cannot be empty';
	}
	
    if(!count($errors)) {
		$sql = "INSERT INTO journals (job_id, stage_num, task_id, text, user_id, timestamp)
				VALUES ('{$myJob->job_id}', '{$myJob->stage_num}', NULL, '$journal', '{$_SESSION['ao_userid']}', now())";

		$results = DBUtil::query($sql);
        if(!$results) {
            LogUtil::getInstance()->logNotice('Failed to add Journal - ' . mysqli_error());
        }
		$newJournalId = DBUtil::getInsertId();

        foreach($recipientIds as $recipientId) {
            NotifyUtil::notifyFromTemplate('journal_posted', $recipientId, $_SESSION['ao_userid'], array('job_id' => $myJob->job_id, 'journal_id' => $newJournalId));
        }

		//store activity history
		JobModel::saveEvent($myJob->job_id, 'Added New Journal');
?>

<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?tab=journals&id=<?=$myJob->job_id?>', 'jobscontainer', true, true, true);
</script>
<?php
		die();
	}
}

//get account meta data
$accountMetaData = AccountModel::getAllMetaData();

?>
<form method="post" name="journal" action="?id=<?=$myJob->job_id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Add Job Journal</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td class="listitem" width="25%"><b>Recipients:</b></td>
        <td class="listrow" colspan="2">
            <select id="recipients">
                <option value=""></option>
<?php
$level = MetaUtil::get($accountMetaData, 'add_journal_recipient_user_dropdown');
$users = $level ? UserModel::getAllByLevel($level, FALSE, $firstLast) : UserModel::getAll(FALSE, $firstLast);
foreach($users as $user) {
?>
                <option value="<?=$user['user_id']?>"><?=$user['select_label']?></option>
<?php
}
?>
            </select>
            <div class="btn-list-container"></div>
        </td>
    </tr>
    <tr valign="top">
        <td class="listitem"><b>Journal:</b>&nbsp;<span class="red">*</span></td>
        <td class="listrow" colspan="2">
            <textarea rows="7" style="width: 100%;" name="journal"></textarea>
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
<script>
$(function() {
    $('#recipients').change(function() {
        var userId = $(this).val(),
            name = $(this).find('option[value="' + userId + '"]').text();
        if(!userId.length || $('input[value="' + userId + '"]').length) { return; }
        
        $('.btn-list-container').append(
                Handlebars.renderTemplate('recipient-btn-group', {name: name, userId: userId})
        ).show();
    });
    
    $(document).on('click', '.btn.remove', function() {
        $(this).closest('.btn-group').remove();
        if(!$('.btn-list-container').find('.btn-group').length) {
            $('.btn-list-container').hide();
        }
    });
    
    $('.btn-list-container').hide();
});
</script>
</html>