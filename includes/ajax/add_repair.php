<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
$firstLast = UIUtil::getFirstLast();


$failType = RequestUtil::get('fail_type'); 
$priority = RequestUtil::get('priority'); 
$contractor = RequestUtil::get('contractor'); 
$notes = RequestUtil::get('notes'); 
$myJob = new Job(RequestUtil::get('id'));

ModuleUtil::checkJobModuleAccess('add_repair', $myJob, TRUE);

$errors = array();
if(RequestUtil::get("submit")) {
    if(empty($failType) || empty($priority) || empty($contractor)) {
        $errors[] = 'Required fields missing';
    } else {
        $sql = "INSERT INTO repairs
                VALUES (NULL, '{$myJob->job_id}', '{$_SESSION['ao_accountid']}', '{$_SESSION['ao_userid']}', '$contractor', '$priority', '$failType', '$notes', NULL,  now(), NULL)";
        DBUtil::query($sql);

        //watch
        UserModel::startWatchingConversation($myJob->job_id, 'job');
        if($contractor) {
            UserModel::startWatchingConversation($myJob->job_id, 'job', $contractor);
        }

        JobModel::saveEvent($myJob->job_id, 'Added New Repair');
        NotifyUtil::notifySubscribersFromTemplate('add_repair', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id));
?>
<script>
    Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$myJob->job_id?>', 'jobscontainer', true, true, true);
</script>
<?php
        die();
    }
}
?>
<form method="post" name="repair" action="?id=<?=$myJob->job_id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Add Rush Job</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>Fail Type:&nbsp;<span class="red">*</span></b>
        </td>
        <td class="listrownoborder">
            <select name="fail_type">
<?php
$failTypes = JobUtil::getAllFailTypes();
foreach($failTypes as $failType) {
?>
                <option value="<?=$failType['fail_type_id']?>"><?=$failType['fail_type']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Priority:&nbsp;<span class="red">*</span></b>
        </td>
        <td class="listrow">
            <select name='priority'>
<?php
$priorities = JobUtil::getAllProrities();
foreach($priorities as $priority) {
?>
                <option value='<?=$priority['priority_id']?>'><?=$priority['priority']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Contractor:&nbsp;<span class="red">*</span></b>
        </td>
        <td class="listrow">
            <select name="contractor">
                <option value=""></option>
<?php
$showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');
$dropdownUserLevels = AccountModel::getMetaValue('assign_repair_contractor_user_dropdown');
$contractors = !empty($dropdownUserLevels) 
                ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast)
                : UserModel::getAll($showInactiveUsers, $firstLast);
$contractors = UserUtil::sortUsersByDBA($contractors);
foreach($contractors as $contractor) {
    $displayName = stripslashes(empty($contractor['dba']) ? "{$contractor['select_label']}" : "{$contractor['dba']} ({$contractor['lname']})");

?>
                <option value="<?=$contractor['user_id']?>"><?=$displayName?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr valign="top">
        <td class="listitem"><b>Notes:</b></td>
        <td class="listrow"><textarea rows="7" style="width: 100%;" name="notes"></textarea></td>
    </tr>
    <tr>
        <td colspan="2" align="right" class="listrow">
            <input name="submit" type="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>
