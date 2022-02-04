<?php

include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('invoice_readwrite')) {
    die('Insufficient Rights');
}
echo ViewUtil::loadView('doc-head');
$myJob = new Job(RequestUtil::get('id'));
$inv = RequestUtil::get('inv');
ModuleUtil::checkJobModuleAccess('invoice_readwrite', $myJob, TRUE, TRUE);
$myCustomer = new Customer($myJob->customer_id);


$header=array();
$invoice_header = UserModel::getSalesmanDetails($myJob->salesman_id);
if(count($invoice_header)>0)
{
    $header=$invoice_header[0];
    $address = (!empty($header['invoice_city']))?$header['invoice_city']:'';
    $address .= (!empty($header['invoice_state']))?', '.$header['invoice_state']:'';
    $address .= (!empty($header['invoice_zip']))?' '.$header['invoice_zip']:'';
}
$logo = (!empty($header['invoice_logo']))?'/invoice_logo/'.$header['invoice_logo']:'/logos/'.$_SESSION['ao_logo'];
$addressObj = RequestUtil::get('office_id') ? new Office(RequestUtil::get('office_id')) : new Account($_SESSION['ao_accountid']);

ob_start();
?>
<style>
@page { size: auto;  margin: 0mm; }
</style>
<table border="0" cellspacing="0" cellpadding="0" width="800" align="center">

    <tr valign="center">

        <td align="center" style="font-size: 35px;">Job Invoice</td>


    </tr>

</table>

<table border="0" cellspacing="0" cellpadding="0" width="800" align="center">
    <tr valign='bottom'>
        <td align="left">
            <img alt="Default Image" height=125 width=280 src="<?= ROOT_DIR . $logo;?>">
            
<?php
/*if($addressObj->get('fax')) {
?>
            <br />
            <b>Fax:</b> <?=UIUtil::formatPhone($addressObj->get('fax'))?>
<?php
}*/
?>
        </td>
        <td align="right"><h2 style="font-size: 16px;width:300px;"><br />
            <?=(!empty($header['company_name']))?$header['company_name']:''?><br>
            <?=(!empty($header['address']))?$header['address']:''?><br>
            <?=(!empty($header['suite_number']))?'Suite: '.$header['suite_number']:''?> <br />
            <?=$address?><br>
            PH: <?php echo UIUtil::formatPhone(UserModel::getProperty($myJob->salesman_id, 'phone')); ?></h2></td>
    </tr>
</table>
<br /><br />
<table border="0" cellspacing="0" cellpadding="0" width="800" align="center">
    <tr>
        <td style='border: 1px solid black;'>
            <table width="100%" style='font-size: 16px;'>
                <tr>
                    <td width=100>
                        <b>Customer:</b>
                    </td>
                    <td width=300>
                        <?=$myCustomer->getDisplayName()?>
                    </td>
                    <td width=120>
                        <b>Claim Number:</b>
                    </td>
                    <td width=300>
                        <?php echo $myJob->claim; ?>
                    </td>
                </tr>
                <tr>
                    <td width=100>
                        <b>Address:</b>
                    </td>
                    <td colspan=3>
                        <?=$myCustomer->getFullAddress()?>
                    </td>
                </tr>
                <tr>
                    <td width=100>
                        <b>Phone:</b>
                    </td>
                    <td width=300>
                        <?php echo UIUtil::formatPhone($myCustomer->get('phone')); ?>
                    </td>
                    <td width=120>
                        <b>Email:</b>
                    </td>
                    <td width=300>
                        <?php echo $myCustomer->get('email'); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
        <td style='border: 1px solid black;'>
            <table border="0" width="100%" style='font-size: 16px; font-weight: bold;'>
                <tr>
                    <td>Description</td>
                    <td width=100>Credit</td>
                    <td width=100>Charge</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr height=400 valign='top'>
        <td style='border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;'>
            <table border="0" width="100%" cellpadding=2 cellspacing="0">
<?php
//credits and charges
$totalCharges = $myJob->getInvoiceChargesTotal($inv);
$totalCredits = $myJob->getInvoiceCreditsTotal($inv);
$credits = $myJob->fetchCredits($inv);
$charges = $myJob->fetchCharges($inv);

foreach($charges as $charge) {
?>
                <tr>
                    <td><?=MapUtil::get($charge, 'note')?></td>
                    <td width="100">&nbsp;</td>
                    <td width="100"><?=MapUtil::get($charge, 'amount')?></td>
                </tr>
<?php
}
foreach($credits as $credit) {
?>
                <tr>
                    <td><?=MapUtil::get($credit, 'note')?></td>
                    <td width="100">(<?=MapUtil::get($credit, 'amount')?>)</td>
                    <td width="100">&nbsp;</td>
                </tr>
<?php
}
?>
            </table>
        </td>
    </tr>
    <tr valign="center">
        <td style='border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;'>
            <table border="0" width="100%" cellspacing="0" cellpadding=2>
                <tr>
                    <td>
                        <b>Totals:</b>
                    </td>
                    <td width=100><b>(<?=CurrencyUtil::formatUSD($totalCredits)?>)</b></td>
                    <td width=100><b><?=CurrencyUtil::formatUSD($totalCharges)?></b></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr valign="top">
        <td style='border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;'>
            <table border="0" width="100%" cellspacing="0" cellpadding=2>
                <tr>
                  <td style='font-size: 20px;'><b>Balance:</b></td>
                  <td width=100 style='font-size: 20px;'><?=$myJob->getInvoiceBalance($inv)?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<?php 
$invoice_note ='';
$sql = "SELECT invoice_note FROM  invoices WHERE invoice_id='{$inv}'";
$invoice = DBUtil::queryToArray($sql);
if($invoice)
{
    $invoice_note = $invoice[0]['invoice_note'];
}

?>
<br />

<table width="800" cellspacing="0" cellpadding="0" border="0" align="center">
    <tr>
        <td>
           <b> Note: </b><?php echo $invoice_note; ?>
        </td>
    </tr>
</table>
<br /><br />
<table border="0" width="800" align="center">
    <tr>
        <td>
            <center>Generated by <b><?php echo $myJob->salesman_fname." ".$myJob->salesman_lname; ?></b></center>
        </td>
    </tr>
</table>
<script>
    $(document).ready(function(){
        window.print();
    });
</script>
</body>
</html>
<?php
$str = ob_get_clean();

/*include("../pdf/dompdf_config.inc.php");

$dompdf = new DOMPDF();
$dompdf->load_html($str);

$dompdf->render();
$dompdf->stream("invoice.pdf");*/

echo $str;
?>
