<?php
include '../common_lib.php';
if(!ModuleUtil::checkAccess('add_job_journal')) {
	die('Insufficient Rights');
}

$myJob = new Job(RequestUtil::get('id'), FALSE);
$errors = array();
if(!$myJob->exists()) {
    $errors[] = 'Invalid job reference';
} else if(!ModuleUtil::checkJobModuleAccess('add_job_journal', $myJob)) {
    $errors[] = 'You do not have permission to add a journal to this job';
}

$journal = @mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('journal'));
$recipientIds = RequestUtil::get('recipients');
//print_r($recipientIds);
$rid = '';
if($recipientIds!="null")
$rid = implode(',',$recipientIds);
settype($recipientIds, 'array');

if(empty($journal)) {
    $errors[] = "Journal cannot be empty";
}

if(!count($errors)) {

    //mentions
    $mentions = UserModel::getMentions($journal);
    $mentionUserIds = array_unique(MapUtil::pluck($mentions, 'user_id'));
 
    $sql = "INSERT INTO journals (job_id, stage_num, task_id, text, user_id, timestamp, recipientid)
            VALUES ('{$myJob->job_id}', '{$myJob->stage_num}', NULL, '$journal', '{$_SESSION['ao_userid']}', now(), '$rid')";
    
    $results = DBUtil::query($sql);
    if(!$results) {
        LogUtil::getInstance()->logNotice('Failed to add Journal - ' . mysqli_error());
        return;
    }
    
    $newJournalId = DBUtil::getInsertId();
    
    foreach($recipientIds as $recipientId) {
        NotifyUtil::notifyFromTemplate('journal_posted', $recipientId, $_SESSION['ao_userid'], array('job_id' => $myJob->job_id, 'journal_id' => $newJournalId));
    }
    
    foreach($mentionUserIds as $mentionUserId) {
        NotifyUtil::notifyFromTemplate('journal_mention', $mentionUserId, $_SESSION['ao_userid'], array('job_id' => $myJob->job_id, 'journal_id' => $newJournalId), TRUE);
    }

    //store activity history
    JobModel::saveEvent($myJob->job_id, 'Added New Journal');
}

if(count($errors)) {
    echo AlertUtil::generate($errors);
    return;
}

$viewData = array(
    'myJob' => $myJob,
    'journal' => JobUtil::getJournalById($newJournalId)
);
echo ViewUtil::loadView('journal', $viewData);