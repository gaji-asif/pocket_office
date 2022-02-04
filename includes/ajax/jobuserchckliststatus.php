<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('check-list'))
    die("Insufficient Rights");

$ac_id = $_SESSION['ao_accountid'];
//get data
$userId = RequestUtil::get('id');
$job_id = RequestUtil::get('job_id');
$item_id = RequestUtil::get('item_id');
$type = RequestUtil::get('type');
$tab = RequestUtil::get('tab');
$status = RequestUtil::get('status');
if(empty($tab))
    $tab = 1;

if(!empty($item_id) && !empty($type)) 
{   
    if($type=='ca' || $type=='na' || $type=='ra')
    {
         $sql = "SELECT t1.tbl_checklist_job_id from tbl_checklist_job as t1 
                left join tbl_checklist as t2 on t2.checklist_id=t1.checklist_id  
                join user_checklist_job_access as t3 on t3.tbl_checklist_job_id=t1.tbl_checklist_job_id 
                where t1.account_id=".$ac_id." and t1.checklist_id=".$item_id." and t1.is_deleted='0' AND t3.user_id='".$userId."'
                order by t1.order_num asc";
        $checklist_job = DBUtil::queryToArray($sql);

       
        foreach($checklist_job as $c_job)
        {
            $c_job_id = $c_job['tbl_checklist_job_id'];
            $sql="SELECT id,completed, na, reviewer_approval from checklist_job_status where user_id='$userId' AND job_id='$job_id' AND checklist_job_id='$c_job_id'";
            $is_exist = DBUtil::queryToArray($sql);
            if(!empty($is_exist)) 
            {
                $comp = ($type!='ca')?$is_exist[0]['completed']:(empty($status)?1:0);
                $na = ($type!='na')?$is_exist[0]['na']:(empty($status)?1:0);
                $rev = ($type!='ra')?$is_exist[0]['reviewer_approval']:(empty($status)?1:0);
                
                $sql = "UPDATE checklist_job_status
                        SET completed='$comp', na='$na', reviewer_approval='$rev' 
                        WHERE user_id='$userId' AND job_id='$job_id' AND checklist_job_id='$c_job_id'";

            }
            else 
            {        
                $comp = ($type=='ca' && empty($status))?1:0;
                $na = ($type=='na' && empty($status))?1:0;
                $rev = ($type=='ra' && empty($status))?1:0;
                $sql = "INSERT INTO checklist_job_status (user_id, job_id, checklist_job_id, completed, na, reviewer_approval)
                            VALUES ('$userId', '$job_id', '$c_job_id','$comp', '$na', '$rev')";
                
            }
            DBUtil::query($sql);
        }
    }
    else
    {
        
        $sql="SELECT id,completed, na, reviewer_approval from checklist_job_status where user_id='$userId' AND job_id='$job_id' AND checklist_job_id='$item_id'";
    
        $is_exist = DBUtil::queryToArray($sql);
        if(!empty($is_exist)) 
        {
            $comp = ($type!='c')?$is_exist[0]['completed']:(($is_exist[0]['completed']=='1')?0:1);
            $na = ($type!='n')?$is_exist[0]['na']:(($is_exist[0]['na']=='1')?0:1);
            $rev = ($type!='r')?$is_exist[0]['reviewer_approval']:(($is_exist[0]['reviewer_approval']=='1')?0:1);
            
            $sql = "UPDATE checklist_job_status
                    SET completed='$comp', na='$na', reviewer_approval='$rev' 
                    WHERE user_id='$userId' AND job_id='$job_id' AND checklist_job_id='$item_id'";

        }
        else 
        {        
            $comp = ($type=='c')?1:0;
            $na = ($type=='n')?1:0;
            $rev = ($type=='r')?1:0;
            $sql = "INSERT INTO checklist_job_status (user_id, job_id, checklist_job_id, completed, na, reviewer_approval)
                        VALUES ('$userId', '$job_id', '$item_id','$comp', '$na', '$rev')";
            
        }
   
        DBUtil::query($sql);
    }
}

$sql = "SELECT checklist_id,checklist_name from tbl_checklist where account_id=".$ac_id." order by order_num asc";
//echo $sql;
$checklist = DBUtil::queryToArray($sql);

?>
<div>
    <ul class="nav nav-tabs">
        <?php 
        $i=0;
        foreach($checklist as $row){
            $sql = "SELECT t1.name from tbl_checklist_job as t1 left 
                    join tbl_checklist as t2 on t2.checklist_id=t1.checklist_id  
                    join user_checklist_job_access as t3 on t3.tbl_checklist_job_id=t1.tbl_checklist_job_id 
                    where t1.account_id=".$ac_id." and t1.checklist_id=".$row['checklist_id']." and t1.is_deleted='0' AND t3.user_id='".$userId."'
                    group by t1.tbl_checklist_job_id order by t1.order_num asc";

                    $joblist = DBUtil::queryToArray($sql);
            
        if(count($joblist))
        {
        $i++;
        ?>
        <li id="list_<?=$i?>"  class="tab_head <?=($i==$tab)?'active':''?>"><a href="javascript:void(0);" onclick="showchecklist_tab('list_<?=$i?>');"><?=$row['checklist_name']?> </a></li>  
        <?php }}?>      
    </ul>
    <div class="clearfix">
        <?php 
        $i=0;
        foreach($checklist as $row){
            $sql = "SELECT t1.tbl_checklist_job_id,t1.name,t4.completed,t4.na,t4.reviewer_approval from tbl_checklist_job as t1 left 
            join tbl_checklist as t2 on t2.checklist_id=t1.checklist_id  
            join user_checklist_job_access as t3 on t3.tbl_checklist_job_id=t1.tbl_checklist_job_id 
            left join checklist_job_status as t4 on t4.checklist_job_id=t1.tbl_checklist_job_id AND t3.user_id=t4.user_id AND t4.job_id='".$job_id."'
            where t1.account_id='".$ac_id."' and t1.checklist_id='".$row['checklist_id']."' and t1.is_deleted='0'  AND t3.user_id='".$userId."'
            group by t1.tbl_checklist_job_id order by t1.order_num asc";

            //echo $sql;die;

            $joblist = DBUtil::queryToArray($sql);
            $count_joblist = count($joblist);
        if($count_joblist)
        {
            $c_count = 0;
            $n_count = 0;
            $r_count = 0;
            foreach($joblist as $checklistjob) {
                if($checklistjob['completed']=='1') {
                    $c_count++;
                }
                $nchecked = '';
                if($checklistjob['na']=='1') {
                    $n_count++;
                }
                $rchecked = '';
                if($checklistjob['reviewer_approval']=='1') {
                    $r_count++;
                }

            }
            $disabled='';
            if($n_count==$count_joblist)
                $disabled='disabled';

            $c_checked = ($c_count==$count_joblist)?'checked':'';
            $n_checked = ($n_count==$count_joblist)?'checked':'';
            $r_checked = ($r_count==$count_joblist)?'checked':'';

            $i++;
        ?>
        <div id="check_list_<?=$i?>" class="list_details" style="min-height:30px;<?=($i==$tab)?'':'display:none;'?>">  
            <table class="table-bordered table-condensed table-padded table-striped" width="100%">
                    <thead>
                        <tr>
                            <th width="40%">Checklist Item</th>
                            <th width="15%"><input <?=$disabled?> <?= $c_checked?> type="checkbox" name="completed_all[<?=$row['checklist_id']?>]" value="<?=$row['checklist_id']?>"  onchange="checkBoxChecklistChange(this.value,'ca',<?= $i ?>,'<?= $c_checked?>');"> Completed </th>
                            <th width="15%"><input <?= $n_checked?> type="checkbox" name="na_all[<?=$row['checklist_id']?>]" value="<?=$row['checklist_id']?>"  onchange="checkBoxChecklistChange(this.value,'na',<?= $i ?>,'<?= $n_checked?>');"> N/A </th>
                            <th width="15%"><input <?=$disabled?> <?= $r_checked?> type="checkbox" name="reviewer_all[<?=$row['checklist_id']?>]" value="<?=$row['checklist_id']?>"  onchange="checkBoxChecklistChange(this.value,'ra',<?= $i ?>,'<?= $r_checked?>');"> Reviewer Approval </th>
                            <th width="15%">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $j=0;
                    foreach($joblist as $checklistjob) {
                        $j++;
                        $strike = "";
                        $cchecked = '';
                        if($checklistjob['completed']=='1') {
                            $cchecked = 'checked';
                        }
                        $nchecked = '';
                        $disabled='';
                        if($checklistjob['na']=='1') {
                            $nchecked = 'checked';
                            $strike = "style='text-decoration: line-through'";
                            $disabled='disabled';
                        }
                        $rchecked = '';
                        if($checklistjob['reviewer_approval']=='1') {
                            $rchecked = 'checked';
                            $strike = "style='text-decoration: line-through'";
                        }

                    ?>
                        <tr>
                            <td <?=$strike?> ><a href="javascript:void(0);" rel="open-modal"  data-script="checklist_job/checklist_detals.php?id=<?=$row['checklist_id']?>&checklist_job_id=<?= $checklistjob['tbl_checklist_job_id'] ?>"  class='basiclink'><?=$checklistjob['name']?></a></td>
                            <td class="name"><input <?=$disabled?> <?=$cchecked?> class="completed" type="checkbox" name="completed[<?=$checklistjob['tbl_checklist_job_id']?>]" value="<?=$checklistjob['tbl_checklist_job_id']?>" onchange="checkBoxChecklistChange(this.value,'c',<?= $i ?>,'<?=$cchecked?>');"></td>
                            <td><input <?=$nchecked?> class="na" type="checkbox" name="na[<?=$checklistjob['tbl_checklist_job_id']?>]" value="<?=$checklistjob['tbl_checklist_job_id']?>" onchange="checkBoxChecklistChange(this.value,'n',<?= $i ?>,'<?=$nchecked?>');"></td>
                            <td><input <?=$disabled?> <?=$rchecked?> class="reviewer_approval" type="checkbox" name="reviewer_approval[<?=$checklistjob['tbl_checklist_job_id']?>]" value="<?=$checklistjob['tbl_checklist_job_id']?>" onchange="checkBoxChecklistChange(this.value,'r',<?= $i ?>,'<?=$rchecked?>');"></td>
                            <td>&nbsp;</td>
                        </tr>
                    <?php }
                    ?>  
                    </tbody>
            </table>                   
        </div>
        <?php }}?>
    </div>
</div>    