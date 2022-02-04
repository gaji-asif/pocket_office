<?php
if(!is_array($columns) || !count($columns)) { return; }
?>
<select name="columns[]" class="tss-multi" multiple>
<?php
foreach($columns as $key => $column) {
?>
    <option value="<?=$key?>">
        <?=StrUtil::humanizeCamelCase($key)?>
    </option>
<?php
}
?>
</select>