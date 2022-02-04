<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
$appointment = DBUtil::getRecord('users');
$id = RequestUtil::get('id');
$dat = RequestUtil::get('dt');
$dates = ($dat=="Today") ? date("Y-m-d") : $dat;

$action = RequestUtil::get('action');
$fromSchedule = RequestUtil::get('from_schedule');
if(!$appointment) {
    UIUtil::showModalError('Users not found!');
}
$myUser = new User(MapUtil::get($appointment, 'user_id'));



$act = RequestUtil::get('info');
$actions = ($act == "General liability insurance") ? 'General liability insurance' : 'Workers Compensations insurance';

if(!$fromSchedule && $action === 'del') {
    DBUtil::deleteRecord('appointments');
    if(!$myJob->shouldBeWatching()) {
        UserModel::stopWatchingConversation($myJob->job_id, 'job');
    }
?>
<script>
    Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', true, true, true);
</script>
<?php
    die();
}

?>
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>View Insurance Expiration Details</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder"><b>User name:</b></td>
        <td class="listrownoborder"><?=$myUser->getDisplayName(FALSE)?></td>
    </tr>
   
    <tr>
        <td class="listitem"><b>Expiry Date:</b></td>
        <td class="listrow"><?=$dates?></td>
    </tr>
    
	<tr>
        <td class="listitem"><b>Insurance Type:</b></td>
        <td class="listrow"><?=$actions?></td>
    </tr>
   
    
    
</table>
</body>
</html>