<style>
    #status-bar {
        top: 0px;
        height: 29px;
        box-sizing: border-box;
    }
</style>
<div class="clearfix" id="status-bar" <?=defined('DEVELOPMENT') ? '' : ''?>>
    <div id="status-bar-items">
        <div class="search">
            <input class="global-search-input" id="global-search-input" type="text" />
            <i class="icon-search"></i>
            <ul id="global-search-suggest"></ul>
        </div>
        <div class="btn-group pull-right">
            <div class="btn btn-small" rel="load-browsing-history">
                <i class="icon-time"></i>
                <div>
                    <ul id="browsing-history-items"></ul>
                </div>
            </div>
            <div class="btn btn-small" rel="load-new-jobs">
                <i class="icon-briefcase"></i>
                <div>
                    <ul id="new-jobs"></ul>
                </div>
            </div>
            <div class="btn btn-small" rel="load-bookmarks">
                <i class="icon-bookmark-empty"></i>
                <div>
                    <ul id="bookmarks"></ul>
                </div>
            </div>
            <div class="btn btn-small">
                <i class="icon-user"></i>
                <div>
                    <ul id="user-menu">
                        <li class="strong"><?=$_SESSION['ao_fname']?> <?=$_SESSION['ao_lname']?></li>
                        <li>
                            <i class="icon-user"></i>&nbsp;
                            <a href="" rel="change-frame-location" data-url="users.php?id=<?=$_SESSION['ao_userid']?>" data-type="user" data-id="<?=$_SESSION['ao_userid']?>">
                                Profile
                            </a>
                        </li>
                        <li>
                            <i class="icon-cogs"></i>&nbsp;
                            <a href="" rel="change-frame-location" data-url="settings.php">
                                Settings
                            </a>
                        </li>
<?php
if(UserModel::isSystemUser()) {
?>
                        <li>
                            <i class="icon-cogs"></i>&nbsp;
                            <a href="system" target="_parent">System Admin</a>
                        </li>
<?php
}
?>
                        <li>
                            <i class="icon-off"></i>&nbsp;
                            <a href="index.php?action=logout">
                                Sign Out
                            </a>
                        </li>
                    </ul>
                </div>
                
            </div>
        </div>
    </div>
</div>
<script>
	$(document).ready(function(){
		loadStatusBar();
	});
	
</script>
