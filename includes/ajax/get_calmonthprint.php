<?php
include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('view_schedule'))
  die('Insufficient Rights');

echo ViewUtil::loadView('doc-head');

$user = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['user']);
$type = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['type']);

$m=$_GET['m'];
$y=$_GET['y'];
if($m=="")
  $m = date("n");
if($y=="")
  $y = date("Y");

$date = strtotime($y."/".$m."/1");
$day = date("D",$date);
$month = date("F",$date);
$totaldays = date("t",$date); //get the total day of specified date


$prev_m = $m-1;
$next_m = $m+1;
$prev_y = $y;
$next_y = $y;

if($prev_m==0)
{
  $prev_y = $y-1;
  $prev_m = 12;
}
if($next_m==13)
{
  $next_y = $y+1;
  $next_m = 1;
}

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
        <td style='font-size: 35px; font-weight: bold;' width=800 align="right">Monthly Schedule Report</td>
      </tr>
    </table>
    <br>
    <table border="0" align="center" width=800 border="0" style='font-size: 16px; font-weight: bold; border: 2px solid #000000;'>
      <tr>
        <td width='140' align="center"><?php echo $month." ".$y; ?></td>
      </tr>
    </table>
    <table border="0" class='scheduleprintcontainer' width=800 align="center">
      <tr>
        <td align="center" style='font-weight: bold;'>Sunday</td>
        <td align="center" style='font-weight: bold;'>Monday</td>
        <td align="center" style='font-weight: bold;'>Tuesday</td>
        <td align="center" style='font-weight: bold;'>Wednesday</td>
        <td align="center" style='font-weight: bold;'>Thursday</td>
        <td align="center" style='font-weight: bold;'>Friday</td>
        <td align="center" style='font-weight: bold;'>Saturday</td>
      </tr>

<?php

if($day=="Sun") $st=1;
if($day=="Mon") $st=2;
if($day=="Tue") $st=3;
if($day=="Wed") $st=4;
if($day=="Thu") $st=5;
if($day=="Fri") $st=6;
if($day=="Sat") $st=7;

if($st >= 6 && $totaldays == 31)
  $tl=42;
elseif($st == 7 && $totaldays == 30)
  $tl = 42;
else
  $tl = 35;

$ctr = 1;
$d=1;

for($i=1;$i<=$tl;$i++)
{
	if($ctr==1)
	  echo "<tr height=100 valign='top'>";

	if($i >= $st && $d <= $totaldays)
	{
	  $date = date('Y-m-d', mktime(0,0,0,$m,$d,$y));
	  $border = 'border: 1px solid #000000; background-color: #ffffff;';
?>
        <td width=120 style='<?php echo $border; ?>'>
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td align="right" width="100%" style='font-size: 10px; border-bottom: 1px solid #000000;'>
                <b><?php echo $d; ?></b>
              </td>
            </tr>
<?php
	//initialize empty arrays
	$repairs_array = array();
	$tasks_array = array();
	$events_array = array();
	$appointments_array = array();
	$deliveries_array = array();

	//get events
	if(empty($type))
	{
		$repairs_array = ScheduleUtil::getRepairs($date, $user);
		$tasks_array = ScheduleUtil::getTasks($date, $user);
		$events_array = ScheduleUtil::getEvents($date, $user);
		$appointments_array = ScheduleUtil::getAppointments($date, $user);
		$deliveries_array = ScheduleUtil::getDeliveries($date, $user);
	}
	else
	{
		if(strpos($type, 'task_type=') !== false)
		{
			$task_type_id = str_replace('task_type=', '', $type);
			$tasks_array = ScheduleUtil::getTasks($date, $user, $task_type_id);
		}
		switch($type)
		{
			case 'appointment':
				$appointments_array = ScheduleUtil::getAppointments($date, $user);
				break;
			case 'delivery':
				$deliveries_array = ScheduleUtil::getDeliveries($date, $user);
				break;
			case 'event':
				$events_array = ScheduleUtil::getEvents($date, $user);
				break;
			case 'repair':
				$repairs_array = ScheduleUtil::getRepairs($date, $user);
				break;
		}
	}

	foreach($repairs_array as $repair)
    {
      if(!empty($repair['completed']))
      {
  ?>
                  <tr>
                    <td>
                      <s><a class='repairschedulelink' href='javascript: applyOverlay("get_repairfromschedule.php?id=<?php echo $repair['repair_id']; ?>");'><?php echo $repair['fail_type'];; ?></a></s>
                    </td>
                  </tr>
  <?php
      }
      else
      {
  ?>
                  <tr>
                    <td>
                      <a class='repairschedulelink' href='javascript: applyOverlay("get_repairfromschedule.php?id=<?php echo $repair['repair_id'];; ?>");'><?php echo $repair['fail_type'];; ?></a>
                    </td>
                  </tr>
  <?php
      }
    }
    foreach($appointments_array as $appointment)
    {
?>
              <tr>
                <td colspan=2 style='background-color: #ffffff'><a href='javascript:applyOverlay("get_appointment.php?from_schedule=1&id=<?php echo $appointment['appointment_id']; ?>");' class='schedulelink'><?php echo $appointment['title']; ?></a></td>
              </tr>
<?php
    }
    foreach($events_array as $event)
    {
?>
              <tr>
                <td colspan=2 style='background-color: #ffffff'><a href='javascript:applyOverlay("view_event.php?id=<?php echo $event['event_id']; ?>");' class='schedulelink'><?php echo stripslashes($event['title']); ?></a></td>
              </tr>
<?php
    }
    foreach($tasks_array as $task)
    {
      $paid_str = '';
      if($task['paid']!='')
        $paid_str = "<img src='" . ROOT_DIR . "/images/icons/dollar_10.png'>";

      if($task['completed']!='')
      {
?>
            <tr>
              <td colspan=2 style='background-color: <?php echo $task['color']; ?>;'><?php echo $paid_str; ?><s><a href='javascript: applyOverlay("get_taskfromschedule.php?id=<?php echo $task['task_id']; ?>");' class='schedulelink'><?php echo $task['lname']; ?></a></s> <span class='smallnote'>(<?php echo $task['task']; ?>)</span></td>
            </tr>
<?php
      }
      else
      {
?>
            <tr>
              <td colspan=2 style='background-color: <?php echo $task['color']; ?>;'><?php echo $paid_str; ?><a href='javascript: applyOverlay("get_taskfromschedule.php?id=<?php echo $task['task_id']; ?>");' class='schedulelink'><?php echo $task['lname']; ?></a> <span class='smallnote'>(<?php echo $task['task']; ?>)</span></td>
            </tr>
<?php
      }
    }
    foreach($deliveries_array as $delivery)
    {
      if($delivery['confirmed']=='')
      {
?>
            <tr>
              <td colspan=2><a href='jobs.php?id=<?php echo $delivery['job_id']; ?>' class='schedulelink'>Material Delivery</a></td>
            </tr>
<?php
      }
      else
      {
?>
            <tr>
              <td colspan=2><b><span style="font-family: wingdings; font-size: 10px;">&#252;</span></b> <a href='jobs.php?id=<?php echo $delivery['job_id']; ?>' class='schedulelink'>Material Delivery</a></td>
            </tr>
<?php
      }
    }
?>
          </table>
        </td>
<?php
		$d++;
	  }
	  else
		echo "<td>&nbsp</td>";

	$ctr++;

	if($ctr > 7)
	{
	  $ctr=1;
	  echo "</tr>";
	}
}

?>
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