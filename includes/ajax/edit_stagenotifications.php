<?php
include '../common_lib.php';
ModuleUtil::checkAccess('edit_users', TRUE);

//get data
$userId = RequestUtil::get('id');
$stageNum = RequestUtil::get('num');
$action = RequestUtil::get('action');
$checked = RequestUtil::get('checked');

//get user
$myUser = new User($userId);

//edit email
if($action == 'toggle' && !is_null($stageNum)) {
	$sql = "INSERT INTO stage_notifications (stage_num, user_id)
			VALUES ('$stageNum', '$userId')";
	if($checked == 'checked') {
		$sql = "DELETE FROM stage_notifications
				WHERE stage_num = '$stageNum' AND user_id='$userId'
				LIMIT 1";
	}
	DBUtil::query($sql);
}

//edit sms
if($action == 'togglesms' && !is_null($stageNum)) {
	$sql = "INSERT INTO stage_notifications_sms (stage_num, user_id)
			VALUES ('$stageNum', '$userId')";
	if($checked == 'checked') {
		$sql = "DELETE FROM stage_notifications_sms
				WHERE stage_num = '$stageNum' AND user_id='$userId'
				LIMIT 1";
	}
	DBUtil::query($sql);
}


//get stage data
$stages = StageModel::getAllStages(TRUE);
$emailNotifications = MapUtil::mapTo(UserModel::getEmailStageNotificationByUser($userId), 'stage_num');
$smsNotifications = MapUtil::mapTo(UserModel::getSmsStageNotificationByUser($userId), 'stage_num');

?>
<table border="0" width="100%">
<?php

if(empty($stages))
{
?>
	<tr>
		<td><b>No Stages</b></td>
	</tr>
<?php
}
else
{
?>
	<tr>
		<td align="center" width="20"><b>E</b></td>
		<td align="center" width="20"><b>T</b></td>
	</tr>
<?php
	foreach($stages as $stage)
	{
		$checked = '';
		$checked_sms = '';
		if(isset($emailNotifications[$stage['stage_num']])) {
			$checked = 'checked';
		}
		if(isset($smsNotifications[$stage['stage_num']])) {
			$checked_sms = 'checked';
		}
?>
	<tr>
		<td><input type="checkbox" <?php echo $checked; ?> rel="edit-user-stage-notification" data-user-id="<?=$userId?>" data-stage-num="<?=$stage['stage_num']?>" data-action="toggle"></td>
		<td><input type="checkbox" <?php echo $checked_sms; ?> rel="edit-user-stage-notification" data-user-id="<?=$userId?>" data-stage-num="<?=$stage['stage_num']?>" data-action="togglesms"></td>
		<td><b><?=$stage['stage_num']?>.</b>&nbsp;<?=$stage['stage']?></td>
	</tr>
<?php
	}
?>
</table>
<?php
}
?>