<?php

include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
if(!ModuleUtil::checkAccess('edit_users'))
    die("Insufficient Rights");

$ac_id = $_SESSION['ao_accountid'];
//get data
$userId = RequestUtil::get('userid');
$job_id = RequestUtil::get('id');
$item_id = RequestUtil::get('item_id');
$type = RequestUtil::get('type');
$tab = RequestUtil::get('tab');
$status = RequestUtil::get('status');
$sqljob = "SELECT salesman from  jobs where job_id=".$job_id;
    
        $salesman = DBUtil::queryToArray($sqljob);
        $salesman=$salesman[0]['salesman'];
        $sqllevel = "SELECT level from users where user_id='".$salesman."'";
        $sqllevel = DBUtil::queryToArray($sqllevel);
        $salesmanlevel=!empty($sqllevel)?$sqllevel[0]['level']:'';
 
if(empty($tab))
    $tab = 1;

if(!empty($item_id) && !empty($type)) 
{   
    if($type=='ca' || $type=='na' || $type=='ra')
    {
         $sql = "SELECT t1.tbl_codegenerator_job_id from tbl_codegenerator_job as t1 
                left join tbl_codegenerator as t2 on t2.codegenerator_id=t1.codegenerator_id  
                join user_codegenerator_job_access as t3 on t3.tbl_codegenerator_job_id=t1.tbl_codegenerator_job_id 
                where t1.account_id=".$ac_id." and t1.codegenerator_id=".$item_id." and t1.is_deleted='0' AND t3.user_id='".$userId."'
                order by t1.order_num asc";
        $codegenerator_job = DBUtil::queryToArray($sql);
       
        foreach($codegenerator_job as $c_job)
        {
            $c_job_id = $c_job['tbl_codegenerator_job_id'];
            $sql="SELECT id,completed, na, reviewer_approval from codegenerator_job_status where user_id='$userId' AND job_id='$job_id' AND codegenerator_job_id='$c_job_id'";
            $is_exist = DBUtil::queryToArray($sql);
            if(!empty($is_exist)) 
            {
                $comp = ($type!='ca')?$is_exist[0]['completed']:(empty($status)?1:0);
                $na = ($type!='na')?$is_exist[0]['na']:(empty($status)?1:0);
                $rev = ($type!='ra')?$is_exist[0]['reviewer_approval']:(empty($status)?1:0);
                
                $sql = "UPDATE codegenerator_job_status
                        SET completed='$comp', na='$na', reviewer_approval='$rev' 
                        WHERE user_id='$userId' AND job_id='$job_id' AND codegenerator_job_id='$c_job_id'";

            }
            else 
            {        
                $comp = ($type=='ca' && empty($status))?1:0;
                $na = ($type=='na' && empty($status))?1:0;
                $rev = ($type=='ra' && empty($status))?1:0;
                $sql = "INSERT INTO codegenerator_job_status (user_id, job_id, codegenerator_job_id, completed, na, reviewer_approval)
                            VALUES ('$userId', '$job_id', '$c_job_id','$comp', '$na', '$rev')";
                
            }
            DBUtil::query($sql);
        }
    }
    else
    {
        $sql="SELECT id,completed, na, reviewer_approval from codegenerator_job_status where user_id='$userId' AND job_id='$job_id' AND codegeneratorjob_id='$item_id'";
        $is_exist = DBUtil::queryToArray($sql);
        if(!empty($is_exist)) 
        {
            $comp = ($type!='c')?$is_exist[0]['completed']:(($is_exist[0]['completed']=='1')?0:1);
            $na = ($type!='n')?$is_exist[0]['na']:(($is_exist[0]['na']=='1')?0:1);
            $rev = ($type!='r')?$is_exist[0]['reviewer_approval']:(($is_exist[0]['reviewer_approval']=='1')?0:1);
            
            $sql = "UPDATE codegenerator_job_status
                    SET completed='$comp', na='$na', reviewer_approval='$rev' 
                    WHERE user_id='$userId' AND job_id='$job_id' AND codegenerator_job_id='$item_id'";

        }
        else 
        {        
            $comp = ($type=='c')?1:0;
            $na = ($type=='n')?1:0;
            $rev = ($type=='r')?1:0;
            $sql = "INSERT INTO codegenerator_job_status (user_id, job_id, codegenerator_job_id, completed, na, reviewer_approval)
                        VALUES ('$userId', '$job_id', '$item_id','$comp', '$na', '$rev')";
            
        }
        DBUtil::query($sql);
    }
}

$sql = "SELECT codegenerator_id,codegenerator_name from tbl_codegenerator where account_id=".$ac_id." order by order_num asc";

$codegenerator = DBUtil::queryToArray($sql);

?>
<script src="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<link rel="stylesheet" href="../../css/style.css">
</style>
    <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">

    <tr valign="center">

        <td align="center" style="font-size: 35px;">Job Code Generator</td>

        <td align="right">

            <i class="icon-remove grey btn-close-modal"></i>

        </td>

    </tr>

</table>
    <ul class="nav nav-tabs">
        <?php 
        $i=0;
        foreach($codegenerator as $row){
                 $sql = "SELECT t1.name from tbl_codegenerator_job as t1 left
                    join tbl_codegenerator as t2 on t2.codegenerator_id=t1.codegenerator_id 
                    join user_codegenerator_job_access as t3 on t3.tbl_codegenerator_job_id=t1.tbl_codegenerator_job_id
                    where t1.codegenerator_id=".$row['codegenerator_id']." and t1.is_deleted='0' AND t3.user_id='".$salesman."' 
                    group by t1.tbl_codegenerator_job_id order by t1.order_num asc";
                    $joblist = DBUtil::queryToArray($sql);
                    
                   if(count($joblist)==0)
                   {
                        $sql = "SELECT t1.name from tbl_codegenerator_job as t1 left
                            join tbl_codegenerator as t2 on t2.codegenerator_id=t1.codegenerator_id 
                            join code_generator_ownership_access as t3 on t3.tbl_codegenerator_job_id=t1.tbl_codegenerator_job_id
                            where t1.codegenerator_id=".$row['codegenerator_id']." and t1.is_deleted='0' AND t3.level_id='".$salesmanlevel."' 
                            group by t1.tbl_codegenerator_job_id order by t1.order_num asc";
                            $joblist = DBUtil::queryToArray($sql);
                   }
                   if($row['codegenerator_id']==9)
                   {
                       //echo "<pre>";print_r($joblist);
                   }
            
        if(count($joblist))
        {
        $i++;
        ?>
       
        <li id="list_<?=$i?>"  class="tab_head <?=($i==$tab)?'active':''?>"><a class="tab-head-a" href="javascript:void(0);" onclick="showcodegenerator_tab('list_<?=$i?>');"><?=$row['codegenerator_name']?> </a></li>  
        <?php }}?>      
    </ul>
    <div class="clearfix">
        <?php 
        $i=0;
        foreach($codegenerator as $row){
            $sql = "SELECT t1.tbl_codegenerator_job_id,t1.name from tbl_codegenerator_job as t1 left 
            join tbl_codegenerator as t2 on t2.codegenerator_id=t1.codegenerator_id  
            join user_codegenerator_job_access  as t4 on  t4.tbl_codegenerator_job_id=t1.tbl_codegenerator_job_id 
            where t1.codegenerator_id=".$row['codegenerator_id']." and t1.is_deleted='0' AND t4.user_id='".$salesman."' 
            group by t1.tbl_codegenerator_job_id order by t1.tbl_codegenerator_job_id asc";
            $joblist = DBUtil::queryToArray($sql);
                if(count($joblist)==0)
                {
                      $sql = "SELECT t1.tbl_codegenerator_job_id,t1.name from tbl_codegenerator_job as t1 left 
                        join tbl_codegenerator as t2 on t2.codegenerator_id=t1.codegenerator_id  
                        join code_generator_ownership_access as t3 on t3.tbl_codegenerator_job_id=t1.tbl_codegenerator_job_id 
                        where t1.codegenerator_id=".$row['codegenerator_id']." and t1.is_deleted='0' AND t3.level_id='".$salesmanlevel."' 
                        group by t1.tbl_codegenerator_job_id order by t1.tbl_codegenerator_job_id asc";
                        $joblist = DBUtil::queryToArray($sql);
                }
                if($row['codegenerator_id']==9)
                {
                   //echo "<pre>";print_r($joblist);
                }
        if(count($joblist))
        {
            $c_count = 0;
            $n_count = 0;
            $r_count = 0;
            foreach($joblist as $codegeneratorjob) {
                if($codegeneratorjob['completed']=='1') {
                    $c_count++;
                }
                $nchecked = '';
                if($codegeneratorjob['na']=='1') {
                    $n_count++;
                }
                $rchecked = '';
                if($codegeneratorjob['reviewer_approval']=='1') {
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
        <div id="code_generatorlist_<?=$i?>" class="list_details" style="min-height:30px;<?=($i==$tab)?'':'display:none;'?>">  
            <table class="table-bordered table-condensed table-padded table-striped" width="100%">
                    <thead>
                        <tr>
                            <th width="5%">Select Item</th>
                            <th width="40%">Code generator Item</th>
                            <!--<th width="15%"> Generate PDF</th>-->
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $j=0;
                    //echo "<pre>";print_r($joblist);
                    foreach($joblist as $codegeneratorjob) {
                        $j++;
                        $strike = "";
                        $cchecked = '';
                        if($codegeneratorjob['completed']=='1') {
                            $cchecked = 'checked';
                        }
                        $nchecked = '';
                        $disabled='';
                        if($codegeneratorjob['na']=='1') {
                            $nchecked = 'checked';
                            $strike = "style='text-decoration: line-through'";
                            $disabled='disabled';
                        }
                        $rchecked = '';
                        if($codegeneratorjob['reviewer_approval']=='1') {
                            $rchecked = 'checked';
                            $strike = "style='text-decoration: line-through'";
                        }

                    ?>
                        <tr>
                            <?php
                                   @$sql="SELECT * FROM code_generator_ownership_access WHERE level_id =".$salesmanlevel." AND tbl_codegenerator_job_id=".$codegeneratorjob['tbl_codegenerator_job_id'];
                                   $myaccessownership = DBUtil::queryToArray($sql);
                                   @ $sql="SELECT * FROM user_codegenerator_job_access WHERE user_id =".$salesman." AND tbl_codegenerator_job_id=".$codegeneratorjob['tbl_codegenerator_job_id'];
                                   $mylevelownership  = DBUtil::queryToArray($sql);
                                   // echo  count($mylevelownership)."}";
                                    //echo count( $myaccessownership);
                                    if(count($myaccessownership)<=0 and count($mylevelownership)>0)
                                    {
                                        
                                 ?>
                             
                                     <td <?=$strike?> >
                                        
                                         <input class="pdcheck" type="checkbox"  value="<?= $codegeneratorjob['tbl_codegenerator_job_id'] ?>">
                                         </td>
                                    <td <?=$strike?> >
                                        <a target="_blank" href="<?=AJAX_DIR?>/codegenerator_job/codegenerator_details.php?id=<?=$row['codegenerator_id']?>&codegenerator_job_id=<?= $codegeneratorjob['tbl_codegenerator_job_id'] ?>"  class='basiclink'><?=$codegeneratorjob['name']?></a>
                                    </td>
                            <?php
                            $flag=1;
                                       
                                    }
                                   if(count($myaccessownership)>0 and count($mylevelownership)<=0 )
                                    {?>
                                    
                                     <td <?=$strike?> >
                                        
                                         <input class="pdcheck" type="checkbox"  value="<?= $codegeneratorjob['tbl_codegenerator_job_id'] ?>">
                                         </td>
                                    <td <?=$strike?> >
                                        <a target="_blank" href="<?=AJAX_DIR?>/codegenerator_job/codegenerator_details.php?id=<?=$row['codegenerator_id']?>&codegenerator_job_id=<?= $codegeneratorjob['tbl_codegenerator_job_id'] ?>"  class='basiclink'><?=$codegeneratorjob['name']?></a>
                                    </td>
                                    <?php $flag=1;
                                     }
                                    
                             if(count($myaccessownership)>0 and count($mylevelownership)>0 )
                                    {?>
                                      
                                     <td <?=$strike?> 
                                     
                                         <input class="pdcheck" type="checkbox"  value="<?= $codegeneratorjob['tbl_codegenerator_job_id'] ?>">
                                         </td>
                                    <td <?=$strike?> >
                                        <a target="_blank" href="<?=AJAX_DIR?>/codegenerator_job/codegenerator_details.php?id=<?=$row['codegenerator_id']?>&codegenerator_job_id=<?= $codegeneratorjob['tbl_codegenerator_job_id'] ?>"  class='basiclink'><?=$codegeneratorjob['name']?></a>
                                    </td>
                                    <?php $flag=1;
                                     }
                                    
                            ?>
                        </tr>
                    <?php }
                    ?> 
                    <?php if($flag!=1){?> <tr><td colspan="2">Nothing Found!!</td></tr><?php $flag=1; } else {?>
                    <tr>
                        <td colspan="2" style="text-align:center;">
                            <a onclick="createpdf()" href="javascript:void(0)" class="genpdf btn btn-blue btn-block" style="width:200px;float: left;margin: 20px 10px;">Generate PDF</a>
                            <!--<a onclick="createzip()" href="javascript:void(0)" class="genzip btn btn-blue btn-block">Create Attachments Zip </a>-->
                            <a  target="_blank" style="display:none;width:200px;float: left;margin: 20px 10px;" class="dowpdf btn btn-blue btn-block">Click Here To Download</a>
                        </td
                    ></tr>
                     <?php }?>
                    
                    </tbody>
            </table>                   
        </div>
        <?php }}?>
        <?php if($flag!=1){?>
         <table class="table-bordered table-condensed table-padded table-striped" width="100%">
                    <thead>
                        <tr>
                            <td><p>Nothing Found!!</p></td>
        </tr>
        <thead>
            </table>
        <?php } ?>
    </div>

<script>
  function showcodegenerator_tab(tab_val)
    { 
        //alert("#code_generator"+tab_val);
        $(".list_details").hide();               
        $(".tab_head").removeClass('active');
        $("#code_generator"+tab_val).show();
        $("#"+tab_val).addClass('active');
        //$( '.pdcheck').prop('checked', false);
    }
   function createpdf()
    { 
        $(".dowpdf").hide();
        var arr=[];
        var i =0;
        if ($('.pdcheck').is(':checked')) {
            $(".genpdf").text('Generating');
            $( '.pdcheck').each(function() {
                if ($(this).is(':checked'))
                {
                arr[i++]=$(this).val();
                }
            });
            var jsonString = JSON.stringify(arr);
               $.ajax({
                    type: "POST",
                    url: "<?=AJAX_DIR?>/codegenerator_job/codegenerator_pdf.php",
                    data: {data : jsonString}, 
                    cache: false,
            
                    success: function(r){
                        if(r!='error'){
                            $(".dowpdf").attr("href",r);
                            $(".dowpdf").show();
                            $(".genpdf").text('Generate PDF');
                        }
                        else
                        {
                            alert('Error!!!');
                        }
                    }
                });
        }
        else
        {
         alert('Please select at least a record!!!');
        }
    }
     function createzip()
    { 
        $(".dowpdf").hide();
        var arr=[];
        var i =0;
        if ($('.pdcheck').is(':checked')) {
            $( '.pdcheck').each(function() {
                if ($(this).is(':checked'))
                {
                 arr[i++]=$(this).val();
                }
            });
            var jsonString = JSON.stringify(arr);
               $.ajax({
                    type: "POST",
                    url: "<?=AJAX_DIR?>/codegenerator_job/codegenerator_zip.php",
                    data: {data : jsonString}, 
                    cache: false,
            
                    success: function(r){
                        if(r!='error'){
                            $(".dowpdf").attr("href",r);
                            $(".dowpdf").show();
                        }
                        else
                        {
                            alert('Error!!!');
                        }
                    }
                });
        }
        else
        {
         alert('Please select at least a record!!!');
        }
    }
</script>