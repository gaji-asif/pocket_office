<?php

include '../../common_lib.php';
if(!ModuleUtil::checkAccess('view_billing'))
  die("Insufficient Rights");

$curDate = DateUtil::formatMySQLDate();
 
//get week start date
RequestUtil::get('ws');
if(date('w') == 1 && 
        (!RequestUtil::get('ws')) || 
        (RequestUtil::get('ws') == strtotime('12:00:00'))) {
    $week_start_date = strtotime('12:00:00');
} else {
    $week_start_date = RequestUtil::get('ws') ?: strtotime('previous monday');
}

//get previous week start date and next week start date
$previous_week_start_date = $week_start_date - 604800;
$next_week_start_date = $week_start_date + 604800;

//get filters
$user = RequestUtil::get('user');
$type = RequestUtil::get('type');

//set nav vars
$next = $_GET;
$prev = $_GET;
$prev['ws'] = $previous_week_start_date;
$next['ws'] = $next_week_start_date;

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
    	<td style='font-weight: bold; width: 5%; text-align: center;'> Sl.</td>
        <td style='font-weight: bold; width: 20%; text-align: center;'>Customer</td>
        <td style='font-weight: bold; width: 20%; text-align: center;'>Total Time</td>
        <td style='font-weight: bold; width: 15%; text-align: center;'>Total Bill</td>
        <td style='font-weight: bold; width: 15%; text-align: center;'>Total Paid</td>
        <td style='font-weight: bold; width: 15%; text-align: center;'>Due Amount</td>
        <td align="center" style='font-weight: bold; width: 10%; text-align: center;'>Action</td>
    </tr>
    <?php 
    $where = '';
    if($_SESSION['ao_level']==3)
      $where = "AND t2.salesman='{$_SESSION['ao_userid']}'";

    $ac_id = $_SESSION['ao_accountid'];
    $sql = "SELECT t2.salesman,CONCAT(t3.fname, ' ', t3.lname) as customer_name,t3.rate,SUM(TIMESTAMPDIFF(MINUTE,t1.start_time,t1.end_time)) as total_time,FORMAT(SUM((TIMESTAMPDIFF(MINUTE,t1.start_time,t1.end_time)/60)*t4.rate),2) as total_bill,IF(p.amnt IS NULL ,0,p.amnt) as total_paid
            FROM job_time_records as t1
            JOIN jobs as t2 on t2.job_id=t1.job_id
            LEFT JOIN users as t3 on t3.user_id=t2.salesman
            LEFT JOIN users as t4 on t4.user_id=t1.user_id
            LEFT JOIN 
            (
                SELECT t5.customer_id,SUM(t5.amount) as amnt 
                FROM tbl_customer_payment AS t5
                WHERE t5.payment_status='A'
                GROUP BY t5.customer_id
            ) as p ON p.customer_id=t2.salesman
            WHERE t2.account_id = '{$_SESSION['ao_accountid']}' $where GROUP BY t2.salesman";
    
    $timeecords = DBUtil::queryToArray($sql);

    foreach($timeecords as $key=>$row)
    {        
        $time = 0;
        if(!empty($row)){
              $time = $row['total_time'];
        }
        $h = $time / 60 % 24;
        $m = $time % 60; 
        $timeonjob = '';
        if($h > 1)
            $timeonjob .= $h.' hours ';
        elseif($h > 0)
            $timeonjob .= $h.' hour ';

        if($m > 1)
            $timeonjob .= $m.' minutes ';
        elseif($m > 0)
            $timeonjob .= $m.' minute ';

        $total_bill =  $row['total_bill'];
        $total_paid =  $row['total_paid'];
        $total_due =  $total_bill-$total_paid;
      ?>
    <tr valign='top' style="background-color: white;">
    <td> <?php echo ++$key;?></td>
    <td> <?php echo $row['customer_name'];?></td>

	<td> 
        <a href="<?=ROOT_DIR?>/balance_details.php?id=<?=$row['salesman']?>" title="View Bill Details" tooltip>
        <?=$timeonjob?> 
        </a>
    </td>
    <td style="text-align: center;"> 
        <?='$'.$total_bill?> 
    </td>
    <td style="text-align: center;"> 
        <?='$'.$total_paid?> 
    </td>
    <td style="text-align: center;"> 
        <?='$'.$total_due?> 
    </td>
    <td style="text-align: center;"> 
        <a class="btn btn-small btn-success" rel="open-modal"  data-script="balance/paybill.php?id=<?=$row['salesman']?>" title="<?='Pay - $'.$total_bill?>" tooltip>
        Pay Now
        </a>
    </td>
    </tr>
    <?php }?>
   <!--  <tr>
        <td colspan=7>
            <table border="0">
                <tr>
                    <td width='20'>
                        <img src='<?= IMAGES_DIR ?>/icons/print_16.png'>
                    </td>
                    <td><a href="<?= AJAX_DIR ?>/get_timerweekprint.php?ws=<?= $week_start_date ?>" target="_blank" class='boldlink'>Print</a></td>
                </tr>
            </table>
        </td>
    </tr> -->
  </table>