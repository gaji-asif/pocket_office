<div class="alert alert-danger">
<?php
if(isset($errors) && is_array($errors)){
    foreach($errors as $error) {
?>
        <div><?=$error?></div>
<?php
    }
}
?>
</div>