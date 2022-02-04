<?php include '../../common_lib.php';
//echo $_SESSION['ao_founder'];

$customer = RequestUtil::get('id');
$date = RequestUtil::get('date');
$w_date = RequestUtil::get('w_date');

?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr valign='top'>
    <td>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" class='smcontainertitle'>
        <tr>
          <th align="left" width="5%" data-sort="string">#</th>
          <th align="left" width="13%" data-sort="string">Job Number</th>
          <th align="left" width="10%" data-sort="string">Start Time</th>
          <th align="left" width="10%" data-sort="string">End Time</th>
          <th align="left" width="15%" data-sort="string">Time on Job</th>
          <th align="left" width="10%" data-sort="string">Date</th>
          <th align="left" width="17%" data-sort="string">User clocked in</th>
          <th align="left" width="15%" data-sort="string">Task Type</th>
          <?php if($_SESSION['ao_level']==1){?>
          <th align="left" width="5%" data-sort="string">Action</th>
          <?php }?>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
  <td>
      <table border="0" cellspacing="0" cellpadding=2 width="100%" class="infocontainernopadding" id="resulttable" name="resulttable">
      <?php

      if(!empty($date))
        $where =  " AND t1.record_date='{$date}'";
      else
        $where =  " AND t1.record_date>'{$w_date}' - INTERVAL 7 DAY AND t1.record_date<='{$w_date}'";

      $ac_id = $_SESSION['ao_accountid'];
      $sql = "SELECT t1.job_id,t1.job_time_record_id ,t1.record_date,t1.start_time,t1.end_time,t2.job_number,t3.fname,t3.lname,t4.name,TIMESTAMPDIFF(MINUTE,t1.start_time,t1.end_time) as total_time
        FROM job_time_records as t1
        JOIN jobs as t2 on t2.job_id=t1.job_id
        LEFT JOIN users as t3 on t3.user_id=t1.user_id  
        LEFT JOIN timer_task_types as t4 on t4.task_id=t1.task_id 
        WHERE t2.account_id = '{$_SESSION['ao_accountid']}' AND t2.salesman='{$customer}' $where";
     
      $timeecords = DBUtil::queryToArray($sql);

      //echo "<pre>";print_r($timeecords);die;

      if(count($timeecords)){

        $i=0;
        foreach($timeecords as $row) 
        {
            $i++;

            $time = $row['total_time'];
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

            if(empty($timeonjob))
                $timeonjob = '1 minute';

        ?>
            <tr>
                <td width="5%"><?=$i?></td>
                <td width="13%"><?=$row['job_number']?></td>
                <td width="10%" ><?=date('h:i A',strtotime($row['start_time']))?></td>
                <td width="10%" ><?=date('h:i A',strtotime($row['end_time']))?></td>
                <td width="15%" ><?=$timeonjob?></td>
                <td width="10%" ><?=date('M d,Y',strtotime($row['record_date']))?></td>
                <td width="17%" ><?=$row['fname'].' '.$row['lname']?></td>
                <td width="15%" ><?=$row['name']?></td>
                <?php if($_SESSION['ao_level']==1){?>
                <td width="5%" >
                    <div class="btn btn-small btn-success"  rel="open-modal"  data-script="billing/edit_timer.php?job_id=<?=$row['job_id']?>&timer_id=<?=$row['job_time_record_id']?>"
                        title="Edit Time" tooltip>
                        <i class="icon-pencil"></i>
                    </div>
                    </td>
                <?php }?>
            </tr>
        <?php 
            
        }
      }?>

      
 
     </table>
     </td>             
    </tr>
    <?php $ws = strtotime('-6 day', strtotime($w_date));?>
    <tr><td class="infofooter"><a href="<?=ROOT_DIR?>/billing.php?ws=<?php echo $ws;?>" class="basiclink"><i class="icon-double-angle-left"></i>&nbsp;Back</a></td></tr>

  </table>