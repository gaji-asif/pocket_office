<?php

include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('view_schedule'))
  die('Insufficient Rights');

echo ViewUtil::loadView('doc-head');

//get week start date
if(date('w') == 1 && 
        (empty($_GET['ws']) || 
        ($_GET['ws'] == strtotime('12:00:00')))) {
    $week_start_date = strtotime('12:00:00');
} else {
    $week_start_date = !empty($_GET['ws']) ? $_GET['ws'] : strtotime('previous monday');
}

//get previous week start date and next week start date
$previous_week_start_date = $week_start_date - 604800;
$next_week_start_date = $week_start_date + 604800;

//get filters
$user = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['user']);
$type = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['type']);

$me = UserModel::getMe();
$addressObj = $me->get('office_id') ? new Office($me->get('office_id')) : new Account($_SESSION['ao_accountid']);
?>
    <table border="0" cellspacing="0" cellpadding="0" width='800' align="center">
      <tr valign='bottom'>
        <td align="center">
          <?=AccountModel::getLogoImageTag()?>
          <br>
          <?=$addressObj->getFullAddress()?>
          <br>
          Phone: <?=UIUtil::formatPhone($addressObj->get('phone'))?>
<?php
if($addressObj->get('fax')) {
?>
          <br>
          <b>Fax:</b> <?=UIUtil::formatPhone($addressObj->get('fax'))?>
<?php
}
?>
        </td>
        <td style='font-size: 35px; font-weight: bold;' width=800 align="right">Weekly Schedule Report</td>
      </tr>
    </table>
    <br>
    <table border="0" align="center" width=800 border="0" style='font-size: 16px; font-weight: bold; border: 2px solid #000000;'>
      <tr>
        <td width='175' align="center">
            Week Of <?=DateUtil::formatDate($week_start_date)?>
        </td>
      </tr>
    </table>
    <table border="0" class='scheduleprintcontainer' width=800 align="center">
      <tr>
        <td align="center" style='font-weight: bold;'>Monday</td>
        <td align="center" style='font-weight: bold;'>Tuesday</td>
        <td align="center" style='font-weight: bold;'>Wednesday</td>
        <td align="center" style='font-weight: bold;'>Thursday</td>
        <td align="center" style='font-weight: bold;'>Friday</td>
        <td align="center" style='font-weight: bold;'>Saturday</td>
        <td align="center" style='font-weight: bold;'>Sunday</td>
      </tr>
      <tr height="<?php echo $_SESSION['ao_weekviewheight']; ?>" valign="top">

<?php
$current_date_in_iteration = $week_start_date;
for($i = 0; $i < 7; $i++)
{
	$date = date('Y-m-d', $current_date_in_iteration);

	$border = 'border: 1px solid #000000;';
?>
        <td width=150 style='<?php echo $border; ?>'>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
              <td align="right" width="100%" style='font-size: 10px; border-bottom: 1px solid #000000; background-color: #ffffff;'>
                <b><?=date('j', $current_date_in_iteration)?></b>
              </td>
            </tr>
            <tr>
              <td>
                <table border="0" width="100%" cellpadding="0">
<?php
	//initialize empty arrays
	$repairsArray = array();
	$tasksArray = array();
	$eventsArray = array();
	$appointmentsArray = array();
	$deliveriesArray = array();

	//get events
	if(empty($type))
	{
		$repairsArray = ScheduleUtil::getRepairs($date, $user);
		$tasksArray = ScheduleUtil::getTasks($date, $user);
		$eventsArray = ScheduleUtil::getEvents($date, $user);
		$appointmentsArray = ScheduleUtil::getAppointments($date, $user);
		$deliveriesArray = ScheduleUtil::getDeliveries($date, $user);
	}
	else
	{
		if(strpos($type, 'task_type=') !== false)
		{
			$task_type_id = str_replace('task_type=', '', $type);
			$tasksArray = ScheduleUtil::getTasks($date, $user, $task_type_id);
		}
		switch($type)
		{
			case 'appointment':
				$appointmentsArray = ScheduleUtil::getAppointments($date, $user);
				break;
			case 'delivery':
				$deliveriesArray = ScheduleUtil::getDeliveries($date, $user);
				break;
			case 'event':
				$eventsArray = ScheduleUtil::getEvents($date, $user);
				break;
			case 'repair':
				$repairsArray = ScheduleUtil::getRepairs($date, $user);
				break;
		}
	}
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
    foreach($eventsArray as $event)
    {
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
    foreach($tasksArray as $task)
    {
        $icons = empty($task['paid']) ? '' : '<i class="icon-usd green" title="Task paid on ' . DateUtil::formatDate($task['paid']) . '" tooltip></i>';
?>
                        <div class="schedule-item task <?=UIUtil::getContrast($task['color'])?>" style="background-color: <?=$task['color']?>;">
                            <p>
                                <a href="" class="<?=!empty($task['completed']) ? 'line-through' : ''?>" rel="open-modal" data-script="get_taskfromschedule.php?id=<?=$task['task_id']?>" data-type="task" data-id="<?=$task['task_id']?>" tooltip><?=stripslashes($task['task'])?></a>
                                 - <?=$task['duration']?> <?=$task['duration'] == 1 ? 'day' : 'days'?>
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
?>
                            <div class="icons"><?=$icons?></div>
                            <i class="icon-briefcase schedule-item-type" style="color: <?=$task['color']?>" title="Task - <?=stripslashes($task['task'])?>" tooltip></i>
                            <?=!empty($task['midroof']) ? '<span class="midroof" title="' . $task['location'] . ' - ' . $task['midroof_timing'] . '" tooltip>mr</span>' : ''?>
                        </div>
<?php
	}
    foreach($deliveriesArray as $delivery)
    {
        $icons = empty($delivery['confirmed']) ? '' : '<i class="icon-ok green" title="Delivery confirmed on ' . DateUtil::formatDate($delivery['confirmed']) . '" tooltip></i>';
?>
                        <div class="schedule-item delivery">
                            <p><a href="<?=ROOT_DIR?>/jobs.php?id=<?=$delivery['job_id']?>" tooltip><?=$delivery['label']?></a></p>
<?php
if(MapUtil::get($delivery, 'salesman')) {
?>
                            <p><?=UIUtil::getUserLink(MapUtil::get($delivery, 'salesman'))?></p>
<?php
}
?>
                            <div class="icons"><?=$icons?></div>
                            <i class="icon-truck schedule-item-type" title="Delivery" tooltip></i>
                        </div>
<?php
	}
?>
                </table>
              </td>
            </tr>
          </table>
        </td>
<?php
    $current_date_in_iteration = strtotime("+1 day", $current_date_in_iteration);
}
?>
      </tr>
    </table>
    <br /><br>
    <table border="0" width='800' align="center">
      <tr>
        <td>
          <center>Generated by <b><?=APP_NAME?></b></center>
        </td>
      </tr>
    </table>
<script>
    $(document).ready(function(){
        window.print();
    });
</script>
</body>
</html>