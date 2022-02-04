<?php
include '../common_lib.php';

if(!UserModel::loggedIn()) {
	die();
}
?>
<li class="strong">Your New Jobs</li>
<?php
$jobs = UserModel::getNewJobs();
foreach($jobs as $job) {
?>
<li>
    <div class="btn-group" rel="change-frame-location" data-url="<?=ROOT_DIR?>/jobs.php?id=<?=$job['job_id']?>">
    <div class="btn btn-small btn-block">
        <i class="icon-briefcase"></i>&nbsp;
        <?=$job['job_number']?>
    </div>
    </div>
</li>
<?php
}