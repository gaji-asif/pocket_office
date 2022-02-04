<?php
extract(@$options);
?>
<div class="flow-form">
<?php
if(!empty($errors)) {
    echo renderErrors($errors);
}
if(!empty($success_message)) {
    echo renderSuccessMessage($success_message);
}
?>
<form action="<?=@$action?>" class="<?=@$class?>" id="<?=@$id?>" method="<?=@$method?>" name="<?=@$name?>" <?=@$attrs?>>
    <?=@$output?>
<?php
if(@$submit_btn == TRUE || @$reset_btn == TRUE) {
?>
    <div class="row">
        <div class="col span-12 text-right">
            <div class="btn-group">
<?php
    if(@$reset_btn === TRUE) {
?>
                <input class="btn" type="reset" value="<?=@$reset_btn_label?>">
<?php
    }
    if(@$submit_btn === TRUE) {
?>
                <input class="btn btn-success" type="submit" name="submit" value="<?=@$submit_btn_label?>">
<?php
    }
?>
            </div>
        </div>
    </div>
<?php
}
?>
    </form>
</div>