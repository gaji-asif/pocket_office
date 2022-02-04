<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');

$myJob = new Job(RequestUtil::get('id'));

ModuleUtil::checkJobModuleAccess('full_job_stage_access', $myJob, TRUE);

if(isset($_POST['stage'])) {
	$stage = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['stage']);
	if(empty($stage)) {
		$error_message = 'Stage cannot be blank';
	} else if(!stageAdvanceAccess($stage['stage_num'])) {
		$error_message = 'Access to desired stage denied';
	} else {
		$sql = "UPDATE jobs
				SET stage_num='$stage', stage_date = curdate()
				WHERE job_id='{$myJob->job_id}'
				LIMIT 1";
		$results = DBUtil::query($sql);

		if($results !== false) {
		    
		    $sql = "select stage from stages where stage_num='$stage' and account_id='" . $_SESSION['ao_accountid'] . "'";
            $res = DBUtil::query($sql);
            $stage_name='';
            while ($stage_num = mysqli_fetch_row($res)) {
                $stage_name = $stage_num[0];
            }
            
			JobModel::saveEvent($myJob->job_id, "Moved to stage $stage_name");
			//NotifyUtil::notifySubscribersFromTemplate('stage_moved', null, array('job_id' => $myJob->job_id), true);
?>
<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$myJob->job_id?>', 'jobscontainer', true, true, true);
</script>
<?php
			die();
		} else {
			$error_message = 'Operation failed';
		}
	}
}

?>
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0" width="100%">
	<tr valign="center">
		<td>
			Jump Job Stage
		</td>
		<td align="right">
			<i class="icon-remove grey btn-close-modal"></i>
		</td>
	</tr>
</table>
<form action="?id=<?=$myJob->job_id?>" method="post" name="jump-to-stage">
<table class="infocontainernopadding" cellpadding="0" cellspacing="0" border="0" width="100%">
<?php
if(!empty($error_message)) {
?>
	<tr>
		<td colspan=2 class="listrow">
			<p class="red"><?=$error_message?></p>
		</td>
	</tr>
<?php
}
?>
	<tr>
		<td class="listitem" width="25%"><b>Stage:</b></td>
		<td class="listrow">
			<select name="stage">
				<option value=""></option>
<?php
$stages_array = StageModel::getAllStages(true);
foreach($stages_array as $stage_id => $stage) {
	if(stageAdvanceAccess($stage['stage_num']) || $stage['stage_num'] == $myJob->stage_num) {
?>
				<option value="<?=$stage['stage_num']?>" <?=$stage['stage_num'] == $myJob->stage_num ? 'selected' : ''?>><?=$stage['stage']?></option>
<?php
	}
}
?>
			</select>
		</td>
	</tr>
	<tr>
		<td colspan=2 align="right" class="listrow">
			<input type="submit" value="Submit" />
		</td>
	</tr>
</table>
</form>