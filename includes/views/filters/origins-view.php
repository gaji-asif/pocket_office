<?php
if(!$name) { return; }
$origins = JobUtil::getAllOrigins();
?>
<select name="filter_<?=$name?>[]" class="tss-multi" multiple>
<?php
foreach($origins as $origin) {
?>
    <option value="<?=MapUtil::get($origin, 'origin_id')?>">
        <?=UIUtil::cleanOutput(MapUtil::get($origin, 'origin'))?>
    </option>
<?php
}
?>
</select>