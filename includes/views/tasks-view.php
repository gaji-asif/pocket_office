<?php
if(empty($myJob) || get_class($myJob) !== 'Job') { return; }

$tasks = $myJob->fetchTasks();
foreach($tasks as $task) {
    $isCompleted = !empty($task['completed']);
    $isPaid = !empty($task['paid']);
?>
<li class="<?=UIUtil::getContrast($task['color'])?>" style="background-color: <?=$task['color']?>;">
<?php
$taskTooltip = 'View task details';
if($isCompleted) {
    $actionClass = 'action';
    $iconClass = 'light-gray';
    $rel = 'mark-paid';
    $paidTooltip = 'Click to mark paid';
    $taskTooltip = 'Completed ' . DateUtil::formatDate($task['completed']) . '. Click to view task details.';
    if($isPaid) {
        $iconClass = 'green';
        $rel = 'undo-mark-paid';
        $paidTooltip = 'Paid ' . DateUtil::formatDate($task['paid']) . '. Click to undo mark paid.';
    }
    
    if(!ModuleUtil::checkJobModuleAccess('edit_job_task', $myJob)) {
        $actionClass = '';
        $rel = '';
        $paidTooltip = '';
    }
?>
    <i class="icon-usd <?=$iconClass?> <?=$actionClass?>" rel="<?=$rel?>" data-job-id="<?=$myJob->job_id?>" data-task-id="<?=$task['task_id']?>" title="<?=$paidTooltip?>" tooltip></i>&nbsp;
<?php
}
?>
    <a href="" class="<?=$isCompleted ? 'line-through' : ''?>"rel="open-modal" data-script="get_task.php?id=<?=$task['task_id']?>" data-type="task" data-id="<?=$task['task_id']?>" tooltip><?=$task['task']?></a>
    <span class="smallnote"> - <?=!empty($task['start_date']) ? DateUtil::getScheduleWeekLink($task['start_date']) : 'Not Set'?></span>
</li>
<?php
}
?>