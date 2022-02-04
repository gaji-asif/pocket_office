<?php

include '../common_lib.php'; 

echo ViewUtil::loadView('doc-head');


$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('invoice_readwrite', $myJob, TRUE);
$invoice_id='';
if(isset($_POST['submit'])) 
{
    $maxinvoice = UserModel::gnerateInvoice($myJob->job_id);
    $no = ((count($maxinvoice)>0)?$maxinvoice[0]['invoice_id']:0)+1;
    $invoice_no="INV2".str_pad($no, 6, "0", STR_PAD_LEFT);
    
    $sql = "INSERT INTO invoices (invoice_no, invoice_name, job_id, user_id, timestamp) VALUES('{$invoice_no}','{$_POST['invoice_name']}','{$myJob->job_id}', '{$_SESSION['ao_userid']}', now())";
        DBUtil::query($sql);
    $invoice_id = DBUtil::getInsertId();
    JobModel::saveEvent($myJob->job_id, 'Invoice Created With Invoice no '.$invoice_no);
    $myJob = new Job(RequestUtil::get('id'));
}

$invoice_note ='';
$sql = "SELECT invoice_note FROM  invoices WHERE invoice_id='{$invoice_id}'";
$invoice = DBUtil::queryToArray($sql);
if($invoice)
{
    $invoice_note = $invoice[0]['invoice_note'];
}

?>

<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Job Invoice</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>

<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <form method="post" name="invoice_master" id="invoice_master" action="?id=<?php echo $myJob->job_id; ?>">
    <tr>
        <td width=125 class="listitemnoborder"><b>Invoice Name:</b></td>
        <td class="listrownoborder">
            
              <input type="text" size=60 name="invoice_name" value="<?php echo (!empty($_POST['invoice_name']))?$_POST['invoice_name']:'';?>">
              <?php if(!$invoice_id){?>
              <input type='submit' value='Generate Invoice' name="submit">
              <?php }?>
           
        </td>
    </tr>
    <tr <?php if($invoice_id) echo ''; else echo 'style="display:none"';?>>
        <td width=125 class="listitemnoborder"><b>Invoice No:</b></td>
        <td class="listrownoborder">
            
              <input type="text" size=60 value="<?php echo $invoice_no;?>">
            
        </td>
    </tr>
     </form>
</table>

<br><br><br>
<?php if($invoice_id){?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">

    <tr>
        <form name="invoice" id="invoice">
        <input type="hidden" id="item_id" value="">  
        <input type="hidden" id="old_type" value=""> 
        <input type="hidden" id="invoice_id" value="<?php echo $invoice_id;?>">
        <td width=125 class="listitemnoborder"><b>Description:</b></td>
        <td class="listrownoborder">
          <input type="text" size=60 id="note">

        </td>

    </tr>

    <tr>

        <td class="listitem"><b>Amount:</b></td>

        <td class="listrow">

            <input type="text" size=10 id='amt' onkeypress="return isNumberKey(event)">

        </td>

    </tr>

    <tr>

        <td class="listitem"><b>Type:</b></td>

        <td class="listrow">

            <select id='type'>

              <option value='charge'>Charge</option>

              <option value='credit'>Credit</option>

            </select>

            <input id="btn_save" type='button' value='Add' onClick="Request.make('<?=AJAX_DIR?>/get_invoicelist.php?id=<?php echo $myJob->job_id; ?>&inv='+document.getElementById('invoice_id').value+ '&item_id='+document.getElementById('item_id').value+'&a='+document.getElementById('amt').value+'&n='+window.btoa(document.getElementById('note').value)+'&t='+document.getElementById('type').value+'&old_type='+document.getElementById('old_type').value, 'invoicecontainer', false, true); document.invoice.reset(); Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', '', true, '');makeAdd();">
            </form>

        </td>

    </tr>

</table>


<div class="list-table-container">

<table border="0" class="table-bordered table-condensed table-striped" width="100%">

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
              <textarea id="invoice_note" name="invoice_note"><?php echo ($invoice_note)?$invoice_note:'';?></textarea>
              <input type='button' value='Save' onClick="Request.make('<?=AJAX_DIR?>/get_invoicelist.php?id=<?php echo $myJob->job_id; ?>&inv=<?php echo $invoice_id; ?>&invoice_note='+document.getElementById('invoice_note').value, 'invoicecontainer', false, true); Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>&tab=contacts', 'jobcontainer', true,true,'');saveInvoice();">
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

    <input type='button' value='Print' onclick='window.open("get_invoiceprint.php?id=<?php echo $myJob->job_id; ?>&inv=" + $("#invoice_id").val()+"&office_id=" + $("#office_picklist").val());'>

</div>
<?php }?>


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