<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');

$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('assign_status_hold', $myJob);

if(RequestUtil::get('submit')) {
    $statusId = RequestUtil::get('status_id');
    
    $sql = "DELETE FROM status_holds
            WHERE job_id = '{$myJob->job_id}'
                AND account_id = '{$_SESSION['ao_accountid']}'";
    DBUtil::query($sql);
    
    if($statusId) {
        $expirationDate = RequestUtil::get('expires') ? "'" . RequestUtil::get('date') . "'" : 'NULL';
        $sql = "INSERT INTO status_holds (status_id, account_id, job_id, user_id, expires, timestamp)
                VALUES ('$statusId', '{$_SESSION['ao_accountid']}', '{$myJob->job_id}', '{$_SESSION['ao_userid']}', $expirationDate, now())";
        DBUtil::query($sql);
        JobModel::saveEvent($myJob->job_id, 'Status Hold Assigned');
    } else {
        JobModel::saveEvent($myJob->job_id, 'Status Hold Removed');
    }
?>
<script>
    Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', true, true, true);
</script>
<?php
    die();
}
?>
<form method="post" id="status-hold-form" name="status-hold" action="?id=<?=$_GET['id']?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>
            Assign Status Hold
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>Status Hold Type:</b>
        </td>
        <td class="listrownoborder">
            <select id="status-type" name="status_id">
                <option value="">No Status Hold</option>
<?php
$statuses = JobModel::getAllStatuses();
foreach($statuses as $status) {
?>
                <option value="<?=$status['status_id']?>" <?=$myJob->status_hold_id == $status['status_id'] ? 'selected' : ''?>><?=$status['status']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td width="25%" class="listitem">
            <b>Expires:</b>
        </td>
        <td class="listrow">
            <input name="expires" id="expires" value="1" type="checkbox" <?=!empty($myJob->status_hold_expires) ? 'checked' : '' ?> />
        </td>
    </tr>
    <tr id="expiration-control">
        <td width="25%" class="listitem">
            <b>Expiration Date:</b>
        </td>
        <td class="listrow">
            <?php $defaultDate = !empty($myJob->status_hold_expires) ? $myJob->status_hold_expires : DateUtil::formatMySQLDate(); ?>
            <input class="pikaday" data-default="<?=$defaultDate?>" type="text" name="date" value="<?=$defaultDate?>" />
        </td>
    </tr>
    <tr>
        <td colspan="2" align="right" class="listrow">
<?php
if($myJob->status_hold_id) {
?>
            <input name="remove" type="button" value="Remove">
<?php
}
?>
            <input name="submit" type="submit" value="Save">
        </td>
    </tr>
</table>
</form>
<script>
$(document).ready(function(){
    $('#expires').change(function() {
        if($(this).is(':checked')) {
            $('#expiration-control').show();
        } else {
            $('#expiration-control').hide();
        }
    }).change();
    
    $('[name="remove"]').click(function() {
        if(confirm('Are you sure you want to remove the status hold?')){
            $('[name="status_id"]').val('');
            $('[name="submit"]').click();
        }
    });
    
    Request.make("get_userjobstotal.php?id=" + $("#status-type").val(), "jobstotal", "", "yes");
});
</script>
</body>
</html>