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
<table class="containertitle" width="100%">
    <tr>
        <td><a class="btn btn-blue btn-small" href="javascript:Request.make('includes/ajax/billing/get_timerweek.php?<?=http_build_query($prev)?>', 'schedulecontainer', true, true);"><i class="icon-angle-left"></i></a></td>
        <td colspan="2" width='100' align="center">
            Week Of <?=DateUtil::formatDate($week_start_date)?>
        </td>
        <td align="right"><a class="btn btn-blue btn-small" href="javascript:Request.make('includes/ajax/billing/get_timerweek.php?<?=http_build_query($next)?>', 'schedulecontainer', true, true);"><i class="icon-angle-right"></i></a></td>
    </tr>
</table>
<table border="0" class="schedulecontainer" width="100%">
    <tr>
    	<td align="center" style='font-weight: bold;'>#</td>
        <td align="center" style='font-weight: bold;'>Customer</td>
        <td align="center" style='font-weight: bold;'>Monday</td>        
        <td align="center" style='font-weight: bold;'>Tuesday</td>
        <td align="center" style='font-weight: bold;'>Wednesday</td>
        <td align="center" style='font-weight: bold;'>Thursday</td>
        <td align="center" style='font-weight: bold;'>Friday</td>
        <td align="center" style='font-weight: bold;'>Saturday</td>
        <td align="center" style='font-weight: bold;'>Sunday</td>
        <td align="center" style='font-weight: bold;'>Total</td>
    </tr>
    <?php 
    $id = RequestUtil::get('id');
    $where = '';
    if($_SESSION['ao_level']==3)
    {
      $where = "AND t2.salesman='{$_SESSION['ao_userid']}'";
    }
    elseif($id!=0)
    {
        $where = "AND t2.salesman='{$id}'";
    }
    $ac_id = $_SESSION['ao_accountid'];
    $sql = "SELECT t1.user_id,CONCAT(t1.fname, ' ', t1.lname) as customer_name
            FROM users as t1
            JOIN jobs as t2 on t2.salesman=t1.user_id
            JOIN job_time_records as t3 on t3.job_id=t2.job_id
            WHERE t2.account_id = '{$_SESSION['ao_accountid']}' $where  GROUP BY t1.user_id ORDER BY  t2.job_id DESC";
    $customers = DBUtil::queryToArray($sql);
    foreach($customers as $key=>$row){?>
    <tr valign='top'>
    <td> <?php echo ++$key;?></td>
    <td class="calendar-cell" style="border: 1px solid #999999; background-color: #ffffff;"> <?php echo $row['customer_name'];?></td>

	<?php

    $w_date = date('Y-m-d',strtotime('-1 Day',$next_week_start_date));
	$current_date_in_iteration = $week_start_date;
    $total_time = 0;
    $date='';
	for($i = 0; $i < 7; $i++)
	{
		$date = date('Y-m-d', $current_date_in_iteration);
		
		$border = 'border: 1px solid #999999; background-color: #ffffff;';
		?>
	        <td class="calendar-cell" style='<?php echo $border; ?>'>
	            <table border="0" width="100%" cellspacing="0" cellpadding="0">
	               
	                <tr>
	                    <td colspan=2>
        		<?php
        		//initialize empty arrays
        		$eventsArray = array();
        		$timer = CustomerModel::getTimer($date, $row['user_id']);
        		$days = "+7 day";
        		//echo '<pre>'; print_r($timer);
        		$nextdate = strtotime($days, strtotime($curDate));
        		$nextdate = date('Y-m-d', $nextdate);
                $time = 0;
        		if(!empty($timer)){
                    $time = $timer[0]['total_time'];
                    $total_time +=$timer[0]['total_time'];
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

                if(!empty($timeonjob)){
        		?>
                <div class="schedule-item event">
                    <p>
                    	<a href="<?=ROOT_DIR?>/billing-details.php?id=<?=$row['user_id']?>&date=<?=$date?>&w_date=<?=$w_date?>" title="<?=$timeonjob?> - View Time Breakup" tooltip>
                            <?=$timeonjob?>
                        </a>
                    </p>
                </div>
                <?php }?>
	                    </td>
	                </tr>
	            </table>
	        </td>
		<?php
			$current_date_in_iteration = strtotime("+1 day", $current_date_in_iteration);
		}

        $total_h = $total_time / 60 % 24;
        $total_m = $total_time % 60; 
        $total_timeonjob = '';
        if($total_h > 1)
            $total_timeonjob .= $total_h.' hours ';
        elseif($total_h > 0)
            $total_timeonjob .= $total_h.' hour ';

        if($total_m > 1)
            $total_timeonjob .= $total_m.' minutes ';
        elseif($total_m > 0)
            $total_timeonjob .= $total_m.' minute ';
	?>
	<td class="calendar-cell" style="border: 1px solid #999999; background-color: #ffffff;"> 
        <a href="<?=ROOT_DIR?>/billing-details.php?id=<?=$row['user_id']?>&w_date=<?=$date?>" title="<?=$total_timeonjob?> - View Time Breakup" tooltip>
        <?=$total_timeonjob?> 
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