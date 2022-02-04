<?php
if(empty($error)) { return; }
?>
<div class="padded">
    <div class="alert alert-danger">
        <strong>Hey!</strong>
        <br /><br />
        <?=$error?>
        <br /><br />
        <a href="" rel="close-me">Click here to close</a>.
    </div>
</div>