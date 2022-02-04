<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
$firstLast = UIUtil::getFirstLast();

$taskType = RequestUtil::get('task_type', 'NULL');
$contractor = RequestUtil::get('contractor', 'NULL');
$stage = RequestUtil::get('stage');
$notes = RequestUtil::get('notes');
$duration = RequestUtil::get('duration');
$myJob = new Job(RequestUtil::get('id'));
$requireStage = AccountModel::getMetaValue('require_task_stage');

ModuleUtil::checkJobModuleAccess('add_job_task', $myJob, TRUE);

$errors = array();
if(RequestUtil::get("submit")) {
    if(empty($taskType) || ($requireStage && empty($stage)) || empty($duration)) {
        $errors[] = 'Required fields missing';
    } else {
        $taskTypesToAdd = array_merge(array($taskType), MapUtil::pluck(TaskUtil::getAutoCreateTasks($taskType), 'task_type_id'));
        
        foreach($taskTypesToAdd as $taskTypeToAdd) {
            $sql = "INSERT INTO tasks
                    VALUES (NULL, '$taskTypeToAdd', '{$myJob->job_id}', '$stage', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', $contractor, NULL, '$notes', '$duration', NULL, NULL, now())";
            DBUtil::query($sql);

            //watch
            UserModel::startWatchingConversation($myJob->job_id, 'job');
            if(RequestUtil::get('contractor')) {
                UserModel::startWatchingConversation($myJob->job_id, 'job', $contractor);
            }
            
            $newTaskId = DBUtil::getInsertId();
            NotifyUtil::notifySubscribersFromTemplate('add_task', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id, 'task_id' => $newTaskId));
        }
        
        JobModel::saveEvent($myJob->job_id, 'Added ' . count($taskTypesToAdd) . ' New Task(s)');
?>
<script>
    Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$myJob->job_id?>', 'jobscontainer', true, true, true);
</script>
<?php
        die();
    }
}
?>
<form method="post" name="task" action="?id=<?=$myJob->job_id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>
            Add Job Task
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>Task Type:&nbsp;<span class="red">*</span></b>
        </td>
        <td class="listrownoborder">
            <select name="task_type">
<?php
$taskTypes = TaskModel::getAllTaskTypes();
foreach($taskTypes as $taskType) {
?>
                <option value="<?=$taskType['task_type_id']?>"><?=$taskType['task']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Stage:&nbsp;<?php if($requireStage) { ?><span class="red">*</span><? } ?></b>
        </td>
        <td class="listrow">
            <select name="stage">
                <option value=""></option>
<?php
$stages = StageModel::getAllStages();
foreach($stages as $stage) {
?>
                <option value="<?=$stage['stage_id']?>"><?=$stage['stage']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Contractor:</b>
        </td>
        <td class="listrow" id="contractorlist">
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
    $displayName = stripslashes(empty($contractor['dba']) ? "{$contractor['select_label']}" : "{$contractor['dba']} ({$contractor['lname']})");

?>
                <option value="<?=$contractor['user_id']?>"><?=$displayName?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Duration:&nbsp;<span class="red">*</span></b>
        </td>
        <td class="listrow">
            <select name="duration">
<?php
for($i = 1; $i < 51; $i++) {
?>
                            <option value="<?=$i?>"><?=$i?> day<?=$i === 1 ? '' : 's'?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr valign="top">
        <td class="listitem"><b>Notes:</b></td>
        <td class="listrow"><textarea rows="7" style="width: 100%;" name="notes"></textarea></td>
    </tr>
    <tr>
        <td colspan="2" align="right" class="listrow">
            <input name="submit" type="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>
