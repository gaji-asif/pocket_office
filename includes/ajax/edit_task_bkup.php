<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
$firstLast = UIUtil::getFirstLast();

$myTask = new Task(RequestUtil::get('id'));
$myJob = new Job($myTask->job_id);

ModuleUtil::checkJobModuleAccess('edit_job_task', $myJob, TRUE);

$schedule = RequestUtil::get('schedule');
$paid = RequestUtil::get('paid');
$contractorId = RequestUtil::get('contractor');

if(RequestUtil::get('submit')) {
    $_POST['completed'] = $paid;
    if(!$schedule) {
        $_POST['start_date'] = NULL;
    }
    if(!$paid) {
        $_POST['paid'] = NULL;
    }
    FormUtil::update('tasks');

	JobModel::saveEvent($myTask->job_id, 'Task Details Modified');

	//completed or modified
	if(empty($myTask->paid) && $paid) {
		NotifyUtil::notifySubscribersFromTemplate('complete_task', $_SESSION['ao_userid'], array('job_id' => $myTask->job_id, 'task_id' => $myTask->task_id));
	}
	else {
		NotifyUtil::notifySubscribersFromTemplate('modify_task', $_SESSION['ao_userid'], array('job_id' => $myTask->job_id, 'task_id' => $myTask->task_id));
		NotifyUtil::notifyFromTemplate('modify_task', $myTask->contractor_id, $_SESSION['ao_userid'], array('job_id' => $myTask->job_id, 'task_id' => $myTask->task_id));
	}
    
    //if a new contractor is assigned
    if($contractorId != $myTask->contractor_id) {
        if($contractorId) {
            NotifyUtil::notifyFromTemplate('task_assigned', $contractorId, $_SESSION['ao_userid'], array('job_id' => $myTask->job_id, 'task_id' => $myTask->task_id));
        }
        NotifyUtil::notifyFromTemplate('task_unassigned', $myTask->contractor_id, $_SESSION['ao_userid'], array('job_id' => $myTask->job_id, 'task_id' => $myTask->task_id));
        
        //handle watching
        if($contractorId) {
            UserModel::startWatchingConversation($myJob->job_id, 'job', RequestUtil::get('contractor'));
        }
        if(!$myJob->shouldBeWatching($myTask->contractor_id)) {
            UserModel::stopWatchingConversation($myJob->job_id, 'job', $myTask->contractor_id);
        }
    }
?>
<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$myTask->job_id?>', 'jobscontainer', true, true, true);
</script>
<?php
    die();
}
?>
<form method="post" action="?id=<?=$myTask->task_id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Edit Task</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder"><b>Task Type:</b></td>
        <td class="listrownoborder">
            <select name="task_type">
<?php
$taskTypes = TaskModel::getAllTaskTypes();
foreach($taskTypes as $taskType) {
?>
                <option value="<?= $taskType['task_type_id'] ?>" <?=$taskType['task_type_id'] == $myTask->task_type_id ? 'selected' : ''?>><?=$taskType['task']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Stage:</b>
        </td>
        <td class="listrow" >
            <select name="stage_id">
<?php
$stages = StageModel::getAllStages();
foreach($stages as $stage) {
?>
                <option value="<?=$stage['stage_id']?>" <?=$stage['stage_id'] == $myTask->stage_id ? 'selected' : ''?>><?=$stage['stage']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Duration:</b></td>
        <td class="listrow">
            <select name="duration">
<?php
for($i = 1; $i <= 50; $i++) {
?>
                <option value="<?=$i?>" <?=$i == $myTask->duration ? 'selected' : ''?>><?=$i?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Contractor:</b></td>
        <td class="listrow">
            <select name="contractor" id="contractor">
                <option value=""></option>
<?php
$showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');
$dropdownUserLevels = AccountModel::getMetaValue('assign_task_contractor_user_dropdown');
$contractors = !empty($dropdownUserLevels) 
                ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast)
                : UserModel::getAll($showInactiveUsers, $firstLast);
$contractors = UserUtil::sortUsersByDBA($contractors);
foreach($contractors as $contractor) {
    //$displayName = stripslashes(empty($contractor['dba']) ? "{$contractor['select_label']}" : "{$contractor['dba']} ({$contractor['lname']})");
    $displayName = stripslashes(empty($contractor['dba']) ? "{$contractor['select_label']}" : "{$contractor['lname']} ({$contractor['dba']})");

?>
                <option value="<?=$contractor['user_id']?>" <?=$contractor['user_id'] == $myTask->contractor_id ? 'selected' : ''?>><?=$displayName?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Schedule:</b></td>
        <td class="listrow">
            <input type="checkbox" name="schedule" value="1" <?=$myTask->start_date ? 'checked' : ''?> />
        </td>
    </tr>
    <tr class="schedule-control">
        <td class="listitemnoborder"><b>Start Date:</b></td>
        <td class="listrownoborder">
            <?php $defaultDate = $myTask->start_date ?: DateUtil::formatMySQLDate(); ?>
            <input class="pikaday" data-default="<?=$defaultDate?>" type="text" name="start_date" value="<?=$defaultDate?>" />
        </td>
    </tr>
    <tr class="schedule-control" valign="top">
        <td class="listitem"><b>Proximity Scheduler:</b></td>
        <td class="listrow" id="proximityschedule"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Completed/Contractor Paid:</b></td>
        <td class="listrow"><input type="checkbox" name="paid" value="<?=$myTask->paid ? DateUtil::formatMySQLTimestamp($myTask->paid) : DateUtil::formatMySQLTimestamp() ?>" <?=$myTask->paid ? 'checked' : '' ?>> <?=$myTask->paid?></td>
    </tr>
    <tr valign="top">
        <td class="listitem"><b>Notes:</b></td>
        <td class="listrow"><textarea rows=7 name="note"><?=UIUtil::cleanOutput($myTask->notes)?></textarea></td>
    </tr>
    <tr>
        <td colspan=2 align="right" class="listrow">
            <input name="submit" type="submit" value="Save">
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
