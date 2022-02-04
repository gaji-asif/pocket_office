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
    $scheduleForm->setType('window');
}

$myJob = new Job($scheduleForm->exists ? $scheduleForm->getJobId() : $jobId);
$myCustomer = new Customer($myJob->customer_id);

if(RequestUtil::get('scheduleWindowJob')) {
    if(!$scheduleForm->exists) {
        //give a temporary upload id
        $scheduleForm->setJobId($myJob->job_id)->setUploadId(-1)->store();
    }
    
	foreach($_POST as $key => $dataPoint) {
		JobModel::setMetaValue($scheduleForm->getMyMetaId(), "schedule_window_job_$key", $dataPoint);
	}

    $viewData = array(
		'meta_data' => $scheduleForm->getMetaData(),
		'job_number' => $myJob->job_number,
		'logo' => LOGOS_PATH . '/' . $_SESSION['ao_logo']
	);
	$html = ViewUtil::loadView('pdf/schedule-window-job', $viewData);
	$fileName = PdfUtil::generatePDFFromHtml($html, 'Window Job Schedule Form', true, UPLOADS_PATH);
    $title = RequestUtil::get('upload_title', 'Schedule Window Job');

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
                Schedule Window Job
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
                  <tr>
                    <td class="listitem"><b># of Windows:</b></td>
                    <td class="listrow"><input type="text" name='no_window' value='<?=$scheduleForm->getMetaData('schedule_window_job_no_window')?>'></td>
                    <td class="listitem"><b>Marked?:</b></td>
                    <td class="listrow"><input type="text" name='marked' value='<?=$scheduleForm->getMetaData('schedule_window_job_marked')?>'></td>
                    <td class="listitem"></td>
                    <td class="listrow"></td>
                  </tr>
                  <tr>
                    <td class="listitem"><b>Window on Story #:</b></td>
                    <td class="listrow"><input type="text" name='window_story' value='<?=$scheduleForm->getMetaData('schedule_window_job_window_story')?>'>

                    </td>
                    <td class="listitem"><b>Side of house:</b></td>
                    <td class="listrow">
                    <select name="window_side">
						<option value="E" <?=($scheduleForm->getMetaData('schedule_window_job_window_side') == 'E' ? 'selected' : '')?>>E</option>
						<option value="W" <?=($scheduleForm->getMetaData('schedule_window_job_window_side') == 'W' ? 'selected' : '')?>>W</option>
						<option value="N" <?=($scheduleForm->getMetaData('schedule_window_job_window_side') == 'N' ? 'selected' : '')?>>N</option>
						<option value="S" <?=($scheduleForm->getMetaData('schedule_window_job_window_side') == 'S' ? 'selected' : '')?>>S</option>
                    </select>
                    </td>
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
                    <td width=125 class="listitemnoborder"><b>Type of Window:</b></td>
                    <td class="listrownoborder" width="366">
                    <select name="window_type">
                      <option value="Wood" <?=($scheduleForm->getMetaData('schedule_window_job_window_type') == 'Wood' ? 'selected' : '')?>>Wood</option>
                      <option value="Aluminum" <?=($scheduleForm->getMetaData('schedule_window_job_window_type') == 'Aluminum' ? 'selected' : '')?>>Aluminum</option>
                      <option value="Vinyl" <?=($scheduleForm->getMetaData('schedule_window_job_window_type') == 'Vinyl' ? 'selected' : '')?>>Vinyl</option>
                      </select></td>
                    <td class="listitem" width="125"><b>Window Color:</b></td>
                     <td class="listrow"><input type="text" name='window_color' value='<?=$scheduleForm->getMetaData('schedule_window_job_window_color')?>'></td>
                  </tr>
                   <tr>
                    <td class="listitem"><b>Window Dimensions:</b></td>
                    <td class="listrow">
						<input type="text" name='window_dimension_x' size="7" value='<?=$scheduleForm->getMetaData('schedule_window_job_window_dimension_x')?>'>
						X<input type="text" size="7" name='window_dimension_y' value='<?=$scheduleForm->getMetaData('schedule_window_job_window_dimension_y')?>'>
					</td>
                    <td class="listitem"><b>Screen:</b></td>
                    <td class="listrow"><input type="text" name='window_screen' value='<?=$scheduleForm->getMetaData('schedule_window_job_window_screen')?>'></td>
                  </tr>
                  <tr>
                    <td class="listitem" valign="top"><b>Glazing Bead:</b></td>
                    <td class="listrow" valign="top"><input type="text" name='glazing_bead' value='<?=$scheduleForm->getMetaData('schedule_window_job_glazing_bead')?>'></td>
                    <td class="listitem"><b>Agreed upon price with Subcontractor:</b></td>
                    <td class="listrow"><input type="text" name='agreed_upon_price' value='<?=$scheduleForm->getMetaData('schedule_window_job_agreed_upon_price')?>'></td>
                 </tr>
                  <tr>
                    <td class="listitem" valign="top"><b>Description of Damage:</b></td>
                    <td class="listrow"><textarea cols="25" rows="6" name="des_damage"><?=$scheduleForm->getMetaData('schedule_window_job_des_damage')?></textarea></td>
                    <td class="listitem" valign="top"><b>Any Specific Details:</b></td>
                    <td class="listrow"><textarea name="specific_detail" cols="25" rows="6"><?=$scheduleForm->getMetaData('schedule_window_job_specific_detail')?></textarea></td>
                  </tr>

          </table>

          <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td align="right" class="listrow">
                  <input name="scheduleWindowJob" type="submit" value="Save">
                </form>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>