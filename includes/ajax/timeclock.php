<?php

include '../common_lib.php';

$user_id = $_SESSION['ao_userid'];

$job_id = RequestUtil::get('job_id');
$status = RequestUtil::get('status');

$myJob = new Job($job_id);

$time_in_word = '';
$name='';

$sql = "SELECT t2.fname,t2.lname,t1.job_time_record_id,t1.record_date,t1.start_time,t1.end_time from job_time_records as t1 JOIN users as t2 ON t2.user_id=t1.user_id
        where t1.user_id!=".$user_id." AND t1.job_id=".$job_id."  AND status='start'
        order by t1.job_time_record_id desc";
$is_exist = DBUtil::queryToArray($sql);
if(!empty($is_exist))
{
    $time_in_word = 'exist';
    $name = $is_exist[0]['lname'].' '.$is_exist[0]['fname'];
}
else
{
    $name='';
    $sql = "UPDATE job_time_records SET end_time=IF((time_to_sec(TIMEDIFF(NOW(),end_time))/3600)>=1,ADDTIME(end_time,'0:5:0'),NOW()),status='stop' where user_id=".$user_id." AND job_id!=".$job_id." AND status='start'";
    DBUtil::query($sql);

    $time = 0;
    $sql = "SELECT t1.job_time_record_id,t1.record_date,t1.start_time,t1.end_time from job_time_records as t1 
        where t1.user_id=".$user_id." AND t1.job_id=".$job_id."  AND status='start'
        order by t1.job_time_record_id desc";
    $usertime = DBUtil::queryToArray($sql);
    $started='';
    if(count($usertime)>0)
    {
        $timeclock_id = $usertime[0]['job_time_record_id'];
        if($status==1){
            $s = 'start';
            $sql = "UPDATE job_time_records
                SET end_time=NOW(), status='$s'
                WHERE  job_time_record_id='$timeclock_id'";
        }
        elseif($status==2){
            $s = 'stop';
            $sql = "UPDATE job_time_records
                SET end_time=IF(end_time IS NULL ,ADDTIME(start_time,'0:5:0'),ADDTIME(end_time,'0:5:0')), 
                status='$s' 
                WHERE  job_time_record_id='$timeclock_id'";
        }
        else{
            $s = 'stop';
            $sql = "UPDATE job_time_records
                SET end_time=NOW(), status='$s'
                WHERE  job_time_record_id='$timeclock_id'";
        }

        
    }
    else
    {   
        $sql = "INSERT INTO job_time_records (job_id, stage_num, user_id, record_date, start_time,end_time, status)
                VALUES ('$job_id', '$myJob->stage_num', '$user_id',DATE(NOW()),NOW(),NOW(), 'start')";

    }

            
    DBUtil::query($sql);

    $sql = "SELECT t1.job_time_record_id,t1.record_date,t1.start_time,t1.end_time from job_time_records as t1 
        where t1.user_id=".$user_id." AND t1.job_id=".$job_id."  AND status='start'
        order by t1.job_time_record_id desc";
    $usertime = DBUtil::queryToArray($sql);
    if(count($usertime)>0){
        $time = (strtotime($usertime[0]['end_time']) -  strtotime($usertime[0]['start_time']));
        /*$h = $time / 3600 % 24;
        $m = $time / 60 % 60; 
        $s = $time % 60;
        if($h > 1)
            $time_in_word .= $h.' hours ';
        elseif($h > 0)
            $time_in_word .= $h.' hour ';

        if($m > 1)
            $time_in_word .= $m.' minutes ';
        elseif($m > 0)
            $time_in_word .= $m.' minute ';

        if($s > 1)
            $time_in_word .= $s.' seconds ';
        elseif($m > 0)
            $time_in_word .= $s.' second ';*/

        $time_in_word = $time;

    }
}
$return_array[] = $time_in_word;
$return_array[] = $name;


echo $time_in_word;