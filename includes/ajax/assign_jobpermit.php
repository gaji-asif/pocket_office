<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');

$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('assign_job_permit', $myJob, TRUE);

//$action = RequestUtil::get('action');
$permit = DBUtil::getRecord('permits', $myJob->job_id, 'job_id');

$errors = array();
if(RequestUtil::get('submit')) {
    $number = RequestUtil::get('number');
    if(empty($number)) {
        $errors[] = 'Required fields missing';
    }
    
    if(!count($errors)) {
        if(count($permit)) {
            $sql = FormUtil::createUpdateSql('permits', $myJob->job_id, 'job_id');
        } else {
            $number = RequestUtil::get('number');
            $sql = "INSERT INTO permits (jurisdiction_id, job_id, account_id, number, timestamp)
                    VALUES ('{$myJob->jurisdiction_id}', '{$myJob->job_id}', '{$_SESSION['ao_accountid']}', '$number', NOW())";
        }
        DBUtil::query($sql);
        
        //force re-cache
        RequestUtil::set('ignore_cache', 1);
        DBUtil::getRecord('permits', $myJob->job_id, 'job_id');
        
        JobModel::saveEvent($myJob->job_id, "Modified Job Permit");
        NotifyUtil::notifySubscribersFromTemplate('add_job_permit', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id));
?>

<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', true, true, true);
</script>
<?php
        die();
    }
}

?>
<form method="post" name="jurisdiction" action="?id=<?=$myJob->job_id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>
            Assign Permit
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="infocontainernopadding">
<?php
if(empty($myJob->jurisdiction)) {
?>
    <tr>
        <td class="listrownoborder">
            Please <a href="<?=AJAX_DIR?>/assign_jobjurisdiction.php?id<?=$myJob->job_id?>">assign a jurisdiction</a> first.
        </td>
    </tr>
<?php
} else {
?>
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>Permit #:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrownoborder">
            <input type="text" name="number" value='<?=$myJob->permit?>'>
        </td>
    </tr>
    <tr>
        <td colspan=2 align="right" class="listrow">
            <input type="submit" name="submit" value="Save">
            <!--<input type="button" value="Remove" rel="change-window-location" data-url="<?=AJAX_DIR?>/assign_jobpermit.php?id=<?=$myJob->job_id?>&action=del" data-confirm="Are you sure you want to remove this permit?">-->
        </td>
    </tr>
<?php
}
?>
</table>
</form>
</body>
</html>