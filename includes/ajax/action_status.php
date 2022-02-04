<?php 
include '../common_lib.php';


$id = RequestUtil::get('id');
$status = RequestUtil::get('status');
$success=0;
if(RequestUtil::get('update')) {
    if(!empty($id)) {
        if($status)
           $status=0;
        else
           $status=1;

        $sql = 'update job_actions set active = "'.$status.'" where job_action_id = '.$id;
        
        DBUtil::query($sql);
        UIUtil::showAlert('Action Status modified');
        $success=1;
    } else {
        UIUtil::showAlert('Required information missing');
    }
}




