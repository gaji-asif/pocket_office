<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('event_readwrite', TRUE);

$title = RequestUtil::get('title');
$description = RequestUtil::get('description');
$date = RequestUtil::get('date', DateUtil::formatMySQLDate());
$startDate = RequestUtil::get('startdate', $date);
$endDate = RequestUtil::get('enddate', $date);
$time = RequestUtil::get('time');
$global = RequestUtil::get('global');
$groups = RequestUtil::get('groups');

$errors = array();
if(RequestUtil::get("submit")) {
	if(empty($title) || empty($description) || empty($startDate) || empty($endDate)) {
		$errors[] = 'Required fields missing';
	}
    if(strtotime($startDate) > strtotime($endDate)) {
		$errors[] = 'Start date must be before end date';
    }
	
    if(!count($errors)) {
		$allDay = 0;
		if(empty($time)) {
			$allDay = 1;
		}

		$startTimestamp = DateUtil::formatMySQLTimestamp("$startDate $time");
		$sql = "INSERT INTO events
                VALUES (0, '{$_SESSION['ao_accountid']}', '{$_SESSION['ao_userid']}', '$startTimestamp', '$endDate', '$title', '$description', now(), '$global', '$allDay')";
		DBUtil::query($sql);

		if($global == 1) {
			$eventId = DBUtil::getInsertId();

			if(empty($groups)) {
				$sql = "SELECT user_id
                        FROM users
                        WHERE account_id = '{$_SESSION['ao_accountid']}'
                            AND user_id <> '{$_SESSION['ao_userid']}'
                            AND is_active = 1
                            AND is_deleted = 0
                        ORDER BY lname ASC";
			}
			else {
				$groupsStr = implode(',', $groups);
				$sql = "SELECT ugl.user_id
                        FROM usergroups_link ugl, users u
                        WHERE ugl.user_id = u.user_id
                            AND ugl.usergroup_id in ($groupsStr)
                            AND u.is_active = 1
                            AND u.is_deleted = 0
                        GROUP BY u.user_id";
                
                //add to event meta
                foreach($groups as $group) {
                    ScheduleModel::setEventMetaValue($eventId, 'usergroup', $group);
                }
			}
            
			$userIds = DBUtil::pluck(DBUtil::query($sql), 'user_id');
			foreach($userIds as $userId) {
				NotifyUtil::notifyFromTemplate('new_event', $userId, $_SESSION['ao_userid'], array('event_id' => $eventId), true);
			}
		}

?>

<script>
	Request.makeModal('<?=AJAX_DIR?>/get_calweek.php', 'schedulecontainer', true, true, true);
</script>
<?php
		die();
	}
}
?>
<form action="?date=<?=$date?>" method="post">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Add Event</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
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
            <b>Start Date:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input class="pikaday" data-default="<?=$startDate?>" type="text" name="startdate" value="<?=$startDate?>" />
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>End Date:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input class="pikaday" data-default="<?=$endDate?>" type="text" name="enddate" value="<?=$endDate?>" />
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Start Time:</b>
        </td>
        <td class="listrow">
            <?=FormUtil::getTimePicklist()?>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Global:</b>
        </td>
        <td class="listrow">
            <input type="checkbox" name="global" value="1" onchange="$('#grouplist').toggleClass('hidden')">
        </td>
    </tr>
<?php
if(ModuleUtil::checkAccess("message_group")) {
?>
    <tr id="grouplist" class="hidden">
        <td class="listitem">
            <b>Notify Group(s):</b>
        </td>
        <td>
            <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
                <tr>
                    <td class="listrow">
                        <select name="groups[]" multiple>
<?php
    $groups = UserModel::getAllUserGroups();
    foreach($groups as $group) {
?>
                            <option value="<?=$group['usergroup_id']?>"><?=$group['label']?></option>
<?php
    }
?>
                        </select>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
<?php
}
?>
    <tr>
        <td class="listitem">
            <b>Description:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <textarea rows="7" name="description"><?=UIUtil::cleanOutput($description)?></textarea>
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
