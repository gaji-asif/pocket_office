<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
$firstLast = UIUtil::getFirstLast();

$taskType = RequestUtil::get('task_type', 'NULL');
$contractor = RequestUtil::get('contractor', 'NULL');
$stage = RequestUtil::get('stage');
$notes = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('notes'));
$duration = RequestUtil::get('duration');
$myJob = new Job(RequestUtil::get('id'));
$requireStage = AccountModel::getMetaValue('require_task_stage');
$date = RequestUtil::get('date', DateUtil::formatMySQLDate());
$startDt = RequestUtil::get('startDt', $date);
$endDt = RequestUtil::get('endDt', $date);
$startTime = RequestUtil::get('Time');
$endTime = RequestUtil::get('endTime');
$is_notify=RequestUtil::get('is_notify');
$errors = array();			
 
$startTimestamp = DateUtil::formatMySQLTimestamp("$startDt $startTime");
$endTimestamp = DateUtil::formatMySQLTimestamp("$endDt $endTime");

ModuleUtil::checkJobModuleAccess('add_job_task', $myJob, TRUE);

$errors = array();
if(RequestUtil::get("submit")) {
 
    $schedule = RequestUtil::get('schedule');

      if(!$schedule) {
      
         if(empty($taskType) || ($requireStage && empty($stage)) || empty($duration)) {
          
          $errors[] = 'Required fields missing';
         }
        $startDt = NULL;
        $endDt= NULL;
      }
      else 
      {
     
        if(empty($taskType) || ($requireStage && empty($stage)) || empty($duration) || empty($startDt) || empty($endDt) ) {
         $errors[] = 'Required fields missing';
        } 
        else {
          if(strtotime($startDt) > strtotime($endDt)) {
		        $errors[] = 'Start date must be before end date';
          }
        }
      }
  
    if(!count($errors)) {
        $taskTypesToAdd = array_merge(array($taskType), MapUtil::pluck(TaskUtil::getAutoCreateTasks($taskType), 'task_type_id'));

        if(empty($startDt))
            $startDt='0000-00-00';
        if(empty($endDt))
            $endDt='0000-00-00';


        
        foreach($taskTypesToAdd as $taskTypeToAdd) {
            //$sql = "INSERT INTO tasks VALUES (NULL, '$taskTypeToAdd', '{$myJob->job_id}', '$stage', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', $contractor, NULL, '$notes', '$duration', NULL, NULL, now())";
            	$sql = "INSERT INTO tasks VALUES (NULL, '$taskTypeToAdd', '{$myJob->job_id}', '$stage', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', $contractor, '$startDt', '$notes', '$duration', NULL, NULL, now(),'$startTime','$endDt','$endTime')";
              
			
          DBUtil::query($sql);

            //watch
            UserModel::startWatchingConversation($myJob->job_id, 'job');
            if(RequestUtil::get('contractor')) {
                UserModel::startWatchingConversation($myJob->job_id, 'job', $contractor);
            }
            
            $newTaskId = DBUtil::getInsertId();
            if($is_notify)
            {
              NotifyUtil::notifySubscribersFromTemplate('add_task', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id, 'task_id' => $newTaskId));
            }
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

<link href = "https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
			  integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
			  crossorigin="anonymous"></script>
			  
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
        <b>
          Task Type:&nbsp;<span class="red">*</span>
        </b>
      </td>
      <td class="listrownoborder">
        <select name="task_type">
          <?php
          $taskTypes = TaskModel::getAllTaskTypes();
          foreach($taskTypes as $taskType) {
          ?>
                    <option value="<?=$taskType['task_type_id']?>"><?=$taskType['task']?>
                    </option>
                    <?php
          }
          ?>
        </select>
      </td>
    </tr>
    <tr>
      <td class="listitem">
        <b>
          Stage:&nbsp;<?php if($requireStage) { ?><span class="red">*</span><?php } ?>
        </b>
      </td>
      <td class="listrow">
        <select name="stage">
          <option value=""></option>
          <?php
$stages = StageModel::getAllStages();
foreach($stages as $stage) {
?>
          <option value="<?=$stage['stage_id']?>"><?=$stage['stage']?>
          </option>
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
         <input type="hidden" id="auto-salesman" name="contractor" >
      <?php

          if(empty($accountMetaData['add_job_salesman_user_dropdown']['meta_value'])) {
              $salesmen = UserModel::getAll(FALSE, $firstLast);
          }
          else {
              $salesmen = UserModel::getAllByLevel($accountMetaData['add_job_salesman_user_dropdown']['meta_value'], FALSE, $firstLast);
          }
      ?>                       
            <span id="jobstotal"></span>
            <input    style="width: 50%;" id = "autocomplete-5">
            <script>
                  $(function() {
                    $( "#autocomplete-5" ).autocomplete({
                       source: "<?=AJAX_DIR?>/fetch_contractor.php",
                       select: function( event , ui ) {
                                event.preventDefault();
                                $("#autocomplete-5").val(ui.item.label);
                                $("#auto-salesman").val(ui.item.value);
                            },
                      focus: function(event, ui) {
                                event.preventDefault();
                                $("#autocomplete-5").val(ui.item.label);
                            }
                    });
                    
                 });
            </script> 
      </td>
    </tr>
    <tr>
      <td class="listitem">
        <b>Schedule:</b>
      </td>
      <td class="listrow">
        <input type="checkbox" name="schedule" value="1" />
      </td>
    </tr>
    <tr class="schedule-control" valign="top">
      <td class="listitemnoborder">
        <b>
          Start Date:<span class="red">*</span>
        </b>
      </td>
      <td class="listrownoborder">
        <input class="pikaday"   type="text" name="startDt" value="" />
      </td>
    </tr>
    <tr class="schedule-control" valign="top">
      <td class="listitem">
        <b>Start Time:</b>
      </td>
      <td class="listrow">
        <?=FormUtil::getTimePicklist('Time')?>
      </td>
    </tr>
    <tr class="schedule-control" valign="top">
      <td class="listitem">
        <b>
          End Date:<span class="red">*</span>
        </b>
      </td>

      <td class="listrow">
        <input class="pikaday" type="text" name="endDt" value="" />

      </td>
    </tr>

    <tr class="schedule-control" valign="top">
      <td class="listitem">
        <b>End Time:</b>
      </td>
      <td class="listrow">
        <?=FormUtil::getTimePicklist('endTime')?>
      </td>
    </tr>
    <tr>
      <td class="listitem">
        <b>
          Duration:&nbsp;<span class="red">*</span>
        </b>
      </td>
      <td class="listrow">
        <select name="duration">
          <?php
for($i = 1; $i < 51; $i++) {
?>
          <option value="<?=$i?>"><?=$i?> day<?=$i === 1 ? '' : 's'?>
          </option>
          <?php
}
?>
        </select>
      </td>
    </tr>
    <tr valign="top">
      <td class="listitem">
        <b>Notes:</b>
      </td>
      <td class="listrow">
        <textarea rows="7" style="width: 100%;" name="notes"></textarea>
      </td>
    </tr>
    <tr>
        <td><input style="margin-left:30px;" type="checkbox" name="is_notify" value="1">Notify Email</td>
        <td align="right" class="listrow">
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
