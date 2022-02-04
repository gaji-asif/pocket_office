<?php
include '../common_lib.php';

if(!UserModel::loggedIn()) {
	die();
}

//update session
UserModel::updateSession();

//icon conversion table
//TODO: run script to modify database records for new icons...
$icon_conversions = array(
	'briefcase_16' => 'icon-briefcase',
	'flag_16' => 'icon-bullhorn',
	'search_16' => 'icon-search',
	'address_16' => 'icon-book',
	'email_16' => 'icon-envelope',
	'user_16' => 'icon-user',
	'default' => 'icon-file'
);

?>
<li class="strong">Browsing History</li>
<?php
$browsingHistory = UserModel::getBrowsingHistory();
foreach($browsingHistory as $row) {
?>
<li>
    <div class="btn-group">
        <div class="btn btn-small btn-block" rel="change-frame-location" data-url="<?=ROOT_DIR?>/<?=$row['script']?>?id=<?=$row['item_id']?>">
            <i class="<?=(isset($icon_conversions[$row['icon']])) ? $icon_conversions[$row['icon']] : $icon_conversions['default']?>"></i>&nbsp;<?=$row['title']?>
        </div>
    </div>
</li>
<?php
}