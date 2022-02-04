<?php
if(!$name) { return; }
$failTypes = JobUtil::getAllFailTypes();
?>
<select name="filter_<?=$name?>[]" class="tss-multi" multiple>
<?php
foreach($failTypes as $failType) {
?>
    <option value="<?=MapUtil::get($failType, 'fail_type_id')?>">
        <?=UIUtil::cleanOutput(MapUtil::get($failType, 'fail_type'))?>
    </option>
<?php
}
?>
</select>