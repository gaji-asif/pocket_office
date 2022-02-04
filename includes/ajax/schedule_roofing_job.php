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
    $scheduleForm->setType('roofing');
}

$myJob = new Job($scheduleForm->exists ? $scheduleForm->getJobId() : $jobId);
$myCustomer = new Customer($myJob->customer_id);

if(RequestUtil::get('scheduleRoofingJob')) {
    if(!$scheduleForm->exists) {
        //give a temporary upload id
        $scheduleForm->setJobId($myJob->job_id)->setUploadId(-1)->store();
    }
    
	foreach($_POST as $key => $dataPoint) {
		JobModel::setMetaValue($scheduleForm->getMyMetaId(), "schedule_roofing_job_$key", $dataPoint);
	}

    $viewData = array(
		'meta_data' => $scheduleForm->getMetaData(),
		'job_number' => $myJob->job_number,
		'logo' => LOGOS_PATH . '/' . $_SESSION['ao_logo']
	);
	$html = ViewUtil::loadView('pdf/schedule-roofing-job', $viewData);
	$fileName = PdfUtil::generatePDFFromHtml($html, 'Roofing Job Schedule Form', true, UPLOADS_PATH);
    $title = RequestUtil::get('upload_title', 'Schedule Roofing Job');

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
                Schedule Roofing Job
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
                    </tr>

                    <tr>
                    <td class="listitem"><b>Permit:</b></td>
                    <td class="listrow">
                    <select name="permit">
                    <option value="yes" <?=($scheduleForm->getMetaData('schedule_roofing_job_permit') == 'yes' ? 'selected' : '')?>>Yes</option>
                    <option value="YES-Midroof Required" <?=($scheduleForm->getMetaData('schedule_roofing_job_permit') == 'YES-Midroof Required' ? 'selected' : '')?>>YES-Midroof Required</option>
                    <option value="no" <?=($scheduleForm->getMetaData('schedule_roofing_job_permit') == 'no' ? 'selected' : '')?>>No</option>
                    </select></td>

                    <td class="listitem"><b>Need a Production Manager:</b></td>
                    <td class="listrow">
                    	<select name="need_a_production_manager">
                    		<option value="no" <?=($scheduleForm->getMetaData('schedule_roofing_job_need_a_production_manager') == 'no' ? 'selected' : '')?>>No</option>
                    		<option value="yes" <?=($scheduleForm->getMetaData('schedule_roofing_job_need_a_production_manager') == 'yes' ? 'selected' : '')?>>Yes</option>

                    	</select>
                    </td>
                    </tr>

                     <tr>

                    <td class="listitem"><b># of Stories:</b></td>
                    <td class="listrow">
                    <select name="stories">
                      <option value="1" <?=($scheduleForm->getMetaData('schedule_roofing_job_stories') == '1' ? 'selected' : '')?>>1</option>
                      <option value="2" <?=($scheduleForm->getMetaData('schedule_roofing_job_stories') == '2' ? 'selected' : '')?>>2</option>
                      <option value="3+" <?=($scheduleForm->getMetaData('schedule_roofing_job_stories') == '3+' ? 'selected' : '')?>>3+</option>
                    </select>
                    </td>
<td width=125 class="listitem"><b>Existing Roof:</b></td>
                    <td class="listrow" width="300"><input type="text" name='existing_roof' value='<?=$scheduleForm->getMetaData('schedule_roofing_job_existing_roof')?>'></td>
                  </tr>


                   <tr>
                   	  <td class="listitem"><b>House Layers/Squares</b></td>
                      <td class="listrow">
                      <select name="house">
                      <option value="">N/A</option>
                      <option value="1" <?=($scheduleForm->getMetaData('schedule_roofing_job_house') == '1' ? 'selected' : '')?>>1</option>
                      <option value="2" <?=($scheduleForm->getMetaData('schedule_roofing_job_house') == '2' ? 'selected' : '')?>>2</option>
                      <option value="3" <?=($scheduleForm->getMetaData('schedule_roofing_job_house') == '3' ? 'selected' : '')?>>3</option>
                      <option value="4" <?=($scheduleForm->getMetaData('schedule_roofing_job_house') == '4' ? 'selected' : '')?>>4</option>
                      <option value="5" <?=($scheduleForm->getMetaData('schedule_roofing_job_house') == '5' ? 'selected' : '')?>>5</option>
                      <option value="6" <?=($scheduleForm->getMetaData('schedule_roofing_job_house') == '6' ? 'selected' : '')?>>6</option>
                      </select>
	                      <input size='6' type="text" name='house_squares' value='<?=$scheduleForm->getMetaData('schedule_roofing_job_house_squares')?>'>

                      </td>
                      <td class="listitem"><b>Garage Layers/Squares</b></td>
                      <td class="listrow">
                      <select name="garage">
                      <option value="">N/A</option>
                      <option value="1" <?=($scheduleForm->getMetaData('schedule_roofing_job_garage') == '1' ? 'selected' : '')?>>1</option>
                      <option value="2" <?=($scheduleForm->getMetaData('schedule_roofing_job_garage') == '2' ? 'selected' : '')?>>2</option>
                      <option value="3" <?=($scheduleForm->getMetaData('schedule_roofing_job_garage') == '3' ? 'selected' : '')?>>3</option>
                      <option value="4" <?=($scheduleForm->getMetaData('schedule_roofing_job_garage') == '4' ? 'selected' : '')?>>4</option>
                      <option value="5" <?=($scheduleForm->getMetaData('schedule_roofing_job_garage') == '5' ? 'selected' : '')?>>5</option>
                      <option value="6" <?=($scheduleForm->getMetaData('schedule_roofing_job_garage') == '6' ? 'selected' : '')?>>6</option>
                      </select>
	                  <input size='6' type="text" name='garage_squares' value='<?=$scheduleForm->getMetaData('schedule_roofing_job_garage_squares')?>'>

                      </td>
                   </tr>

                   <tr>
                      <td class="listitem"><b>Shed Layers/Squares</b></td>
                      <td class="listrow">
                      <select name="shed">
                      <option value="">N/A</option>
                      <option value="1" <?=($scheduleForm->getMetaData('schedule_roofing_job_shed') == '1' ? 'selected' : '')?>>1</option>
                      <option value="2" <?=($scheduleForm->getMetaData('schedule_roofing_job_shed') == '2' ? 'selected' : '')?>>2</option>
                      <option value="3" <?=($scheduleForm->getMetaData('schedule_roofing_job_shed') == '3' ? 'selected' : '')?>>3</option>
                      <option value="4" <?=($scheduleForm->getMetaData('schedule_roofing_job_shed') == '4' ? 'selected' : '')?>>4</option>
                      <option value="5" <?=($scheduleForm->getMetaData('schedule_roofing_job_shed') == '5' ? 'selected' : '')?>>5</option>
                      <option value="6" <?=($scheduleForm->getMetaData('schedule_roofing_job_shed') == '6' ? 'selected' : '')?>>6</option>
                      </select>
	                      <input size='6' type="text" name='shed_squares' value='<?=$scheduleForm->getMetaData('schedule_roofing_job_shed_squares')?>'>

                      </td>
                      <td class="listitem"><b>Patio Layers/Squares</b></td>
                      <td class="listrow">
                      <select name="patio">
                      <option value="">N/A</option>
                      <option value="1" <?=($scheduleForm->getMetaData('schedule_roofing_job_patio') == '1' ? 'selected' : '')?>>1</option>
                      <option value="2" <?=($scheduleForm->getMetaData('schedule_roofing_job_patio') == '2' ? 'selected' : '')?>>2</option>
                      <option value="3" <?=($scheduleForm->getMetaData('schedule_roofing_job_patio') == '3' ? 'selected' : '')?>>3</option>
                      <option value="4" <?=($scheduleForm->getMetaData('schedule_roofing_job_patio') == '4' ? 'selected' : '')?>>4</option>
                      <option value="5" <?=($scheduleForm->getMetaData('schedule_roofing_job_patio') == '5' ? 'selected' : '')?>>5</option>
                      <option value="6" <?=($scheduleForm->getMetaData('schedule_roofing_job_patio') == '6' ? 'selected' : '')?>>6</option>
                      </select>
	                      <input size='6' type="text" name='patio_squares' value='<?=$scheduleForm->getMetaData('schedule_roofing_job_patio_squares')?>'>
                      </td>
                   </tr>

                  <tr>
                    <td class="listitem"><b>New Roof:</b></td>
                    <td class="listrow"><input type="text" name='new_roof' value='<?=$scheduleForm->getMetaData('schedule_roofing_job_new_roof')?>'></td>
                    <td class="listitem"><b>Roof Color:</b></td>
                    <td class="listrow"><input type="text" name='job_new_roof_color' value='<?=$scheduleForm->getMetaData('schedule_roofing_job_job_new_roof_color')?>'></td>
                  </tr>
                  <tr>
                    <td class="listitem"><b>Squares:</b></td>
                    <td class="listrow"><input type="text" name='squares' value='<?=$scheduleForm->getMetaData('schedule_roofing_job_squares')?>'></td>
                    <td class="listitem"><b>Pitch:</b></td>
                    <td class="listrow"><input type="text" name='pitch' value='<?=$scheduleForm->getMetaData('schedule_roofing_job_pitch')?>'></td>
                  </tr>
                  <tr>
                    <td class="listitem"><b>Roofing Tear Off:</b></td>
                    <td class="listrow">
                    <select name="roofings_tear_off">
                      <option value="no" <?=($scheduleForm->getMetaData('schedule_roofing_job_roofings_tear_off') == 'no' ? 'selected' : '')?>>No</option>
                      <option value="yes" <?=($scheduleForm->getMetaData('schedule_roofing_job_roofings_tear_off') == 'yes' ? 'selected' : '')?>>Yes</option>
                      </select>
                    </td>
                    <td class="listitem"><b>Roofing Color:</b></td>
                    <td class="listrow"><input type="text" name='roofings_color' value='<?=$scheduleForm->getMetaData('schedule_roofing_job_roofings_color')?>'></td>
                  </tr>
                  <tr>

                    <td class="listitem"><b>Drip Edge Installation</b></td>
                    <td class="listrow">
                     <select name="drip_edge">
                      <option value="N/A" <?=($scheduleForm->getMetaData('schedule_roofing_job_drip_edge') == 'N/A' ? 'selected' : '')?>>N/A</option>
                      <option value="Eave" <?=($scheduleForm->getMetaData('schedule_roofing_job_drip_edge') == 'Eave' ? 'selected' : '')?>>Eave </option>
                      <option value="Rake" <?=($scheduleForm->getMetaData('schedule_roofing_job_drip_edge') == 'Rake' ? 'selected' : '')?>>Eave and Rake</option>
                      </select>
					</td>
                    <td class="listitem"><b>Agreed upon price with Subcontractor:</b></td>
                    <td class="listrow"><input type="text" name='agreed_upon_price' value='<?=$scheduleForm->getMetaData('schedule_roofing_job_agreed_upon_price')?>'></td>
                 </tr>
                 <tr>
                 	<td class="listitem" valign="top"><b>Tear Off Notes</b></td>
                    <td class="listrow"><textarea name="tear_off_notes" cols="25" rows="6"><?=$scheduleForm->getMetaData('schedule_roofing_job_tear_off_notes')?></textarea></td>
                    <td class="listitem" valign="top"><b>Any Specific Details:</b></td>
                    <td class="listrow"><textarea name="job_details" cols="25" rows="6"><?=$scheduleForm->getMetaData('schedule_roofing_job_job_details')?></textarea></td>
                  </tr>
          </table>

          <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td align="right" class="listrow">
                  <input name="scheduleRoofingJob" type="submit" value="Save">
                </form>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
