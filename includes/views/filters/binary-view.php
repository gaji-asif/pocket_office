<?php
if(!$name) { return; }
?>
<div>
    <div class="control-binary off clearfix">
        <div class="on">Yes</div>
        <div class="off">No</div>
        <input name="filter_<?=$name?>" type="hidden" />
    </div>
</div>