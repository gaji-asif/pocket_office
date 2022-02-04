<?php
if(@$type != 'hidden') {
?>
<div class="row">
    <div class="col span-3 row-label"><p><?=@$label?></p></div>
    <div class="col span-9">
<?php
}
?>
        <input class="<?=@$class?>" id="<?=@$id?>" name="<?=@$name?>" type="<?=@$type?>" value="<?=@$value?>" <?=@$attrs?> />
<?php
if(@$type != 'hidden') {
    if(!empty($helper_text)) {
?>
        <span class="muted"><?=@$helper_text?></span>
<?php
    }
?>
    </div>
</div>
<?php
}
