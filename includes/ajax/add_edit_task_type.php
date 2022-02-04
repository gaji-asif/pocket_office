<?php
include '../common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);

$id = RequestUtil::get('id');
$task = DBUtil::getRecord('task_type');
$taskName = RequestUtil::get('task');
$taskColor = RequestUtil::get('color');

if($id && !count($task)) {
    UIUtil::showModalError('Could not retrieve task type data');
}

$autoCreateTasks = TaskUtil::getAutoCreateTasks($id);

$errors = array();
if(RequestUtil::get('submit')) {
    if(empty($taskName) || empty($taskColor)) {
		$errors[] = 'Required fields missing';
    }
    
    if(!count($errors)) {
        //update
        if(count($task)) {
            FormUtil::update();
            TaskUtil::updateAutoCreateTasks($id, $autoCreateTasks);
        }
        //new
        else {
            $sql = "INSERT INTO task_type (account_id, task, color)
                    VALUES ('{$_SESSION['ao_accountid']}', '$taskName', '$taskColor')";
            DBUtil::query($sql);
        }
?>
<script>
    parent.window.location.href = '/tasktypes.php';
</script>
<?php
        die();
    }
}
?>

<form method="post" name="task_type" action="?id=<?=$id?>">
<input name="table" type="hidden" value="task_type" />
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td><?=count($task) ? 'Edit' : 'Add'?> Task Type</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td class="listitem" width="25%">
            <b>Task Type Name:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input name="task" type="text" class="form-control" value="<?=MapUtil::get($task, 'task')?>" />
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Color:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input name="color" class="form-control sm color {hash:true}" value="<?=MapUtil::get($task, 'color')?>" />
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Auto-create Tasks:</b>
        </td>
        <td class="listrow">
            <select name="task_types">
                <option value=""></option>
<?php
$taskTypes = TaskModel::getAllTaskTypes();
foreach($taskTypes as $taskType) {
    if(MapUtil::get($taskType, 'task_type_id') == $id) { continue; }
?>
                <option value="<?=MapUtil::get($taskType, 'task_type_id')?>" data-color="<?=MapUtil::get($taskType, 'color')?>">
                    <?=MapUtil::get($taskType, 'task')?>
                </option>
<?php
}
?>
            </select>&nbsp;<input type="button" rel="add-auto-create-task" value="Add" />
            <ul class="job-items-list" id="auto-create-tasks">
<?php
foreach($autoCreateTasks as $autoCreateTask) {
?>
                <li>
                    <a href="" class="red" rel="del-my-li"><i class="icon-trash"></i></a>
                    &nbsp;<i class="icon-circle" style="color: <?=MapUtil::get($autoCreateTask, 'color')?>;"></i>
                    &nbsp;<?=MapUtil::get($autoCreateTask, 'task')?>
                    <input type="hidden" name="auto_create_tasks[]" value="<?=MapUtil::get($autoCreateTask, 'task_type_id')?>" />
                </li>
<?php
}
?>
            </ul>
        </td>
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