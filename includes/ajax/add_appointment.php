<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');

$title = RequestUtil::get('title');
$description = RequestUtil::get('description');
$time = RequestUtil::get('time');
$date = RequestUtil::get('date');
$myJob = new Job(RequestUtil::get('id'), FALSE);
if(!$myJob->exists()) {
    UIUtil::showModalError('Job not found');
}

ModuleUtil::checkJobModuleAccess('add_job_appointment', $myJob, TRUE);

if(RequestUtil::get('submit')) {
    $errors = array();
	if(empty($title) || empty($description) || empty($date) || empty($time)) {
		$errors[] = 'Required fields missing';
    }
	
    if(!count($errors)) {
		$datetime = DateUtil::formatMySQLTimestamp("$date $time");
		$sql = "INSERT INTO appointments
                VALUES(NULL, '{$_SESSION['ao_userid']}', '{$myJob->job_id}', '$datetime', '$title', '$description', now())";
		DBUtil::query($sql);

		JobModel::saveEvent($myJob->job_id, "Added New Appointment");
		NotifyUtil::notifySubscribersFromTemplate('add_appointment', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id));
        
        //handle watching
        UserModel::startWatchingConversation($myJob->job_id, 'job');
?>
<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', true, true, true);
</script>
<?php
		die();
	}
}
?>
<form action="?id=<?=$myJob->job_id?>" method="post">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Add Appointment</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?php if(!empty($errors)){?>
    <?=AlertUtil::generate($errors, 'error', TRUE)?>
<?php }?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>Title:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrownoborder">
            <input type="text" name="title" value="<?=$title?>">
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Date:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input class="pikaday" data-default="<?=DateUtil::formatMySQLDate()?>" type="text" name="date" value="<?=DateUtil::formatMySQLDate()?>" />
        </td>
    </tr>
    <tr>
        <td class="listitem">
          <b>Start Time:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <?=FormUtil::getTimePicklist()?>
        </td>
    </tr>
    <tr valign="top">
        <td class="listitem">
            <b>Description:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <textarea rows="7" name="description"><?=$description?></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="3" align="right" class="listrow">
            <input name="submit" type="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>