<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
$firstLast = UIUtil::getFirstLast();

$userId = RequestUtil::get('user_id');
$myJob = new Job(RequestUtil::get('id'));

ModuleUtil::checkJobModuleAccess('modify_job_subscribers', $myJob, TRUE);

$errors = array();
if(RequestUtil::get("submit")) {
    if(empty($userId)) {
        $errors[] = 'User cannot be blank';
    } else {
        $sql = "INSERT INTO subscribers
                VALUES (NULL, '{$myJob->job_id}', '$userId', now())";
        DBUtil::query($sql);
        NotifyUtil::notifyFromTemplate('add_job_subscriber', $userId, NULL, array('job_id' => $myJob->job_id));
    ?>
<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>&tab=subscribers', 'jobscontainer',true,true);
</script>
<?php
    }
}
?>
<form method="post" name="subscriber" action="?id=<?=$myJob->job_id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Add Job Subscriber</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>User:</b>
        </td>
        <td class="listrownoborder">
            <select name="user_id">
                <option value=""></option>
<?php
$currentSubscribers = $myJob->getSubscribers();
$showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');
$dropdownUserLevels = AccountModel::getMetaValue('assign_job_subscriber_user_dropdown');
$users = !empty($dropdownUserLevels)
            ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast)
            : UserModel::getAll($showInactiveUsers, $firstLast);
foreach($users as $user) {
    if(array_key_exists(MapUtil::get($user, 'user_id'), $currentSubscribers) || MapUtil::get($user, 'user_id') == $myJob->salesman_id) {
        continue;
    }
?>
                <option value="<?=$user['user_id']?>"><?=$user['select_label']?></option>
<?php
}
?>
            </select>
            <input name="submit" type="submit" value="Add">
        </td>
    </tr>
    <tr>
        <td class="listitem" valign="top"><b>Subscribers:</b></td>
        <td class="listrow">
<?php
if(!empty($myJob->salesman_lname)) {
?>
            <div class="subscriber"><?php echo $myJob->salesman_lname.", ".$myJob->salesman_fname; ?></div>
<?php
}
foreach($currentSubscribers as $subscriber) {
?>
            <div class="subscriber"><?=$subscriber['lname']?>, <?=$subscriber['fname']?></div>
<?php
}
?>
        </td>
    </tr>
</table>
</form>
</body>
</html>