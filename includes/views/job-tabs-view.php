<?php
$stages = StageModel::getAllStages($groupByStageNum = TRUE);
$jobActions = JobUtil::getActions();
$numStages = count($stages)-1;

//start Job Viewr tagging and viewing
$view_at = date('Y-m-d H:i:s');

    $sql = 'insert into job_viewer (job_id,user_id,view_at) values('.$myJob->job_id.','.$_SESSION['ao_userid'].',"'.$view_at.'")';
    DBUtil::query($sql);


$job_viewer = DBUtil::queryToArray('select t2.fname,t2.lname from job_viewer as t1 join users as t2 on t2.user_id=t1.user_id where t1.user_id!='.$_SESSION["ao_userid"].' AND t1.job_id='.$myJob->job_id.' group by t1.user_id' );
//End Job Viewr tagging and viewing
//echo "<pre>";print_r($job_viewer);die;

$stage = DBUtil::queryToArray('select order_num from stages where stage_num='.$myJob->stage_num);
$stage_order=0;
if(count($stage)>0)
{
    foreach($stage as $row) {
        $stage_order=$row['order_num'];
    }
}


if($numStages>0){
    //$percentage = floor(($myJob->stage_num / $numStages) * 100);
    $percentage = floor(($stage_order / $numStages) * 100);    
}else
    $percentage=0;

$percentage = $percentage > 100 ? 100 : $percentage;
?>
<tr>
    <td colspan=11>
        <table class="progress_bar_div" width='100%' border=0 cellpadding=5 cellspacing=0 style='border-top:1px solid #cccccc;'>
            <tr>
                <td colspan="10">
                    <table border=0 width='100%' border=0 cellpadding=5 cellspacing=0>
                        <tr valign='middle'>
                            <td>
                                <div class="bar">
                                    <div class="percentage" style="width: <?=$percentage?>%;"><?=$percentage?>%</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
<?php
foreach($tabsArray as $key => $tab) {
    $active = ($tab == $cur_tab) || (empty($cur_tab) && $key == 0) ? 'active' : '';
    //echo $tab;
    $class = $tab;
    if($tab == 'Time Records')
        $class ='timerecords';
    elseif($tab == 'To Do List')
        $class ='todolist';

?>
                <td class="job-tab-link <?=$active?> <?=$class?>" rel="switch-job-tab" data-tab="<?=$class?>"><?=ucfirst($tab)?></td>
<?php
}
?>
                <td class="job-tab-link-filler">
                    <table border=0>
                        <tr>
                            <td width=20 align='center'><img src='<?= ROOT_DIR ?>/images/icons/bookmark_16.png'></td>
                            <td id='bookmark'>
<?php

$active = '';
if(!JobUtil::jobIsBookmarked($myJob->job_id)) {
    $bookmarkLinkText = 'Bookmark';
}
else {
    $bookmarkLinkText = 'Remove Bookmark';
    $active = 'active';
}
?>
                                    <a href="#" data-job-id="<?=$myJob->job_id?>" class="basiclink bookmark-link <?=$active?>"><?=$bookmarkLinkText?></a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>

<tr><td>&nbsp:</td></tr>
<tr>
    <td colspan=11>
        <table width='100%' border=0>
            <tr valign='top'>
                <td colspan=11>
                    <table border=0>
            <?php


            if(ModuleUtil::checkJobModuleAccess('modify_job', $myJob)) 
            {
            ?>
                    <tr>
                        <td>
                            <b>Actions:</b>
                            <select name='myactions' id='myactions'>
                            <?php
                            	foreach($jobActions as $jobAction) 
                                {
                            		if(ModuleUtil::checkJobModuleAccess(MapUtil::get($jobAction, 'hook'), $myJob))
                                    {
                            ?>
                                        <option value="<?=MapUtil::get($jobAction, 'script')?>?id=<?=$myJob->job_id?>"><?=MapUtil::get($jobAction, 'action')?></option>
                            <?php
                            		}
                            	}
                            ?>
                            </select>
                            <input type="button" value="Go" onclick="applyOverlay($('#myactions').val());">
                        </td>
                    </tr>
            <?php
            }

            if(ModuleUtil::checkAccess('full_job_stage_access')) 
            {
            ?>
                <tr>
                    <td>
                        <b>Jump to Stage:</b>
                        <select name='mystages' id='mystages'>
                        <?php
                            reset($stages);
                            
                            foreach($stages as $stage) {
                                if(stageAdvanceAccess($stage['stage_num'])) {
                        ?>
                                    <option value="<?=$stage['stage_num']?>" <?=($stage['stage_num'] == $myJob->stage_num ? 'selected' : '')?>><?=$stage['stage']?></option>
                        <?php
                                }
                            }
                        ?>
                        </select>
                        <input type="button" value="Go" onclick="changeStage('<?=$myJob->job_id?>', $('#mystages').val());">
                    </td>
                </tr>
            <?php
            }
            ?>
            
            <?php
            if(count($job_viewer)>0) 
            {
            ?>
            <tr>
                <td>
                    <div class="pillbox clearfix">
                        <b>Job Viewer: </b> 
                        <?php foreach($job_viewer as $viewer){?>
                        <div class="btn btn-blue" title="Viewing This job" tooltip>
                            <i class="icon-user"></i> <?=$viewer['lname'].' '.$viewer['fname']?>
                        </div>
                        <?php }?>
                    </div>
                </td>
            </tr>
            <?php }?>
            
                        
                </table>
            </td>

            <?php
                $time = 0;
                $user_id = $_SESSION['ao_userid'];
                $sql = "SELECT t1.record_date,t1.start_time,t1.end_time,t1.status from job_time_records as t1 
                    where t1.user_id=".$user_id." AND t1.job_id=".$myJob->job_id." AND status='start'
                    order by t1.job_time_record_id desc";
                $usertime = DBUtil::queryToArray($sql);

                
                $status='0';
                $start_status = 0;
                if(count($usertime)>0){
                    $time = (strtotime($usertime[0]['end_time']) -  strtotime($usertime[0]['start_time']));

                    $time_in_word = calculate($time);
                    

                    if($usertime[0]['status']=='start')
                        $status='1';


                    $end_time_diff = strtotime(date('Y-m-d H:i:s')) -  strtotime($usertime[0]['end_time']);
                    $h = $end_time_diff / 3600 % 24;
                    if($h>=1)
                    {
                        $start_status = 1;
                    }

                }

                function calculate($time)
                {
                    $time_in_word = 'Time on job ';
                    $h = $time / 3600 % 24;
                    $m = $time / 60 % 60; 
                    $s = $time % 60;
                    if($h > 1)
                        $time_in_word .= $h.' hours ';
                    elseif($h > 0)
                        $time_in_word .= $h.' hour ';

                    if($m > 1)
                        $time_in_word .= $m.' minutes ';
                    elseif($m > 0)
                        $time_in_word .= $m.' minutes ';

                    if($s > 1)
                        $time_in_word .= $s.' seconds ';
                    else
                        $time_in_word .= $s.' second ';

                    return $time_in_word;
                }

                

                ?>

            <td align='right'>
                <table cellspacing=0 border=0>
                    <tr>
                        <td class='timer_area' id='timer_area' width=100>
                        
                            <input type="hidden" id="time_job_id" value="<?php echo $myJob->job_id;?>">
                            <input type="hidden" id="timeclock_id" value="">
                            <a style="display: <?php echo ($status)?'none':'';?>" href="javascript:void(0);"  onclick="startTimer();" class="btn btn-block btn-success btn-start-timer">
                                Start Time</i>
                            </a>
                            <a style="display: <?php echo ($status)?'':'none';?>;"  href="javascript:void(0);" class="btn btn-block btn-success  btn-stop-timer">
                                 </i>
                            </a>

                            <a rel="open-modal" data-script="stop_timer.php?job_id=<?php echo $myJob->job_id;?>&action=" style="display: <?php echo ($status)?'':'none';?>; width: 100px;" href="javascript:void(0);" class="btn btn-block btn-danger btn-end-timer">
                                End Now</i>
                            </a>

<div class="time_script_area">
<script>
    GLOBALS.loading_no_counter++;
    $(function() {
        $('div.percentage').animate({width: '<?=$percentage?>%'}, (50 * <?=$percentage?>), 'easeInOutQuart');
    });
    if(GLOBALS.loading_no_counter<=1)
    {
        var time = '<?php echo $time;?>';
        function increse_time()
        {        
            time = parseInt(time) + 1;
            var time_in_word = 'Time on job ';
            var h = parseInt(time/3600) % 24;        
            var m = parseInt(time / 60) % 60; 
            var s = time % 60;
            if(h > 1)
                time_in_word += h+' hours ';
            else if(h > 0)
                time_in_word += h+' hour ';

            if(m > 1)
                time_in_word += m+' minutes ';
            else if(m > 0)
                time_in_word += m+' minutes ';

            if(s > 1)
                time_in_word += s+' seconds ';
            else
                time_in_word += s+' second ';


            $(".btn-stop-timer").text(time_in_word);

        }
        
        $(document).ready(function(e) 
        {        
            setInterval(increse_time,1000);
            var status=1;
            <?php if($start_status){?>

                        if(!confirm("Are you sure? You are still working on this job for last few hours?"))
                        {
                            var time_job_id = $("#time_job_id").val();
                            $.ajax({
                                url: '<?php echo AJAX_DIR;?>/timeclock.php',
                                data: {job_id:time_job_id,status:2},
                                success: function(data) {       
                                    $(".btn-start-timer").show();  
                                    $(".btn-stop-timer").text('').hide();
                                    $(".btn-end-timer").hide();
                                    time = 0;
                                    increse_time();
                                },
                            });
                        }
                        else
                        {
                            <?php if($status){?>
                                var time_job_id = $("#time_job_id").val();
                                $.ajax({
                                    url: '<?php echo AJAX_DIR;?>/timeclock.php',
                                    data: {job_id:time_job_id,status:1},
                                    success: function(data) {       
                                        if(data=='exist'){
                                            alert('Someone working on this job! Please go to Time Records Tab to se in details!');
                                        }else{
                                            time = data;
                                            increse_time();
                                        }
                                    },
                                });
                            <?php }?>
                        }
                        
            <?php }elseif($status){?>
                        var time_job_id = $("#time_job_id").val();
                        $.ajax({
                            url: '<?php echo AJAX_DIR;?>/timeclock.php',
                            data: {job_id:time_job_id,status:1},
                            success: function(data) {       
                                if(data=='exist'){
                                    alert('Someone working on this job! Please go to Time Records Tab to se in details!');
                                }else{
                                    time = data;
                                    increse_time();
                                }
                            },
                        });
            <?php }?>

        });

        function timestamp() {
           
        }

        function startTimer()
        {
            if(confirm("Would you ike to start timer for this job?"))
            {
                $(".btn-start-timer").hide();  
                $(".btn-stop-timer").show();
                $(".btn-end-timer").show();
                var time_job_id = $("#time_job_id").val();
                $.ajax({
                    url: '<?php echo AJAX_DIR;?>/timeclock.php',
                    data: {job_id:time_job_id,status:1},
                    success: function(data) {   
                        console.log(data);    
                        if(data=='exist'){
                            $(".btn-start-timer").show();  
                            $(".btn-stop-timer").text('').hide();
                            $(".btn-end-timer").hide();
                            alert('Someone working on this job! Please go to Time Records Tab to se in details!');      
                            time = 0;
                        }else{
                            time = data;
                            increse_time();
                        }                    
                    },
                });
            }
        }
    }

</script>
</div>

                        </td>
                    </tr>
                </table>
            </td>
            
            <td align='right'>
                <table cellspacing=0 border=0>
                    <?php
                    if($myJob->pif_date == '') {
                        $iconStr = "<img src='" . ROOT_DIR . "/images/icons/dollar_grey_16.png'>";
                    if(ModuleUtil::checkAccess('mark_paid') || (moduleOwnership('mark_paid') && (JobUtil::isSubscriber($myJob->job_id) || $myJob->salesman_id == $_SESSION['ao_userid'] || $myJob->user_id == $_SESSION['ao_userid'])))
                        $iconStr = "<span onmouseover='this.style.cursor=\"pointer\";' onclick='if(confirm(\"Are you sure you want to mark paid?\")){Request.make(\"includes/ajax/get_job.php?action=paid&id=" . $myJob->job_id . "\",\"jobscontainer\",\"yes\",\"yes\");}'><img title='Mark Paid' src='" . ROOT_DIR . "/images/icons/dollar_grey_16.png' tooltip></span>";
                    ?>
                            <tr>
                                <td rowspan=2 align='right'><?php echo $iconStr; ?></td>
                                <td class='smalltitle' width=100>Job Not Paid</td>
                            </tr>
                    <?php
                    }
                    else 
                    {
                        $iconStr = "<img src='" . ROOT_DIR . "/images/icons/dollar_32.png'>";
                        if((ModuleUtil::checkAccess('mark_paid') || (moduleOwnership('mark_paid') && (JobUtil::isSubscriber($myJob->job_id) || $myJob->salesman_id == $_SESSION['ao_userid'] || $myJob->user_id == $_SESSION['ao_userid']))))
                        $iconStr = "<span onmouseover='this.style.cursor=\"pointer\";' onclick='if(confirm(\"Are you sure you want to mark unpaid?\")){Request.make(\"includes/ajax/get_job.php?action=unpaid&id=" . $myJob->job_id . "\",\"jobscontainer\",\"yes\",\"yes\");}'><img title='Mark Unpaid' src='" . ROOT_DIR . "/images/icons/dollar_32.png' tooltip></span>";
                    ?>
                        <tr>
                            <td rowspan=3 align='right'width=32><?php echo $iconStr; ?></td>
                            <td width=100><b>Job Paid in Full</b></td>
                        </tr>
                        <tr>
                            <td class='smallnote'>
                                on <?=DateUtil::formatDate($myJob->pif_date)?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            </td>
            </tr>
        </table>
    </td>
</tr>

