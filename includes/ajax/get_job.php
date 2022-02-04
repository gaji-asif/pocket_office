<?php

    //ini_set('display_errors', 'On');

    //error_reporting(E_ALL | E_STRICT);



include '../common_lib.php';

if(!ModuleUtil::checkAccess('view_jobs'))

die("Insufficient Rights");



$sheetId = RequestUtil::get('sheet_id');

$taskId = RequestUtil::get('taskid');

$action = RequestUtil::get('action');

$tab = RequestUtil::get('tab') ?: 'details';

$jobId = RequestUtil::get('id');



$myJob = new Job($jobId);



ModuleUtil::checkJobModuleAccess('view_jobs', $myJob, TRUE, TRUE);



if($action=='confirm' && !empty($sheetId)) {

    $sql = "update sheets set confirmed=curdate() where sheet_id = $sheetId && job_id='".$myJob->job_id."' limit 1";

	DBUtil::query($sql);

	$myJob = new Job($jobId);

}



if($action=='unconfirm') {

    $sql = "update sheets set confirmed=null where sheet_id = $sheetId && job_id='".$myJob->job_id."' limit 1";

    DBUtil::query($sql);

	$myJob = new Job($jobId);

}



if($action=='marktaskpaid'&&(ModuleUtil::checkAccess('edit_job_task')||(moduleOwnership('edit_job_task')&&(JobUtil::isSubscriber($myJob->job_id) || $myJob->salesman_id==$_SESSION['ao_userid']||$myJob->user_id==$_SESSION['ao_userid']))))

{

    $myTask = new Task($taskId);



    $sql = "update tasks set paid=now() where task_id='".$myTask->task_id."' and job_id='".$myJob->job_id."' and account_id='".$_SESSION['ao_accountid']."' limit 1";

    DBUtil::query($sql);

    $myJob = new Job($jobId);

    JobModel::saveEvent($myTask->job_id, 'Task Details Modified');



	NotifyUtil::notifySubscribersFromTemplate('modify_task', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id, 'task_id' => $myTask->task_id));

}



if($action=='marktaskunpaid'&&(ModuleUtil::checkAccess('edit_job_task')||(moduleOwnership('edit_job_task')&&(JobUtil::isSubscriber($myJob->job_id) || $myJob->salesman_id==$_SESSION['ao_userid']||$myJob->user_id==$_SESSION['ao_userid']))))

{

	$myTask = new Task($taskId);



	$sql = "update tasks set paid=null where task_id='".$myTask->task_id."' and job_id='".$myJob->job_id."' and account_id='".$_SESSION['ao_accountid']."' limit 1";

	DBUtil::query($sql);

	$myJob = new Job($jobId);

	JobModel::saveEvent($myTask->job_id, 'Task Details Modified');



	NotifyUtil::notifySubscribersFromTemplate('modify_task', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id, 'task_id' => $myTask->task_id));

}



if($action=='paid'&&(ModuleUtil::checkAccess('mark_paid')||(moduleOwnership('mark_paid')&&(JobUtil::isSubscriber($myJob->job_id) || $myJob->salesman_id==$_SESSION['ao_userid']||$myJob->user_id==$_SESSION['ao_userid']))))

{

    $sql = "update jobs set pif_date=curdate() where job_id='".$myJob->job_id."' and account_id='".$_SESSION['ao_accountid']."' limit 1";

    DBUtil::query($sql);

    $myJob = new Job($jobId);

    JobModel::saveEvent($myJob->job_id, "Marked Paid in Full");



	NotifyUtil::notifySubscribersFromTemplate('job_marked_paid', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id));

}

if($action=='unpaid'&&(ModuleUtil::checkAccess('mark_paid')||(moduleOwnership('mark_paid')&&(JobUtil::isSubscriber($myJob->job_id) || $myJob->salesman_id==$_SESSION['ao_userid']||$myJob->user_id==$_SESSION['ao_userid']))))

{

    $sql = "update jobs set pif_date=null where job_id='".$myJob->job_id."' and account_id='".$_SESSION['ao_accountid']."' limit 1";

    DBUtil::query($sql);

    $myJob = new Job($jobId);

    JobModel::saveEvent($myJob->job_id, "Marked Unpaid");

}



$myCustomer = new Customer($myJob->customer_id);

?>

<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">

<?php

$viewData = array(

	'myJob' => $myJob,

	'quick_settings_spacer' => true,

	'row_class' => 'no-hover',

    'key' => 0

);

echo ViewUtil::loadView('job-list-row', $viewData);



$tabsArray = array(

    'details',

    'journals',

	'subscribers',

    'uploads',

    'contacts',
    
    'checklist',
    
    'To Do List',
    
    'Time Records',

    'invoice',

    'email'

);



$viewData = array(

    'myJob' => $myJob,

    'myCustomer' => $myCustomer,

    'cur_tab' => $tab,

    'tabsArray' => $tabsArray

);



echo ViewUtil::loadView('job-tabs', $viewData);



foreach($tabsArray as $key => $t) {

    $viewData['show_content_style'] = '';

    if($t == $tab) {
       
        $viewData['show_content_style'] = 'style="display: table-row;"';

    }
    
    if($t == 'Time Records')
        $t ='timerecords';
    elseif($t == 'Check List')
         $t ='checklist';
    elseif($t == 'To Do List')
        $t ='todolist';

    echo ViewUtil::loadView('job-' . $t, $viewData);

}

if($myJob->salesman_id == $_SESSION['ao_userid']) {

    JobUtil::storeJobViewHistory($myJob->job_id);

}

UserModel::storeBrowsingHistory($myJob->job_number, 'briefcase_16', 'jobs.php', $myJob->job_id);





?>

  <tr><td colspan=11>&nbsp;</td></tr>

  <tr class='odd'>

    <td colspan=11 class='infofooter'>

      <table border="0" cellpadding="0" cellspacing="0" width="100%">

        <tr>

          <td>

            <a href="javascript:clearElement('notes'); Request.make('<?=AJAX_DIR?>/get_joblist.php?<?=$_SESSION['ao_full_joblist_query_string']?>', 'jobscontainer', true, true);" class='basiclink'>

				<i class="icon-double-angle-left"></i>&nbsp;Back

			</a>

          </td>

        </tr>

      </table>

    </td>

  </tr>

</table>

<script>

function refresh(closeModal, callback) {

    Request.make('<?=AJAX_DIR?>/get_job.php?id=<?=$jobId?>', 'jobscontainer', true, true, function() {

        if(closeModal) {

            deleteOverlay();

        }

        Functions.executeCallback(callback);

    });

}

</script>