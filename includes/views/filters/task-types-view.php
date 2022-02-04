<?php
if(!$name) { return; }
$taskTypes = TaskModel::getAllTaskTypes();
?>
<select name="filter_<?=$name?>[]" class="tss-multi" multiple>
<?php
foreach($taskTypes as $taskType) {
?>
    <option value="<?=MapUtil::get($taskType, 'task_type_id')?>">
        <?=UIUtil::cleanOutput(MapUtil::get($taskType, 'task'))?>
    </option>
<?php
}
?>
</select>