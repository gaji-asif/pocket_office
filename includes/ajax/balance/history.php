<?php

include '../../common_lib.php';
if(!ModuleUtil::checkAccess('view_balance'))
  die("Insufficient Rights");

?>
<script>
	$('input#m').val('<?=date('n', $week_start_date)?>');
	$('input#y').val('<?=date('y', $week_start_date)?>');
	$('select#user').val('<?=$user?>');
	$('select#type').val('<?=$type?>');
</script>
<style type="text/css">
	.calendar-cell{
		width: 11% !important;
	}

</style>
<table border="0" class="schedulecontainer" width="100%">
    <tr height="28" >
    	<td style='font-weight: bold;'> Sl.</td>
        <td style='font-weight: bold; text-align: center;'>Customer</td>        
        <td style='font-weight: bold; text-align: center;'>Amount(USD)</td>
        <td style='font-weight: bold; text-align: center;'>Transaction Id</td>
        <td style='font-weight: bold; text-align: center;'>Date</td>
        <td style='font-weight: bold; text-align: center;'>Payment Mode</td>
    </tr>

    <?php 
    $id = RequestUtil::get('id');
    $where = '';
    if($_SESSION['ao_level']==3)
    {
      $where = " AND t3.user_id='{$_SESSION['ao_userid']}'";
    }
    elseif($id!=0)
    {
        $where = "AND t3.user_id='{$id}'";
    }

    $ac_id = $_SESSION['ao_accountid'];
    $sql = "SELECT CONCAT(t3.fname, ' ', t3.lname) as customer_name,t1.amount,t1.payment_date,IF(t1.paypal_trn_id IS NULL,t1.system_trn_id,t1.paypal_trn_id) as transaction_id,t1.payment_mode
            FROM tbl_customer_payment as t1
            LEFT JOIN users as t3 on t3.user_id=t1.customer_id            
            WHERE t1.account_id = '{$_SESSION['ao_accountid']}' AND t1.payment_status='A' $where
            ORDER BY t1.payment_id DESC";
    //echo $sql;die;
    $payments = DBUtil::queryToArray($sql);
    if(!empty($payments))
    {
        foreach($payments as $key=>$row)
        {  
        ?>
        <tr valign='top' style="background-color: white;">
            <td> <?php echo ++$key;?></td>
            <td> <?php echo $row['customer_name'];?></td>
            <td style="text-align: right;"> <?=$row['amount']?>  </td>
            <td style="text-align: center;"> <?php echo $row['transaction_id'];?></td>
            <td style="text-align: center;"> <?php echo date('d/m/Y',strtotime($row['payment_date']));?></td>
            <td style="text-align: left;"> <?php echo $row['payment_mode'];?></td>
        </tr>
    <?php }
    }
    else
    {?>
        <tr valign='top' style="background-color: white;">
            <td colspan="6" style="text-align: center;font-size: 20px;color: red;"> No History Payment Found</td>
        </tr>
    <?php
    }?>

    <tr><td colspan="6" class="infofooter"><a href="<?=ROOT_DIR?>/balance.php" class="basiclink"><i class="icon-double-angle-left"></i>&nbsp;Back</a></td></tr>
  </table>