<?php
//CONFIG
//include_once 'config/global.php';
//include '../../../common_lib.php';
//if(!ModuleUtil::checkAccess('job_material_sheet'))
  //die("Insufficient Rights");
//echo '123123';
//echo ViewUtil::loadView('doc-head');
//print_r($_REQUEST);
$myJob = new Job($_REQUEST['job_id']);
//if(moduleOwnership('job_material_sheet') && (!JobUtil::isSubscriber($myJob->job_id) && $myJob->salesman_id!=$_SESSION['ao_userid'] && $myJob->user_id!=$_SESSION['ao_userid']))
 // die("Insufficient Rights");
$myCustomer = new Customer($myJob->customer_id);


$me = UserModel::getMe();
$addressObj = $me->get('office_id') ? new Office($me->get('office_id')) : new Account($_SESSION['ao_accountid']);


?>

<style>
  div.logo > img {height: 85px; width:160px;}
   
  
  .odd {background-color: #F5F5F5;}
  .even {background-color: white;}
  .lblmsg{ font-size: 20px;}


</style>

<!-- <div class="logo"><img class="logo" src="<?=@$logo?>" /></div> -->

<table border="0" cellspacing="0" cellpadding="0" width='800' align="center">
<?php
$mySheet = new Sheet($_REQUEST['sheet_id']);
?>
      <tr valign='bottom'>
        <td align="left" colspan="2">
          <div class="logo"><img class="logo" src="<?=@$logo?>" /></div>
        </td>
        
      </tr>
      <tr valign='bottom'>
 <td align="left">
          <?=$addressObj->getFullAddress()?>
          <br>
          Phone: <?=UIUtil::formatPhone($addressObj->get('phone'))?>
          <?php if($addressObj->get('fax')) { ?>
            <br>
            <b>Fax:</b> <?=UIUtil::formatPhone($addressObj->get('fax'))?>
          <?php } ?>
        </td>
            <td valign="bottom" class="test" align="right"><div class="lblmsg"><b> Material Order Summary</b></div></td>
      </tr>
    </table>
    <br><br>

<!--<h1>Material Order Summary</h1>-->

<table width='800' cellspacing="0" cellpadding="0"> 
      <tr>
        <td style='border: 1px solid black;' border="1">
<table width="100%" style='font-size: 16px;' border="0" >
            <tr>
              <td width=100>
                <b>Customer:</b>
              </td>
              <td >
                <?=$myCustomer->getDisplayName()?>
              </td>
              <td>&nbsp;</td>
              <td >
                <b>Job Number:</b>
              </td>

              <td >
                <?php echo $myJob->job_number; ?>
              </td>
            </tr>
            <tr>
              <td >
                <b>Address:</b>
              </td>
              <td colspan="2">
                <?=$myCustomer->getFullAddress()?>
              </td>
            </tr>
            <tr>
              <td width=100>
                <b>Phone:</b>
              </td>
              <td width=300>
                <?php echo UIUtil::formatPhone($myCustomer->get('phone')); ?>
              </td><td>&nbsp;</td>
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
        <td style='border: 1px solid black;'  border="1">
          <table width="100%"  style='font-size: 16px;' border="0">
            <tr>
              <td width=100>
                <b>Salesman:</b>
              </td>
              <td width=300>
                <?php echo $myJob->salesman_fname." ".$myJob->salesman_lname; ?>
              </td>
              <td>&nbsp;</td>
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
              <td>&nbsp;</td>
              <td width=120>
                <b>Job DOB:</b>
              </td>
              <td width=300>
                <?php echo $myJob->dob; ?>
              </td>
            </tr>
            <tr>
              <td width=100>
                <b>Jurisdiction:</b>
              </td>
              <td width=300>
                <?php echo $myJob->jurisdiction; ?>
              </td>
              <td>&nbsp;</td>
              <td width=120>
                <b>Permit #:</b>
              </td>
              <td width=300>
                <?php echo $myJob->permit; ?>
              </td>
            </tr>
            <tr>
              <td width=100>
                <b>Order Notes:</b>
              </td>
              <td colspan=3>
                <?=UIUtil::cleanOutput($mySheet->notes, FALSE)?>
              </td>
              </tr>
          </table>
        </td>
      </tr>
      <?php
  if(!empty($mySheet->supplier_id))
  {
?>
      <tr><td>&nbsp;</td></tr>
      <tr>
        <td style='border: 1px solid black;' border="1">
          <table width="100%" style='font-size: 16px;' border="0">
            <tr>
              <td >
                <b>Supplier:</b>
              </td>
              <td colspan="2">
                <?php echo $mySheet->supplier_name; ?>
              </td> 
                            
              <td >
                <b>Phone:</b>
              </td>
              <td >
                <?php echo UIUtil::formatPhone($mySheet->supplier_phone); ?>
              </td>
            </tr>
            <tr>
              <td >
                <b>Contact:</b>
              </td>
              <td colspan="2">
                <?php echo $mySheet->supplier_contact; ?>
              </td>
              
              <td >
                <b>Fax:</b>
              </td>
              <td >
                <?php echo UIUtil::formatPhone($mySheet->supplier_fax); ?>
              </td>
            </tr>
            <tr>
              <td >
                <b>Email:</b>
              </td>
              <td colspan="2">
                <?php echo $mySheet->supplier_email; ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
<?php
  }
?>
<tr><td>&nbsp;</td></tr>
      <tr>
        <td>
          <table width="55%" border="0" style='font-size: 16px;'>
            <tr>
              <td width=45><b>Order Date:</b></td>
              <td width=90><?php echo DateUtil::formatDate()?></td>
              
              <td width=110><b>Delivery Date:</b></td>
              <td>
                <?php if($mySheet->delivery_date!='') { ?>
                <?php echo DateUtil::formatDate($mySheet->delivery_date)?>
              <?php } ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td style='border: 1px solid black;' border="1">
          <table width="100%" border="0" style='font-size: 16px; font-weight: bold;'>
            <tr>
              <td width=110>Unit</td>  
              <td width=60>Qty</td>  
              <td>Item</td>
              <td width=150>Manufacturer</td>
              <td width=200>Color</td>
              <td width=75 align="right">Cost</td>
              <td width=10>&nbsp;</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr height=200 valign='top'>
        <td border="1" style='border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;'>
          <table border="0" width="100%" cellpadding=2 cellspacing="0">
<?php
$sql = "select materials.material_id, materials.material, sheet_items.quantity, colors.color, materials.price, units.unit".
       " from materials, units, sheet_items".
       " left join colors on (sheet_items.color_id=colors.color_id)".
       " where units.unit_id=materials.unit_id and sheet_items.material_id=materials.material_id and sheet_items.sheet_id='".$mySheet->sheet_id."'";
//echo $sql;
$res = DBUtil::query($sql);

$i=1;
$total_qty=0;
$total_cost=0;
while(list($material_id, $material, $qty, $color, $price, $unit)=mysqli_fetch_row($res))
{
  $sql = "select brands.brand from brands, materials where brands.brand_id=materials.brand_id and materials.material_id='".$material_id."' limit 1";
  $res_brand = DBUtil::query($sql);
  list($brand)=mysqli_fetch_row($res_brand);

  $class='odd';
  if($i%2==0)
    $class='even';

  $material = stripslashes($material);

  $total_cost += ($price*$qty);
  $price = number_format(($price*$qty), 2, '.', '');
?>
            <tr class="<?php echo $class; ?>">
              <td width=60 align="center"><?php echo $unit; ?></td>
              <td width=60 ><?php echo $qty; ?></td>
              <td><b><?php echo $material; ?></b></td>
              <td width=200><?php echo $brand; ?></td>
              <td width=200><?php echo $color; ?></td>
              <td width=75 align="right">$<?php echo $price; ?></td>
              <td width=10>&nbsp;</td>
            </tr>
<?php
  $i++;
  $total_qty++;
}
  $total_cost = number_format($total_cost, 2, '.', '');
?>
          </table>
        </td>
      </tr>
      <tr valign='top'>
        <td border="1" style='border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;'>
          <table border="0" width="100%" cellspacing="0" cellpadding=2>
            <tr class="odd">
              <td width=60  style='font-size: 16px;'><b><?php //echo $total_qty; ?></b></td>
              <td style='font-size: 16px;'>Total Items</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td align="right" style='font-size: 16px;'><b>$<?php echo $total_cost; ?></b></td>
              <td width=10>&nbsp;</td>
            </tr>
          </table>
        </td>
      </tr>


      </table>
      <br /><br />
    <table border="0" width='800' align="center">
      <tr>
        <td style='font-size: 12px;'>
          <center>Generated by <b><?=APP_NAME?></b></center>
        </td>
      </tr>
    </table>
