<?php

include '../common_lib.php';

echo ViewUtil::loadView('doc-head');



$jobId = RequestUtil::get('job_id');

$sheetId = RequestUtil::get('sheet_id');

$myJob = new Job($jobId, FALSE);

if(!$myJob->exists()) {

    UIUtil::showModalError('Job not found!');

}

ModuleUtil::checkJobModuleAccess('job_material_sheet', $myJob, TRUE);



$mySheet = new Sheet($sheetId, FALSE);

if(!$mySheet->exists()) {

    UIUtil::showModalError('Material sheet not found!');

}







if(isset($_POST['submitted'])) 

{



	if(empty($_POST['label'])) {

		UIUtil::showAlert('Label cannot be empty');

	}

	else {

		$label = RequestUtil::get('label');

		$notes = RequestUtil::get('notes');

		$supplier = RequestUtil::get('supplier');

		if(empty($supplier)) {

			//$supplier = 'null';
      $supplier = 0;

		}



		if(RequestUtil::get('delivery') != 'no') {

			$deliveryDate = "'" . mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['deliverydate']) . "'";

		}

		else {

			$deliveryDate = 'NULL';

		}



		if($_POST['confirmed'] != 'yes') {

			$confirmation = 'NULL';

		}

		else {

			$confirmation = 'curdate()';

		}



		$sql = "UPDATE sheets

				SET label = '$label', confirmed = $confirmation, delivery_date = $deliveryDate, notes = '$notes', supplier_id = '$supplier'

				WHERE sheet_id='{$mySheet->sheet_id}'

				LIMIT 1";

		DBUtil::query($sql);



		$mySheet = new Sheet($sheetId, FALSE);



?>

<script>

	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$myJob->job_id?>', 'jobscontainer', true, true);

</script>

<?php

	}

}



 $sql = "select filename from uploads where job_id='$jobId' and title='$label' order by upload_id desc limit 1";



  $upldarray =   DBUtil::queryToArray($sql);

    

  $filename='';

  if($upldarray[0]['filename'] != ''){

    $filename=ROOT_DIR.'/uploads/'.$upldarray[0]['filename'];

  }

  else

  {

   

    $modifymaterialForm = new ModifyMaterialForm();

 

    $modifymaterialForm->setType('material');

   

    $viewData = array(

      'meta_data' => $modifymaterialForm->getMetaData(),

      'job_number' => $myJob->job_number,

      'logo' => LOGOS_PATH . '/' . $_SESSION['ao_logo']

    );        

 

    $html = ViewUtil::loadView('pdf/modify-material-sheet', $viewData);

    //echo "<pre>";print_r($html);exit;

    $filename = PdfUtil::generatePDFFromHtml($html, 'Modify Material Sheet', true, UPLOADS_PATH);

    

    $filename=ROOT_DIR.'/uploads/'.$filename;

  }



  

?>



	<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">

      <tr>

        <td>

          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">

            <tr valign="center">

              <td>

                Modify Material Sheet

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

          <table border="0" width="100%" cellspacing="0" cellpadding="0">

			<tr>

				<form method="post" action='?sheet_id=<?=$sheetId?>&job_id=<?=$myJob->job_id?>'>

              <td width=125 class="listitemnoborder"><b>Label:</b></td>

              <td class="listrownoborder"><input type="text" name="label" value="<?=$mySheet->label?>" /></td>

            </tr>

            <tr>

              <td width=125 class="listitem"><b>Job Size:</b></td>

              <td class="listrow"><?=$mySheet->size?> sq</td>

            </tr>

            <tr valign="top">

            <input type="hidden" name='submitted'>

              <td class="listitem"><b>Delivery Date:</b></td>

<?php

  if(empty($mySheet->delivery_date)) {

?>

              <td class="listrow"><font color="red">Not Scheduled</font></td>

            </tr>

            <tr>

              <td class="listitemnoborder">&nbsp;</td>

              <td class="listrownoborder">

                    <?php $defaultDate = DateUtil::formatMySQLDate();?>

                    <input class="pikaday" data-default="<?=$defaultDate?>" type="text" name="deliverydate" value="<?=$defaultDate?>" />

              </td>

            </tr>

            <tr>

              <td class="listitem"><b>Do Not Schedule:</b></td>

              <td class="listrow"><input type='checkbox' name='delivery' value='no' checked/></td>

            </tr>

<?php

  } else {

?>

              <td class="listrow"><?=$mySheet->delivery_date?></td>

            </tr>

            <tr>

              <td class="listitemnoborder">&nbsp;</td>

              <td class="listrownoborder">

                    <?php $defaultDate = $mySheet->delivery_date ?: DateUtil::formatMySQLDate();?>

                    <input class="pikaday" data-default="<?=$defaultDate?>" type="text" name="deliverydate" value="<?=$defaultDate?>" />

              </td>

            </tr>

            <tr>

              <td class="listitem"><b>Do Not Schedule:</b></td>

              <td class="listrow">

                <input type='checkbox' id='delivery' name='delivery' value='no' />

              </td>

            </tr>

<?php

  }

    $checked = '';

    if($mySheet->confirmed!='')

      $checked = 'checked';

?>

            <tr valign='top'>

              <td class="listitem"><b>Confirmed:</b></td>

              <td class="listrow">

                <input type='checkbox' id='confirmed' name='confirmed' value='yes' <?=$checked?>/>

                <?=$mySheet->confirmed?>

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

                  <option value='<?=$supplier['supplier_id']?>' <?=$mySheet->supplier_id == $supplier['supplier_id'] ? 'selected' : ''?>><?=$supplier['supplier']?></option>

<?php

}

?>

                </select>

              </td>

            </tr>

<?php

  if(!empty($mySheet->supplier_id))

  {

    $sql = "select contact, email, phone, fax from suppliers where supplier_id='".$mySheet->supplier_id."' limit 1";

    $res = DBUtil::query($sql);

    list($contact, $email, $phone, $fax)=mysqli_fetch_row($res);

?>

            <tr>

              <td class="listitemnoborder" style="font-size:10px;"><b>Contact:</b></td>

              <td class="listrownoborder" style="font-size:10px;"><?=$contact?></td>

            </tr>

            <tr>

              <td class="listitemnoborder" style="font-size:10px;"><b>Email:</b></td>

              <td class="listrownoborder" style="font-size:10px;"><?=$email?></td>

            </tr>

            <tr>

              <td class="listitemnoborder" style="font-size:10px;"><b>Phone:</b></td>

              <td class="listrownoborder" style="font-size:10px;"><?=UIUtil::formatPhone($phone)?></td>

            </tr>

            <tr>

              <td class="listitemnoborder" style="font-size:10px;"><b>Fax:</b></td>

              <td class="listrownoborder" style="font-size:10px;"><?=UIUtil::formatPhone($fax)?></td>

            </tr>



<?php

  }

?>

            <tr valign="top">

              <td class="listitem"><b>Notes:</b></td>

              <td class="listrow">

                  <textarea name="notes" rows="4"><?=UIUtil::cleanOutput($mySheet->notes, TRUE)?></textarea>

              </td>

            </tr>

            <tr>

              <td colspan=2>

                <table border="0" width="100%" cellpadding="0" cellspacing="0">

                  <tr>

                    <td width=125 class="listitem">

                      <table border="0" cellspacing="0" cellpadding="0" width="100%">

                        <tr>

                          <td align="right"><img src='<?=IMAGES_DIR?>/icons/add_16.png'></td>

                          <td width=30 align="right"><b>Add:</b></td>

                        </tr>

                      </table>

                    </td>

                    <td class="listrow">

                      <select id='cat' onchange='getMaterialDropDown(this.value, <?=$mySheet->sheet_id?>, <?=$myJob->job_id?>);'>

                        <option value=''>Category</option>

                        <option value=''>----</option>

<?php

$sql = "select category_id, category from categories where account_id='".$_SESSION['ao_accountid']."' order by category asc";

$res = DBUtil::query($sql);



while(list($id, $cat)=mysqli_fetch_row($res))

{

?>

                        <option value='<?=$id?>'><?=$cat?></option>

<?php

}

?>

                      </select>

                    </td>

                  </tr>



                  <tr id='materialblock' style='display:none;'>

                    <td class="listitemnoborder">&nbsp;</td>

                    <td class="listrownoborder" id='materials'>&nbsp;</td>

                  </tr>



                  <tr id='colorblock' style='display:none;'>

                    <td class="listitemnoborder">&nbsp;</td>

                    <td class="listrownoborder" id='colors'>&nbsp;</td>

                  </tr>



                </table>

              </td>

            </tr>

            <tr>

              <td colspan=2 style='padding:4px;' class="listrow">

                <table border="0" class='smcontainertitle' width="100%" cellpadding="0" cellspacing="0">

                <tr>

                    <td>Material</td>

                    <td width="96">Brand</td>

                    <td width="148">Color</td>

                    <td width="42" align="center">Unit</td>

                    <td width="52" align="right">Price</td>

                    <td width="52" align="right">Qty</td>

                    <td width="50" align="right">Total</td>

                    <td width="62">&nbsp;</td>

                  </tr>

                </table>

                <table border="0" class="infocontainernopadding" id='materiallistcontainer' width="100%" cellpadding=2 cellspacing="0">

                  <tr>

                    <td colspan=2>

                      <script>

                        Request.make('<?=AJAX_DIR?>/get_sheet.php?sheet_id=<?=$sheetId?>&job_id=<?=$myJob->job_id?>', 'materiallistcontainer', '', true);

                      </script>

                    </td>

                  </tr>

                </table>

              </td>

            </tr>

            <tr>

              <td align="right" colspan=2 style='padding:2px;'>

<?php

//if(ModuleUtil::checkAccess('delete_material_sheet')||(moduleOwnership('delete_material_sheet') && ($myJob->salesman_id==$_SESSION['ao_userid'] || $myJob->user_id==$_SESSION['ao_userid'])))

if(1==2)

{

?>

                <input type='button' value='Delete' onclick='javascript:window.location="?id=<?=$mySheet->job_id?>&action=del";'>

<?php

}

//if(ModuleUtil::checkAccess('send_material_sheet')||(moduleOwnership('send_material_sheet') && ($myJob->salesman_id==$_SESSION['ao_userid'] || $myJob->user_id==$_SESSION['ao_userid'])))

if(1==2)

{

?>

                <input type='button' value='Send' onclick=''>

<?php

}

?>

				<input type='button' value='All Material Sheets' onclick="window.location.href = '<?=AJAX_DIR?>/get_all_material_sheets.php?id=<?=$myJob->job_id?>';">

				<input type='button' value='Print' onclick='window.open("get_materialsprint.php?sheet_id=<?=$mySheet->sheet_id?>&id=<?=$mySheet->job_id?>");'>

          <input type='button' value='Create Pdf' onclick='window.open("<?=$filename?>");'>

          <input type="submit" value="Save">

                </form>

              </td>

            </tr>

          </table>

        </td>

      </tr>

    </table>

  </body>

</html>