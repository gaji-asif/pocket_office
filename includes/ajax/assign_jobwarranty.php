<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');

$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('assign_job_warranty', $myJob);

if(RequestUtil::get('submit')) {
	$warrantyId = RequestUtil::get('warranty');
	$proccessedDate = RequestUtil::get('processed');
    
	//remove warranty
    JobUtil::deleteJobMeta($myJob->job_id, array('job_warranty', 'job_warranty_processed'));

	//add warranty
	if(!empty($warrantyId)) {
        JobUtil::setJobMeta($myJob->job_id, 'job_warranty', $warrantyId);

		//add processed date
		if(!empty($proccessedDate)) {
            JobUtil::setJobMeta($myJob->job_id, 'job_warranty_processed', $proccessedDate);
		}
	}
	JobModel::saveEvent($myJob->job_id, "Modified Job Warranty");
	NotifyUtil::notifySubscribersFromTemplate('add_job_warranty', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id));
?>

<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$myJob->job_id?>', 'jobscontainer', true, true, true);
</script>
<?php
	die();
}

//get current warranty data
$currentWarrantyId = @$myJob->meta_data['job_warranty']['meta_value'];
$currentWarrantyProcessedDate = date("Y-m-d");
$processedChecked = '';
if(!empty($myJob->meta_data['job_warranty_processed']['meta_value'])) {
	$processedChecked = 'checked';
	$currentWarrantyProcessedDate = $myJob->meta_data['job_warranty_processed']['meta_value'];
	$currentWarrantyProcessedFriendlyDate = DateUtil::formatDate($currentWarrantyProcessedDate);
} else {
	$currentWarrantyProcessedDate = date("Y-m-d H:i:s");
}

?>

<form method="post" name='warranty' action='?id=<?=$_GET['id']?>'>
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>
            Assign Warranty
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>Warranty:</b>
        </td>
        <td class="listrownoborder">
            <select name='warranty'>
                <option value="">No Warranty</option>
<?php
$warranties = JobUtil::getAllWarranties();
foreach($warranties as $warranty) {
?>
                <option value='<?=$warranty['warranty_id']?>' <?php if($currentWarrantyId == $warranty['warranty_id']){ echo 'selected';} ?>><?=$warranty['label']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Processed:</b>
        </td>
        <td class="listrow">
            <label>
                <input type="checkbox" name="processed" value="<?= $currentWarrantyProcessedDate ?>" <?= $processedChecked ?> />
                <?= $currentWarrantyProcessedFriendlyDate ?>
            </label>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="right" class="listrow">
            <input type="submit" name="submit" value="Save">
            </form>
        </td>
    </tr>
</table>
</body>
</html>