<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');

$id = RequestUtil::get('id');
$myJob = new Job($id);

ModuleUtil::checkJobModuleAccess('modify_insurance', $myJob, TRUE);

if(RequestUtil::get('submit')) {
    $is_notify=RequestUtil::get('is_notify');
    $_POST['ins_approval'] = RequestUtil::get('ins_approval') ?: NULL;
    $_POST['date_of_loss'] = RequestUtil::get('date_of_loss') ?: NULL;
    $_POST['claim'] = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('claim')) ?: NULL;
    //echo "<pre>";print_r($_POST);die;
    FormUtil::update('jobs');
    $myJob->storeSnapshot();   
    JobModel::setMetaValue($myJob->job_id, 'insurance_policy', RequestUtil::get('policy'));
	
    JobModel::saveEvent($myJob->job_id, 'Insurance Information Modified');
    if($is_notify)
    {
	    NotifyUtil::notifySubscribersFromTemplate('modify_insurance', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id));
    }
	
?>
<script>
  Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id;?>', 'jobscontainer', true, true, true);
</script>
<?php
	die();
}
?>
<form method="post" name="insurance" action="?id=<?=$id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Modify Job Insurance</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitem"><b>Provider:</b></td>
        <td class="listrow">
            <select name="insurance_id" id="insurance_id">
                <option value=""></option>
<?php
$providers = InsuranceModel::getAllProviders();
foreach($providers as $provider) {
?>
                <option value="<?=$provider['insurance_id']?>" <?=$myJob->insurance_id == $provider['insurance_id'] ? 'selected' : ''?>><?=$provider['insurance']?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <?php
$providers_details = InsuranceModel::getProviderById($myJob->insurance_id);
//print_r($providers_details);
    ?>

    <tr>
        <td class="listitem"><b>Other Details:</b></td>
        <td class="listrow" id="insvalue">           
           <b>Phone:</b> <?=$providers_details['phone_no'] ; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Fax:</b> <?=$providers_details['fax_no'] ; ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Email:</b> <?=$providers_details['email'] ; ?> <br><br>
           <b>Comment:</b> <?=$providers_details['commment'] ; ?>
        </td>
    </tr>

    <tr>
        <td class="listitem"><b>Policy:</b></td>
        <td class="listrow">
            <input type="text" name="policy" value="<?=MetaUtil::get($myJob->meta_data, 'insurance_policy')?>">
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Claim:</b></td>
        <td class="listrow">
            <input type="text" name="claim" value="<?=$myJob->claim?>">
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Claim Approved:</b></td>
        <td class="listrow">
            <input type="checkbox" name="ins_approval" value="<?=DateUtil::formatMySQLTimestamp($myJob->insurance_approval ?: NULL)?>" <?=$myJob->insurance_approval ? 'checked' : ''?>>
<?php
if(!empty($myJob->insurance_approval)) {
?>
            <span class="smallnote">Approved on <?=DateUtil::formatDateTime($myJob->insurance_approval)?></span>
<?php
}
?>
        </td>
    </tr>
    
    
    <tr>
        <td class="listitem">
            <b>DOL:</b>
        </td>
        <td class="listrow">
            <input class="pikaday" type="text" name="date_of_loss" value="<?=(!empty($myJob->date_of_loss))?$myJob->date_of_loss:''?>" />
        </td>
    </tr>
    
    <tr>
        <td class="listitem"><b>Adjuster Name:</b></td>
        <td class="listrow">
            <input type="text" name="adjuster_name" value="<?=$myJob->adjuster_name?>">
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Adjuster Email:</b></td>
        <td class="listrow">
            <input type="text" name="adjuster_email" value="<?=$myJob->adjuster_email?>">
        </td>
    </tr>
    </style>
    <?php
    $phone_arr=explode(':',$myJob->adjuster_phone);
    $adjuster_phone = $phone_arr[0];
    $adjuster_ext = $phone_arr[1];
    
    ?>
    <tr>
        <td class="listitem"><b>Adjuster Phone Number:</b></td>
        <td class="listrow">
            <input type="text" name="adjuster_phone" class="masked-phone" value="<?=$adjuster_phone?>">
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Adjuster Phone Extension:</b></td>
        <td class="listrow">
            <input type="text" name="adjuster_ext" value="<?=$adjuster_ext?>">
        </td>
    </tr>
    <tr>
        <td style="font-weight:bold;"><input style="margin-left:30px;" type="checkbox" name="is_notify" value="1">Notify Email</td>
        <td colspan=2 align="right" class="listrow">
            <input type="submit" name="submit" value="Save">
        </td>
    </tr>
    </table>
 <?php   
 $sql="select * from insurance_notes where insurance_id='$myJob->insurance_id' order by insurance_note_id desc";
$notes = DBUtil::queryToArray($sql);
$i=1;
if(count($notes)>0)
{
    ?>
<span id="note_area">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Insurance Notes</td>
    </tr>
</table>

<table class="table-bordered table-condensed table-striped" width="100%">
    <thead>
        <tr>
            <th>#</th>
            <th>Note</th>
        </tr>

    </thead>

    <tbody>
    <?php           
    foreach($notes as $row) {
    ?>
        <tr>
            <td><?=$i?></td>
            <td ><?=$row['notes']?></td>
        </tr>
    <?php
      $i++;
    }?>
</tbody>

</table>
</span
<?php
}
else
{
?>
<span id="note_area">
    

</span>
<?php 
}
?>
</form>
</body>
</html>

<script>
   $('#insurance_id').change(function(){
        var insurance_id = $(this).val();
        //alert(insurance_id);
        $.ajax({
            type: "GET",
            url: "<?=AJAX_DIR?>/insurance_detail.php",
            data: "insurance_id="+insurance_id,
            success: function( data ) {
                  //alert(data);
                document.getElementById("insvalue").innerHTML = data;
            }
        });
        
        getNotes(insurance_id);
        
    });
    
    function getNotes(id)
    {
        $.ajax({
            type: "GET",
            url: "<?=AJAX_DIR?>/get_insurance_notes.php",
            data: "insurance_id="+id,
            success: function( data ) {
                  //alert(data);
                document.getElementById("note_area").innerHTML = data;
            }
        });
    }
    </script>