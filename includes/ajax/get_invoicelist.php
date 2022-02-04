<?php

include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('invoice_readwrite')) {
	die("Insufficient Rights");
}

$myJob = new Job(RequestUtil::get('id'));

if(moduleOwnership('invoice_readwrite') && ($myJob->salesman_id != $_SESSION['ao_userid'] && $myJob->user_id != $_SESSION['ao_userid'])) {
    die("Insufficient Rights");
}

$inv = RequestUtil::get('inv');
$action = RequestUtil::get('action');
$itemId = RequestUtil::get('itemid');
$amount = RequestUtil::get('a');
$note = RequestUtil::get('n');
$type = RequestUtil::get('t');
$invoice_name = RequestUtil::get('invoice_name');
if($invoice_name)
{
    $sql = "UPDATE  invoices SET invoice_name='{$invoice_name}' WHERE invoice_id='{$inv}'";
    DBUtil::query($sql);
}
$invoice_note = RequestUtil::get('invoice_note');
if($invoice_note)
{
    $sql = "UPDATE  invoices SET invoice_note='{$invoice_note}' WHERE invoice_id='{$inv}'";
    DBUtil::query($sql);
}

if($action == 'd') {
    
    $sql = "DELETE FROM invoices  WHERE invoice_id = '{$inv}'";
	DBUtil::query($sql);
	
	$sql = "DELETE FROM charges WHERE invoice_id = '{$inv}'";
	DBUtil::query($sql);
	
	$sql = "DELETE FROM credits WHERE invoice_id = '{$inv}'";
	DBUtil::query($sql);
	?>
	<script>
      Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?= $myJob->job_id ?>&tab=invoice', 'jobscontainer',true,true,true);
    </script>
    <?php
	
}
elseif($action == 'del') {
	if($type == 'credit') {
            $sql = "DELETE FROM credits
                    WHERE credit_id = '{$itemId}'
                    LIMIT 1";
	}
	else if($type == 'charge') {
		$sql = "DELETE FROM charges
                WHERE charge_id = '{$itemId}'
                LIMIT 1";
	}
	DBUtil::query($sql);
}
elseif($action == 'edit')
{
    
}

if(!empty($amount) && !empty($note) && !empty($type)) 
{  
    $item_id = RequestUtil::get('item_id');

    if(!empty($item_id))
    {
        $note = base64_decode($note) ;
        $old_type=RequestUtil::get('old_type');

        if($old_type!=$type)
        {
            if($type == 'credit') 
            {
                $sql = "INSERT INTO credits VALUES (0, '{$inv}', '$amount', '$note', now())";
                DBUtil::query($sql);
                $sql = "DELETE FROM  charges WHERE charge_id='{$item_id}' LIMIT 1";
                DBUtil::query($sql);
            }
            elseif($type == 'charge') 
            {
                $sql = "INSERT INTO charges VALUES (0, '{$inv}', '$amount', '$note', now())";
                DBUtil::query($sql);
                $sql = "DELETE FROM credits WHERE credit_id = '{$item_id}' LIMIT 1";
                DBUtil::query($sql);
            }
        }
        elseif($type == 'credit') 
        {
            $sql = "UPDATE  credits SET amount='{$amount}',note='{$note}' WHERE credit_id='{$item_id}'";
        }
        elseif($type == 'charge') 
        {            
            $sql = "UPDATE charges SET amount='{$amount}',note='{$note}' WHERE charge_id='{$item_id}'";
        }
        //echo $sql;die;
        if(DBUtil::query($sql) or die(mysqli_error)) {
            JobModel::saveEvent($myJob->job_id, "Invoice $type Updated the amount to ($amount)");
        }
    }
    else
    {
        $note = base64_decode($note) ;
        $sql = "INSERT INTO charges VALUES (0, '{$inv}', '$amount', '$note', now())";
    	if($type == 'credit') {
    		$sql = "INSERT INTO credits VALUES (0, '{$inv}', '$amount', '$note', now())";
    	}
        if(DBUtil::query($sql) or die(mysqli_error)) {
            JobModel::saveEvent($myJob->job_id, "Invoice $type added ($amount)");
        }
    }
}

//credits and charges
$totalCharges = $myJob->getInvoiceChargesTotal($inv);
$totalCredits = $myJob->getInvoiceCreditsTotal($inv);
$credits = $myJob->fetchCredits($inv);
$charges = $myJob->fetchCharges($inv);

foreach($charges as $charge) {
?>
<tr valign="center">
	<td><?=MapUtil::get($charge, 'note')?></td>
    <td>&nbsp;</td>
	<td class="red"><?=MapUtil::get($charge, 'amount')?></td>
    <td class="text-center">
        <div class="btn btn-small btn-success"
             onclick="makeEdit('<?=MapUtil::get($charge, 'charge_id')?>','charge','<?=MapUtil::get($charge, 'amount')?>','<?=MapUtil::get($charge, 'note')?>');"
             title="Edit" tooltip>
            <i class="icon-pencil"></i>
        </div>
        <div class="btn btn-small btn-danger"
             onclick="Request.make('<?=AJAX_DIR?>/get_invoicelist.php?id=<?=$myJob->job_id?>&inv=<?php echo $inv; ?>&action=del&t=charge&itemid=<?=MapUtil::get($charge, 'charge_id')?>', 'invoicecontainer', false, true);"
             title="Delete" tooltip>
            <i class="icon-trash"></i>
        </div>
    </td>
</tr>
<?php
}
foreach($credits as $credit) {

?>
<tr valign="center">
	<td><?=MapUtil::get($credit, 'note')?></td>
	<td style="color: green;">(<?=MapUtil::get($credit, 'amount')?>)</td>
	<td>&nbsp;</td>
	<td class="text-center">
	    <div class="btn btn-small btn-success"
            onclick="makeEdit('<?=MapUtil::get($credit, 'credit_id')?>','credit','<?=MapUtil::get($credit, 'amount')?>','<?=MapUtil::get($credit, 'note')?>');"
            title="Edit" tooltip>
            <i class="icon-pencil"></i>
        </div>
        <div class="btn btn-small btn-danger"
            onclick="Request.make('<?=AJAX_DIR?>/get_invoicelist.php?id=<?=$myJob->job_id?>&inv=<?php echo $inv; ?>&action=del&t=credit&itemid=<?=MapUtil::get($credit, 'credit_id')?>', 'invoicecontainer', '', true);"
            title="Delete" tooltip>
            <i class="icon-trash"></i>
        </div>
    </td>
</tr>
<?php
}
?>
<tr valign="center">
	<td><b>Totals:</b></td>
	<td style="font-weight:bold; color: green;">(<?=CurrencyUtil::formatUSD($totalCredits)?>)</td>
    <td style="font-weight:bold; color: red;" colspan=2><?=CurrencyUtil::formatUSD($totalCharges)?></td>
</tr>
<tr valign="center">
    <td colspan=2><b>Balance:</b></td>
    <td colspan=2><b><?=$myJob->getInvoiceBalance($inv)?></b></td>
</tr>