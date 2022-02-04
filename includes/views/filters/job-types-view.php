<?php
if(!$name) { return; }
$jobTypes = JobUtil::getAllJobTypes();
?>
<select name="filter_<?=$name?>[]" class="tss-multi" multiple>
<?php
foreach($jobTypes as $jobType) {
?>
    <option value="<?=MapUtil::get($jobType, 'job_type_id')?>">
        <?=UIUtil::cleanOutput(MapUtil::get($jobType, 'job_type'))?>
    </option>
<?php
}
?>
</select>