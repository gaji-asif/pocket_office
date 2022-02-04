<?php
include '../common_lib.php';
UserModel::isAuthenticated();
$myUser = UserModel::getMe();
$subscriptions = UserModel::getSubscriptions();

?>
<div class="btn-list-container">
<?php
$count = 0;
foreach($subscriptions as $job) {
	$jobData = JobUtil::getDataForNotification($job['job_id']);
	if(count($jobData) <= 1) {
		continue;
	}
?>
	<div class="btn btn-blue" rel="change-window-location" data-url="<?=ROOT_DIR?>/jobs.php?id=<?=$jobData['job_id']?>">
		<?=$jobData['lname']?>, <?=$jobData['fname'][0]?> (<?=$jobData['job_number']?>)
	</div>
<?php
    $count++;
}
if(!$count) {
?>
	<h1 class="no-results">No Subscriptions</h1>
<?php
}
?>
</div>