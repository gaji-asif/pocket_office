<div class="row">
    <div class="col span-3 row-label"><p><?=@$label?></p></div>
    <div class="col span-9">
        <select class="<?=@$class?>" id="<?=@$id?>" name="<?=@$name?>" type="<?=@$type?>" <?=@$attrs?> />
            <option value=""></option>
<?php
foreach(@$values as $value)
{
?>
            <option value="<?=$value[@$keys[0]]?>" <?=$selected != $value[@$keys[0]] ?: 'selected'?>>
                <?=$value[@$keys[1]]?>
            </option>
<?php
}
?>
        </select>
<?php
if(!empty($helper_text)) {
?>
        <span class="muted"><?=@$helper_text?></span>
<?php
}
?>
    </div>
</div>