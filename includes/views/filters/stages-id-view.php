<?php
if(!$name) { return; }
$stages = StageModel::getAllStages();
?>
<select name="filter_<?=$name?>[]" class="tss-multi" multiple>
<?php
foreach($stages as $stage) {
?>
    <option value="<?=MapUtil::get($stage, 'stage_id')?>">
        <?=UIUtil::cleanOutput(MapUtil::get($stage, 'stage'))?>
    </option>
<?php
}
?>
</select>