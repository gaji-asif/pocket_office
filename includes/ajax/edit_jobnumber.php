<?php
include '../common_lib.php'; 
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
$id = RequestUtil::get('id');
$myJob = new Job($id, FALSE);
if(!count($myJob)) {
    UIUtil::showModalError('Job not found!');
}
ModuleUtil::checkJobModuleAccess('modify_job_number', $myJob, TRUE);

$errors = array();
if(RequestUtil::get('submit')) {
    $jobNumber = RequestUtil::get('job_number');

	if(strlen($jobNumber) < 6) {
		$errors[] = 'Number must be at least 6 characters';
	}
    
	if(!count($errors)) {
		FormUtil::update('jobs');
        
        $myJob->storeSnapshot();
		JobModel::saveEvent($myJob->job_id, "Job Number Modified");
		//NotifyUtil::notifySubscribersFromTemplate('edit_job_number', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id));
?>

<script>
    Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', true, true, true);
</script>
<?php
		die();
	}
}

?>
<form method="post" action="?id=<?=$id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>
            Modify Job Number
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder"><b>Job Number:</b></td>
        <td class="listrownoborder">
            <input type="text" name="job_number" value="<?=$myJob->job_number?>">
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