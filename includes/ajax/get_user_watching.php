<?php
include '../common_lib.php';
UserModel::isAuthenticated();

$user = UserModel::getMe();

$jobsBeingWatched = UserModel::getConversationsBeingWatchedByType('job');
?>
<div class="btn-list-container">
<?php
$count = 0;
foreach($jobsBeingWatched as $job) {
	$jobData = JobUtil::getDataForNotification($job['conversation_id']);
	if(empty($jobData)) {
		continue;
	}
?>
	<div id="watch-jobs-<?=$jobData['job_id']?>" class="btn-group">
		<div class="btn btn-blue" rel="change-window-location" data-url="<?=ROOT_DIR?>/jobs.php?id=<?=$jobData['job_id']?>">
			<?=$jobData['lname']?>, <?=$jobData['fname'][0]?> (<?=$jobData['job_number']?>)
		</div>
		<div class="btn btn-blue" rel="stop-watching" data-conversation-id="<?=$jobData['job_id']?>" data-type="jobs"><i class="icon-remove"></i></div>
	</div>
<?php
	$count++;
}
if($count == 0)
{
?>
	<h1 class="no-results">No Conversations Being Watched</h1>
<?php
}
?>
</div>
