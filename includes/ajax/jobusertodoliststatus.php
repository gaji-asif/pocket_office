<?php
include '../common_lib.php';
if(!ModuleUtil::checkAccess('edit_users'))
    die("Insufficient Rights");

$ac_id = $_SESSION['ao_accountid'];
//get data
$userId = $_SESSION['ao_userid'];//RequestUtil::get('id');
$job_id = RequestUtil::get('job_id');
$item_id = RequestUtil::get('item_id');
$type = RequestUtil::get('type');
$tab = RequestUtil::get('tab');
$date_val = RequestUtil::get('date_val');
$status = RequestUtil::get('status');
if(empty($tab))
    $tab = 1;

if(!empty($item_id) && !empty($type)) 
{   
    if($type=='ca')
    {
        $sql = "SELECT t1.tbl_todolist_job_id from tbl_todolist_job as t1 
                left join tbl_todolist as t2 on t2.todolist_id=t1.todolist_id  
                join todolist_user_access as t3 on t3.tbl_todolist_job_id=t1.tbl_todolist_job_id 
                where t1.account_id=".$ac_id." and t1.todolist_id=".$item_id." and t1.is_deleted='0' AND t3.user_id='".$userId."'
                order by t1.order_num asc";
        $todolist_job = DBUtil::queryToArray($sql);
       
        foreach($todolist_job as $c_job)
        {
            $c_job_id = $c_job['tbl_todolist_job_id'];
            $sql="SELECT id,completed from todolist_job_status where user_id='$userId' AND job_id='$job_id' AND todolist_job_id='$c_job_id'";
            $is_exist = DBUtil::queryToArray($sql);
            if(!empty($is_exist)) 
            {
                $comp = ($type!='ca')?$is_exist[0]['completed']:(empty($status)?1:0);                
                $sql = "UPDATE todolist_job_status
                        SET completed='$comp'
                        WHERE user_id='$userId' AND job_id='$job_id' AND todolist_job_id='$c_job_id'";
            }
            else 
            {        
                $comp = ($type=='ca' && empty($status))?1:0;
                $sql = "INSERT INTO todolist_job_status (user_id, job_id, todolist_job_id, completed)
                            VALUES ('$userId', '$job_id', '$c_job_id','$comp')";                
            }
            DBUtil::query($sql);
        }
    }
    else
    {
        $sql="SELECT id,completed from todolist_job_status where user_id='$userId' AND job_id='$job_id' AND todolist_job_id='$item_id'";
        $is_exist = DBUtil::queryToArray($sql);
        if(!empty($is_exist)) 
        {
            $comp = ($type!='c')?$is_exist[0]['completed']:(($is_exist[0]['completed']=='1')?0:1);            
            $sql = "UPDATE todolist_job_status
                    SET completed='$comp'
                    WHERE user_id='$userId' AND job_id='$job_id' AND todolist_job_id='$item_id'";
        }
        else 
        {        
            $comp = ($type=='c')?1:0;
            $sql = "INSERT INTO todolist_job_status (user_id, job_id, todolist_job_id, completed)
                        VALUES ('$userId', '$job_id', '$item_id','$comp')";            
        }
        DBUtil::query($sql);
    }
}

if(!empty($item_id) && !empty($date_val)) 
{   
    $sql="SELECT id,date_of_complete from todolist_job_status where user_id='$userId' AND job_id='$job_id' AND todolist_job_id='$item_id'";
    $is_exist = DBUtil::queryToArray($sql);
    if(!empty($is_exist)) 
    {            
        $sql = "UPDATE todolist_job_status
                SET date_of_complete='$date_val'
                WHERE user_id='$userId' AND job_id='$job_id' AND todolist_job_id='$item_id'";
    }
    else 
    {        
        $sql = "INSERT INTO todolist_job_status (user_id, job_id, todolist_job_id, date_of_complete)
                    VALUES ('$userId', '$job_id', '$item_id','$date_val')";            
    }
    DBUtil::query($sql);

}

$sql = "SELECT todolist_id,todolist_name from tbl_todolist where account_id=".$ac_id." order by order_num asc";

$todolist = DBUtil::queryToArray($sql);

?>
<div>
    <ul class="nav nav-tabs">
        <?php 
        $i=0;
        foreach($todolist as $row){
            $sql = "SELECT t1.name from tbl_todolist_job as t1 left 
                    join tbl_todolist as t2 on t2.todolist_id=t1.todolist_id  
                    join todolist_user_access as t3 on t3.tbl_todolist_job_id=t1.tbl_todolist_job_id 
                    where t1.account_id=".$ac_id." and t1.todolist_id=".$row['todolist_id']." and t1.is_deleted='0' AND t3.user_id='".$userId."'
                    group by t1.tbl_todolist_job_id order by t1.order_num asc";
                    //echo $sql;die;
                    $joblist = DBUtil::queryToArray($sql);
            
        if(count($joblist))
        {
        $i++;
        ?>
        <li id="list_<?=$i?>"  class="tab_head <?=($i==$tab)?'active':''?>"><a href="javascript:void(0);" onclick="showtodolist_tab('list_<?=$i?>');"><?=$row['todolist_name']?> </a></li>  
        <?php }}?>      
    </ul>
    <div class="clearfix">
        <?php 
        $i=0;
        foreach($todolist as $row){
            $sql = "SELECT t1.tbl_todolist_job_id,t1.name,t4.completed,t4.date_of_complete from tbl_todolist_job as t1 left 
            join tbl_todolist as t2 on t2.todolist_id=t1.todolist_id  
            join todolist_user_access as t3 on t3.tbl_todolist_job_id=t1.tbl_todolist_job_id 
            left join todolist_job_status as t4 on t4.todolist_job_id=t1.tbl_todolist_job_id AND t3.user_id=t4.user_id AND t4.job_id='".$job_id."'
            where t1.account_id='".$ac_id."' and t1.todolist_id='".$row['todolist_id']."' and t1.is_deleted='0'  AND t3.user_id='".$userId."'
            group by t1.tbl_todolist_job_id order by t1.order_num asc";


            $joblist = DBUtil::queryToArray($sql);
            $count_joblist = count($joblist);
        if($count_joblist)
        {
            $c_count = 0;
            foreach($joblist as $todolistjob) {
                if($todolistjob['completed']=='1') {
                    $c_count++;
                }

            }
            $disabled='';
            if($c_count==$count_joblist)
                $disabled='disabled';

            $c_checked = ($c_count==$count_joblist)?'checked':'';

            $i++;
        ?>
        <div id="todo_list_<?=$i?>" class="list_details" style="min-height:30px;<?=($i==$tab)?'':'display:none;'?>">  
            <table class="table-bordered table-condensed table-padded table-striped" width="100%">
                    <thead>
                        <tr>
                            <th width="60%">To Do List Item</th>
                            <th width="20%"><input <?=$disabled?> <?= $c_checked?> type="checkbox" name="completed_all[<?=$row['todolist_id']?>]" value="<?=$row['todolist_id']?>"  onchange="checkBoxChange(this.value,'ca',<?= $i ?>,'<?= $c_checked?>');"> Completed</th>
                            <th width="20%"> Date </th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $j=0;
                    foreach($joblist as $todolistjob) {
                        $j++;
                        $strike = "";
                        $cchecked = '';
                        if($todolistjob['completed']=='1') {
                            $cchecked = 'checked';
                        }

                    ?>
                        <tr>
                            <td <?=$strike?> ><a href="javascript:void(0);" rel="open-modal"  data-script="todolist/todolist_details.php?id=<?=$row['todolist_id']?>&todolist_job_id=<?= $todolistjob['tbl_todolist_job_id'] ?>"  class='basiclink'><?=$todolistjob['name']?></a></td>
                            <td class="name"><input <?=$cchecked?> class="completed" type="checkbox" name="completed[<?=$todolistjob['tbl_todolist_job_id']?>]" value="<?=$todolistjob['tbl_todolist_job_id']?>" onchange="checkBoxChange(this.value,'c',<?= $i ?>,'<?=$cchecked?>');"></td>
                            <td><input style="background-color: whitesmoke;border: 1px solid black;color: black;" class="date_approval pikaday" type="textbox" name="date_approval[<?=$todolistjob['tbl_todolist_job_id']?>]" value="<?=$todolistjob['date_of_complete']?>" onblur="saveDateChange(this.value,<?= $i ?>,<?=$todolistjob['tbl_todolist_job_id']?>);"></td>
                        </tr>
                    <?php }
                    ?>  
                    </tbody>
            </table>                   
        </div>
        <?php }}?>
    </div>
</div>    