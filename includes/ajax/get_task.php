<?php
include '../common_lib.php'; 

echo ViewUtil::loadView('doc-head');

$myTask = new Task(RequestUtil::get('id'));
$myJob = new Job($myTask->job_id);
$myCustomer = new Customer($myJob->customer_id);

ModuleUtil::checkJobModuleAccess('view_task', $myJob, TRUE);

if(RequestUtil::get('action') == 'del') {
	$sql = "delete from tasks where task_id = '{$myTask->task_id}' limit 1";
	//NotifyUtil::notifySubscribersFromTemplate('del_task', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id, 'task_id' => $myTask->task_id));
	DBUtil::query($sql);
                
    if(!$myJob->shouldBeWatching($myTask->user_id)) {
        UserModel::stopWatchingConversation($myJob->job_id, 'job', $myTask->user_id);
    }
    if(!$myJob->shouldBeWatching($myTask->contractor_id)) {
        UserModel::stopWatchingConversation($myJob->job_id, 'job', $myTask->contractor_id);
    }
?>

<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$myJob->job_id?>', 'jobscontainer', true, true, true);
</script>
<?php
	die();
}

?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                View Task Detail
              </td>
              <td align="right">
              <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="infocontainernopadding">
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
<?php
if($myTask->completed!='')
{
?>
            <tr>
              <td colspan=2>
                <table border="0" width="100%" cellspacing="0" cellpadding="0" >
                  <tr valign="center">
                    <td width=150 class="listitemnoborder" style='border-bottom:1px solid #cccccc;'><img src='<?=IMAGES_DIR?>/icons/tick_32.png'></td>
                    <td class="listrownoborder" style='border-bottom:1px solid #cccccc;'>
                      <span class="smalltitle">Completed</span>
                      <br>in <b><?=$myTask->total_length?></b> day(s)
                      <br /><span class='smallnote'>on <?=$myTask->completed?></span>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
<?php
}
if($myTask->paid!='')
{
?>
            <tr>
              <td colspan=2>
                <table border="0" width="100%" cellspacing="0" cellpadding="0" >
                  <tr valign="center">
                    <td width=150 class="listitemnoborder" style='border-bottom:1px solid #cccccc;'><img src='<?=IMAGES_DIR?>/icons/dollar_32.png'></td>
                    <td class="listrownoborder" style='border-bottom:1px solid #cccccc;'>
                      <span class="smalltitle">Contractor Paid</span>
                      <br /><span class='smallnote'>on <?=$myTask->paid?></span>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
<?php
}
?>
            <tr>
              <td width=150 class="listitemnoborder"><b>Task Type:</b></td>
                <td class="listrownoborder">
                    <i class="icon-circle" style="color: <?=$myTask->task_color?>"></i>&nbsp;
                    <?=$myTask->task_type?>
                </td>
            </tr>
            <tr>
              <td class="listitem"><b>Creator:</b></td>
              <td class="listrow"><?=$myTask->user_fname." ".$myTask->user_lname?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Customer:</b></td>
              <td class="listrow"><?=$myCustomer->getDisplayName()?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Address:</b></td>
              <td class="listrow"><?=$myCustomer->getFullAddress()?></td>
            </tr>
            <tr>
              <td class="listitem"><b>DOB:</b></td>
              <td class="listrow"><?=DateUtil::formatMySQLDate($myTask->timestamp)?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Associated Stage:</b></td>
              <td class="listrow"><?=$myTask->stage_name?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Contractor:</b></td>
              <td class="listrow"><?=$myTask->contractor_fname." ".$myTask->contractor_lname?></td>
            </tr>
            <tr>
              <td class="listitem"><b>DBA:</b></td>
              <td class="listrow"><?=$myTask->contractor_dba?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Allotted Duration:</b></td>
              <td class="listrow">
<?php
  if($myTask->duration=='')
    echo "TBD";
  else echo $myTask->duration." day(s)";
?>
              </td>
            </tr>
            <tr>
              <td class="listitem"><b>Start Date:</b></td>
              <td class="listrow">
                 
                <?=($myTask->start_date==''||$myTask->start_date=='NULL') ? '<font color="red">Not Scheduled</font>' : DateUtil::formatMySQLDate($myTask->state_date) ?>
              </td>
            </tr>
            <tr valign='top'>
              <td class="listitem"><b>Notes:</b></td>
              <td class="listrow"><?=UIUtil::cleanOutput($myTask->notes, FALSE)?></td>
            </tr>
            <tr>
              <td align="right" colspan=2 class="listrow">
<?php
if(ModuleUtil::checkAccess('edit_job_task'))
{
?>
                <input type='button' value='Edit' onclick='window.location="edit_task.php?id=<?=$myTask->task_id?>";'>
<?php
}
if(ModuleUtil::checkAccess('delete_job_task'))
{
?>
                <input type='button' value='Remove' onclick='if(confirm("Are you sure?")){window.location="get_task.php?id=<?=$myTask->task_id?>&action=del";}'>
<?php
}
?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>