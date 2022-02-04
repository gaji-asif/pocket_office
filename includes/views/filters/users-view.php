<?php
if(!$name) { return; }
$users = UserModel::getAll(TRUE, TRUE);
?>
<select name="filter_<?=$name?>[]" class="tss-multi" multiple>
<?php
foreach($users as $user) {
?>
    <option value="<?=MapUtil::get($user, 'user_id')?>">
        <?=UIUtil::cleanOutput(MapUtil::get($user, 'select_label'))?>
    </option>
<?php
}
?>
</select>