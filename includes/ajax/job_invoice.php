<?php

include '../common_lib.php'; 

echo ViewUtil::loadView('doc-head');



$myJob = new Job(RequestUtil::get('id'));
$inv_id=RequestUtil::get('inv_id');

$sql = "SELECT * FROM  invoices WHERE invoice_id='{$inv_id}'";
$invoice = DBUtil::queryToArray($sql);
 //print_r($invoice);die;       
/*if(empty($myJob->invoice_id)) 
{
    $sql = "INSERT INTO invoices (job_id, user_id, timestamp)
            VALUES('{$myJob->job_id}', '{$_SESSION['ao_userid']}', now())";
    DBUtil::query($sql);
    $myJob->invoice_id = DBUtil::getInsertId();
    JobModel::saveEvent($myJob->job_id, 'Invoice Created');
    $myJob = new Job(RequestUtil::get('id'));
}*/

$header=array();
$invoice_header = UserModel::getSalesmanDetails($myJob->salesman_id);
$header='';
if(count($invoice_header)>0)
{
    $header=$invoice_header[0];
    $address = (!empty($header['invoice_city']))?$header['invoice_city']:'';
    $address .= (!empty($header['invoice_state']))?', '.$header['invoice_state']:'';
    $address .= (!empty($header['invoice_zip']))?' '.$header['invoice_zip']:'';
}
$logo = (!empty($header['invoice_logo']))?'/invoice_logo/'.$header['invoice_logo']:'/logos/'.$_SESSION['ao_logo'];

?>

<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">

    <tr valign="center">

        <td align="center" style="font-size: 35px;">Job Invoice</td>

        <td align="right">

            <i class="icon-remove grey btn-close-modal"></i>

        </td>

    </tr>

</table>

<table border="0" cellspacing="0" cellpadding="0" width="95%" align="center">
    <tr valign='bottom'>
        <td align="left">
            <img alt="Default Image" height=130 width=250 src="<?= ROOT_DIR . $logo;?>">
        </td>
        <td align="right"><h2 style="font-size: 16px;width:300px;">
            <?=(!empty($header['company_name']))?$header['company_name']:''?><br>
            <?=(!empty($header['address']))?$header['address']:''?><br>
            <?=(!empty($header['suite_number']))?'Suite: '.$header['suite_number']:''?> <br />
            <?=$address?><br>
            PH: <?php echo UIUtil::formatPhone(UserModel::getProperty($myJob->salesman_id, 'phone')); ?></h2>
        </td>
    </tr>
</table>


<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">

    <tr>
        <form name="invoice" id="invoice">
        <input type="hidden" id="item_id" value="">  
        <input type="hidden" id="old_type" value=""> 
        <input type="hidden" id="invoice_id" value="<?php echo $inv_id;?>">     
        <td width=125 class="listitemnoborder"><b>Description:</b></td>
        <td class="listrownoborder">
          <input type="text" size=60 id="note" value="">
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Amount:</b></td>
        <td class="listrow">
            <input type="text" size=10 id='amt' onkeypress="return isNumberKey(event)" value="">
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Type:</b></td>
        <td class="listrow">
            <select id='type'>
              <option value='charge'>Charge</option>
              <option value='credit'>Credit</option>
            </select>
            <input id="btn_save" type='button' value='Add' onClick="Request.make('<?=AJAX_DIR?>/get_invoicelist.php?id=<?php echo $myJob->job_id; ?>&inv=<?php echo $inv_id; ?>&item_id='+document.getElementById('item_id').value+'&a='+document.getElementById('amt').value+'&n='+window.btoa(document.getElementById('note').value)+'&t='+document.getElementById('type').value+'&old_type='+document.getElementById('old_type').value, 'invoicecontainer', false, true); document.invoice.reset(); Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobcontainer', '', true, '');makeAdd();">
            </form>
        </td>

    </tr>

</table>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <form method="post" name="invoice_master" id="invoice_master">
    <tr>
        <td width=125 class="listitemnoborder"><b>Invoice Name:</b></td>
        <td class="listrownoborder">
              <input type="text" size=60 id="invoice_name" name="invoice_name" value="<?php echo ($invoice[0]['invoice_name'])?$invoice[0]['invoice_name']:'';?>">
              <input type='button' value='Save' onClick="Request.make('<?=AJAX_DIR?>/get_invoicelist.php?id=<?php echo $myJob->job_id; ?>&inv=<?php echo $inv_id; ?>&invoice_name='+document.getElementById('invoice_name').value, 'invoicecontainer', false, true); Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>&tab=contacts', 'jobcontainer', true,true,'');saveInvoice();">
        </td>
    </tr>
    <tr>
        <td width=125 class="listitemnoborder"><b>Invoice No:</b></td>
        <td class="listrownoborder">
              <input disabled type="text" size=60 value="<?php echo ($invoice[0]['invoice_no'])?$invoice[0]['invoice_no']:'';?>">
              
        </td>
    </tr>

     </form>
</table>

<div class="list-table-container">

<table border="0" class="table-bordered table-condensed table-striped"  width="100%">

    <thead>

        <tr>

            <th>Description</th>

            <th width="25%">Credit</th>

            <th width="25%">Charge</th>

            <th width="10%" class="text-center">Actions</th>

        </tr>

    </thead>

    <tbody id="invoicecontainer"></tbody>

</table>

<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <form method="post" name="invoice_master" id="invoice_master">
    <tr>
        <td width=125 class="listitemnoborder"><b>Invoice Note:</b></td>
        <td class="listrownoborder">
              <textarea id="invoice_note" name="invoice_note"><?php echo ($invoice[0]['invoice_note'])?$invoice[0]['invoice_note']:'';?></textarea>
              <input type='button' value='Save' onClick="Request.make('<?=AJAX_DIR?>/get_invoicelist.php?id=<?php echo $myJob->job_id; ?>&inv=<?php echo $inv_id; ?>&invoice_note='+document.getElementById('invoice_note').value, 'invoicecontainer', false, true); Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>&tab=contacts', 'jobcontainer', true,true,'');saveInvoice();">
        </td>
    </tr>
     </form>
</table>

</div>

<div class="list-table-container text-right">

    <b>Office:</b>

    <select name='office' id='office_picklist'>

        <option value=''>Default</option>

<?php

$sql = "select office_id, title from offices where account_id='".$_SESSION['ao_accountid']."' order by title asc";

$res = DBUtil::query($sql);



while(list($office_id, $title)=mysqli_fetch_row($res))

{

  $selected = '';

  if($myUser->office_id == $office_id)

    $selected = "selected"

?>

        <option value='<?php echo $office_id; ?>' <?php echo $selected; ?>><?php echo $title; ?></option>

<?php

}

?>

    </select>

    <input type='button' value='Print' onclick='window.open("get_invoiceprint.php?id=<?php echo $myJob->job_id; ?>&inv=<?php echo $inv_id; ?>&office_id=" + $("#office_picklist").val());'>

</div>

<script>

    $(document).ready(function() {

        Request.make('<?=AJAX_DIR?>/get_invoicelist.php?id=<?php echo $myJob->job_id; ?>&inv=<?php echo $inv_id; ?>', 'invoicecontainer', '', true);

    });

</script>

</body>

</html>


<script type="text/javascript">

function isNumberKey(evt){
    var charCode = (evt.which) ? evt.which : event.keyCode
    if(charCode == 46)
        return true;

    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;
    return true;
}

function makeEdit(id,type,amnt,note)
{
    
    $("#item_id").val(id);
    $("#old_type").val(type);
    $("#type").val(type);
    $("#amt").val(amnt);
    $("#note").val(note);
    $("#btn_save").val('Save');
    
}

function makeAdd()
{
    $("#item_id").val('');
    $("#old_type").val('');
    $("#type").val('charge');
    $("#amt").val('');
    $("#note").val('');
    $("#btn_save").val('Add');
}


function saveInvoice()
{
    alert('Data Saved successfully!');
}


</script>