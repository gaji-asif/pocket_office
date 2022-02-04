<?php
if(!$name) { return; }
$stages = StageModel::getAllStages(TRUE);
?>
<select name="filter_<?=$name?>[]" class="tss-multi" multiple>
<?php
foreach($stages as $stage) {
?>
    <option value="<?=MapUtil::get($stage, 'stage_num')?>">
        <?=UIUtil::cleanOutput(MapUtil::get($stage, 'stage'))?>
    </option>
<?php
}
?>
</select>