<?php
include '../common_lib.php';
if(!viewWidget('widget_today') || !ModuleUtil::checkAccess('view_schedule')) { die(); }

$repairsArray = ScheduleUtil::getRepairs();
$tasksArray = ScheduleUtil::getTasks();
$eventsArray = ScheduleUtil::getEvents();
$appointmentsArray = ScheduleUtil::getAppointments();
$deliveriesArray = ScheduleUtil::getDeliveries();
$firstLast = UIUtil::getFirstLast();

if(empty($repairsArray) && empty($tasksArray) && empty($eventsArray) && empty($appointmentsArray) && empty($deliveriesArray)) {
?>
<h1 class="widget">No Tasks, Repairs, Deliveries or Events Today</h1
<?php
	return;
}
?>
<table cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td>
<?php
foreach($repairsArray as $repair) {
    $repairTooltip = !empty($repair['completed']) ? 'Completed ' . DateUtil::formatDate($repair['completed']) . '. Click to view task details.' : 'View repair details';
?>
            <div class="schedule-item repair">
                <p>
                    <a href="" class="<?=!empty($repair['completed']) ? 'line-through' : ''?>" rel="open-modal" data-script="get_repairfromschedule.php?id=<?=$repair['repair_id']?>" title="<?=$repairTooltip?>" tooltip><?=$repair['fail_type']?></a>
                </p>
                <p><a href="<?=ROOT_DIR?>/jobs.php?id=<?=$repair['job_id']?>" tooltip><?=$repair['job_number']?></a></p>
<?php
if(MapUtil::get($repair, 'contractor')) {
?>
                <p><?=UIUtil::getUserLink(MapUtil::get($repair, 'contractor'))?></p>
<?php
}
?>
                <i class="icon-wrench schedule-item-type" title="Repair" tooltip></i>
            </div>
<?php
}
foreach($appointmentsArray as $appointment) {
?>
            <div class="schedule-item appointment">
                <p><?=DateUtil::formatTime($appointment['datetime'])?><p>
                <p>
                    <a href="" rel="open-modal" data-script="get_appointment.php?from_schedule=1&id=<?=$appointment['appointment_id']?>" title="View appointment details" tooltip>
                        <?=stripslashes($appointment['title'])?>
                    </a>
                </p>
                <p><a href="<?=ROOT_DIR?>/jobs.php?id=<?=$appointment['job_id']?>" tooltip><?=$appointment['job_number']?></a></p>
                <i class="icon-time schedule-item-type" title="Appointment" tooltip></i>
            </div>
<?php
}
foreach($eventsArray as $event) {
?>
            <div class="schedule-item event">
                <p><?=$event['all_day'] ? 'All day' : DateUtil::formatTime($event['date'])?></p>
                <p>
                    <a href="" rel="open-modal" data-script="view_event.php?id=<?=$event['event_id']?>" title="View event details" tooltip>
                        <?=stripslashes($event['title'])?>
                    </a>
                </p>
                <i class="icon-calendar schedule-item-type" title="Event" tooltip></i>
            </div>
<?php
}
foreach($tasksArray as $task) {
    $job = new Job($task['job_id'], FALSE);
    $icons = '';
    if($job) {
        if($job->hasCredit('final')) {
            $icons = '<i class="icon-smile green" title="Job final payment received" tooltip></i>';
        } else if($job->hasCredit('1st')) {
            $icons = '<i class="icon-smile yellow" title="Job 1st payment received" tooltip></i>';
        }
    }
    $icons .= empty($task['paid']) ? '' : '<i class="icon-usd green" title="Task paid on ' . DateUtil::formatDate($task['paid']) . '" tooltip></i>';
    $icons .= !empty($task['midroof']) ? '<span title="' . $task['location'] . ' - ' . $task['midroof_timing'] . '" tooltip>mr</span>' : '';
?>
            <div class="schedule-item task <?=UIUtil::getContrast($task['color'])?>" style="background-color: <?=$task['color']?>;">
                <p>
                    <a href="" class="<?=!empty($task['completed']) ? 'line-through' : ''?>" rel="open-modal" data-script="get_taskfromschedule.php?id=<?=$task['task_id']?>" data-type="task" data-id="<?=$task['task_id']?>" tooltip><?=stripslashes($task['task'])?></a>
                </p>
                <p>
                    <a href="<?=ROOT_DIR?>/jobs.php?id=<?=$task['job_id']?>" tooltip><?=$task['job_number']?></a>
                </p>
<?php
if(MapUtil::get($task, 'contractor')) {
?>
                <p><?=UIUtil::getUserLink(MapUtil::get($task, 'contractor'))?></p>
<?php
}
if(!empty($icons)) {
?>
                <div class="icons">
                    <?=$icons?>
                </div>
<?php
}
?>
                <i class="icon-briefcase schedule-item-type" style="color: <?=$task['color']?>" title="Task - <?=stripslashes($task['task'])?>" tooltip></i>
            </div>
<?php
}
foreach($deliveriesArray as $delivery) {
?>
            <div class="schedule-item delivery">
                <p>
                    <?=empty($delivery['confirmed']) ? '' : '<i class="icon-ok green" title="Delivery confirmed on ' . DateUtil::formatDate($delivery['confirmed']) . '" tooltip></i>&nbsp;'?>
                    <a href="<?=ROOT_DIR?>/jobs.php?id=<?=$delivery['job_id']?>" tooltip><?=$delivery['label']?></a>
                </p>
                <p><a href="<?=ROOT_DIR?>/users.php?id=<?=$delivery['salesman']?>" tooltip><?=$delivery['lname']?></a></p>
                <i class="icon-truck schedule-item-type" title="Delivery" tooltip></i>
            </div>
<?php
	}
?>
        </td>
    </tr>
</table>