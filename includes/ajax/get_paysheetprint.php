<?php
include '../common_lib.php'; 

echo ViewUtil::loadView('doc-head');

$myJob = new Job(RequestUtil::get('id'));
$myCustomer = new Customer($myJob->customer_id);

ModuleUtil::checkJobModuleAccess('profitability_readwrite', $myJob, TRUE);

$sql = "select commission from profit_sheets where profit_sheet_id='$myJob->profit_sheet_id' limit 1";
$res = DBUtil::query($sql);
list($commission_percentage)=mysqli_fetch_row($res);

$me = UserModel::getMe();
$addressObj = $me->get('office_id') ? new Office($me->get('office_id')) : new Account($_SESSION['ao_accountid']);

ob_start();
?>
    <table border= cellspacing="0" cellpadding="0" width='800' align="center">
      <tr valign='bottom'>
        <td align="center">
          <?=AccountModel::getLogoImageTag()?>
          <br>
          <?=$addressObj->getFullAddress()?>
          <br>
          Phone: <?=UIUtil::formatPhone($addressObj->get('phone'))?>
<?php
if($addressObj->get('fax')) {
?>
          <br>
          <b>Fax:</b> <?=UIUtil::formatPhone($addressObj->get('fax'))?>
<?php
}
?>
        </td>
        <td style='font-size: 35px; font-weight: bold;' width=800 align="right">Job Paysheet</td>
      </tr>
    </table>
    <br><br>
    <table width='800' align="center" border="0" cellspacing="0" cellpadding="0">
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
                <b>Job Number:</b>
              </td>
              <td width=300>
                <?php echo $myJob->job_number; ?>
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
          <table width="100%" style='font-size: 16px;'>
            <tr>
              <td width=100>
                <b>Salesman:</b>
              </td>
              <td width=300>
                <?php echo $myJob->salesman_fname." ".$myJob->salesman_lname; ?>
              </td>
              <td width=120>
                <b>Phone:</b>
              </td>
              <td width=300>
                <?php echo UIUtil::formatPhone(UserModel::getProperty($myJob->salesman_id, 'phone')); ?>
              </td>
            </tr>
            <tr>
              <td width=100>
                <b>Job Type:</b>
              </td>
              <td width=300>
                <?php echo $myJob->job_type; ?>
              </td>
              <td width=120>
                <b>Job DOB:</b>
              </td>
              <td width=300>
                <?php echo $myJob->dob; ?>
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
$sql = "select profit_charge_id, note, amount from profit_charges where profit_sheet_id='".$myJob->profit_sheet_id."' order by amount asc";
$res_charges = DBUtil::query($sql);
$num_charges = mysqli_num_rows($res_charges);

$sql = "select profit_credit_id, note, amount from profit_credits where profit_sheet_id='".$myJob->profit_sheet_id."' order by amount asc";
$res_credits = DBUtil::query($sql);
$num_credits = mysqli_num_rows($res_credits);

$materials_total = number_format($myJob->materials_total, 2, '.', '');

  print<<<HTML
    <tr class='odd' valign="center">
      <td colspan=2><b>Material Sheet</b></td>
      <td width=100>$materials_total</td>
    </tr>
HTML;

  $i=0;
  if($num_charges!=0)
  {
    $total_charges = 0;
    while(list($charge_id, $note, $amt)=mysqli_fetch_row($res_charges))
    {
      $class='odd';
      if($i%2==0)
        $class='even';
      $total_charges+=$amt;
?>
        <tr class='<?php echo $class; ?>'>
          <td><b><?php echo $note; ?></b></td>
          <td width=100>&nbsp;</td>
          <td width=100><?php echo $amt; ?></td>
        </tr>
<?php
      $i++;
    }
  }
  if($num_credits!=0)
  {
    $total_credits = 0;
    while(list($credit_id, $note, $amt)=mysqli_fetch_row($res_credits))
    {
      $class='odd';
      if($i%2==0)
        $class='even';
      $total_credits+=$amt;
?>
        <tr class='<?php echo $class; ?>'>
          <td><b><?php echo $note; ?></b></td>
          <td width=100>(<?php echo $amt; ?>)</td>
          <td width=100>&nbsp;</td>
        </tr>
<?php
      $i++;
    }
  }
  $total_charges = number_format($total_charges, 2, '.', '');
  $total_credits = number_format($total_credits, 2, '.', '');
  $gross = number_format(($total_credits-($total_charges+$materials_total)), 2, '.', '');
  $commission = number_format(($gross*($commission_percentage/100)), 2, '.', '');
  if($commission<0)
    $commission = '0.00';

  $net = number_format(($gross-$commission), 2, '.', '');
  if($net<0)
    $net = '0.00';
?>
          </table>
        </td>
      </tr>
      <tr class='odd' valign="center">
        <td style='border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;'>
          <table border="0" width="100%" cellspacing="0" cellpadding=2>
            <td align="right">
              <b>Totals:</b>
            </td>
            <td width=100><b>(<?php echo $total_credits; ?>)</b></td>
            <td width=100><b><?php echo $total_charges; ?></b></td>
          </table>
        </td>
      </tr>
      <tr valign='top'>
        <td style='border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;'>
          <table border="0" width="100%" cellspacing="0" cellpadding=2>
            <tr class='odd'>
              <td align="right" style='font-size: 16px;'><b>Gross Profit:</b></td>
              <td width=100 style='font-size: 16px;'><?php echo $gross; ?></td>
            </tr>
            <tr class='odd'>
              <td align="right" style='font-size: 20px;'><b><?php echo $commission_percentage; ?>% Commission TBP:</b></td>
              <td width=100 style='font-size: 20px;'><?php echo $commission; ?></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <br /><br />
    <table border="0" width='800' align="center">
      <tr>
        <td>
          <center>Generated by <b><?=APP_NAME?></b></center>
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
echo $str;
?>
