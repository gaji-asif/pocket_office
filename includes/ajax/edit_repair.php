<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
$firstLast = UIUtil::getFirstLast();

$myRepair = new Repair(RequestUtil::get('id'));
$myJob = new Job($myRepair->job_id);

ModuleUtil::checkJobModuleAccess('edit_repair', $myJob, TRUE);

$schedule = RequestUtil::get('schedule');
$completed = RequestUtil::get('completed');
$contractorId = RequestUtil::get('contractor');

if(RequestUtil::get('submit')) {
    if(!$schedule) {
        $_POST['startdate'] = NULL;
    }
    if(!$completed) {
        $_POST['completed'] = NULL;
    }
    
    FormUtil::update('repairs');
    
	JobModel::saveEvent($myRepair->job_id, 'Repair Details Modified');

    //completed or modified
	if(empty($myRepair->completed) && $completed) {
		NotifyUtil::notifySubscribersFromTemplate('repair_completed', $_SESSION['ao_userid'], array('job_id' => $myRepair->job_id));
	} else {
		NotifyUtil::notifySubscribersFromTemplate('modify_repair', $_SESSION['ao_userid'], array('job_id' => $myRepair->job_id));
		NotifyUtil::notifyFromTemplate('modify_repair', $myRepair->contractor_id, $_SESSION['ao_userid'], array('job_id' => $myRepair->job_id));
	}
    
    //if a new contractor is assigned
    if ($contractorId != $myRepair->contractor_id) {
        if($contractorId) {
            NotifyUtil::notifyFromTemplate('repair_assigned',$contractorId, $_SESSION['ao_userid'], array('job_id' => $myRepair->job_id));
        }
        NotifyUtil::notifyFromTemplate('repair_unassigned', $myRepair->contractor_id, $_SESSION['ao_userid'], array('job_id' => $myRepair->job_id));
        
        //handle watching
        if($contractorId) {
            UserModel::startWatchingConversation($myJob->job_id, 'job', $contractorId);
        }
        if(!$myJob->shouldBeWatching($myRepair->contractor_id)) {
            UserModel::stopWatchingConversation($myJob->job_id, 'job', $myRepair->contractor_id);
        }
    }
?>
<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$myRepair->job_id?>', 'jobscontainer', true, true, true);
</script>
<?php
    die();
}
?>
<form method="post" action="?id=<?=$myRepair->repair_id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Edit Rush Job</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder"><b>Fail Type:</b></td>
        <td class="listrownoborder">
            <select name="fail_type">
<?php
$failTypes = JobUtil::getAllFailTypes();
foreach($failTypes as $failType) {
?>
                <option value="<?=$failType['fail_type_id'] ?>" <?=$myRepair->fail_type_id == $failType['fail_type_id'] ? 'selected' : ''?>><?=$failType['fail_type']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Priority:</b>
        </td>
        <td class="listrow">
            <select name="priority">
<?php
$priorities = JobUtil::getAllProrities();
foreach($priorities as $priority) {
?>
                            <option value="<?=$priority['priority_id'] ?>" <?=$myRepair->priority_id == $priority['priority_id'] ? 'selected' : ''?>><?=$priority['priority']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Contractor:</b></td>
        <td class="listrow">
            <select name="contractor">
                <option value=""></option>
<?php
$showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');
$dropdownUserLevels = AccountModel::getMetaValue('assign_repair_contractor_user_dropdown');
$contractors = !empty($dropdownUserLevels) 
                ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast)
                : UserModel::getAll($showInactiveUsers, $firstLast);
$contractors = UserUtil::sortUsersByDBA($contractors);
foreach($contractors as $contractor) {
    $displayName = stripslashes(empty($contractor['dba']) ? "{$contractor['select_label']}" : "{$contractor['dba']} ({$contractor['lname']})");
?>
                <option value="<?=$contractor['user_id']?>" <?=$contractor['user_id'] == $myRepair->contractor_id ? 'selected' : ''?>><?=$displayName?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Schedule:</b></td>
        <td class="listrow">
            <input type="checkbox" name="schedule" value="1" <?=$myRepair->start_date ? 'checked' : ''?> />
        </td>
    </tr>
    <tr class="schedule-control">
        <td class="listitemnoborder"><b>Start Date:</b></td>
        <td class="listrownoborder">
            <?php $defaultDate = $myRepair->start_date ?: DateUtil::formatMySQLDate(); ?>
            <input class="pikaday" data-default="<?=$defaultDate?>" type="text" name="startdate" value="<?=$defaultDate?>" />
        </td>
    </tr>
    <tr class="schedule-control" valign="top">
        <td class="listitem"><b>Proximity Scheduler:</b></td>
        <td class="listrow" id="proximityschedule"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Completed:</b></td>
        <td class="listrow"><input type="checkbox" name="completed" value="<?=$myRepair->completed ? DateUtil::formatMySQLTimestamp($myRepair->completed) : DateUtil::formatMySQLTimestamp() ?>" <?=$myRepair->completed ? 'checked' : '' ?>> <?=$myRepair->completed?></td>
    </tr>
    <tr valign="top">
        <td class="listitem"><b>Notes:</b></td>
        <td colspan=2 class="listrow"><textarea rows=7 name="notes"><?=UIUtil::cleanOutput($myRepair->notes)?></textarea></td>
    </tr>
    <tr>
        <td colspan=2 align="right" class="listrow">
            <input type="submit" name="submit" value="Save">
        </td>
    </tr>
</table>
</form>
<script>
$(function() {
    $('[name="contractor"]').change(getContractorSchedule).change();
    
    $('[name="schedule"]').change(function() {
        if($(this).is(':checked')) {
            $('.schedule-control').show();
        } else {
            $('.schedule-control').hide();
        }
    }).change();
});
</script>
</body>
</html>
