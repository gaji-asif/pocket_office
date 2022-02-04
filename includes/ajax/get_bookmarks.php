<?php
include '../common_lib.php';

if(!UserModel::loggedIn()) {
	die();
}
?>
<li class="strong">Your Bookmarks</li>
<?php
$bookmarks = UserUtil::getBookmarks();
foreach($bookmarks as $bookmark) {
?>
<li>
    <div class="btn-group" rel="change-frame-location" data-url="<?=ROOT_DIR?>/jobs.php?id=<?=MapUtil::get($bookmark, 'job_id')?>">
        <div class="btn btn-small btn-block">
            <i class="icon-briefcase"></i>&nbsp;<?=MapUtil::get($bookmark, 'job_number')?>
        </div>
    </div>
</li>
<?php
}