<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');

$label = RequestUtil::get('label');
$supplier = RequestUtil::get('supplier', 'NULL');
$notes = RequestUtil::get('notes');
$size = RequestUtil::get('size');
$myJob = new Job(RequestUtil::get('id'));

//samir - try to get existing schedule form

$uploadId = RequestUtil::get('upload_id');

$jobId= RequestUtil::get('id');
//echo $uploadId; exit;

try {
    $materialForm = new MaterialForm();
    $materialForm->initByUploadId($uploadId);
} catch (Exception $e) {
    $newForm = TRUE;
    $materialForm = new MaterialForm();
    $materialForm->setType('material');
}

$myJob = new Job($materialForm->exists ? $materialForm->getJobId() : $jobId);
$myCustomer = new Customer($myJob->customer_id);
//echo $myJob1;

//samir code end.. 

ModuleUtil::checkJobModuleAccess('job_material_sheet', $myJob, TRUE);
$errors = array();

if(RequestUtil::get('submit')) {
	//samir code
	//echo $label; die;
    
    if(!$materialForm->exists) {
        //give a temporary upload id
       $materialForm->setJobId($myJob->job_id)->setUploadId(-1)->store();
    }  
	
	foreach($_POST as $key => $dataPoint) {
		JobModel::setMetaValue($materialForm->getMyMetaId(), "material_sheet_$key", $dataPoint);
	
	}
        $viewData = array(
		'meta_data' => $materialForm->getMetaData(),
		'job_number' => $myJob->job_number,
		'logo' => LOGOS_PATH . '/' . $_SESSION['ao_logo']
	);
  //jigar comment code 31-03-2016 start 
	  
  //  $html = ViewUtil::loadView('pdf/material-sheet', $viewData);
	//  $fileName = PdfUtil::generatePDFFromHtml($html, 'Material Sheet', true, UPLOADS_PATH);
 //   $title = RequestUtil::get('upload_title', $label);
    
    //jigar comment code 31-03-2016 end 
    
	
	//samir code end
	
	if(empty($label)) {
		$errors[] = 'Label cannot be blank';
	}
	if(empty($size)) {
		$errors[] = 'Size cannot be blank';
	}
	//code start..


	// code end
	
      //jigar comment code 31-03-2016 start
      
   if(!count($errors)) {
	 	$sql = "INSERT INTO sheets
               VALUES (NULL, '{$myJob->job_id}', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', {$supplier}, '$label', '$size', '$notes', NULL, NULL, now())";
	 	DBUtil::query($sql);
	 	$newSheetId = DBUtil::getInsertId();
	 	//$materialForm->setUploadId(DBUtil::getInsertId())->store();		
  
       if($newForm) {        
  
       $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
               VALUES ('{$myJob->job_id}', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$fileName', '$title', now())";
        
      // $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp,sheet_id)
        //       VALUES ('{$myJob->job_id}', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$fileName', '$title', now(),'$newSheetId')";
  
     //  DBUtil::query($sql);
     //  $materialForm->setUploadId(DBUtil::getInsertId())->store();
   }
  //jigar comment code 31-03-2016 end 
		JobModel::saveEvent($myJob->job_id, "Added New Material Sheet");
		//NotifyUtil::notifySubscribersFromTemplate('add_job_sheet', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id));
?>
<script>
    Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$myJob->job_id?>', 'jobscontainer', false, true, false, function(){
        window.location = '<?=AJAX_DIR?>/job_materials.php?sheet_id=<?=$newSheetId?>&job_id=<?=$myJob->job_id?>';
    });
</script>
<?php
        die();
	}	
}
?>
<form action="?id=<?=$myJob->job_id?>" method="post" name="newsheet">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Create Material Sheet</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>

<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>Label:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrownoborder">
            <input type="text" name="label" value="<?=$label?>">
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Job Size:</b>&nbsp;<span class="red">*</span></td>
        <td class="listrow">
            <input type="text" name="size" value="<?=$size?>"> <?=$_SESSION['ao_jobunit']?>
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Supplier:</b></td>
        <td class="listrow">
            <select name="supplier">
                <option value="">None Chosen</option>
<?php
$suppliers = MaterialModel::getAllSuppliers();
foreach($suppliers as $supplier) {
?>
                    <option value="<?=MapUtil::get($supplier, 'supplier_id')?>"><?=MapUtil::get($supplier, 'supplier')?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr valign="top">
        <td class="listitem"><b>Notes:</b></td>
        <td class="listrow">
            <textarea name="notes" rows="7" style="width: 100%;"><?=$notes?></textarea>
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
