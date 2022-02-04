<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
$id = RequestUtil::get('id');
$myJob = new Job($id, FALSE);
if(!count($myJob)) {
    UIUtil::showModalError('Job not found!');
}
ModuleUtil::checkJobModuleAccess('edit_job_date', $myJob, TRUE);

$errors = array();
if(RequestUtil::get('submit')) {
    FormUtil::update('jobs');

    $myJob->storeSnapshot();
    JobModel::saveEvent($myJob->job_id, "Job Creation Date Modified");
?>

<script>
    Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', true, true, true);
</script>
<?php
    die();
}

?>
<form method="post" action="?id=<?=$id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>
            Modify Job Creation Date
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td class="listitemnoborder"><b>Creation Date:</b></td>
        <td class="listrownoborder">
            <?php $defaultDate = $myJob->timestamp ?: DateUtil::formatMySQLDate(); ?>
            <input class="pikaday" data-default="<?=$defaultDate?>" type="text" name="timestamp" value="<?=$defaultDate?>" />
        </td>
    </tr>
    <tr>
        <td colspan=2 align="right" class="listrow">
            <input type="submit" name="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>