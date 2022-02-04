<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');

$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('approve_job_estimate', $myJob, TRUE);

//is it approved?
$approved = isset($myJob->meta_data['estimate_approved_date']) ? TRUE : FALSE;

//save
if(RequestUtil::get('submit')) {
    //save comments
    $comments = RequestUtil::get('comments');
    JobModel::setMetaValue($myJob->job_id, 'estimate_approved_comments', $comments);
    
    
    //approve it
    if(RequestUtil::get('approved') && !$approved) {
        //approved by
        $approvedBy = $_SESSION['ao_userid'];
        JobModel::setMetaValue($myJob->job_id, 'estimate_approved_by', $approvedBy);
        
        //approved on
        $approvedDate = DateUtil::formatMySQLTimestamp();
        JobModel::setMetaValue($myJob->job_id, 'estimate_approved_date', $approvedDate);
        
        $approved = TRUE;
        
        //notify
        NotifyUtil::notifySubscribersFromTemplate('job_estimate_approved', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id), TRUE);
    }
    //remove approval
    else if (!RequestUtil::get('approved') && $approved){
        JobModel::deleteMetaValue($myJob->job_id, 'estimate_approved_by');
        JobModel::deleteMetaValue($myJob->job_id, 'estimate_approved_date');
        
        $approved = FALSE;
        
        //notify
        NotifyUtil::notifySubscribersFromTemplate('job_estimate_remove_approval', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id), TRUE);
    }
?>
<script>
    Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', true, true, true);
</script>
<?php
    die();
}

?>
<form action="?id=<?=$myJob->job_id?>" name="approve-job-estimate" id="form-approve-job-estimate" method="post">
<table border="0" class="data-table-header" cellpadding="0" cellspacing="0" width="100%">
    <tr valign="center">
        <td>
            Approve Job Estimate
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<table border="0" class="infocontainernopadding" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td class="listitemnoborder" width="200">
            <b>Approved:</b>
        </td>
        <td class="listrownoborder">
            <input type="checkbox" name="approved" value="1" <?=!$approved ?: 'checked'?>>
        </td>
    </tr>
<?php
if($approved) {
    $user_info = UserModel::getDataForNotification($myJob->meta_data['estimate_approved_by']['meta_value']);
?>
    <tr>
        <td class="listitem">
            <b>Approved On:</b>
        </td>
        <td class="listrow">
            <?=DateUtil::formatDateTime($myJob->meta_data['estimate_approved_date']['meta_value'])?>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Approved By:</b>
        </td>
        <td class="listrow">
            <?=$user_info['fname']?> <?=$user_info['lname']?>
        </td>
    </tr>
<?php
}
?>
    <tr>
        <td class="listitem">
            <b>Comments:</b>
        </td>
        <td class="listrow">
            <textarea name="comments" style="height: 100px; width: 100%;"><?=prepareText(@$myJob->meta_data['estimate_approved_comments']['meta_value'])?></textarea>
        </td>
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