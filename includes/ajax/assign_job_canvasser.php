<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
$firstLast = UIUtil::getFirstLast();

$jobId = RequestUtil::get('id');
$myJob = new Job(RequestUtil::get('id'), FALSE);
if(!$myJob->exists()) {
    UIUtil::showModalError('Job not found');
}
ModuleUtil::checkJobModuleAccess('assign_job_canvasser', $myJob, TRUE);

$errors = array();
if(RequestUtil::get('submit')) {
    $userId = RequestUtil::get('user_id');
    DBUtil::deleteRecord('canvassers', $jobId, 'job_id');

	if($userId) {
        $sql = "INSERT INTO canvassers (job_id, user_id)
                VALUES ('{$myJob->get('job_id')}', '$userId')";
        DBUtil::query($sql);
        NotifyUtil::notifyFromTemplate('add_job_canvasser', $userId, NULL, array('job_id' => $myJob->get('job_id')));
    } else {
        NotifyUtil::notifyFromTemplate('remove_job_canvasser', $myJob->get('canvasser_id'), NULL, array('job_id' => $myJob->get('job_id')));
    }
    $myJob->storeSnapshot();
    
    JobModel::saveEvent($myJob->get('job_id'), 'Assigned New Job Canvasser');
?>

<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$jobId?>', 'jobscontainer', true, true, true);
</script>
<?php
    die();
}
?>
<form method="post" name="canvasser" action='?id=<?=$jobId?>'>
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Assign Canvasser</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
      <td width="25%" class="listitemnoborder">
        <b>Canvasser:</b>
      </td>
      <td class="listrownoborder">
          <select name="user_id">
              <option value="">
<?php
$showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');
$dropdownUserLevels = AccountModel::getMetaValue('assign_job_canvasser_user_dropdown');
$canvassers = !empty($dropdownUserLevels) 
                ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast)
                : UserModel::getAll($showInactiveUsers, $firstLast);
foreach($canvassers as $canvasser) {
?>
            <option value="<?=$canvasser['user_id']?>" <?=$myJob->get('canvasser_id') == $canvasser['user_id'] ? 'selected' : ''?>><?=$canvasser['select_label']?></option>
<?php
}
?>
        </select>
    </td>
  </tr>
  <tr>
      <td colspan=2 align="right" class="listrow">
          <input type="submit" name="submit" value="Save">
      </td>
  </tr>
</table>
</form>
</body>
</html>