<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
if(!ModuleUtil::checkIsFounder()) {
    UIUtil::showListError('You do not have permission to access this.');
}

$id = RequestUtil::get('id');
$action = RequestUtil::get('action');

if($action === 'del' && $id) {
    $task = DBUtil::getRecord('tasks', $id, 'task_type');
    if(count($task)) {
        $errors[] = 'Tasks currently associated - cannot remove';
    }
    
    if(!count($errors)) {
        $sql = "DELETE FROM task_type
                WHERE task_type_id = '$id'
                    AND account_id = '{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
        
        //auto-create
        $sql = "UPDATE auto_create_tasks
                SET active = 0
                WHERE task_type_id = '$id'
                    AND child_task_type_id = '$id'
                    AND account_id = '{$_SESSION['ao_accountid']}'";
        DBUtil::query($sql);
        
        $info[] = "Task type successfully removed";
    }
}

?>
<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - Task Types</h1>
<div class="btn-group pull-right page-menu">
    <div rel="open-modal" data-script="add_edit_task_type.php" class="btn btn-success" title="Add task type" tooltip>
        <i class="icon-plus"></i>
    </div>
</div>
<?=AlertUtil::generate($errors, 'error')?>
<?=AlertUtil::generate($info, 'info')?>
<div class="list-table-container">
    <table class="table table-bordered table-condensed table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th>Auto Tasks</th>
                <th width="10%" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
<?php
$taskTypes = TaskModel::getAllTaskTypes();
foreach($taskTypes as $taskType) {
    $autoCreateTasks = TaskUtil::getAutoCreateTasks(MapUtil::get($taskType, 'task_type_id'));
    $autoCreateTasksArr = array();
    foreach($autoCreateTasks as $autoCreateTask) {
        $autoCreateTasksArr[] = '<i class="icon-circle" style="color: ' . MapUtil::get($autoCreateTask, 'color') . ' ;"></i>&nbsp;' . MapUtil::get($autoCreateTask, 'task');
    }
?>
            <tr>
                <td>
                    <i class="icon-circle" style="color: <?=MapUtil::get($taskType, 'color')?>;"></i>&nbsp;
                    <?=MapUtil::get($taskType, 'task')?>
                </td>
                <td><?=implode(', ', $autoCreateTasksArr)?></td>
                <td class="text-center">
                    <div class="btn-group">
                        <a href="" class="btn btn-small btn-danger" rel="change-window-location" data-url="<?=ROOT_DIR?>/tasktypes.php?id=<?=MapUtil::get($taskType, 'task_type_id')?>&action=del" data-confirm="Are you sure you want to remove task type '<?=MapUtil::get($taskType, 'task')?>'?" title="Delete '<?=MapUtil::get($taskType, 'task')?>'" tooltip>
                            <i class="icon-trash"></i>
                        </a>
                        <a href="" class="btn btn-small" rel="open-modal" data-script="add_edit_task_type.php?id=<?=MapUtil::get($taskType, 'task_type_id')?>" title="Edit '<?=MapUtil::get($taskType, 'task')?>'" tooltip>
                            <i class="icon-pencil"></i>
                        </a>
                    </div>
                </td>
            </tr>
<?php
}
?>
        </tbody>
    </table>
</div>
</body>
</html>
