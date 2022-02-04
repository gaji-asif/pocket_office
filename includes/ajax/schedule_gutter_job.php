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
    $scheduleForm->setType('gutter');
}

$myJob = new Job($scheduleForm->exists ? $scheduleForm->getJobId() : $jobId);
$myCustomer = new Customer($myJob->customer_id);

if(RequestUtil::get('scheduleGutterJob')) {
    if(!$scheduleForm->exists) {
        //give a temporary upload id
        $scheduleForm->setJobId($myJob->job_id)->setUploadId(-1)->store();
    }
    
	foreach($_POST as $key => $dataPoint) {
		JobModel::setMetaValue($scheduleForm->getMyMetaId(), "schedule_gutter_job_$key", $dataPoint);
	}

    $viewData = array(
		'meta_data' => $scheduleForm->getMetaData(),
		'job_number' => $myJob->job_number,
		'logo' => LOGOS_PATH . '/' . $_SESSION['ao_logo']
	);
	$html = ViewUtil::loadView('pdf/schedule-gutter-job', $viewData);
	$fileName = PdfUtil::generatePDFFromHtml($html, 'Gutter Job Schedule Form', true, UPLOADS_PATH);
    $title = RequestUtil::get('upload_title', 'Schedule Gutter Job');

	if($newForm) {
        $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
                VALUES ('{$myJob->job_id}', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$fileName', '$title', now())";
        DBUtil::query($sql);
        $insertId = DBUtil::getInsertId();
        $scheduleForm->setUploadId($insertId)->store();
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

<?=ViewUtil::loadView('doc-head')?>

    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                Schedule Gutter Job
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





          <table width="100%" border='0' cellspacing='0' cellpadding='0'>
				<tr>
                    <td width=125 class="listitem"><b>Gutter lineal footage:</b></td>
                    <td class="listrow"><input type="text" name='gutter_l_f' value='<?=$scheduleForm->getMetaData('schedule_gutter_job_gutter_l_f')?>'></td>
                    <td class="listitem"><b>Downspout lineal footage:</b></td>
                    <td class="listrow"><input type="text" name='downspout_l_f' value='<?=$scheduleForm->getMetaData('schedule_gutter_job_downspout_l_f')?>'></td>
                  </tr>
                  <tr>
                    <td width=125 class="listitem"><b>Gutter Color:</b></td>
                    <td class="listrow"><input type="text" name='gutter_color' value='<?=$scheduleForm->getMetaData('schedule_gutter_job_gutter_color')?>'></td>
                    <td class="listitem"><b>Gutter Size:</b></td>
                    <td class="listrow">
                    <select name="gutter_size">
                    <option value="5" <?=($scheduleForm->getMetaData('schedule_gutter_job_gutter_size') == '5' ? 'selected' : '')?>>5</option>
                    <option value="6" <?=($scheduleForm->getMetaData('schedule_gutter_job_gutter_size') == '6' ? 'selected' : '')?>>6</option>
                    </select>
                    </td>
                  </tr>



                 <tr>
                   <td class="listitem"><b>Gutter Material:</b></td>
                    <td class="listrow">
                     <select name="gutter_material">
                      <option value="Aluminum" <?=($scheduleForm->getMetaData('schedule_gutter_job_gutter_material') == 'Aluminum' ? 'selected' : '')?>>Aluminum</option>
                      <option value="Plastic" <?=($scheduleForm->getMetaData('schedule_gutter_job_gutter_material') == 'Plastic' ? 'selected' : '')?>>Plastic</option>
                      <option value="Galvalume" <?=($scheduleForm->getMetaData('schedule_gutter_job_gutter_material') == 'Galvalume' ? 'selected' : '')?>>Galvalume</option>
                      </select>
                      </td>

                    <td width=125 class="listitem"><b>Downspout Size:</b></td>
                    <td class="listrow">
                    <select name="downspout_size">
                    <option value="2x3" <?=($scheduleForm->getMetaData('schedule_gutter_job_downspout_size') == '2x3' ? 'selected' : '')?>>2x3</option>
                    <option value="3x4" <?=($scheduleForm->getMetaData('schedule_gutter_job_downspout_size') == '3x4' ? 'selected' : '')?>>3x4</option>
                    </select>
                    </td>
                  </tr>
                 <tr>




                 <tr>
                  <td class="listitem"><b>Gutter cover type:</b></td>
                    <td class="listrow"><input type="text" name='cover_type' value='<?=$scheduleForm->getMetaData('schedule_gutter_job_cover_type')?>'></td>

                    <td width=125 class="listitem"><b>Gutter cover lineal footage:</b></td>

                    <td class="listrow"><input type="text" name='cover_lineal_footage' value='<?=$scheduleForm->getMetaData('schedule_gutter_job_cover_lineal_footage')?>'></td>

                  </tr>
                 <tr>

                 <tr>
                  <td class="listitem"><b>Pitch:</b></td>
                    <td class="listrow"><input type="text" name='pitch' value='<?=$scheduleForm->getMetaData('schedule_gutter_job_pitch')?>'></td>

                    <td width=125 class="listitem"><b>Electrical Outlet Location:</b></td>

                    <td class="listrow"><input type="text" name='electrical_outlet_location' value='<?=$scheduleForm->getMetaData('schedule_gutter_job_electrical_outlet_location')?>'></td>

                  </tr>
                 <tr>


                  <tr>
                    <td class="listitem"><b># of Stories:</b></td>
                    <td class="listrow">
                    <select name="stories">
                      <option value="1" <?=($scheduleForm->getMetaData('schedule_gutter_job_stories') == '1' ? 'selected' : '')?>>1</option>
                      <option value="2" <?=($scheduleForm->getMetaData('schedule_gutter_job_stories') == '2' ? 'selected' : '')?>>2</option>
                      <option value="3+" <?=($scheduleForm->getMetaData('schedule_gutter_job_stories') == '3+' ? 'selected' : '')?>>3+</option>
                    </select>
                    </td>
                    <td class="listitem"><b>Agreed upon price with Subcontractor:</b></td>
                    <td class="listrow"><input type="text" name='agreed_upon_price' value='<?=$scheduleForm->getMetaData('schedule_gutter_job_agreed_upon_price')?>'></td>
                  </tr>



                 	<td class="listitem" valign="top"><b>Tear Off Notes:</b></td>
                    <td class="listrow"><textarea name="tear_off_notes" cols="25" rows="6"><?=$scheduleForm->getMetaData('schedule_gutter_job_tear_off_notes')?></textarea></td>
                    <td class="listitem" valign="top"><b>Any Specific Details:</b></td>
                    <td class="listrow"><textarea name="job_details" cols="25" rows="6"><?=$scheduleForm->getMetaData('schedule_gutter_job_job_details')?></textarea></td>
                  </tr>
          </table>





          <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td align="right" class="listrow">
                  <input name="scheduleGutterJob" type="submit" value="Save">
                </form>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>