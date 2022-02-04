<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
$appointment = DBUtil::getRecord('appointments');
$id = RequestUtil::get('id');
$action = RequestUtil::get('action');
$fromSchedule = RequestUtil::get('from_schedule');
if(!$appointment) {
    UIUtil::showModalError('Appointment not found!');
}
$myUser = new User(MapUtil::get($appointment, 'user_id'));
$myJob = new Job(MapUtil::get($appointment, 'job_id'));
ModuleUtil::checkJobModuleAccess('view_job_appointment', $myJob, TRUE);

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
        <td>View Appointment Detail</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder"><b>Title:</b></td>
        <td class="listrownoborder"><?=MapUtil::get($appointment, 'title')?></td>
    </tr>
    <tr>
        <td class="listitem"><b>Creator:</b></td>
        <td class="listrow"><?=$myUser->getDisplayName(FALSE)?></td>
    </tr>
    <tr>
        <td class="listitem"><b>Date:</b></td>
        <td class="listrow"><?=DateUtil::formatDate(MapUtil::get($appointment, 'datetime'))?></td>
    </tr>
    <tr>
        <td class="listitem"><b>Time:</b></td>
        <td class="listrow"><?=DateUtil::formatTime(MapUtil::get($appointment, 'datetime'))?></td>
    </tr>
    <tr>
        <td class="listitem"><b>Job Number:</b></td>
        <td class="listrow"><?=$myJob->get('job_number')?></td>
    </tr>
    <tr>
        <td class="listitem"><b>Salesman:</b></td>
        <td class="listrow"><?=UserUtil::getDisplayName($myJob->get('salesman'), FALSE)?></td>
    </tr>
    <tr valign='top'>
        <td class="listitem"><b>Description:</b></td>
        <td class="listrow"><?=UIUtil::cleanOutput(MapUtil::get($appointment, 'text'), FALSE)?></td>
    </tr>
    <tr>
        <td class="listitem"><b>Created</b></td>
        <td class="listrow"><?=DateUtil::formatDateTime(MapUtil::get($appointment, 'timestamp'))?></td>
    </tr>
    <tr>
        <td align="right" colspan=2 class="listrow">
<?php
if(!$fromSchedule && (ModuleUtil::checkAccess('edit_job_appointment') || ModuleUtil::checkJobModuleAccess('view_job_appointment', $myJob))) {
?>
            <div class="btn btn-danger btn-small"
                 rel="change-window-location"
                 data-url="get_appointment.php?id=<?=$id?>&action=del"
                 data-confirm="Are you sure?"
                 title="Remove appointment" tooltip><i class="icon-trash"></i></div>
<?php
}
if($fromSchedule) {
?>
            <div class="btn btn-small"
                 rel="change-parent-location"
                 data-url="/jobs.php?id=<?=$myJob->getMyId()?>"
                 title="View job" tooltip><i class="icon-briefcase"></i></div>
<?php
}
?>
        </td>
    </tr>
</table>
</body>
</html>