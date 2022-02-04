<?php
if(empty($error)) { return; }
?>
<div class="padded">
    <div class="alert alert-danger">
        <strong>Hey!</strong>
        <br /><br />
        <?=$error?>
    </div>
</div>