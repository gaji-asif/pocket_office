<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_schedule'))
  die("Insufficient Rights");

$curDate = DateUtil::formatMySQLDate();

//get week start date
if(date('w') == 1 && 
        (!RequestUtil::get('ws')) || 
        (RequestUtil::get('ws') == strtotime('12:00:00'))) {
    $week_start_date = strtotime('12:00:00');
} else {
    $week_start_date = RequestUtil::get('ws') ?: strtotime('previous monday');
}

//get previous week start date and next week start date
$previous_week_start_date = $week_start_date - 604800;
$next_week_start_date = $week_start_date + 604800;

//get filters
$customer = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('customer'));
$provider = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('provider'));
$user = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('user'));
$type = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('type'));

//set nav vars
$next = $_GET;
$prev = $_GET;
$prev['ws'] = $previous_week_start_date;
$next['ws'] = $next_week_start_date;

?>
<script>
	$('input#m').val('<?=date('n', $week_start_date)?>');
	$('input#y').val('<?=date('y', $week_start_date)?>');
	$('select#user').val('<?=$user?>');
	$('select#type').val('<?=$type?>');
</script>
<table class="containertitle" width="100%">
    <tr>
        <td><a class="btn btn-blue btn-small" href="javascript:Request.make('includes/ajax/get_calweek.php?<?=http_build_query($prev)?>', 'schedulecontainer', true, true);"><i class="icon-angle-left"></i></a></td>
        <td width='175' align="center">
            Week Of <?=DateUtil::formatDate($week_start_date)?>
        </td>
        <td align="right"><a class="btn btn-blue btn-small" href="javascript:Request.make('includes/ajax/get_calweek.php?<?=http_build_query($next)?>', 'schedulecontainer', true, true);"><i class="icon-angle-right"></i></a></td>
    </tr>
</table>
<table border="0" class="schedulecontainer" width="100%">
    <tr>
        <td align="center" style='font-weight: bold;'>Monday</td>
        <td align="center" style='font-weight: bold;'>Tuesday</td>
        <td align="center" style='font-weight: bold;'>Wednesday</td>
        <td align="center" style='font-weight: bold;'>Thursday</td>
        <td align="center" style='font-weight: bold;'>Friday</td>
        <td align="center" style='font-weight: bold;'>Saturday</td>
        <td align="center" style='font-weight: bold;'>Sunday</td>
    </tr>
    <tr valign='top'>

<?php

$current_date_in_iteration = $week_start_date;
for($i = 0; $i < 7; $i++)
{
	$date = date('Y-m-d', $current_date_in_iteration);
	//echo "--".date('Y-m-d') ;
	$border = 'border: 1px solid #999999; background-color: #ffffff;';
	if(date('Y-m-d') == $date)
		$border = 'border: 2px solid #0086CC; background-color: #ffffff;';
?>
        <td class="calendar-cell week" style='<?php echo $border; ?>'>
            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                <tr>
<?php
	if(ModuleUtil::checkAccess('event_readwrite'))
	{
?>
                    <td class='smallnote' align="left" width="100%" style='background-color: #ededed; border-bottom: 1px solid #cccccc;'>
                        <a href="" class="boldlink" rel="open-modal" data-script="add_event.php?date=<?=$date?>" title="Add Event" tooltip>+</a>
                    </td>
<?php
	}
	else echo "<td style='background-color: #ededed; border-bottom: 1px solid #cccccc;'>&nbsp;</td>";
?>
                    <td class='smallnote' align="right" width="100%" style='background-color: #ededed; border-bottom: 1px solid #cccccc;'>
                        <b><?=date('j', $current_date_in_iteration)?></b>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
<?php
	//initialize empty arrays
	$repairsArray = array();
	$tasksArray = array();
	$eventsArray = array();
	$appointmentsArray = array();
	$deliveriesArray = array();
	$insuracenotification =array();
	$insuracenotification2 =array();
	$todolistArray =array();

	//get events
	if(empty($type)) {
		$repairsArray = ScheduleUtil::getRepairs($date, $user, '',$customer, $provider);
		$tasksArray = ScheduleUtil::getTasks($date, $user, '','',$customer, $provider);
		$eventsArray = ScheduleUtil::getEvents($date, $user, '',$customer, $provider);
		$appointmentsArray = ScheduleUtil::getAppointments($date, $user, '',$customer, $provider);
		$deliveriesArray = ScheduleUtil::getDeliveries($date, $user, '',$customer, $provider);
		$insuracenotification = ScheduleUtil::getInsuracenotification();
		
		$todolistArray = ScheduleUtil::getTodolist($date, $user, '',$customer, $provider);
		$insuracenotification2 = ScheduleUtil::getInsuracenotification2();
		
		$days = "+7 day";
		//echo '<pre>'; print_r($fectchdata);
		$nextdate = strtotime($days, strtotime($curDate));
		$nextdate = date('Y-m-d', $nextdate);
		$em=array();
			if(count($insuracenotification2)>0)
			{
				foreach($insuracenotification2 as $data1)
				{
					$em[] = $data1['email'];
				}
			}
		
	} else {
		if(strpos($type, 'task_type=') !== false) {
			$task_type_id = str_replace('task_type=', '', $type);
			$tasksArray = ScheduleUtil::getTasks($date, $user,$task_type_id,'',$customer, $providerd);
		}
		switch($type) {
			case 'appointment':
				$appointmentsArray = ScheduleUtil::getAppointments($date, $user, '',$customer, $provider);
				break;
			case 'delivery':
				$deliveriesArray = ScheduleUtil::getDeliveries($date, $user,'', $customer, $provider);
				break;
			case 'event':
				$eventsArray = ScheduleUtil::getEvents($date, $user, '',$customer, $provider);
				break;
			case 'repair':
				$repairsArray = ScheduleUtil::getRepairs($date, $user, '',$customer, $provider);
				break;
			case 'todolist':
				$todolistArray = ScheduleUtil::getTodolist($date, $user, '',$customer, $provider);
				break;
		}
	}
	//echo "<pre>";print_r($todolistArray);
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

	
	
	foreach($insuracenotification as $data)
	{
		//date('j', $current_date_in_iteration);
		
		$gendate = date('Y-m-d',strtotime($data['generalins']));
		$workdate = date('Y-m-d',strtotime($data['workerins']));
		
		//echo $data["generalins"];
		//if($gendate == $date || $nextdate == $date || $workdate == $date)
		if($gendate == $date )
		{
	
		if($curDate == $gendate )
		{	
			$today = ($curDate == $gendate) ? 'Today' : $gendate;
			//echo "General liability insurance expires ".$today." for User ".$data['fname']."  ".$data['lname']."<br />";	
		?>
			<p>
            	<a href="" rel="open-modal-insurance" data-script="get_insurance_detail.php?info=General liability insurance&dt=<?=$today?>&id=<?=$data['user_id']?>" title="Insurance Expiration Details" tooltip>
            		<?php echo $data["fname"].' '.$data["lname"] .'\'s General liability insurance Expire On '. $today ?>    	
                </a>
            </p>
						
			<?php
			
		}

		if($curDate == $workdate )
		{
			$today = ($curDate == $workdate) ? 'Today' : $workdate;
			//echo "Workers Compensations insurance expires ".$today." for User ".$data['fname']."  ".$data['lname']."<br />";
			?>
			<p>
            	<a href="" rel="open-modal-insurance" data-script="get_insurance_detail.php?info=Workers Compensations insurance&dt=<?=$today?>&id=<?=$data['user_id']?>" title="Insurance Expiration Details" tooltip>
            		<?php echo $data["fname"].' '.$data["lname"] .'\'s Workers Compensations insurance Expire On '. $today ?>    	
                </a>
            </p>
						
			<?php

		}
		}
		
		if($nextdate == $date)
		{
			if($nextdate == $gendate )
			{
					$today = ($curDate == $gendate) ? 'Today' : $gendate;
				//echo "General liability insurance expires ".$today." for User ".$data['fname']."  ".$data['lname']."<br />";	
					?>
			<p>
            	<a href="" rel="open-modal-insurance" data-script="get_insurance_detail.php?info=General liability insurance&dt=<?=$today?>&id=<?=$data['user_id']?>" title="Insurance Expiration Details" tooltip>
            		<?php echo $data["fname"].' '.$data["lname"] ."'s General liability insurance Expire On ". $today ?>    	
                </a>
            </p>
						
			<?php
					//echo "------>".$data["fname"].' '.$data["lname"] .' General liability insurance '. $today .' '. $data['generalins'];
			}
			if($nextdate == $workdate )
			{
				$today = ($curDate == $workdate) ? 'Today' : $workdate;
				//echo "Workers Compensations insurance expires ".$today." for User ".$data['fname']."  ".$data['lname']."<br />";
				?>
			<p>
            	<a href="" rel="open-modal-insurance" data-script="get_insurance_detail.php?info=Workers Compensations insurance&dt=<?=$today?>&id=<?=$data['user_id']?>" title="Insurance Expiration Details" tooltip>
            		<?php echo $data["fname"].' '.$data["lname"] ."'s Workers Compensations insurance Expire On ". $today ?>    	
                </a>
            </p>
						
			<?php
				//echo "<br>=====>".$data["fname"].' '.$data["lname"] .' Workers Compensations insurance '. $today ;
			}
			
		}
		
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
        $icons = empty($delivery['confirmed']) ? '' : '<i class="icon-ok green" title="Delivery confirmed on ' . DateUtil::formatDate($delivery['confirmed']) . '" tooltip></i>';
?>
                        <div class="schedule-item delivery">
                            <p><a href="<?=ROOT_DIR?>/jobs.php?id=<?=$delivery['job_id']?>" tooltip><?=$delivery['label']?></a></p><?php
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


	foreach($todolistArray as $todo) {
	    $todotip = (!empty($todo['completed']))?'Completed on or before '.$todo['date_of_complete'].', Click to view To Do List details.':'View To Do List details';
	?>
            <div class="schedule-to-do-list to-do-list" style="background-color:<?=$todo['color']?>; margin:5px 5px;  padding:5px 5px; ">
                <p style="margin:2px 2px ">
                    <a href="" class="<?=!empty($repair['completed']) ? 'line-through' : ''?>" rel="open-modal" data-script="todolist/todolist_details.php?id=<?=$todo['todolist_id']?>&todolist_job_id=<?= $todo['tbl_todolist_job_id'] ?>" title="<?=$todotip?>" tooltip><?=$todo['todolist_job']?></a>
                </p>
                <p style="margin:2px 2px "><a href="<?=ROOT_DIR?>/jobs.php?id=<?=$todo['job_id']?>" tooltip><?=$todo['job_number']?></a></p>

                <i class="to-do-list-item-type" title="To Do List" tooltip></i>
            </div>
	<?php
		}?>
                    </td>
                </tr>
            </table>
        </td>
<?php
	$current_date_in_iteration = strtotime("+1 day", $current_date_in_iteration);
}
?>
    </tr>
    <tr>
        <td colspan=7>
            <table border="0">
                <tr>
                    <td width='20'>
                        <img src='<?= IMAGES_DIR ?>/icons/print_16.png'>
                    </td>
                    <td><a href="<?= AJAX_DIR ?>/get_calweekprint.php?ws=<?= $week_start_date ?>" target="_blank" class='boldlink'>Print</a></td>
                </tr>
            </table>
        </td>
    </tr>
  </table>