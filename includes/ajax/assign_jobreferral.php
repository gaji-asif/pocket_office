<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
$firstLast = UIUtil::getFirstLast();

$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('assign_job_referral', $myJob);

if(RequestUtil::get('submit')) {
    $referralId = RequestUtil::get('referral');
    $_POST['referral_paid'] = RequestUtil::get('paid') ? DateUtil::formatMySQLTimestamp() : '';
    FormUtil::update('jobs');
    $myJob->storeSnapshot();

	JobModel::saveEvent($myJob->job_id, 'Assigned New Job Referral');
	if($referralId && $referralId != $myJob->referral_id) {
		NotifyUtil::notifyFromTemplate('referral_assigned', $referralId, null, array('job_id' => $myJob->job_id));
	}
?>

<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', true, true, true);
</script>
<?php
	die();
}
?>
<form method="post" name="user" action="?id=<?php echo $_GET['id']; ?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>
            Assign Referral
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>User:</b>
        </td>
        <td class="listrownoborder">
            <select name="referral" onchange="setReferralPaid(this, '#paid');">
                <option value="">No Referral</option>
<?php
$showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');
$dropdownUserLevels = AccountModel::getMetaValue('assign_job_referral_user_dropdown');
$users = !empty($dropdownUserLevels) 
                ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast)
                : UserModel::getAll($showInactiveUsers, $firstLast);
foreach($users as $user) {
	if($user['is_active'] && !$user['is_deleted']) {
?>
                <option value="<?=$user['user_id']?>" <?php if($myJob->referral_id == $user['user_id']){ echo 'selected'; } ?>><?=MapUtil::get($user, 'select_label')?></option>
<?php
	} else if($myJob->referral_id == $user['user_id']) {
		$userStatus = $user['is_deleted'] ? 'Deleted' : 'Inactive';
?>
                <option value="<?= $user['user_id'] ?>" selected><?="{$user['lname']}, {$user['fname']} ($userStatus)"?></option>
<?php
	}
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Referral Paid:</b>
        </td>
        <td class="listrow">
            <input type="checkbox" <?=empty($myJob->referral_id) ? 'disabled' : ''?> id="paid" name="paid" value="1" <?=!empty($myJob->referral_paid) ? 'checked' : ''?>>
            <span class='smallnote'><?=!empty($myJob->referral_paid) ? 'Paid on '.DateUtil::formatDate($myJob->referral_paid).' @ '.DateUtil::formatTime($myJob->referral_paid) : ''?></span>
        </td>
    </tr>
    <tr>
        <td colspan=2 align="right" class="listrow">
            <input type="submit" name="submit" value="Save">
            </form>
        </td>
    </tr>
</table>
</body>
</html>