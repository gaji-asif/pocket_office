<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('edit_users'))
	die("Insufficient Rights");

//get data
$userId = RequestUtil::get('id');
$stageId = RequestUtil::get('stageid');

//get user
$myUser = new User($userId);

//get stage data
$stages = StageModel::getAllStages(true);
$levelAccess = MapUtil::mapTo(UserModel::getStageAdvancementByLevel($myUser->getLevel()), 'stage_id');
$userAccess = MapUtil::mapTo(UserModel::getStageAdvancementByUser($userId), 'stage_id');

//edit
if($stageId) {
	//remove from exceptions table
	if(isset($userAccess[$stageId])) {
		$sql = "DELETE FROM user_stage_access
				WHERE user_id = '$userId' AND stage_id = '$stageId'
				LIMIT 1";
	}
	else {
		//add false to exceptions table
		if(isset($levelAccess[$stageId])) {
			$sql = "INSERT INTO user_stage_access (user_id, account_id, stage_id)
					VALUES ('$userId', '{$_SESSION['ao_accountid']}', '$stageId')";

		}
		//add true to exceptions table
		else {
			$sql = "INSERT INTO user_stage_access (user_id, account_id, stage_id, has_access)
					VALUES ('$userId', '{$_SESSION['ao_accountid']}', '$stageId', '1')";
		}
	}

	//execute query and rebuild data arrays upon success
	if(DBUtil::query($sql)) {
		$levelAccess = MapUtil::mapTo(UserModel::getStageAdvancementByLevel($myUser->getLevel(), TRUE), 'stage_id');
        $userAccess = MapUtil::mapTo(UserModel::getStageAdvancementByUser($userId, TRUE), 'stage_id');
	}
}
?>
<table border="0" width="100%">
<?php
foreach($stages as $stage)
{
	$class = '';
	$checked = '';
	$exception = false;
	if(isset($levelAccess[$stage['stage_id']])) {
		$checked = 'checked';
		if(@$userAccess[$stage['stage_id']]['has_access'] == '0') {
			$checked = '';
			$exception = true;
		}
	} else if(@$userAccess[$stage['stage_id']]['has_access'] == '1') {
		$checked = 'checked';
		$exception = true;
	}

	if($exception) {
		$class = "red";
	}
?>
	<tr>
		<td width="20"><input type="checkbox" rel="edit-user-stage-advance-access" data-user-id="<?=$userId?>" data-stage-id="<?=$stage['stage_id']?>" <?=$checked?> /></td>
		<td class="<?=$class?>"><b><?=$stage['stage_num']?>.</b>&nbsp;<?=$stage['stage']?></td>
	</tr>
<?php
}
?>
</table>