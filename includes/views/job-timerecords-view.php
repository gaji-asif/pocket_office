<?php
if(empty($myJob) || get_class($myJob) !== 'Job') { return; }

$action = RequestUtil::get('action');
$invoice_id = RequestUtil::get('invoice_id');

?>

<tr class="job-tab-content timerecords" <?=@$show_content_style?>>
    <td colspan=11>
        <div>
            
            <div class="clearfix">
                <div id="list_inv" style="min-height:30px;">
                    
                    
                    <div class="clearfix" style="padding:15px;">
    
                        <div id="list_meas" style="min-height:30px;">
                            
                            <table class="table-bordered table-condensed table-padded table-striped" width="100%">
        
                            <thead>
                                <tr>
                                    <th data-sort="string">#</th>
                                    <th data-sort="string">Start Time</th>
                                    <th data-sort="string">End Time</th>
                                    <th data-sort="string">Time on Job</th>
                                    <th data-sort="string">Decimal Hours</th>
                                    <th data-sort="string">Date</th>
                                    <th data-sort="string">User clocked in</th>
                                    <th data-sort="string">Task Type</th>
                                    <th data-sort="string">Status</th>
                                    <?php if($_SESSION['ao_level']==1){?>
                                    <th data-sort="string">Action</th>
                                    <?php }?>
                                </tr>
                            </thead>
        
                            <tbody id="timerecords-list">
                                <?php
                                $ac_id = $_SESSION['ao_accountid'];
                                $sql = "SELECT t1.job_time_record_id ,t1.record_date,t1.start_time,t1.end_time,t2.fname,t2.lname,t3.name,t1.status from job_time_records as t1 
                                    join users as t2 on t2.user_id=t1.user_id  
                                    left join timer_task_types as t3 on t3.task_id=t1.task_id 
                                    where t2.account_id=".$ac_id." and t1.job_id=".$myJob->job_id."
                                    order by t1.job_time_record_id desc";
                                $timeecords = DBUtil::queryToArray($sql);

                                if(count($timeecords)){
                            
                                $i=0;
                                $total_h = 0;
                                $total_m = 0;
                                $total_time_in_hour = 0;
                                foreach($timeecords as $row) 
                                {
                                    $i++;

                                    $time = strtotime($row['end_time'])-strtotime($row['start_time']);
                                    
                                    $h = $time / 3600 % 24;
                                    $m = $time / 60 % 60; 

                                    $total_h +=$h;
                                    $total_m +=$m;

                                    $timeonjob = '';
                                    if($h > 1)
                                        $timeonjob .= $h.' hours ';
                                    elseif($h > 0)
                                        $timeonjob .= $h.' hour ';

                                    if($m > 1)
                                        $timeonjob .= $m.' minutes ';
                                    elseif($m > 0)
                                        $timeonjob .= $m.' minute ';

                                    if(empty($timeonjob)){
                                        $total_m +=1;
                                        $timeonjob = '1 minute';
                                    }
                                    
                                    $total_in_min = $h*60 + $m;
                                    $time_in_hour = timeInDecimalHours($total_in_min);
                                    $total_time_in_hour +=$time_in_hour;

                                ?>
                                    <tr <?=($row['status']!='stop')?'style="color: red;"':''?>>
                                        <td><?=$i?></td>
                                        <td><?=date('h:i A',strtotime($row['start_time']))?></td>
                                        <td><?=date('h:i A',strtotime($row['end_time']))?></td>
                                        <td><?=$timeonjob?></td>
                                        <td><?=$time_in_hour?></td>
                                        <td><?=date('M d,Y',strtotime($row['record_date']))?></td>
                                        <td><?=$row['fname'].' '.$row['lname']?></td>
                                        <td><?=$row['name']?></td>
                                        <td><?=($row['status']!='stop')?'Clocked in':'Completed'?></td>
                                        <?php if($_SESSION['ao_level']==1){?>
                                        <td>
                                            <div class="btn btn-small btn-success"  rel="open-modal"  data-script="edit_userjobtime.php?job_id=<?=$myJob->job_id?>&timer_id=<?=$row['job_time_record_id']?>"
                                                title="Edit Time" tooltip>
                                                <i class="icon-pencil"></i>
                                            </div>
                                            </td>
                                        <?php }?>
                                    </tr>
                                <?php 
                                    
                                }

                                $e_h = (int)($total_m / 60); 
                                $e_m = $total_m % 60; 
                                $total_h = $total_h + $e_h;

                                $t_timeonjob = '';
                                if($total_h > 1)
                                    $t_timeonjob .= $total_h.' hours ';
                                elseif($total_h > 0)
                                    $t_timeonjob .= $total_h.' hour ';

                                if($e_m > 1)
                                    $t_timeonjob .= $e_m.' minutes ';
                                elseif($e_m > 0)
                                    $t_timeonjob .= $e_m.' minute ';


                                ?>
                                    <tr style="font-weight: bold;">
                                        <td colspan="3" style="text-align: center;">Total Time</td>
                                        <td><?=$t_timeonjob?></td>                                        
                                        <td colspan="<?=($_SESSION['ao_level']==1)?6:5?>"><?=$total_time_in_hour?></td>
                                    </tr>     
                                <?php
                                }
                                else
                                {
                                ?>
                                
                                <tr>
                                        <td style="text-align:center" colspan="<?php echo ($_SESSION['ao_level']==1)?9:8;?>"><br>No time records found for this job<br><br></td>
                                        
                                    </tr>
                                <?php 
                                }
                                ?>
        
        
                            </tbody>
        
                            </table>
        
        
        
                        </div>
    
                    
    
                    </div>
                </div>
            </div>
        </div>        
        
    </td>
</tr>



