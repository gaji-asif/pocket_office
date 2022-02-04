<?php

include '../common_lib.php'; 

echo ViewUtil::loadView('doc-head');

$myRepair = new Repair(RequestUtil::get('id'));
$myJob = new Job($myRepair->job_id);
 
ModuleUtil::checkJobModuleAccess('view_repair', $myJob, TRUE);

if($_GET['action'] == 'del') {
    //echo "<pre>";print_r($myRepair);die;
	$sql = "delete from repairs where repair_id='" . $myRepair->repair_id . "' limit 1";
	//NotifyUtil::notifySubscribersFromTemplate('del_repair', $_SESSION['ao_userid'], array('job_id' => $myRepair->job_id, 'repair_id' => $myRepair->repair_id));
	DBUtil::query($sql);
    
    if(!$myJob->shouldBeWatching($myRepair->user_id)) {
        UserModel::stopWatchingConversation($myJob->job_id, 'job', $myRepair->user_id);
    }
    if(!$myJob->shouldBeWatching($myRepair->contractor_id)) {
        UserModel::stopWatchingConversation($myJob->job_id, 'job', $myRepair->contractor_id);
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
                View Rush Job Detail
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
if($myRepair->completed!='')
{
?>
            <tr>
              <td colspan=2>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr valign="center">
                    <td width="25%" class="listitemnoborder" style='border-bottom:1px solid #cccccc;'><img src='<?=IMAGES_DIR?>/icons/plus_32.png'></td>
                    <td class="listrownoborder" style='border-bottom:1px solid #cccccc;'>
                      <span class="smalltitle">Completed</span>
                      <br>in <b><?php echo $myRepair->total_length; ?></b> day(s)
                      <br /><span class='smallnote'>on <?php echo $myRepair->completed; ?></span>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
<?php
}
?>
            <tr>
              <td width="25%" class="listitemnoborder"><b>Fail Type:</b></td>
              <td class="listrownoborder"><?php echo $myRepair->fail_type; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Priority:</b></td>
              <td class="listrow"><?php echo $myRepair->priority; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Creator:</b></td>
              <td class="listrow"><?php echo $myRepair->user_fname." ".$myRepair->user_lname; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>DOB:</b></td>
              <td class="listrow"><?php echo $myRepair->timestamp; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Contractor:</b></td>
              <td class="listrow"><?php echo $myRepair->contractor_fname." ".$myRepair->contractor_lname; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>DBA:</b></td>
              <td class="listrow"><?php echo $myRepair->contractor_dba; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Start Date:</b></td>
              <td class="listrow">
<?php
if($myRepair->start_date=='')
  echo "<span style='color: red;'>Not Scheduled</span>";
else echo $myRepair->start_date;
?>
              </td>
            </tr>
            <tr valign='top'>
              <td class="listitem"><b>Notes:</b></td>
              <td class="listrow"><?=UIUtil::cleanOutput($myRepair->notes, FALSE)?></td>
            </tr>
            <tr>
              <td align="right" colspan=2 class="listrow">
<?php
if(ModuleUtil::checkAccess('edit_repair'))
{
?>
                <input type='button' value='Edit' onclick='window.location="edit_repair.php?id=<?php echo $myRepair->repair_id; ?>";'>
<?php
}
if(ModuleUtil::checkAccess('delete_repair'))
{
?>
                <input type='button' value='Remove' onclick='if(confirm("Are you sure?")){window.location="get_repair.php?id=<?php echo $myRepair->repair_id; ?>&action=del";}'>
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

