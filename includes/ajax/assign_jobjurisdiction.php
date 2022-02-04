<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');

$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('assign_job_jurisdiction', $myJob, TRUE);

if(RequestUtil::get('submit')) {
    FormUtil::update('jobs');
    $myJob->storeSnapshot();

	JobModel::saveEvent($myJob->job_id, "Modified Job Jurisdiction");
	NotifyUtil::notifySubscribersFromTemplate('add_job_jurisdiction', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id));
?>
<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$myJob->job_id?>', 'jobscontainer', true, true, true);
</script>
<?php
	die();
}
?>
<form method="post" name="jurisdiction" action='?id=<?=$myJob->job_id?>'>
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>
            Assign Jurisdiction
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>Jurisdiction:</b>
        </td>
        <td class="listrownoborder">
            <select name="jurisdiction">
                <option value=""></option>
<?php
$jurisdictions = CustomerModel::getAllJurisdictions();
foreach($jurisdictions as $jurisdiction)
{
?>
                <option value="<?= $jurisdiction['jurisdiction_id'] ?>" <?=$myJob->jurisdiction_id == $jurisdiction['jurisdiction_id'] ? 'selected' : ''?>><?=$jurisdiction['location']?></option>
<?php
}
?>
            </select>
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