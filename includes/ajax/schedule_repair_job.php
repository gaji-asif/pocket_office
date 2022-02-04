<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('view_jobs', TRUE);

//try to get existing schedule form
$uploadId = RequestUtil::get('upload_id');
$jobId = RequestUtil::get('id');

try {
    $scheduleForm = new ScheduleForm();
    $scheduleForm->initByUploadId($uploadId);
} catch (Exception $e) {
    $newForm = TRUE;
    $scheduleForm = new ScheduleForm();
    $scheduleForm->setType('repair');
}

$myJob = new Job($scheduleForm->exists ? $scheduleForm->getJobId() : $jobId);
$myCustomer = new Customer($myJob->customer_id);

if(RequestUtil::get('scheduleRepairJob')) {
    if(!$scheduleForm->exists) {
        //give a temporary upload id
        $scheduleForm->setJobId($myJob->job_id)->setUploadId(-1)->store();
    }
    
	foreach($_POST as $key => $dataPoint) {
		JobModel::setMetaValue($scheduleForm->getMyMetaId(), "schedule_repair_job_$key", $dataPoint);
	}

    $viewData = array(
		'meta_data' => $scheduleForm->getMetaData(),
		'job_number' => $myJob->job_number,
		'logo' => LOGOS_PATH . '/' . $_SESSION['ao_logo']
	);
	$html = ViewUtil::loadView('pdf/schedule-repair-job', $viewData);
	$fileName = PdfUtil::generatePDFFromHtml($html, 'Repair Job Schedule Form', true, UPLOADS_PATH);
    $title = RequestUtil::get('upload_title', 'Schedule Repair Job');

	if($newForm) {
        $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
                VALUES ('{$myJob->job_id}', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$fileName', '$title', now())";
        DBUtil::query($sql);
        $scheduleForm->setUploadId(DBUtil::getInsertId())->store();
	}
	else {
		$sql = "UPDATE uploads SET filename = '$fileName', timestamp = now(), active = 1, title = '$title' WHERE upload_id = '{$scheduleForm->getUploadId()}'";
		DBUtil::query($sql);
	}
?>
<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$myJob->job_id?>&tab=uploads', 'jobscontainer', true, true, true);
</script>
<?php
	die();
}
?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
<tr>
    <td>
        <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
                <td>
                    Schedule Repair Job
                </td>
                <td align="right">
                    <i class="icon-remove grey btn-close-modal"></i>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td class="infocontainernopadding">
        <table width="100%" border='0' cellspacing='0' cellpadding='0'>
            <tr>
                <td>
                    <table width="100%" border='0' cellspacing='0' cellpadding='0'>
                        <tr>
                            <td>
                                <form method="post" name='customer' action='?upload_id=<?=$scheduleForm->getUploadId()?>&id=<?=$myJob->job_id?>'>
                                    <table border="0" width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td width=125 class="listitemnoborder"><b>Title:</b></td>
                                            <td class="listrownoborder" colspan="5"><input type="text" name='upload_title' value='<?=$scheduleForm->getUploadTitle()?>'></td>
                                        </tr>
                    <tr>
                        <td width=125 class="listitem"><b>Customer:</b></td>
                        <td class="listrow"><input readonly="readonly" type="text" name='customer' value="<?=$myCustomer->getDisplayName()?>"></td>
                        <td width=125 class="listitem"><b>Salesman:</b></td>
                        <td class="listrow"><input readonly="readonly" type="text" name='salesman' value='<?=$myJob->salesman_fname.' '.$myJob->salesman_lname?>'></td>
                        <td width=125 class="listitem"><b>Job#:</b></td>
                        <td class="listrow"><input readonly="readonly" type="text" name='job' value='<?=$myJob->job_number?>'></td>
                    </tr>
                    <tr>
                        <td class="listitem"><b>Address:</b></td>
                        <td class="listrow"><input readonly="readonly" type="text" name='address' value="<?=$myCustomer->get('address')?>"></td>
                        <td class="listitem"><b>Phone#:</b></td>
                        <td class="listrow"><input readonly="readonly" type="text" name='phone' value="<?=UIUtil::formatPhone($myCustomer->get('phone'))?>"></td>
                        <td class="listitem"><b>Start Date:</b></td>
                        <td class="listrow"><input readonly="readonly" type="text" name='startdate' value="<?=DateUtil::formatDate($myCustomer->get('timestamp'))?>"></td>
                    </tr>
                    <tr>
                        <td class="listitem"><b>City / State / Zip:</b></td>
                        <td class="listrow">
                            <input readonly="readonly" type="text" name='city' size="11" value="<?=$myCustomer->get('city') ?>">
                            <input readonly="readonly" type="text" name='state' size="11" value="<?=$myCustomer->get('state') ?>">
                            <input readonly="readonly" type="text" name='zip' size="10" value="<?=$myCustomer->get('zip')?>">
                        </td>
                        <td class="listitem"><b>Salesman/Ph#:</b></td>
                        <td class="listrow"><input readonly="readonly" type="text" name='phone2' value='<?=$myJob->salesman_phone?>'></td>
                        <td class="listitem"></td><td class="listrow"></td>
                    </tr>
                                    </table>
                            </td>
                        </tr>
                    </table>

                    <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                        <tr valign="center">
                            <td>
                                Job Details
                            </td>
                            <td align="right"></td>
                        </tr>
                    </table>
                    <table border="0" width="100%" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width=125 class="listitemnoborder"><b>House:</b></td>
                            <td class="listrownoborder"><input type="text" name='house' value='<?=$scheduleForm->getMetaData("schedule_repair_job_house")?>'></td>
                            <td class="listitemnoborder"><b>Garage:</b></td>
                            <td class="listrownoborder"><input type="text" name='garage' value='<?=$scheduleForm->getMetaData("schedule_repair_job_garage")?>'></td>
                        </tr>
                        <tr>
                            <td width=125 class="listitem"><b>Shed:</b></td>
                            <td class="listrow"><input type="text" name='shed' value='<?=$scheduleForm->getMetaData("schedule_repair_job_shed")?>'></td>
                            <td class="listitem"><b>Patio:</b></td>
                            <td class="listrow"><input type="text" name='patio' value='<?=$scheduleForm->getMetaData("schedule_repair_job_patio")?>'></td>
                        </tr>
                        <tr>
                            <td width=125 class="listitem"><b>Gutters:</b></td>
                            <td class="listrow"><input type="text" name='gutters' value='<?=$scheduleForm->getMetaData("schedule_repair_job_gutters")?>'></td>
                            <td class="listitem"><b>Color:</b></td>
                            <td class="listrow"><input type="text" name='color' value='<?=$scheduleForm->getMetaData("schedule_repair_job_color")?>'></td>
                        </tr>
                        <tr>
                            <td width=125 class="listitem"><b>Total L.F:</b></td>
                            <td class="listrow"><input type="text" name='total_l_f' value='<?=$scheduleForm->getMetaData("schedule_repair_job_total_l_f")?>'></td>
                            <td class="listitem"><b>Downspout:</b></td>
                            <td class="listrow"><input type="text" name='downspout' value='<?=$scheduleForm->getMetaData("schedule_repair_job_downspout")?>'></td>
                        </tr>
                        <tr>
                            <td width=125 valign="top" class="listitem"><b>Repair Details:</b></td>
                            <td class="listrow"><textarea name="repair_details" cols="25" rows="6"><?=$scheduleForm->getMetaData("schedule_repair_job_repair_details")?></textarea></td>
                            <td class="listitem"><b>Agreed upon price with Subcontractor:</b></td>
                            <td class="listrow"><input type="text" name='agreed_upon_price' value='<?=$scheduleForm->getMetaData("schedule_repair_job_agreed_upon_price")?>'></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td align="right" class="listrow">
                    <input name="scheduleRepairJob" type="submit" value="Save">
                    </form>
                </td>
            </tr>
        </table>
    </td>
</tr>
</table>
</body>
</html>