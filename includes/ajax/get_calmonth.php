<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_schedule'))
  die("Insufficient Rights");

$m=RequestUtil::get('m');
$y=(isset($_GET['y']))?$_GET['y']:'';
if($m=="")
  $m = date("n");
if($y=="")
  $y = date("Y");

$customer = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('customer'));
$provider = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('provider'));
$user = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('user'));
$type = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('type'));

$date = strtotime($y."/".$m."/1");
$day = date("D",$date);
$month = date("F",$date);
$totaldays = date("t",$date); //get the total day of specified date

$next = $_GET;
$prev = $_GET;

$prev['m'] = $m-1;
$next['m'] = $m+1;
$prev['y'] = $y;
$next['y'] = $y;

if($prev['m']==0)
{
  $prev['y']--;
  $prev['m'] = 12;
}
if($next['m']==13)
{
  $next['y']++;
  $next['m'] = 1;
}
?>
<script>
	$('select#m').val('<?=$m?>');
	$('select#y').val('<?=$y?>');
	$('select#user').val('<?=$user?>');
	$('select#type').val('<?=$type?>');
</script>
    <table class="containertitle" width="100%">
      <tr>
        <td><a class="btn btn-blue btn-small" href="javascript:Request.make('includes/ajax/get_calmonth.php?<?=http_build_query($prev)?>', 'schedulecontainer', true, true);"><i class="icon-angle-left"></i></a></td>
        <td width='140' align="center"><?php echo $month." ".$y; ?></td>
        <td align="right"><a class="btn btn-blue btn-small" href="javascript:Request.make('includes/ajax/get_calmonth.php?<?=http_build_query($next)?>', 'schedulecontainer', true, true);"><i class="icon-angle-right"></i></a></td>
      </tr>
    </table>
    <table border="0" class="schedulecontainer" width="100%">
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
    $border = 'border: 1px solid #999999; background-color: #ffffff;';
    if($date == date('Y-m-d'))
      $border = 'border: 2px solid #0086CC; background-color: #ffffff;';
?>
        <td  class="calendar-cell month" style='<?php echo $border; ?>'>
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
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
?>
              <td class='smallnote' align="right" width="100%" style='background-color: #ededed; border-bottom: 1px solid #cccccc;'>
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
  $todolistArray =array();

	//get events
	if(empty($type))
	{
		$repairs_array = ScheduleUtil::getRepairs($date, $user, '',$customer, $provider);
		$tasks_array = ScheduleUtil::getTasks($date, $user,  '', '',$customer, $provider);
		$events_array = ScheduleUtil::getEvents($date, $user, '',$customer, $provider);
		$appointments_array = ScheduleUtil::getAppointments($date, $user, '',$customer, $provider);
		$deliveries_array = ScheduleUtil::getDeliveries($date, $user, '',$customer, $provider);
    $todolistArray = ScheduleUtil::getTodolist($date, $user, '',$customer, $provider);
	}
	else
	{
		if(strpos($type, 'task_type=') !== false)
		{
			$task_type_id = str_replace('task_type=', '', $type);
			$tasks_array = ScheduleUtil::getTasks($date, $user, $task_type_id ,'',$customer, $provider);
		}
		switch($type)
		{
			case 'appointment':
				$appointments_array = ScheduleUtil::getAppointments($date, $user, '',$customer, $provider);
				break;
			case 'delivery':
				$deliveries_array = ScheduleUtil::getDeliveries($date, $user, '',$customer, $provider);
				break;
			case 'event':
				$events_array = ScheduleUtil::getEvents($date, $user, '',$customer, $provider);
				break;
			case 'repair':
				$repairs_array = ScheduleUtil::getRepairs($date, $user,  '',$customer, $provider);
				break;
      case 'todolist':
        $todolistArray = ScheduleUtil::getTodolist($date, $user, '',$customer, $provider);
        break;
		}
	}
  //echo "<pre>";print_r($todolistArray);
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
              <td colspan=2 style='background-color: <?php echo $task['color']; ?>;'><?php echo $paid_str; ?><s><a href='javascript: applyOverlay("get_taskfromschedule.php?id=<?php echo $task['task_id']; ?>");' class='schedulelink'><?php echo $task['job_number']; ?></a></s> <span class='smallnote'>(<?php echo $task['task']; ?>)</span></td>
            </tr>
<?php
      }
      else
      {
?>
            <tr>
              <td colspan=2 style='background-color: <?php echo $task['color']; ?>;'><?php echo $paid_str; ?><a href='javascript: applyOverlay("get_taskfromschedule.php?id=<?php echo $task['task_id']; ?>");' class='schedulelink'><?php echo $task['job_number']; ?></a> <span class='smallnote'>(<?php echo $task['task']; ?>)</span></td>
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
              <td colspan=2><a href='jobs.php?id=<?php echo $delivery['job_id']; ?>' class='schedulelink' tooltip>Material Delivery</a></td>
            </tr>
<?php
      }
      else
      {
?>
            <tr>
              <td colspan=2><b><span style="font-family: wingdings; font-size: 10px;">&#252;</span></b> <a href='jobs.php?id=<?php echo $delivery['job_id']; ?>' class='schedulelink' tooltip>Material Delivery</a></td>
            </tr>
<?php
      }
    }

    foreach($todolistArray as $todo)
    {
      $todotip = (!empty($todo['completed']))?'Completed on or before '.$todo['date_of_complete'].', Click to view To Do List details.':'View To Do List details';
?>
        <tr>
          <td colspan=2 style="background-color: <?=$todo['color']?>; margin:5px 5px;  padding:5px 5px; ">
          <a href='javascript:applyOverlay("todolist/todolist_details.php?id=<?=$todo['todolist_id']?>&todolist_job_id=<?= $todo['tbl_todolist_job_id'] ?>");' class='schedulelink' title="<?=$todotip?>" tooltip><?php echo $todo['todolist_job']; ?></a>
          <p style="margin:2px 2px "><a href="<?=ROOT_DIR?>/jobs.php?id=<?=$todo['job_id']?>" tooltip><?=$todo['job_number']?></a></p>
          </td>
        </tr>
<?php
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
      <tr>
        <td colspan=7>
          <table border="0">
            <tr>
              <td width='20'>
                <img src='<?=IMAGES_DIR?>/icons/print_16.png'>
              </td>
              <td><a href="<?=AJAX_DIR?>/get_calmonthprint.php?m=<?php echo $m; ?>&y=<?php echo $y; ?>&type=<?=$type?>&user=<?=$user?>" target="_blank" class='boldlink'>Print</a></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>