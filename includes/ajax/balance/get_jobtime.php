<?php include '../../common_lib.php';
//echo $_SESSION['ao_founder'];

$customer = RequestUtil::get('id');
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr valign='top'>
    <td>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" class='smcontainertitle'>
        <tr>
          <th align="left" width="10%" data-sort="string">#</th>
          <th align="left" width="20%" data-sort="string">Insured</th>
          <th align="left" width="20%" data-sort="string">Job Number</th>
          <th align="left" width="50%" data-sort="string">Time on Job</th>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
  <td>
      <table border="0" cellspacing="0" cellpadding=2 width="100%" class="infocontainernopadding" id="resulttable" name="resulttable">
      <?php

      if($_SESSION['ao_level']==3)
         $customer = $_SESSION['ao_userid'];

      $ac_id = $_SESSION['ao_accountid'];
      $sql = "SELECT t1.job_id,t2.job_number,CONCAT(t3.fname, ' ', t3.lname) as isured,SUM(TIMESTAMPDIFF(MINUTE,t1.start_time,t1.end_time)) as total_time
        FROM job_time_records as t1
        JOIN jobs as t2 on t2.job_id=t1.job_id
        LEFT JOIN customers as t3 on t3.customer_id=t2.customer_id  
        WHERE t2.account_id = '{$_SESSION['ao_accountid']}' AND t2.salesman='{$customer}'
        GROUP BY t2.job_id ORDER BY t2.job_id ASC";
     
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
                <td width="10%"><?=$i?></td>
                <td width="20%"><?=$row['isured']?></td>
                <td width="20%"><?=$row['job_number']?></td>
                <td width="50%" ><?=$timeonjob?></td>
            </tr>
        <?php 
            
        }
      }?>

      
 
     </table>
     </td>             
    </tr>
    <tr><td class="infofooter"><a href="<?=ROOT_DIR?>/balance.php" class="basiclink"><i class="icon-double-angle-left"></i>&nbsp;Back</a></td></tr>

  </table>