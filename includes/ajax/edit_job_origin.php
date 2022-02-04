<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');

$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('modify_job_origin', $myJob, TRUE);

if(RequestUtil::get('submit')) {
	if(FormUtil::update('jobs')) {
        $myJob->storeSnapshot();
		JobModel::saveEvent($myJob->job_id, 'Job Origin Modified');
	}
?>
<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', true, true, true);
</script>
<?php
	die();
}

?>
<form method="post" action="?id=<?=$myJob->job_id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Modify Job Origin</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>Job Origin:</b>
        </td>
        <td class="listrownoborder">
            <select name="origin">
<?php
$origins = JobUtil::getAllOrigins();
foreach($origins as $origin) {
?>
                <option value="<?=$origin['origin_id']?>" <?=$origin['origin_id'] == $myJob->origin_id ? 'selected' : ''?>><?=$origin['origin']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan=2 align="right" class="listrow">
            <input name="submit" type="submit" value="Save">
            </form>
        </td>
    </tr>
</table>
</body>
</html>